<?php

namespace App\Console\Commands;

use App\Models\ActivityLog;
use App\Models\Canteen;
use App\Models\Category;
use App\Models\DailyTransaction;
use App\Models\Menu;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use PDO;

class MigrateLegacySqlite extends Command
{
    protected $signature = 'migrate:legacy {--force : Jalankan migrasi ulang dan replace data legacy}';
    protected $description = 'Migrasi data SQLite KantinGO lama ke MySQL KantinGO 2.0 (Multi-Vendor)';

    public function handle(): int
    {
        $this->info('=== MEMULAI MIGRASI DATA SQLITE KE MYSQL ===');

        // Cari file SQLite
        $sqlitePath = sys_get_temp_dir() . '/kantingo-6e38e5c90f07/database.sqlite';
        if (!file_exists($sqlitePath)) {
            // Coba cari file database.sqlite lain di temp
            $found = glob(sys_get_temp_dir() . '/kantingo-*/database.sqlite');
            if ($found) {
                $sqlitePath = $found[0];
            } else {
                $this->error('File database.sqlite tidak ditemukan di: ' . $sqlitePath);
                return 1;
            }
        }

        $this->info('Membaca SQLite dari: ' . $sqlitePath);
        $sqlite = new PDO('sqlite:' . $sqlitePath, null, null, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);

        DB::beginTransaction();
        try {
            // 1. Migrasi Admin
            $this->info('1. Migrasi Akun Admin...');
            $legacyAdmin = $sqlite->query("SELECT * FROM admin WHERE id=1")->fetch();
            $legacyProfile = $sqlite->query("SELECT * FROM admin_profile WHERE id=1")->fetch();

            $admin = User::updateOrCreate(
                ['username' => 'admin'],
                [
                    'name' => $legacyProfile['nama_admin'] ?? 'Admin Kantin UNPAM',
                    'email' => 'admin@unpam.ac.id',
                    'password' => $legacyAdmin['password_hash'] ?? Hash::make('KantinGo123!'),
                    'role' => 'admin',
                    'user_type' => null,
                    'is_active' => true,
                ]
            );
            $this->line("   -> Admin terdaftar: {$admin->name} ({$admin->email})");

            // 2. Buat Akun Seller & Kantin Utama Viktor (Tempat Menu & Pesanan Legacy)
            $this->info('2. Menyiapkan Kantin Utama Viktor UNPAM & Akun Seller...');
            $sellerViktor = User::updateOrCreate(
                ['username' => 'seller_viktor'],
                [
                    'name' => 'Budi Hendrawan (Seller Viktor)',
                    'email' => 'budi.sarirasa@kantin.unpam.ac.id',
                    'password' => Hash::make('password123'),
                    'role' => 'seller',
                    'phone' => '081234567890',
                    'is_active' => true,
                ]
            );

            $canteenViktor = Canteen::updateOrCreate(
                ['slug' => 'kantin-utama-viktor'],
                [
                    'seller_id' => $sellerViktor->id,
                    'name' => 'Kantin Utama Viktor UNPAM',
                    'campus_location' => 'Kampus 2 Viktor UNPAM, Lantai 1 (Food Court)',
                    'description' => 'Kantin pusat Kampus Viktor UNPAM menyajikan aneka olahan nasi, mi, dan minuman segar.',
                    'opening_hours' => '07:30 - 17:00 WIB',
                    'is_open' => true,
                    'logo' => 'kantingo-logo-transparent.png',
                ]
            );
            $this->line("   -> Kantin terdaftar: {$canteenViktor->name} (Seller: {$sellerViktor->name})");

            // Kategori Kantin Viktor
            $catMakanan = Category::firstOrCreate(['canteen_id' => $canteenViktor->id, 'slug' => 'makanan'], ['name' => 'Makanan']);
            $catMinuman = Category::firstOrCreate(['canteen_id' => $canteenViktor->id, 'slug' => 'minuman'], ['name' => 'Minuman']);

            // 3. Tambahan Kantin Multi-Vendor UNPAM (Kantin Maju Jaya & Kantin Sehat Pusat)
            $this->info('3. Menyiapkan Kantin Tambahan Multi-Vendor UNPAM...');
            $sellerWitana = User::updateOrCreate(
                ['username' => 'seller_witana'],
                [
                    'name' => 'Sri Rahayu (Seller Witana)',
                    'email' => 'sri.majujaya@kantin.unpam.ac.id',
                    'password' => Hash::make('password123'),
                    'role' => 'seller',
                    'phone' => '081298765432',
                    'is_active' => true,
                ]
            );

            $canteenWitana = Canteen::updateOrCreate(
                ['slug' => 'kantin-maju-jaya-witana'],
                [
                    'seller_id' => $sellerWitana->id,
                    'name' => 'Kantin Maju Jaya Witana Harja',
                    'campus_location' => 'Kampus Witana Harja UNPAM, Area Parkir Barat',
                    'description' => 'Menyediakan soto ayam, ayam geprek sambal bawang, dan aneka jus segar.',
                    'opening_hours' => '08:00 - 16:30 WIB',
                    'is_open' => true,
                ]
            );

            $sellerPusat = User::updateOrCreate(
                ['username' => 'seller_pusat'],
                [
                    'name' => 'Hendra Wijaya (Seller Pusat)',
                    'email' => 'hendra.sehatup@kantin.unpam.ac.id',
                    'password' => Hash::make('password123'),
                    'role' => 'seller',
                    'phone' => '081377889900',
                    'is_active' => true,
                ]
            );

            $canteenPusat = Canteen::updateOrCreate(
                ['slug' => 'kantin-sehat-kampus-pusat'],
                [
                    'seller_id' => $sellerPusat->id,
                    'name' => 'Kantin Sehat Kampus Pusat',
                    'campus_location' => 'Kampus 1 Pusat Pamulang, Gedung A Lt. Dasar',
                    'description' => 'Pilihan makanan higienis, gado-gado, nasi pecel, serta aneka minuman tradisional.',
                    'opening_hours' => '07:00 - 17:00 WIB',
                    'is_open' => true,
                ]
            );

            // 4. Tambah Akun Demo Customer (Mahasiswa, Dosen, Staf UNPAM)
            $this->info('4. Menyiapkan Akun Demo Customer UNPAM...');
            $mhs = User::updateOrCreate(
                ['email' => 'budi.santoso@mhs.unpam.ac.id'],
                [
                    'name' => 'Budi Santoso',
                    'username' => 'budi_mhs',
                    'password' => Hash::make('password123'),
                    'role' => 'customer',
                    'user_type' => 'mahasiswa',
                    'identifier_number' => '211011400123',
                    'phone' => '085711223344',
                    'is_active' => true,
                ]
            );

            $dosen = User::updateOrCreate(
                ['email' => 'dr.irwan@dosen.unpam.ac.id'],
                [
                    'name' => 'Dr. Irwan Setiawan, M.Kom.',
                    'username' => 'irwan_dosen',
                    'password' => Hash::make('password123'),
                    'role' => 'customer',
                    'user_type' => 'dosen',
                    'identifier_number' => '0412058001',
                    'phone' => '081399887766',
                    'is_active' => true,
                ]
            );

            $staf = User::updateOrCreate(
                ['email' => 'rina@staf.unpam.ac.id'],
                [
                    'name' => 'Rina Wulandari, S.E.',
                    'username' => 'rina_staf',
                    'password' => Hash::make('password123'),
                    'role' => 'customer',
                    'user_type' => 'staf',
                    'identifier_number' => '19890415201501',
                    'phone' => '081288776655',
                    'is_active' => true,
                ]
            );

            // 5. Migrasi Menu dari SQLite
            $this->info('5. Migrasi 5 Menu SQLite ke Kantin Utama Viktor...');
            $legacyMenus = $sqlite->query("SELECT * FROM menu")->fetchAll();
            $menuMap = [];

            foreach ($legacyMenus as $m) {
                $catId = $m['kategori'] === 'Minuman' ? $catMinuman->id : $catMakanan->id;
                $menu = Menu::updateOrCreate(
                    ['canteen_id' => $canteenViktor->id, 'name' => $m['nama_menu']],
                    [
                        'category_id' => $catId,
                        'kategori' => $m['kategori'],
                        'price' => (int)$m['harga'],
                        'stock' => (int)$m['stok'],
                        'image' => $m['gambar'],
                        'is_active' => (bool)$m['aktif'],
                    ]
                );
                $menuMap[$m['id']] = $menu->id;
                $this->line("   -> Menu [{$menu->id}]: {$menu->name} (Rp" . number_format($menu->price) . ", stok: {$menu->stock})");
            }

            // Menu untuk kantin Witana & Pusat (dummy pelengkap multi-vendor)
            Menu::firstOrCreate(['canteen_id' => $canteenWitana->id, 'name' => 'Soto Ayam Lamongan'], ['kategori' => 'Makanan', 'price' => 15000, 'stock' => 30, 'image' => 'default.svg']);
            Menu::firstOrCreate(['canteen_id' => $canteenWitana->id, 'name' => 'Ayam Geprek Sambal Korek'], ['kategori' => 'Makanan', 'price' => 14000, 'stock' => 45, 'image' => 'ayam.svg']);
            Menu::firstOrCreate(['canteen_id' => $canteenWitana->id, 'name' => 'Jus Alpukat'], ['kategori' => 'Minuman', 'price' => 8000, 'stock' => 25, 'image' => 'jeruk.svg']);

            Menu::firstOrCreate(['canteen_id' => $canteenPusat->id, 'name' => 'Gado-Gado Spesial'], ['kategori' => 'Makanan', 'price' => 13000, 'stock' => 20, 'image' => 'default.svg']);
            Menu::firstOrCreate(['canteen_id' => $canteenPusat->id, 'name' => 'Nasi Pecel Madiun'], ['kategori' => 'Makanan', 'price' => 12000, 'stock' => 25, 'image' => 'default.svg']);
            Menu::firstOrCreate(['canteen_id' => $canteenPusat->id, 'name' => 'Wedang Jahe Hangat'], ['kategori' => 'Minuman', 'price' => 5000, 'stock' => 40, 'image' => 'teh.svg']);

            // 6. Migrasi 18 Pesanan dari SQLite
            $this->info('6. Migrasi 18 Pesanan SQLite ke MySQL...');
            $legacyOrders = $sqlite->query("SELECT * FROM pesanan ORDER BY id ASC")->fetchAll();
            $orderMap = [];

            foreach ($legacyOrders as $o) {
                $order = Order::updateOrCreate(
                    ['order_code' => $o['kode_pesanan']],
                    [
                        'canteen_id' => $canteenViktor->id,
                        'customer_id' => $mhs->id,
                        'customer_name' => $o['nama_pengambil'],
                        'customer_type' => 'Mahasiswa / XI RPL 1',
                        'detail_menu_snapshot' => $o['detail_menu'],
                        'total_price' => (int)$o['total_harga'],
                        'status' => $o['status'],
                        'payment_method' => $o['metode_pembayaran'],
                        'payment_status' => $o['status_pembayaran'],
                        'access_token' => $o['akses_token'],
                        'checkout_key' => $o['checkout_key'],
                        'paid_at' => $o['paid_at'],
                        'created_at' => $o['created_at'],
                        'updated_at' => $o['created_at'],
                    ]
                );
                $orderMap[$o['id']] = $order->id;
            }
            $this->line("   -> " . count($legacyOrders) . " Pesanan berhasil dimigrasikan.");

            // 7. Migrasi 23 Pesanan Item dari SQLite
            $this->info('7. Migrasi 23 Pesanan Item SQLite...');
            $legacyItems = $sqlite->query("SELECT * FROM pesanan_item ORDER BY id ASC")->fetchAll();
            foreach ($legacyItems as $it) {
                if (!isset($orderMap[$it['pesanan_id']])) continue;
                $newOrderId = $orderMap[$it['pesanan_id']];
                $newMenuId = $menuMap[$it['menu_id']] ?? null;

                OrderItem::updateOrCreate(
                    [
                        'order_id' => $newOrderId,
                        'menu_name' => $it['nama_menu'],
                        'quantity' => (int)$it['jumlah'],
                    ],
                    [
                        'menu_id' => $newMenuId,
                        'price' => (int)$it['harga'],
                        'subtotal' => (int)$it['harga'] * (int)$it['jumlah'],
                    ]
                );
            }
            $this->line("   -> " . count($legacyItems) . " Pesanan Item berhasil dimigrasikan.");

            // 8. Migrasi 8 Pembayaran dari SQLite
            $this->info('8. Migrasi 8 Pembayaran SQLite...');
            $legacyPayments = $sqlite->query("SELECT * FROM pembayaran ORDER BY id ASC")->fetchAll();
            foreach ($legacyPayments as $p) {
                if (!isset($orderMap[$p['pesanan_id']])) continue;
                Payment::updateOrCreate(
                    ['order_id' => $orderMap[$p['pesanan_id']]],
                    [
                        'method' => $p['metode'],
                        'amount' => (int)$p['jumlah'],
                        'created_at' => $p['created_at'],
                        'updated_at' => $p['created_at'],
                    ]
                );
            }
            $this->line("   -> " . count($legacyPayments) . " Pembayaran berhasil dimigrasikan.");

            // 9. Migrasi Transaksi Harian
            $this->info('9. Migrasi Transaksi Harian SQLite...');
            $legacyDaily = $sqlite->query("SELECT * FROM transaksi_harian")->fetchAll();
            foreach ($legacyDaily as $d) {
                DailyTransaction::updateOrCreate(
                    [
                        'canteen_id' => $canteenViktor->id,
                        'date' => $d['tanggal'],
                        'payment_method' => $d['metode_pembayaran'],
                    ],
                    [
                        'total_revenue' => (int)$d['total_pendapatan'],
                        'transaction_count' => (int)$d['jumlah_transaksi'],
                    ]
                );
            }
            $this->line("   -> Transaksi harian berhasil dimigrasikan.");

            // 10. Catat Activity Log awal
            ActivityLog::create([
                'user_id' => $admin->id,
                'canteen_id' => $canteenViktor->id,
                'action' => 'MIGRATION_INITIALIZED',
                'description' => 'Migrasi data penuh dari database SQLite ke MySQL KantinGO 2.0 selesai dilaksanakan.',
                'ip_address' => '127.0.0.1',
                'user_agent' => 'Artisan CLI / Migration Tool',
            ]);

            DB::commit();
            $this->info(PHP_EOL . '=== MIGRASI DATA SQLITE KE MYSQL BERHASIL 100%! ===');
            return 0;
        } catch (\Throwable $e) {
            DB::rollBack();
            $this->error('Gagal melakukan migrasi data: ' . $e->getMessage());
            $this->error($e->getTraceAsString());
            return 1;
        }
    }
}
