<?php

namespace Database\Seeders;

use App\Models\FeatureItem;
use App\Models\HeroContent;
use App\Models\MarketingPage;
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

        foreach ($this->marketingPages() as $pageKey => $payload) {
            MarketingPage::query()->updateOrCreate(
                ['page_key' => $pageKey],
                ['payload' => $payload],
            );
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

    private function marketingPages(): array
    {
        return [
            'learn-more' => [
                'heroBadge' => 'Pelajari Lebih Lanjut',
                'heroTitlePrefix' => 'Semua yang Anda butuhkan untuk',
                'heroTitleHighlight' => 'Bisnis Digital',
                'heroDescription' => 'Temukan layanan cloud dan IT terbaik yang dirancang untuk memenuhi kebutuhan bisnis Anda di era digital.',
                'stats' => [
                    ['icon' => 'Server', 'value' => '500+', 'label' => 'Server'],
                    ['icon' => 'Clock', 'value' => '99.9%', 'label' => 'Uptime'],
                    ['icon' => 'Users', 'value' => '10RB+', 'label' => 'Pelanggan'],
                ],
                'serviceSectionBadge' => 'Layanan Kami',
                'serviceSectionTitle' => 'Solusi Komprehensif',
                'serviceSectionDescription' => 'Dari hosting sederhana hingga pengembangan aplikasi enterprise, kami memiliki semuanya untuk Anda.',
                'services' => [
                    [
                        'slug' => 'cloud-vps',
                        'icon' => 'server',
                        'title' => 'Cloud VPS',
                        'subtitle' => 'Server Pribadi Virtual',
                        'description' => 'Rasakan performa tingkat enterprise dengan server virtual kami yang dilengkapi dengan sumber daya dedicated dan akses root penuh. Cocok untuk aplikasi yang membutuhkan kontrol penuh atas lingkungan server.',
                        'features' => [
                            'Prosesor dedicated dengan performa tinggi',
                            'Memori RAM yang terjamin',
                            'Storage SSD NVMe ultra-cepat',
                            'Akses root penuh',
                            'Pilihan OS Linux dan Windows',
                            'Backup otomatis harian',
                        ],
                        'gradient' => 'sky',
                    ],
                    [
                        'slug' => 'web-hosting',
                        'icon' => 'globe',
                        'title' => 'Web Hosting',
                        'subtitle' => 'Hosting Website',
                        'description' => 'Hosting website cepat dan andal dengan panel kontrol cPanel dan penyimpanan SSD. Solusi sempurna untuk website personal, blog, hingga website bisnis skala kecil-menengah.',
                        'features' => [
                            'cPanel mudah digunakan',
                            'Penyimpanan SSD cepat',
                            'SSL gratis selamanya',
                            'Email profesional unlimited',
                            'Database MySQL dan PostgreSQL',
                            'Support PHP 8.x terbaru',
                        ],
                        'gradient' => 'cyan',
                    ],
                    [
                        'slug' => 'domain',
                        'icon' => 'database',
                        'title' => 'Layanan Domain',
                        'subtitle' => 'Registrasi Domain',
                        'description' => 'Daftar domain sempurna untuk bisnis Anda dengan manajemen DNS gratis dan perlindungan privasi domain. Kami menyediakan berbagai ekstensi domain populer.',
                        'features' => [
                            '500+ ekstensi domain',
                            'Manajemen DNS gratis',
                            'SSL certificate gratis',
                            'Privacy protection',
                            'Auto-renewal',
                            'DNS management panel',
                        ],
                        'gradient' => 'violet',
                    ],
                    [
                        'slug' => 'backup',
                        'icon' => 'hard-drive',
                        'title' => 'Solusi Backup',
                        'subtitle' => 'Cadangan Data',
                        'description' => 'Lindungi data bisnis Anda dengan backup otomatis harian dan restore satu klik. Kami menggunakan penyimpanan redundan untuk keamanan ekstra.',
                        'features' => [
                            'Backup otomatis harian',
                            'Penyimpanan off-site',
                            'Restore dengan satu klik',
                            'Retensi backup 30 hari',
                            'Backup manual tambahan',
                            'Enkripsi data end-to-end',
                        ],
                        'gradient' => 'sky',
                    ],
                    [
                        'slug' => 'app-development',
                        'icon' => 'code',
                        'title' => 'Pengembangan Aplikasi',
                        'subtitle' => 'App Development',
                        'description' => 'Tim pengembang profesional kami siap membantu Anda membangun aplikasi web dan mobile yang responsif, modern, dan berkinerja tinggi menggunakan teknologi terkini.',
                        'features' => [
                            'Web application development',
                            'Mobile app development',
                            'UI/UX design modern',
                            'API development dan integration',
                            'Maintenance dan support',
                            'Teknologi modern lintas platform',
                        ],
                        'gradient' => 'violet',
                    ],
                    [
                        'slug' => 'it-consulting',
                        'icon' => 'message-square',
                        'title' => 'Konsultasi IT',
                        'subtitle' => 'IT Consulting',
                        'description' => 'Dapatkan panduan ahli untuk transformasi digital bisnis Anda. Kami membantu Anda merencanakan, mengimplementasikan, dan mengoptimalkan infrastruktur teknologi.',
                        'features' => [
                            'Konsultasi infrastruktur IT',
                            'Transformasi digital',
                            'Evaluasi keamanan sistem',
                            'Optimasi performa',
                            'Pelatihan tim IT',
                            'Rencana IT strategis',
                        ],
                        'gradient' => 'cyan',
                    ],
                ],
                'reasonsBadge' => 'Keunggulan',
                'reasonsTitle' => 'Mengapa Banua Cloud',
                'reasonsDescription' => 'Kami berkomitmen memberikan layanan terbaik untuk kesuksesan bisnis Anda.',
                'reasons' => [
                    ['icon' => 'Zap', 'title' => 'Super Cepat', 'description' => 'Infrastruktur performa tinggi dengan CDN global untuk kecepatan optimal.'],
                    ['icon' => 'Shield', 'title' => 'Aman Terjamin', 'description' => 'Enkripsi tingkat bank dan proteksi DDoS enterprise untuk keamanan data Anda.'],
                    ['icon' => 'Globe2', 'title' => 'Global Network', 'description' => '15+ data center di seluruh dunia dengan latensi rendah untuk pengguna global.'],
                    ['icon' => 'Star', 'title' => 'Support 24/7', 'description' => 'Tim support profesional siap membantu Anda kapan saja, tanpa pandang waktu.'],
                ],
                'faqBadge' => 'FAQ',
                'faqTitle' => 'Pertanyaan Umum',
                'faqDescription' => 'Temukan jawaban untuk pertanyaan yang sering diajukan tentang layanan kami.',
                'faqs' => [
                    ['question' => 'Bagaimana cara memilih layanan yang tepat untuk bisnis saya?', 'answer' => 'Tim sales kami siap membantu Anda memilih layanan yang sesuai dengan kebutuhan. Anda dapat menghubungi kami melalui live chat, email, atau telepon untuk berkonsultasi secara gratis.'],
                    ['question' => 'Apakah saya bisa upgrade layanan di kemudian hari?', 'answer' => 'Tentu. Semua layanan kami dapat di-upgrade kapan saja. Anda cukup mengajukan upgrade melalui panel klien kami, dan tim kami akan memprosesnya dalam waktu singkat.'],
                    ['question' => 'Berapa lama waktu setup layanan?', 'answer' => 'Cloud VPS biasanya siap dalam 1-24 jam. Web hosting dan domain dapat aktif dalam hitungan menit setelah pembayaran terkonfirmasi.'],
                    ['question' => 'Apakah ada garansi uptime?', 'answer' => 'Ya, kami memberikan garansi uptime 99.9% untuk layanan infrastruktur utama. Jika uptime tidak terpenuhi, Anda berhak mendapatkan kompensasi sesuai SLA.'],
                    ['question' => 'Bagaimana sistem support Banua Cloud?', 'answer' => 'Kami menyediakan support 24/7 melalui ticket system, live chat, dan telepon. Tim support kami terdiri dari teknisi berpengalaman yang siap membantu Anda kapan saja.'],
                ],
                'ctaTitle' => 'Siap Memulai?',
                'ctaDescription' => 'Hubungi tim sales kami sekarang untuk konsultasi gratis dan temukan solusi terbaik untuk bisnis Anda.',
                'ctaPrimary' => 'Hubungi Sales',
                'ctaSecondary' => 'Lihat Harga',
            ],
            'service:cloud-vps' => [
                'slug' => 'cloud-vps',
                'name' => 'Cloud VPS',
                'accent' => 'sky',
                'icon' => 'server',
                'breadcrumbLabel' => 'Cloud VPS',
                'heroBadge' => 'Server Pribadi Virtual',
                'heroTitlePrefix' => 'Cloud VPS dengan',
                'heroTitleHighlight' => 'Performa Tinggi',
                'heroDescription' => 'Rasakan kontrol penuh atas server Anda dengan sumber daya dedicated, performa tingkat enterprise, dan harga yang terjangkau.',
                'heroPrimaryCta' => 'Pesan Sekarang',
                'heroSecondaryCta' => 'Lihat Pricing',
                'featureSectionTitle' => 'Mengapa Memilih Cloud VPS Kami?',
                'featureSectionDescription' => 'Dilengkapi dengan infrastruktur enterprise untuk performa maksimal dan keandalan.',
                'features' => [
                    ['icon' => 'Cpu', 'title' => 'Performa Tinggi', 'description' => 'Prosesor terbaru dengan clock speed tinggi untuk beban kerja intensif.'],
                    ['icon' => 'HardDrive', 'title' => 'NVMe SSD Storage', 'description' => 'Penyimpanan NVMe SSD ultra-cepat dengan kecepatan baca tulis tinggi.'],
                    ['icon' => 'Zap', 'title' => 'Resource Terjamin', 'description' => 'CPU, RAM, dan storage dedicated tidak berbagi dengan pengguna lain.'],
                    ['icon' => 'Shield', 'title' => 'Keamanan Terdepan', 'description' => 'Firewall hardware, DDoS protection, dan backup otomatis harian.'],
                    ['icon' => 'Globe', 'title' => 'Global Network', 'description' => 'Multiple datacenter lokasi dengan bandwidth tinggi dan latency rendah.'],
                    ['icon' => 'Terminal', 'title' => 'Akses Root Penuh', 'description' => 'Kontrol penuh dengan akses root untuk konfigurasi custom.'],
                ],
                'pricingTitle' => 'Rencana VPS yang Fleksibel',
                'pricingDescription' => 'Pilih paket yang sesuai dengan kebutuhan Anda. Semua paket mencakup support 24/7.',
                'pricingCards' => [
                    [
                        'name' => 'Starter VPS',
                        'price' => '149.000',
                        'period' => '/bulan',
                        'specs' => ['CPU' => '2 vCPU', 'RAM' => '2 GB', 'Storage' => '30 GB NVMe', 'Bandwidth' => 'Unlimited', 'IP' => '1 IPv4'],
                        'features' => ['Panel control optional', 'Backup mingguan', 'Support 24/7', '99.9% uptime'],
                        'popular' => false,
                        'color' => 'sky',
                    ],
                    [
                        'name' => 'Professional VPS',
                        'price' => '299.000',
                        'period' => '/bulan',
                        'specs' => ['CPU' => '4 vCPU', 'RAM' => '4 GB', 'Storage' => '60 GB NVMe', 'Bandwidth' => 'Unlimited', 'IP' => '1 IPv4'],
                        'features' => ['Panel control optional', 'Backup harian', 'Priority support', '99.9% uptime', 'DDoS protection'],
                        'popular' => true,
                        'color' => 'cyan',
                    ],
                    [
                        'name' => 'Enterprise VPS',
                        'price' => '599.000',
                        'period' => '/bulan',
                        'specs' => ['CPU' => '8 vCPU', 'RAM' => '8 GB', 'Storage' => '120 GB NVMe', 'Bandwidth' => 'Unlimited', 'IP' => '1 IPv4'],
                        'features' => ['Panel control optional', 'Backup harian', 'Dedicated support', '99.9% uptime', 'DDoS protection', 'Free SSL'],
                        'popular' => false,
                        'color' => 'violet',
                    ],
                ],
                'extraSection' => [
                    'type' => 'badge-grid',
                    'title' => 'Pilihan Operating System',
                    'description' => 'Install OS favorit Anda dengan satu klik atau gunakan custom ISO.',
                    'items' => [
                        ['label' => 'Ubuntu 22.04 LTS'],
                        ['label' => 'Ubuntu 20.04 LTS'],
                        ['label' => 'CentOS 8'],
                        ['label' => 'Debian 11'],
                        ['label' => 'AlmaLinux 8'],
                        ['label' => 'Windows Server 2019'],
                        ['label' => 'Windows Server 2022'],
                    ],
                ],
                'ctaTitle' => 'Siap Memulai?',
                'ctaDescription' => 'Tim kami siap membantu Anda memilih VPS yang tepat. Hubungi kami untuk konsultasi gratis.',
                'ctaPrimary' => 'Pesan Sekarang',
                'ctaSecondary' => 'Hubungi Kami',
            ],
            'service:web-hosting' => [
                'slug' => 'web-hosting',
                'name' => 'Web Hosting',
                'accent' => 'cyan',
                'icon' => 'globe',
                'breadcrumbLabel' => 'Web Hosting',
                'heroBadge' => 'Web Hosting Indonesia',
                'heroTitlePrefix' => 'Hosting Cepat dan',
                'heroTitleHighlight' => 'Terpercaya',
                'heroDescription' => 'Wujudkan website profesional dengan hosting berkecepatan tinggi, SSL gratis, dan support terbaik di Indonesia.',
                'heroPrimaryCta' => 'Pesan Sekarang',
                'heroSecondaryCta' => 'Lihat Pricing',
                'featureSectionTitle' => 'Keunggulan Web Hosting Kami',
                'featureSectionDescription' => 'Didesain untuk performa maksimal dengan teknologi terkini.',
                'features' => [
                    ['icon' => 'Zap', 'title' => 'Speed Optimal', 'description' => 'Server dengan LiteSpeed Web Server dan SSD NVMe untuk loading super cepat.'],
                    ['icon' => 'Shield', 'title' => 'SSL Gratis', 'description' => 'Auto-install SSL Let\'s Encrypt tanpa batas dengan renew otomatis.'],
                    ['icon' => 'Mail', 'title' => 'Email Unlimited', 'description' => 'Buat email profesional tanpa batas dengan webmail Roundcube.'],
                    ['icon' => 'Database', 'title' => 'Database MySQL', 'description' => 'Database MySQL dan PostgreSQL dengan phpMyAdmin untuk akses mudah.'],
                    ['icon' => 'FileCode', 'title' => 'PHP 8.x Support', 'description' => 'Dukungan PHP terbaru dengan pilihan versi per domain.'],
                    ['icon' => 'Cpu', 'title' => 'cPanel Kontrol', 'description' => 'Panel kontrol intuitif untuk mengelola website dengan mudah.'],
                ],
                'pricingTitle' => 'Rencana Hosting Terjangkau',
                'pricingDescription' => 'Mulai dari harga ramah kantong dengan fitur lengkap.',
                'pricingCards' => [
                    [
                        'name' => 'Starter Hosting',
                        'price' => '29.000',
                        'period' => '/bulan',
                        'specs' => ['Disk Space' => '5 GB SSD', 'Bandwidth' => 'Unlimited', 'Email' => '5 Akun', 'Database' => '5 MySQL', 'Subdomain' => '5', 'Addon Domain' => '1'],
                        'features' => ['cPanel kontrol', 'SSL gratis', 'Backup mingguan', 'Support 24/7'],
                        'popular' => false,
                        'color' => 'sky',
                    ],
                    [
                        'name' => 'Business Hosting',
                        'price' => '59.000',
                        'period' => '/bulan',
                        'specs' => ['Disk Space' => '15 GB SSD', 'Bandwidth' => 'Unlimited', 'Email' => 'Unlimited', 'Database' => 'Unlimited MySQL', 'Subdomain' => 'Unlimited', 'Addon Domain' => '3'],
                        'features' => ['cPanel kontrol', 'SSL gratis', 'Backup harian', 'Priority support', 'LiteSpeed cache'],
                        'popular' => true,
                        'color' => 'cyan',
                    ],
                    [
                        'name' => 'Ultimate Hosting',
                        'price' => '99.000',
                        'period' => '/bulan',
                        'specs' => ['Disk Space' => '30 GB SSD NVMe', 'Bandwidth' => 'Unlimited', 'Email' => 'Unlimited', 'Database' => 'Unlimited MySQL', 'Subdomain' => 'Unlimited', 'Addon Domain' => 'Unlimited'],
                        'features' => ['cPanel kontrol', 'SSL gratis', 'Backup harian', 'Dedicated support', 'LiteSpeed cache', 'Priority queue'],
                        'popular' => false,
                        'color' => 'violet',
                    ],
                ],
                'extraSection' => [
                    'type' => 'badge-grid',
                    'title' => 'Kompatibel dengan Teknologi Populer',
                    'description' => 'Semua framework dan CMS favorit Anda dapat dijalankan dengan mudah.',
                    'items' => [
                        ['label' => 'WordPress'],
                        ['label' => 'Laravel'],
                        ['label' => 'CodeIgniter'],
                        ['label' => 'React'],
                        ['label' => 'Vue.js'],
                        ['label' => 'Next.js'],
                        ['label' => 'Node.js'],
                        ['label' => 'Python'],
                        ['label' => 'PHP'],
                        ['label' => 'MySQL'],
                        ['label' => 'PostgreSQL'],
                        ['label' => 'MongoDB'],
                    ],
                ],
                'ctaTitle' => 'Mulai Online Hari Ini',
                'ctaDescription' => 'Tim sales kami siap membantu Anda memilih hosting yang tepat untuk website Anda.',
                'ctaPrimary' => 'Pesan Sekarang',
                'ctaSecondary' => 'Hubungi Kami',
            ],
            'service:domain' => [
                'slug' => 'domain',
                'name' => 'Layanan Domain',
                'accent' => 'violet',
                'icon' => 'database',
                'breadcrumbLabel' => 'Layanan Domain',
                'heroBadge' => 'Registrasi Domain',
                'heroTitlePrefix' => 'Dapatkan Domain',
                'heroTitleHighlight' => 'Impian Anda',
                'heroDescription' => 'Mulai perjalanan online Anda dengan domain yang tepat. Harga terjangkau dengan fitur lengkap.',
                'heroPrimaryCta' => 'Cek Ketersediaan',
                'heroSecondaryCta' => 'Transfer Domain',
                'featureSectionTitle' => 'Mengapa Beli Domain di Banua Cloud?',
                'featureSectionDescription' => 'Fitur lengkap untuk mengelola domain Anda dengan mudah.',
                'features' => [
                    ['icon' => 'Search', 'title' => 'Domain Checker', 'description' => 'Cek ketersediaan domain favorit Anda secara real-time dengan mudah.'],
                    ['icon' => 'Shield', 'title' => 'Privacy Protection', 'description' => 'Lindungi informasi pribadi Anda dengan WHOIS privacy gratis.'],
                    ['icon' => 'Globe', 'title' => 'DNS Management', 'description' => 'Kelola DNS records dengan mudah melalui panel kontrol kami.'],
                    ['icon' => 'Lock', 'title' => 'SSL Gratis', 'description' => 'Auto-install SSL untuk subdomain dengan integrasi Cloudflare.'],
                    ['icon' => 'ArrowRight', 'title' => 'Domain Transfer', 'description' => 'Pindahkan domain Anda ke kami dengan proses mudah dan harga terjangkau.'],
                    ['icon' => 'Star', 'title' => '500+ Ekstensi', 'description' => 'Pilihan ekstensi domain lengkap dari .com hingga .id.'],
                ],
                'extraSection' => [
                    'type' => 'price-grid',
                    'title' => 'Domain Populer',
                    'description' => 'Harga kompetitif untuk ekstensi domain paling dicari.',
                    'items' => [
                        ['title' => '.com', 'price' => '149.000', 'suffix' => '/th', 'popular' => true],
                        ['title' => '.id', 'price' => '75.000', 'suffix' => '/th', 'popular' => true],
                        ['title' => '.net', 'price' => '159.000', 'suffix' => '/th', 'popular' => false],
                        ['title' => '.org', 'price' => '159.000', 'suffix' => '/th', 'popular' => false],
                        ['title' => '.co.id', 'price' => '250.000', 'suffix' => '/th', 'popular' => true],
                        ['title' => '.web.id', 'price' => '75.000', 'suffix' => '/th', 'popular' => false],
                        ['title' => '.biz', 'price' => '99.000', 'suffix' => '/th', 'popular' => false],
                        ['title' => '.info', 'price' => '79.000', 'suffix' => '/th', 'popular' => false],
                    ],
                ],
                'ctaTitle' => 'Domain Ready untuk Online',
                'ctaDescription' => 'Dapatkan domain terbaik untuk bisnis atau proyek Anda hari ini.',
                'ctaPrimary' => 'Cek Ketersediaan',
                'ctaSecondary' => 'Chat dengan Kami',
            ],
            'service:backup' => [
                'slug' => 'backup',
                'name' => 'Solusi Backup',
                'accent' => 'sky',
                'icon' => 'hard-drive',
                'breadcrumbLabel' => 'Solusi Backup',
                'heroBadge' => 'Solusi Backup Data',
                'heroTitlePrefix' => 'Lindungi Data Bisnis',
                'heroTitleHighlight' => 'Anda',
                'heroDescription' => 'Backup otomatis harian dengan restore sekali klik. Jamin keamanan data bisnis Anda dengan solusi backup enterprise.',
                'heroPrimaryCta' => 'Pesan Sekarang',
                'heroSecondaryCta' => 'Pelajari Lebih Lanjut',
                'featureSectionTitle' => 'Fitur Lengkap',
                'featureSectionDescription' => 'Semua yang Anda butuhkan untuk menjaga keamanan data.',
                'features' => [
                    ['icon' => 'Clock', 'title' => 'Backup Otomatis', 'description' => 'Jadwalkan backup harian, mingguan, atau kustom sesuai kebutuhan.'],
                    ['icon' => 'RefreshCw', 'title' => 'Restore Cepat', 'description' => 'Restore data dengan satu klik melalui panel kontrol.'],
                    ['icon' => 'Cloud', 'title' => 'Off-Site Storage', 'description' => 'Data backup disimpan di datacenter terpisah untuk keamanan ekstra.'],
                    ['icon' => 'Lock', 'title' => 'Enkripsi End-to-End', 'description' => 'Data dienkripsi sebelum dikirim dan disimpan dengan aman.'],
                    ['icon' => 'Server', 'title' => 'Multiple Destination', 'description' => 'Backup ke berbagai lokasi: local, cloud, atau FTP dan SFTP.'],
                    ['icon' => 'Shield', 'title' => 'Retensi Fleksibel', 'description' => 'Pilih periode retensi 7, 14, 30, atau 90 hari.'],
                ],
                'pricingTitle' => 'Rencana Backup',
                'pricingDescription' => 'Pilih kapasitas yang sesuai dengan kebutuhan bisnis Anda.',
                'pricingCards' => [
                    [
                        'name' => 'Starter Backup',
                        'price' => '75.000',
                        'period' => '/bulan',
                        'specs' => ['Storage' => '10 GB', 'Retention' => '7 Hari', 'Frequency' => 'Harian', 'Destinations' => '2'],
                        'features' => ['Backup otomatis', 'Restore 1 klik', 'Support 24/7', 'Enkripsi AES-256'],
                        'popular' => false,
                        'color' => 'sky',
                    ],
                    [
                        'name' => 'Professional Backup',
                        'price' => '149.000',
                        'period' => '/bulan',
                        'specs' => ['Storage' => '50 GB', 'Retention' => '30 Hari', 'Frequency' => 'Harian + Manual', 'Destinations' => '5'],
                        'features' => ['Backup otomatis', 'Restore 1 klik', 'Priority support', 'Enkripsi AES-256', 'Off-site storage'],
                        'popular' => true,
                        'color' => 'cyan',
                    ],
                    [
                        'name' => 'Enterprise Backup',
                        'price' => '299.000',
                        'period' => '/bulan',
                        'specs' => ['Storage' => '200 GB', 'Retention' => '90 Hari', 'Frequency' => 'Custom', 'Destinations' => 'Unlimited'],
                        'features' => ['Backup otomatis', 'Restore 1 klik', 'Dedicated support', 'Enkripsi AES-256', 'Off-site storage', 'API access'],
                        'popular' => false,
                        'color' => 'violet',
                    ],
                ],
                'ctaTitle' => 'Amankan Data Sekarang',
                'ctaDescription' => 'Jangan tunggu sampai data hilang. Lindungi bisnis Anda hari ini.',
                'ctaPrimary' => 'Pesan Sekarang',
                'ctaSecondary' => 'Chat dengan Kami',
            ],
            'service:app-development' => [
                'slug' => 'app-development',
                'name' => 'Pengembangan Aplikasi',
                'accent' => 'violet',
                'icon' => 'code',
                'breadcrumbLabel' => 'Pengembangan Aplikasi',
                'heroBadge' => 'App Development',
                'heroTitlePrefix' => 'Wujudkan Aplikasi',
                'heroTitleHighlight' => 'Impian Anda',
                'heroDescription' => 'Tim pengembang profesional siap membangun aplikasi web dan mobile yang responsif, modern, dan berkinerja tinggi.',
                'heroPrimaryCta' => 'Konsultasi Gratis',
                'heroSecondaryCta' => 'Lihat Portfolio',
                'featureSectionTitle' => 'Layanan Pengembangan',
                'featureSectionDescription' => 'Solusi lengkap untuk semua kebutuhan pengembangan aplikasi Anda.',
                'features' => [
                    ['icon' => 'Globe', 'title' => 'Web Application', 'description' => 'Aplikasi web responsif dan modern dengan teknologi terkini.', 'tags' => ['Vue.js', 'React', 'Next.js', 'Laravel', 'Node.js']],
                    ['icon' => 'Smartphone', 'title' => 'Mobile App', 'description' => 'Aplikasi mobile untuk iOS dan Android dengan performa optimal.', 'tags' => ['React Native', 'Flutter', 'Swift', 'Kotlin']],
                    ['icon' => 'Palette', 'title' => 'UI/UX Design', 'description' => 'Desain antarmuka yang menarik dan user-friendly.', 'tags' => ['Figma', 'Adobe XD', 'Sketch', 'Prototyping']],
                    ['icon' => 'Server', 'title' => 'API Development', 'description' => 'API yang robust dan secure untuk integrasi aplikasi modern.', 'tags' => ['REST API', 'GraphQL', 'Node.js', 'Python']],
                ],
                'extraSection' => [
                    'type' => 'timeline-grid',
                    'title' => 'Proses Pengembangan',
                    'description' => 'Metodologi agile untuk hasil yang optimal dan tepat waktu.',
                    'items' => [
                        ['step' => '01', 'title' => 'Konsultasi', 'description' => 'Kami mendengarkan kebutuhan dan tujuan bisnis Anda.'],
                        ['step' => '02', 'title' => 'Perencanaan', 'description' => 'Tim kami membuat roadmap dan timeline pengembangan.'],
                        ['step' => '03', 'title' => 'Development', 'description' => 'Pengembangan aplikasi dengan metodologi agile.'],
                        ['step' => '04', 'title' => 'Testing', 'description' => 'Quality assurance untuk memastikan kualitas produk.'],
                        ['step' => '05', 'title' => 'Launch', 'description' => 'Deploy aplikasi dan pelatihan pengguna.'],
                        ['step' => '06', 'title' => 'Maintenance', 'description' => 'Dukungan lanjutan dan pembaruan berkala.'],
                    ],
                ],
                'ctaTitle' => 'Mulai Proyek Anda',
                'ctaDescription' => 'Konsultasi gratis untuk mendiskusikan kebutuhan aplikasi Anda.',
                'ctaPrimary' => 'Konsultasi Gratis',
                'ctaSecondary' => 'Chat dengan Kami',
            ],
            'service:it-consulting' => [
                'slug' => 'it-consulting',
                'name' => 'Konsultasi IT',
                'accent' => 'cyan',
                'icon' => 'message-square',
                'breadcrumbLabel' => 'Konsultasi IT',
                'heroBadge' => 'IT Consulting',
                'heroTitlePrefix' => 'Strategi IT untuk',
                'heroTitleHighlight' => 'Bisnis Lebih Baik',
                'heroDescription' => 'Dapatkan panduan ahli untuk transformasi digital bisnis Anda. Kami membantu Anda merencanakan, mengimplementasikan, dan mengoptimalkan infrastruktur teknologi.',
                'heroPrimaryCta' => 'Jadwalkan Konsultasi',
                'heroSecondaryCta' => 'Pelajari Lebih Lanjut',
                'featureSectionTitle' => 'Layanan Konsultasi',
                'featureSectionDescription' => 'Solusi komprehensif untuk semua kebutuhan IT bisnis Anda.',
                'features' => [
                    ['icon' => 'Briefcase', 'title' => 'Konsultasi Infrastruktur', 'description' => 'Evaluasi dan optimalisasi infrastruktur IT Anda untuk performa maksimal.'],
                    ['icon' => 'TrendingUp', 'title' => 'Transformasi Digital', 'description' => 'Panduan lengkap untuk transformasi bisnis di era digital.'],
                    ['icon' => 'Shield', 'title' => 'Keamanan Siber', 'description' => 'Evaluasi keamanan dan implementasi solusi perlindungan data.'],
                    ['icon' => 'BarChart3', 'title' => 'Optimasi Performa', 'description' => 'Analisis dan peningkatan performa sistem Anda.'],
                    ['icon' => 'GraduationCap', 'title' => 'Pelatihan Tim IT', 'description' => 'Program pelatihan untuk meningkatkan kapasitas tim teknologi Anda.'],
                    ['icon' => 'Users', 'title' => 'Rencana IT Strategis', 'description' => 'Penyusunan roadmap IT yang sejalan dengan tujuan bisnis.'],
                ],
                'extraSection' => [
                    'type' => 'checklist',
                    'title' => 'Mengapa Memilih Banua Cloud?',
                    'description' => 'Keuntungan bekerja sama dengan tim konsultan kami.',
                    'items' => [
                        ['text' => 'Tim berpengalaman dengan sertifikasi internasional'],
                        ['text' => 'Pendekatan praktis dan hasil terukur'],
                        ['text' => 'Konsultasi offline dan online'],
                        ['text' => 'Laporan detail dan rekomendasi actionable'],
                        ['text' => 'Follow-up support setelah konsultasi'],
                        ['text' => 'Harga transparan tanpa biaya tersembunyi'],
                    ],
                ],
                'ctaTitle' => 'Siap Meningkatkan Bisnis Anda?',
                'ctaDescription' => 'Jadwalkan konsultasi gratis untuk mendiskusikan kebutuhan IT bisnis Anda.',
                'ctaPrimary' => 'Jadwalkan Konsultasi',
                'ctaSecondary' => 'Chat dengan Kami',
            ],
        ];
    }
}
