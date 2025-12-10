<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Gunakan transaksi agar jika error, data tidak masuk setengah-setengah
        DB::transaction(function () {
            $this->seedUsersAndProfiles();
            $this->seedIntegrations(); // Config API (Midtrans, RajaOngkir, dll)
            $this->seedLogistics();    // Kurir & Service
            $this->seedProducts();     // Kategori, Produk, Gambar, Review
            $this->seedOrders();       // Transaksi, Payment, Shipment
            $this->seedContent();      // Banner
        });
    }

    private function seedUsersAndProfiles()
    {
        // 1. Users
        $password = Hash::make('password'); // Password default untuk semua user

        $users = [
            [
                'user_id' => 1, 'role' => 'admin', 'username' => 'admin.sistem', 'name' => 'Sistem Administrator',
                'email' => 'admin@telu.ac.id', 'password' => $password, 'status' => 'active'
            ],
            [
                'user_id' => 2, 'role' => 'seller', 'username' => 'tokobaju.bdg', 'name' => 'Toko Baju Bandung',
                'email' => 'seller_bdg@example.com', 'password' => $password, 'status' => 'active'
            ],
            [
                'user_id' => 3, 'role' => 'seller', 'username' => 'gadget.sby', 'name' => 'Gadget Surabaya',
                'email' => 'seller_sby@example.com', 'password' => $password, 'status' => 'active'
            ],
            [
                'user_id' => 4, 'role' => 'buyer', 'username' => 'aldi.pembeli', 'name' => 'Aldi Kurniawan',
                'email' => 'aldi@example.com', 'password' => $password, 'status' => 'active'
            ],
            [
                'user_id' => 5, 'role' => 'buyer', 'username' => 'siti.belanja', 'name' => 'Siti Rahmawati',
                'email' => 'siti@example.com', 'password' => $password, 'status' => 'active'
            ],
        ];
        DB::table('users')->insert($users);

        // 2. Profiles
        $profiles = [
            ['user_id' => 2, 'phone' => '081234567890', 'address' => 'Jalan Merdeka No. 10, Bandung', 'bio' => 'Menjual pakaian preloved berkualitas.'],
            ['user_id' => 3, 'phone' => '087654321098', 'address' => 'Jalan Pahlawan No. 5, Surabaya', 'bio' => 'Pusat konsinyasi gadget.'],
            ['user_id' => 4, 'phone' => '085000111222', 'address' => 'Jakarta Pusat', 'bio' => 'Buyer aktif.'],
            ['user_id' => 5, 'phone' => '089988776655', 'address' => 'Yogyakarta', 'bio' => 'Suka belanja.'],
        ];
        DB::table('profiles')->insert($profiles);

        // 3. Addresses
        $addresses = [
            [
                'address_id' => 1, 'user_id' => 4, 'label' => 'Rumah Utama', 'recipient' => 'Aldi Kurniawan',
                'phone' => '085000111222', 'city' => 'Jakarta Barat', 'province' => 'DKI Jakarta',
                'full_address' => 'Jl. Kebon Jeruk Raya No. 15', 'is_default' => 1
            ],
            [
                'address_id' => 2, 'user_id' => 3, 'label' => 'Toko Fisik', 'recipient' => 'Gadget Surabaya',
                'phone' => '087654321098', 'city' => 'Surabaya', 'province' => 'Jawa Timur',
                'full_address' => 'Ruko Mulyosari Blok B10', 'is_default' => 1
            ],
        ];
        DB::table('addresses')->insert($addresses);

        // 4. Bank Accounts
        DB::table('bank_accounts')->insert([
            ['user_id' => 2, 'bank_name' => 'BCA', 'account_name' => 'Toko Baju', 'account_no' => '1234567890', 'is_default' => 1],
            ['user_id' => 3, 'bank_name' => 'Mandiri', 'account_name' => 'Gadget Sby', 'account_no' => '0987654321', 'is_default' => 1],
        ]);
    }

    private function seedIntegrations()
    {
        // 1. Providers
        DB::table('integration_providers')->insert([
            ['integration_provider_id' => 1, 'code' => 'midtrans', 'name' => 'Midtrans Payment Gateway'],
            ['integration_provider_id' => 2, 'code' => 'rajaongkir', 'name' => 'RajaOngkir Rates'],
            ['integration_provider_id' => 3, 'code' => 'whatsapp', 'name' => 'WhatsApp (Fonnte)'],
            ['integration_provider_id' => 4, 'code' => 'binderbyte', 'name' => 'BinderByte Tracking'],
            ['integration_provider_id' => 5, 'code' => 'gemini', 'name' => 'Google Gemini AI'],
        ]);

        // 2. Keys (Credentials)
        // Note: Saya update meta_json RajaOngkir sesuai diskusi Komerce V1
        DB::table('integration_keys')->insert([
            [
                'provider_id' => 1, 'label' => 'Midtrans Sandbox', 'is_active' => 1,
                'public_k' => 'SB-Mid-client-XXXXX', // Client Key
                'encrypted_k' => 'SB-Mid-server-YYYYY', // Server Key (Harusnya di encrypt, ini dummy)
                'meta_json' => json_encode(['environment' => 'sandbox'])
            ],
            [
                'provider_id' => 2, 'label' => 'RajaOngkir Komerce', 'is_active' => 1,
                'public_k' => 'YOUR_KOMERCE_API_KEY',
                'encrypted_k' => null,
                'meta_json' => json_encode(['base_url' => 'https://rajaongkir.komerce.id/api/v1']) // Config URL Baru
            ],
            [
                'provider_id' => 3, 'label' => 'Fonnte WA', 'is_active' => 1,
                'public_k' => 'YOUR_FONNTE_TOKEN',
                'encrypted_k' => null,
                'meta_json' => null
            ],
        ]);
    }

    private function seedLogistics()
    {
        // 1. Carriers
        DB::table('shipping_carriers')->insert([
            ['shipping_carrier_id' => 1, 'code' => 'jne', 'name' => 'JNE', 'provider_type' => 'rates', 'is_enabled' => 1],
            ['shipping_carrier_id' => 2, 'code' => 'sicepat', 'name' => 'SiCepat', 'provider_type' => 'aggregator', 'is_enabled' => 1],
            ['shipping_carrier_id' => 3, 'code' => 'pos', 'name' => 'POS Indonesia', 'provider_type' => 'rates', 'is_enabled' => 1],
        ]);

        // 2. Services
        DB::table('shipping_services')->insert([
            ['carrier_id' => 1, 'service_code' => 'reg', 'service_name' => 'Reguler', 'is_enabled' => 1],
            ['carrier_id' => 1, 'service_code' => 'yes', 'service_name' => 'Yakin Esok Sampai', 'is_enabled' => 1],
            ['carrier_id' => 2, 'service_code' => 'best', 'service_name' => 'Best Express', 'is_enabled' => 1],
            ['carrier_id' => 2, 'service_code' => 'gokil', 'service_name' => 'Gokil Cargo', 'is_enabled' => 1],
        ]);
    }

    private function seedProducts()
    {
        // 1. Categories
        DB::table('categories')->insert([
            ['category_id' => 1, 'name' => 'Fashion', 'slug' => 'fashion', 'parent_id' => null],
            ['category_id' => 2, 'name' => 'Pakaian Atas', 'slug' => 'pakaian-atas', 'parent_id' => 1],
            ['category_id' => 3, 'name' => 'Elektronik', 'slug' => 'elektronik', 'parent_id' => null],
            ['category_id' => 4, 'name' => 'Smartphone', 'slug' => 'smartphone', 'parent_id' => 3],
        ]);

        // 2. Products
        $products = [
            [
                'product_id' => 1, 'seller_id' => 2, 'category_id' => 2,
                'title' => 'Kemeja Batik Slim Fit Preloved', 'description' => 'Kondisi 9/10.',
                'price' => 150000, 'stock' => 1, 'condition' => 'used', 'main_image' => 'img/p1.jpg'
            ],
            [
                'product_id' => 2, 'seller_id' => 3, 'category_id' => 4,
                'title' => 'iPhone 11 Pro 64GB', 'description' => 'Face ID off.',
                'price' => 6500000, 'stock' => 1, 'condition' => 'used', 'main_image' => 'img/p7.jpg'
            ],
            [
                'product_id' => 3, 'seller_id' => 2, 'category_id' => 2,
                'title' => 'Kaos Polos Hitam', 'description' => 'Brand Uniqlo.',
                'price' => 75000, 'stock' => 3, 'condition' => 'used', 'main_image' => 'img/p2.jpg'
            ]
        ];
        DB::table('products')->insert($products);

        // 3. Product Images
        DB::table('product_images')->insert([
            ['product_id' => 1, 'url' => 'img/p1_a.jpg', 'is_primary' => 1],
            ['product_id' => 1, 'url' => 'img/p1_b.jpg', 'is_primary' => 0],
            ['product_id' => 2, 'url' => 'img/p7_a.jpg', 'is_primary' => 1],
        ]);

        // 4. Reviews
        DB::table('reviews')->insert([
            ['product_id' => 1, 'user_id' => 4, 'rating' => 5, 'comment' => 'Barang sesuai deskripsi!'],
        ]);
    }

    private function seedOrders()
    {
        // 1. Orders
        DB::table('orders')->insert([
            [
                'order_id' => 1, 'code' => 'INV-20250101-001',
                'buyer_id' => 4, 'seller_id' => 2, 'shipping_address_id' => 1,
                'status' => 'completed', 'payment_status' => 'settlement',
                'subtotal_amount' => 150000, 'shipping_cost' => 10000, 'total_amount' => 160000,
                'created_at' => now()->subDays(5)
            ],
        ]);

        // 2. Order Items
        DB::table('order_items')->insert([
            [
                'order_id' => 1, 'product_id' => 1,
                'product_title_snapshot' => 'Kemeja Batik Slim Fit Preloved',
                'unit_price' => 150000, 'quantity' => 1, 'subtotal' => 150000
            ]
        ]);

        // 3. Payment Gateways & Methods
        DB::table('payment_gateways')->insert([
            ['payment_gateway_id' => 1, 'code' => 'midtrans', 'name' => 'Midtrans', 'is_enabled' => 1]
        ]);

        DB::table('payment_methods')->insert([
            ['gateway_id' => 1, 'code' => 'bca_va', 'name' => 'BCA Virtual Account', 'is_enabled' => 1],
            ['gateway_id' => 1, 'code' => 'gopay', 'name' => 'GoPay', 'is_enabled' => 1],
        ]);

        // 4. Payments
        DB::table('payments')->insert([
            [
                'order_id' => 1, 'gateway_id' => 1, 'method_code' => 'bca_va',
                'amount' => 160000, 'status' => 'settlement', 'paid_at' => now()->subDays(5)
            ]
        ]);

        // 5. Shipments
        DB::table('shipments')->insert([
            [
                'order_id' => 1, 'carrier_id' => 1, 'service_code' => 'reg',
                'tracking_number' => 'JNE1234567890', 'status' => 'delivered', 'cost' => 10000
            ]
        ]);
    }

    private function seedContent()
    {
        DB::table('banner_slides')->insert([
            [
                'title' => 'Promo Awal Tahun', 'description' => 'Diskon up to 50%',
                'image_path' => 'banners/promo1.jpg', 'is_active' => 1, 'sort_order' => 1
            ],
            [
                'title' => 'Gadget Murah', 'description' => 'Cek koleksi terbaru',
                'image_path' => 'banners/promo2.jpg', 'is_active' => 1, 'sort_order' => 2
            ],
        ]);
    }
}
