<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\TestWith;
use Tests\TestCase;

class LaundryWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_seeded_catalog_displays_the_five_guide_services(): void
    {
        $this->seed();

        $this->get('/')->assertSee('Layanan Laundry')->assertSee('Perkiraan berat (Kg)')
            ->assertSee('Jumlah (Pcs)')->assertSee('Nomor WhatsApp');

        $this->assertDatabaseCount('services', 5);
        $this->assertDatabaseHas('users', ['email' => 'admin@gmail.com', 'is_admin' => true]);
    }

    #[TestWith(['/dashboard'])]
    #[TestWith(['/admin/dashboard'])]
    #[TestWith(['/admin/services'])]
    #[TestWith(['/admin/services/create'])]
    public function test_guests_are_redirected_from_admin_pages(string $path): void
    {
        $this->get($path)->assertRedirect('/login');
    }

    public function test_registered_customer_cannot_access_the_admin_dashboard_or_manage_services(): void
    {
        $this->actingAs(User::factory()->create());

        $this->get('/dashboard')->assertForbidden();
        $this->get('/admin/dashboard')->assertForbidden();
        $this->post(route('services.store'), [])->assertForbidden();
        $this->patch(route('admin.orders.updateStatus', Order::factory()->create()), ['status' => 'Dicuci'])->assertForbidden();
    }

    public function test_admin_can_create_edit_and_delete_a_service(): void
    {
        $admin = User::factory()->admin()->create();
        $payload = [
            'service_name' => 'Cuci Uji',
            'category' => 'Kiloan',
            'price_per_unit' => 25000,
            'unit_type' => 'Kg',
            'description' => 'Layanan uji.',
        ];

        $this->actingAs($admin)->post(route('services.store'), $payload)
            ->assertRedirect(route('services.index'))->assertSessionHas('success');

        $service = Service::sole();
        $this->get(route('services.index'))->assertSee('Cuci Uji');
        $this->get(route('services.edit', $service))->assertSee('Cuci Uji');

        $payload['service_name'] = 'Setrika Uji';
        $payload['category'] = 'Satuan';
        $payload['unit_type'] = 'Pcs';
        $this->put(route('services.update', $service), $payload)->assertRedirect(route('services.index'));
        $this->assertDatabaseHas('services', ['id' => $service->id, ...$payload]);

        $this->delete(route('services.destroy', $service))->assertRedirect(route('services.index'));
        $this->assertModelMissing($service);
    }

    public function test_admin_cannot_delete_a_service_that_has_been_ordered(): void
    {
        $service = Service::factory()->create();
        $detail = OrderDetail::factory()->for($service)->create();

        $this->actingAs(User::factory()->admin()->create())->from(route('services.index'))
            ->delete(route('services.destroy', $service))
            ->assertRedirect(route('services.index'))->assertSessionHas('error');

        $this->assertModelExists($service);
        $this->assertModelExists($detail);
    }

    public function test_admin_can_change_tarif_but_not_the_unit_of_an_ordered_service(): void
    {
        $service = Service::factory()->create();
        $detail = OrderDetail::factory()->for($service)->create();
        $payload = [
            'service_name' => $service->service_name,
            'category' => $service->category,
            'price_per_unit' => 30000,
            'unit_type' => 'Pcs',
            'description' => $service->description,
        ];

        $this->actingAs(User::factory()->admin()->create());
        $this->put(route('services.update', $service), $payload)->assertSessionHasErrors('unit_type');
        $this->assertSame('Kg', $service->refresh()->unit_type);

        $payload['unit_type'] = 'Kg';
        $this->put(route('services.update', $service), $payload)->assertRedirect(route('services.index'));
        $this->assertDatabaseHas('services', ['id' => $service->id, 'price_per_unit' => 30000]);
        $this->assertDatabaseHas('order_details', ['id' => $detail->id, 'subtotal' => 25000]);
    }

    public function test_invalid_service_is_rejected_without_writing_data(): void
    {
        $this->actingAs(User::factory()->admin()->create())->post(route('services.store'), [
            'service_name' => '', 'category' => 'Tidak Ada', 'price_per_unit' => -1, 'unit_type' => 'Gram', 'description' => '',
        ])->assertSessionHasErrors(['service_name', 'category', 'price_per_unit', 'unit_type', 'description']);

        $this->assertDatabaseCount('services', 0);
    }

    public function test_checkout_rejects_an_empty_selection(): void
    {
        $item = Service::factory()->create();

        $this->from('/')->post('/checkout', ['customer_name' => 'Pelanggan Uji',
            'whatsapp_number' => '081234567890', 'items' => [$item->id => 0]])
            ->assertRedirect('/')->assertSessionHas('error', 'Pilih minimal satu layanan!');

        $this->assertDatabaseCount('orders', 0);
    }

    public function test_checkout_rejects_invalid_customer_information(): void
    {
        $this->post('/checkout', ['customer_name' => '', 'whatsapp_number' => 'nomor-salah', 'items' => []])
            ->assertSessionHasErrors(['customer_name', 'whatsapp_number', 'items']);

        $this->assertDatabaseCount('orders', 0);
    }

    #[TestWith([999])]
    #[TestWith(['1invalid'])]
    public function test_checkout_rejects_an_unknown_service_id(int|string $serviceId): void
    {
        Service::factory()->create(['id' => 1]);

        $this->from('/')->post('/checkout', ['customer_name' => 'Pelanggan Uji',
            'whatsapp_number' => '081234567890', 'items' => [$serviceId => 1]])->assertRedirect('/')->assertSessionHasErrors('items');

        $this->assertDatabaseCount('orders', 0);
        $this->assertDatabaseCount('order_details', 0);
    }

    public function test_checkout_saves_the_order_and_calculates_the_total(): void
    {
        $item = Service::factory()->create(['price_per_unit' => 25000, 'unit_type' => 'Kg']);
        $other = Service::factory()->create(['price_per_unit' => 5000, 'unit_type' => 'Pcs']);

        $this->from('/')->post('/checkout', ['customer_name' => 'Pelanggan Uji',
            'whatsapp_number' => '081234567890', 'items' => [$item->id => 2, $other->id => 3]])
            ->assertRedirect('/')->assertSessionHas('success');

        $this->assertDatabaseHas('orders', ['customer_name' => 'Pelanggan Uji',
            'whatsapp_number' => '081234567890', 'total_price' => 65000, 'status' => 'Antrean']);
        $this->assertDatabaseHas('order_details', ['service_id' => $item->id, 'qty_or_weight' => 2, 'subtotal' => 50000]);
        $this->assertDatabaseHas('order_details', ['service_id' => $other->id, 'qty_or_weight' => 3, 'subtotal' => 15000]);
    }

    public function test_admin_dashboard_displays_orders_and_can_update_status(): void
    {
        $order = Order::factory()->create(['customer_name' => 'Pelanggan Dashboard']);
        OrderDetail::factory()->for($order)->create();

        $this->actingAs(User::factory()->admin()->create())->get('/dashboard')->assertSee('Pelanggan Dashboard')->assertSee($order->whatsapp_number);
        foreach (['Dicuci', 'Selesai'] as $status) {
            $this->from('/dashboard')->patch(route('admin.orders.updateStatus', $order), ['status' => $status])
                ->assertRedirect('/dashboard')->assertSessionHas('success');

            $this->assertDatabaseHas('orders', ['id' => $order->id, 'status' => $status]);
        }

        $this->patch(route('admin.orders.updateStatus', $order), ['status' => 'cancelled'])
            ->assertSessionHasErrors('status');
        $this->assertSame('Selesai', $order->refresh()->status);
    }
}
