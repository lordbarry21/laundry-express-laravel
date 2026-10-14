<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LaundryTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 1. Uji Pelanggan dapat membuka form pendaftaran dan melihat daftar layanan
     */
    public function test_customer_can_see_order_page_and_services(): void
    {
        $service = Service::create([
            'service_name'   => 'Cuci Kering Setrika Reguler',
            'category'       => 'Kiloan',
            'price_per_unit' => 7000,
            'unit_type'      => 'Kg',
            'description'    => 'Layanan cuci bersih wangi',
        ]);

        $response = $this->get(route('order.create'));

        $response->assertStatus(200);
        $response->assertSee('Cuci Kering Setrika Reguler');
        $response->assertSee('7.000');
    }

    /**
     * 2. Uji Pelanggan dapat melakukan pendaftaran pesanan cuci kilat
     */
    public function test_customer_can_place_order(): void
    {
        $service = Service::create([
            'service_name'   => 'Cuci Kilat Express 4 Jam',
            'category'       => 'Express',
            'price_per_unit' => 12000,
            'unit_type'      => 'Kg',
            'description'    => 'Selesai 4 jam',
        ]);

        $payload = [
            'customer_name'   => 'Bari H. Achmad',
            'whatsapp_number' => '081234567890',
            'service_id'      => $service->id,
            'qty_or_weight'   => 4,
        ];

        $response = $this->post(route('order.store'), $payload);

        $this->assertDatabaseHas('orders', [
            'customer_name'   => 'Bari H. Achmad',
            'whatsapp_number' => '081234567890',
            'total_price'     => 48000,
            'status'          => 'Antrean',
        ]);

        $order = Order::first();
        $this->assertNotNull($order);

        $this->assertDatabaseHas('order_details', [
            'order_id'      => $order->id,
            'service_id'    => $service->id,
            'qty_or_weight' => 4,
            'subtotal'      => 48000,
        ]);

        $response->assertRedirect(route('order.success', $order->id));
    }

    /**
     * 3. Uji Pelanggan dapat melihat nota transaksi sukses
     */
    public function test_customer_can_view_order_success_note(): void
    {
        $service = Service::create([
            'service_name'   => 'Dry Cleaning Jas',
            'category'       => 'Satuan',
            'price_per_unit' => 25000,
            'unit_type'      => 'Pcs',
        ]);

        $order = Order::create([
            'customer_name'   => 'Budi Santoso',
            'whatsapp_number' => '089876543210',
            'total_price'     => 50000,
            'status'          => 'Antrean',
        ]);

        OrderDetail::create([
            'order_id'      => $order->id,
            'service_id'    => $service->id,
            'qty_or_weight' => 2,
            'subtotal'      => 50000,
        ]);

        $response = $this->get(route('order.success', $order->id));

        $response->assertStatus(200);
        $response->assertSee('Budi Santoso');
        $response->assertSee('50.000');
        $response->assertSee('Dry Cleaning Jas');
    }

    /**
     * 4. Uji Admin dapat login dan mengakses dashboard rekap pesanan
     */
    public function test_admin_can_login_and_access_dashboard(): void
    {
        $admin = User::create([
            'name'     => 'Admin Toko',
            'email'    => 'admin@laundry.com',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->post('/login', [
            'email'    => 'admin@laundry.com',
            'password' => 'password123',
        ]);

        $this->assertAuthenticatedAs($admin);
        $response->assertRedirect(route('dashboard'));

        $dashResponse = $this->actingAs($admin)->get(route('dashboard'));
        $dashResponse->assertStatus(200);
        $dashResponse->assertSee('Dashboard Rekap Transaksi Laundry');
    }

    /**
     * 5. Uji Admin dapat mengupdate status progres pengerjaan cuci
     */
    public function test_admin_can_update_order_status(): void
    {
        $admin = User::create([
            'name'     => 'Admin Toko',
            'email'    => 'admin@laundry.com',
            'password' => bcrypt('password123'),
        ]);

        $order = Order::create([
            'customer_name'   => 'Siti Rahma',
            'whatsapp_number' => '08111222333',
            'total_price'     => 35000,
            'status'          => 'Antrean',
        ]);

        $response = $this->actingAs($admin)->patch(route('admin.orders.updateStatus', $order->id), [
            'status' => 'Dicuci',
        ]);

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('orders', [
            'id'     => $order->id,
            'status' => 'Dicuci',
        ]);
    }

    /**
     * 6. Uji Admin dapat melakukan CRUD Master Layanan
     */
    public function test_admin_can_manage_services_crud(): void
    {
        $admin = User::create([
            'name'     => 'Admin Toko',
            'email'    => 'admin@laundry.com',
            'password' => bcrypt('password123'),
        ]);

        // Create
        $response = $this->actingAs($admin)->post(route('services.store'), [
            'service_name'   => 'Cuci Karpet Tebal',
            'category'       => 'Satuan',
            'price_per_unit' => 50000,
            'unit_type'      => 'Pcs',
            'description'    => 'Pembersihan mendalam karpet',
        ]);

        $response->assertRedirect(route('services.index'));
        $this->assertDatabaseHas('services', [
            'service_name'   => 'Cuci Karpet Tebal',
            'price_per_unit' => 50000,
        ]);

        $service = Service::where('service_name', 'Cuci Karpet Tebal')->first();

        // Update
        $this->actingAs($admin)->put(route('services.update', $service->id), [
            'service_name'   => 'Cuci Karpet Super Bersih',
            'category'       => 'Satuan',
            'price_per_unit' => 55000,
            'unit_type'      => 'Pcs',
            'description'    => 'Update deskripsi',
        ]);

        $this->assertDatabaseHas('services', [
            'id'             => $service->id,
            'service_name'   => 'Cuci Karpet Super Bersih',
            'price_per_unit' => 55000,
        ]);

        // Delete
        $this->actingAs($admin)->delete(route('services.destroy', $service->id));
        $this->assertDatabaseMissing('services', [
            'id' => $service->id,
        ]);
    }
}
