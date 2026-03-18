<?php

namespace Database\Seeders;

use App\Models\FeatureItem;
use App\Models\HeroContent;
use App\Models\Service;
use App\Models\SiteSetting;
use App\Models\Testimonial;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SiteDataSeeder extends Seeder
{
    public function run(): void
    {
        SiteSetting::query()->updateOrCreate(
            ['id' => 1],
            [
                'maintenance_mode' => filter_var(env('VITE_MAINTENANCE_MODE', false), FILTER_VALIDATE_BOOLEAN),
                'site_name' => env('VITE_COMPANY_NAME', 'Banua Cloud Nusantara'),
                'site_description' => env('VITE_COMPANY_DESCRIPTOR', 'Trusted IT Solutions Partner in Indonesia'),
                'company_name' => env('APP_NAME', 'Banua Cloud Nusantara'),
                'company_email' => env('VITE_SUPPORT_EMAIL', 'support@banuacloud.id'),
                'company_phone' => env('VITE_SUPPORT_PHONE', '+62 812-3456-7890'),
                'company_whatsapp' => env('VITE_SUPPORT_WHATSAPP_RAW', '6281234567890'),
                'company_address' => 'Jakarta, Indonesia',
                'social_instagram' => env('VITE_SOCIAL_INSTAGRAM', ''),
                'social_linkedin' => env('VITE_SOCIAL_LINKEDIN', ''),
                'social_twitter' => env('VITE_SOCIAL_TWITTER', ''),
                'social_facebook' => '',
                'email_notifications' => true,
                'order_alerts' => true,
                'support_alerts' => true,
                'two_factor_enabled' => false,
                'session_timeout' => 30,
            ],
        );

        HeroContent::query()->updateOrCreate(
            ['id' => 1],
            [
                'title' => 'Solusi Cloud untuk Bisnis Modern',
                'subtitle' => 'Rasakan performa super cepat dengan infrastruktur cloud tingkat enterprise kami. Solusi yang skalabel, aman, dan terpercaya disesuaikan dengan kebutuhan bisnis Anda.',
                'cta_primary' => 'Mulai Sekarang',
                'cta_secondary' => 'Lihat Harga',
            ],
        );

        if (FeatureItem::query()->doesntExist()) {
            foreach (
                [
                    ['icon' => 'Zap', 'title' => 'Super Cepat', 'description' => 'Deploy aplikasi Anda dalam hitungan detik dengan infrastruktur berperforma tinggi dan jaringan CDN global kami.'],
                    ['icon' => 'Shield', 'title' => 'Keamanan Enterprise', 'description' => 'Enkripsi tingkat bank dan sertifikasi kepatuhan untuk menjaga data Anda tetap aman setiap saat.'],
                    ['icon' => 'Globe', 'title' => 'Jaringan Global', 'description' => 'Deploy di 15+ wilayah di seluruh dunia dengan koneksi latensi rendah untuk pengguna Anda.'],
                    ['icon' => 'Database', 'title' => 'Backup Otomatis', 'description' => 'Backup otomatis harian dengan restore satu klik memastikan data Anda selalu aman.'],
                    ['icon' => 'Lock', 'title' => 'Perlindungan DDoS', 'description' => 'Mitigasi DDoS tingkat enterprise untuk menjaga layanan Anda tetap berjalan saat serangan.'],
                    ['icon' => 'Cpu', 'title' => 'Auto Scaling', 'description' => 'Skalakan sumber daya secara otomatis berdasarkan pola traffic untuk performa optimal.'],
                ] as $index => $feature
            ) {
                FeatureItem::query()->create([
                    ...$feature,
                    'sort_order' => $index,
                ]);
            }
        }

        if (Testimonial::query()->doesntExist()) {
            foreach (
                [
                    ['name' => 'Ahmad Pratama', 'role' => 'CTO', 'company' => 'TechStart Indonesia', 'content' => 'Banua Cloud telah mengubah cara kami mendeploy aplikasi. Kecepatan dan keandalan tidak tertandingi, dan tim support mereka selalu siap membantu kapan saja.'],
                    ['name' => 'Siti Nurhaliza', 'role' => 'Founder', 'company' => 'DigitalAgency', 'content' => 'Kami telah mencoba banyak penyedia cloud, tapi Banua Cloud menonjol dengan uptime luar biasa dan harga kompetitif. Sangat direkomendasikan untuk bisnis di Indonesia.'],
                    ['name' => 'Budi Santoso', 'role' => 'IT Manager', 'company' => 'EcomStore', 'content' => 'Migrasi ke Banua Cloud berjalan mulus. Tim mereka menangani semuanya dengan profesional, dan performa website kami meningkat signifikan.'],
                ] as $index => $testimonial
            ) {
                Testimonial::query()->create([
                    ...$testimonial,
                    'sort_order' => $index,
                ]);
            }
        }

        if (Service::query()->doesntExist()) {
            DB::transaction(function (): void {
                foreach ($this->defaultServices() as $serviceIndex => $service) {
                    $plans = $service['plans'];
                    unset($service['plans']);

                    $serviceRecord = Service::query()->create([
                        ...$service,
                        'sort_order' => $serviceIndex,
                    ]);

                    foreach ($plans as $planIndex => $plan) {
                        $serviceRecord->plans()->create([
                            ...$plan,
                            'sort_order' => $planIndex,
                        ]);
                    }
                }
            });
        }
    }

    private function defaultServices(): array
    {
        return [
            [
                'name' => 'Cloud VPS',
                'slug' => 'cloud-vps',
                'description' => 'Server virtual private dengan performa tinggi dengan sumber daya khusus dan akses root.',
                'icon_key' => 'server',
                'enabled' => true,
                'plans' => [
                    [
                        'name' => 'Starter',
                        'price' => 149000,
                        'period' => '/bulan',
                        'specs' => ['cpu' => '1 Core', 'ram' => '1GB', 'storage' => '25GB SSD', 'bandwidth' => '1TB'],
                        'features' => ['cPanel', 'SSL Gratis', 'Backup Mingguan'],
                        'popular' => false,
                        'color' => 'sky',
                    ],
                    [
                        'name' => 'Professional',
                        'price' => 299000,
                        'period' => '/bulan',
                        'specs' => ['cpu' => '2 Core', 'ram' => '2GB', 'storage' => '50GB SSD', 'bandwidth' => '2TB'],
                        'features' => ['cPanel', 'SSL Gratis', 'Backup Harian', 'Priority Support'],
                        'popular' => true,
                        'color' => 'sky',
                    ],
                    [
                        'name' => 'Business',
                        'price' => 499000,
                        'period' => '/bulan',
                        'specs' => ['cpu' => '4 Core', 'ram' => '4GB', 'storage' => '100GB SSD', 'bandwidth' => 'Unlimited'],
                        'features' => ['cPanel', 'SSL Gratis', 'Backup Harian', 'Dedicated Support'],
                        'popular' => false,
                        'color' => 'sky',
                    ],
                ],
            ],
            [
                'name' => 'Web Hosting',
                'slug' => 'web-hosting',
                'description' => 'Shared hosting cepat dan andal dengan cPanel dan penyimpanan SSD untuk website Anda.',
                'icon_key' => 'globe',
                'enabled' => true,
                'plans' => [
                    [
                        'name' => 'Starter',
                        'price' => 29000,
                        'period' => '/bulan',
                        'specs' => ['cpu' => 'Shared', 'ram' => 'Unlimited', 'storage' => '5GB SSD', 'bandwidth' => 'Unlimited'],
                        'features' => ['cPanel', 'SSL Gratis', 'Email Unlimited'],
                        'popular' => false,
                        'color' => 'cyan',
                    ],
                    [
                        'name' => 'Business',
                        'price' => 59000,
                        'period' => '/bulan',
                        'specs' => ['cpu' => 'Shared', 'ram' => 'Unlimited', 'storage' => '15GB SSD NVMe', 'bandwidth' => 'Unlimited'],
                        'features' => ['cPanel', 'SSL Gratis', 'Email Unlimited', 'LiteSpeed'],
                        'popular' => true,
                        'color' => 'cyan',
                    ],
                ],
            ],
            [
                'name' => 'Layanan Domain',
                'slug' => 'domain',
                'description' => 'Daftar domain sempurna Anda dengan manajemen DNS gratis dan sertifikat SSL.',
                'icon_key' => 'database',
                'enabled' => true,
                'plans' => [],
            ],
            [
                'name' => 'Solusi Backup',
                'slug' => 'backup',
                'description' => 'Backup otomatis harian dengan restore satu klik untuk menjaga data Anda tetap aman.',
                'icon_key' => 'hard-drive',
                'enabled' => true,
                'plans' => [
                    [
                        'name' => 'Basic',
                        'price' => 99000,
                        'period' => '/bulan',
                        'specs' => ['cpu' => '-', 'ram' => '-', 'storage' => '10GB', 'bandwidth' => '-'],
                        'features' => ['Daily Backup', '30 Days Retention', 'Restore One-Click'],
                        'popular' => false,
                        'color' => 'violet',
                    ],
                    [
                        'name' => 'Pro',
                        'price' => 199000,
                        'period' => '/bulan',
                        'specs' => ['cpu' => '-', 'ram' => '-', 'storage' => '50GB', 'bandwidth' => '-'],
                        'features' => ['Hourly Backup', '90 Days Retention', 'Priority Restore', 'Support'],
                        'popular' => true,
                        'color' => 'violet',
                    ],
                ],
            ],
            [
                'name' => 'Pengembangan Aplikasi',
                'slug' => 'app-development',
                'description' => 'Pengembangan aplikasi web dan mobile dengan teknologi modern dan responsif.',
                'icon_key' => 'code',
                'enabled' => true,
                'plans' => [],
            ],
            [
                'name' => 'Konsultasi IT',
                'slug' => 'it-consulting',
                'description' => 'Konsultasi teknologi IT untuk transformasi digital dan optimalisasi bisnis Anda.',
                'icon_key' => 'message-square',
                'enabled' => true,
                'plans' => [],
            ],
        ];
    }
}
