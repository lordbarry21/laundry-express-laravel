<?php

namespace Tests\Feature;

use App\Models\LaundryItem;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\TestWith;
use Tests\TestCase;

class LaundryWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_seeded_catalog_displays_the_five_guide_services(): void
    {
        $this->seed();

        $this->get('/')->assertSee('Layanan Laundry')->assertSee('Cuci Baju Reguler')
            ->assertSee('Setrika Baju Ekspres')->assertSee('Paket Cuci dan Setrika');

        $this->assertDatabaseCount('laundry_items', 5);
        $this->assertDatabaseHas('users', ['email' => 'admin@gmail.com']);
    }

    #[TestWith(['/dashboard'])]
    #[TestWith(['/admin/dashboard'])]
    #[TestWith(['/admin/laundry_items'])]
    #[TestWith(['/admin/laundry_items/create'])]
    public function test_guests_are_redirected_from_admin_pages(string $path): void
    {
        $this->get($path)->assertRedirect('/login');
    }

    public function test_admin_can_create_edit_and_delete_a_service_with_images(): void
    {
        Storage::fake('public');
        $admin = User::factory()->create();
        $payload = ['name' => 'Cuci Uji', 'category' => 'Cuci', 'price' => 25000,
            'description' => 'Layanan uji.', 'image' => UploadedFile::fake()->createWithContent('first.png', base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAwMCAO+a1XkAAAAASUVORK5CYII='))];

        $this->actingAs($admin)->post(route('laundry_items.store'), $payload)
            ->assertRedirect(route('laundry_items.index'))->assertSessionHas('success');

        $item = LaundryItem::sole();
        $firstImage = $item->image;
        Storage::disk('public')->assertExists($firstImage);
        $this->get(route('laundry_items.index'))->assertSee('Cuci Uji');
        $this->get(route('laundry_items.edit', $item))->assertSee('Cuci Uji');

        $payload['name'] = 'Setrika Uji';
        $payload['category'] = 'Setrika';
        $payload['image'] = UploadedFile::fake()->createWithContent('second.png', base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAwMCAO+a1XkAAAAASUVORK5CYII='));
        $this->put(route('laundry_items.update', $item), $payload)->assertRedirect(route('laundry_items.index'));

        $item->refresh();
        $this->assertDatabaseHas('laundry_items', ['id' => $item->id, 'name' => 'Setrika Uji']);
        Storage::disk('public')->assertMissing($firstImage);
        Storage::disk('public')->assertExists($item->image);

        $this->delete(route('laundry_items.destroy', $item))->assertRedirect(route('laundry_items.index'));

        $this->assertModelMissing($item);
        Storage::disk('public')->assertMissing($item->image);
    }

    public function test_invalid_service_is_rejected_without_writing_data(): void
    {
        $this->actingAs(User::factory()->create())->post(route('laundry_items.store'), [
            'name' => '', 'category' => 'Tidak Ada', 'price' => -1, 'description' => '',
        ])->assertSessionHasErrors(['name', 'category', 'price', 'description']);

        $this->assertDatabaseCount('laundry_items', 0);
    }

    public function test_checkout_rejects_an_empty_selection(): void
    {
        $item = LaundryItem::factory()->create();

        $this->from('/')->post('/checkout', ['customer_name' => 'Pelanggan Uji',
            'queue_number' => 5, 'items' => [$item->id => 0]])
            ->assertRedirect('/')->assertSessionHas('error', 'Pilih minimal satu layanan!');

        $this->assertDatabaseCount('orders', 0);
    }

    public function test_checkout_rejects_invalid_customer_information(): void
    {
        $this->post('/checkout', ['customer_name' => '', 'queue_number' => 0, 'items' => []])
            ->assertSessionHasErrors(['customer_name', 'queue_number', 'items']);

        $this->assertDatabaseCount('orders', 0);
    }

    public function test_checkout_rolls_back_an_unknown_service(): void
    {
        $this->from('/')->post('/checkout', ['customer_name' => 'Pelanggan Uji',
            'queue_number' => 5, 'items' => [999 => 1]])->assertRedirect('/')->assertSessionHas('error');

        $this->assertDatabaseCount('orders', 0);
        $this->assertDatabaseCount('order_details', 0);
    }

    public function test_checkout_saves_the_order_and_calculates_the_total(): void
    {
        $item = LaundryItem::factory()->create(['price' => 25000]);

        $this->from('/')->post('/checkout', ['customer_name' => 'Pelanggan Uji',
            'queue_number' => 5, 'items' => [$item->id => 2]])
            ->assertRedirect('/')->assertSessionHas('success');

        $this->assertDatabaseHas('orders', ['customer_name' => 'Pelanggan Uji',
            'queue_number' => '5', 'total_price' => 50000, 'status' => 'pending']);
        $this->assertDatabaseHas('order_details', ['laundry_item_id' => $item->id, 'quantity' => 2]);
    }

    public function test_admin_dashboard_displays_orders_and_can_update_status(): void
    {
        $order = Order::factory()->create(['customer_name' => 'Pelanggan Dashboard']);
        OrderDetail::factory()->for($order)->create();

        $this->actingAs(User::factory()->create())->get('/dashboard')->assertSee('Pelanggan Dashboard');
        $this->from('/dashboard')->patch(route('admin.orders.updateStatus', $order), ['status' => 'completed'])
            ->assertRedirect('/dashboard')->assertSessionHas('success');

        $this->assertDatabaseHas('orders', ['id' => $order->id, 'status' => 'completed']);
    }
}
