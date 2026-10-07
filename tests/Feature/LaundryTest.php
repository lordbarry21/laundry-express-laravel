<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LaundryTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Uji halaman beranda pelanggan dapat diakses dan menampilkan layanan
     */
    public function test_customer_can_see_homepage_and_services(): void
    {
        Service::create([
            'service_name' => 'Cuci Kering Lipat',
            'category' => 'Kiloan',
            'price_per_unit' => 5000,
            'unit_type' => 'Kg',
            'description' => 'Cuci bersih dan lipat rapi',
        ]);

        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('Cuci Kering Lipat');
    }

    /**
     * Uji pelanggan dapat mendaftar pesanan cucian
     */
    public function test_customer_can_place_order(): void
    {
        $srv = Service::create([
            'service_name' => 'Cuci Kilat Express',
            'category' => 'Express',
            'price_per_unit' => 15000,
            'unit_type' => 'Kg',
        ]);

        $postData = [
            'customer_name' => 'Ahmad Dani',
            'whatsapp_number' => '081234567890',
            'service_id' => [$srv->id],
            'qty_or_weight' => [3],
        ];

        $response = $this->post('/pesan', $postData);
        $this->assertDatabaseHas('orders', [
            'customer_name' => 'Ahmad Dani',
            'whatsapp_number' => '081234567890',
            'total_price' => 45000,
            'status' => 'Antrean',
        ]);

        $order = Order::first();
        $response->assertRedirect(route('orders.show', $order->id));
    }

    /**
     * Uji admin dapat login dan mengakses dashboard
     */
    public function test_admin_can_login_and_access_dashboard(): void
    {
        $admin = User::create([
            'name' => 'Administrator',
            'username' => 'admin',
            'email' => 'admin@laundry.com',
            'password' => bcrypt('admin123'),
        ]);

        $response = $this->post('/login', [
            'username' => 'admin',
            'password' => 'admin123',
        ]);

        $response->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticatedAs($admin);
    }
}
