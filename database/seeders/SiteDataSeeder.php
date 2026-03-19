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
                'site_description' => env('VITE_COMPANY_DESCRIPTOR', 'Mitra infrastruktur IT, cloud, dan jaringan untuk bisnis di Indonesia'),
                'company_name' => env('VITE_COMPANY_FULL_NAME', env('VITE_COMPANY_NAME', env('APP_NAME', 'Banua Cloud Nusantara'))),
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
                'title' => 'Mitra Infrastruktur IT untuk Bisnis Modern',
                'subtitle' => 'Banua Cloud Nusantara membantu perusahaan merancang, membangun, dan mengelola cloud, jaringan kantor dan gedung, backup, serta pengembangan aplikasi dengan pendekatan yang rapi dan terukur.',
                'cta_primary' => 'Pelajari Profil Kami',
                'cta_secondary' => 'Lihat Layanan',
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
                    ['name' => 'Ahmad Pratama', 'role' => 'CTO', 'company' => 'TechStart Indonesia', 'content' => 'Banua Cloud Nusantara telah mengubah cara kami mendeploy aplikasi. Kecepatan dan keandalan tidak tertandingi, dan tim support mereka selalu siap membantu kapan saja.'],
                    ['name' => 'Siti Nurhaliza', 'role' => 'Founder', 'company' => 'DigitalAgency', 'content' => 'Kami telah mencoba banyak penyedia cloud, tapi Banua Cloud Nusantara menonjol dengan uptime luar biasa dan harga kompetitif. Sangat direkomendasikan untuk bisnis di Indonesia.'],
                    ['name' => 'Budi Santoso', 'role' => 'IT Manager', 'company' => 'EcomStore', 'content' => 'Migrasi ke Banua Cloud Nusantara berjalan mulus. Tim mereka menangani semuanya dengan profesional, dan performa website kami meningkat signifikan.'],
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
        $pages = [
            'learn-more' => [
                'heroBadge' => 'Profil Perusahaan',
                'heroTitlePrefix' => 'Kapabilitas Infrastruktur untuk',
                'heroTitleHighlight' => 'Perusahaan Modern',
                'heroDescription' => 'Banua Cloud Nusantara hadir sebagai mitra implementasi untuk cloud, jaringan kantor dan gedung, backup, domain, serta pengembangan aplikasi yang disesuaikan dengan kebutuhan operasional bisnis.',
                'stats' => [
                    ['icon' => 'Server', 'value' => '500+', 'label' => 'Server'],
                    ['icon' => 'Clock', 'value' => '99.9%', 'label' => 'Uptime'],
                    ['icon' => 'Users', 'value' => '10RB+', 'label' => 'Pelanggan'],
                ],
                'serviceSectionBadge' => 'Bidang Layanan',
                'serviceSectionTitle' => 'Ruang lingkup solusi yang kami tangani dari perencanaan sampai operasional',
                'serviceSectionDescription' => 'Kami tidak hanya menyediakan layanan cloud, tetapi juga membantu konstruksi jaringan, implementasi aplikasi, backup, dan pengelolaan infrastruktur yang dibutuhkan perusahaan.',
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
                            'Storage SSD NVMe berkecepatan tinggi',
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
                            'Email profesional tanpa batas',
                            'Database MySQL dan PostgreSQL',
                            'Dukungan PHP 8.x terbaru',
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
                            'Perlindungan privasi domain',
                            'Perpanjangan otomatis',
                            'Panel pengelolaan DNS',
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
                            'Penyimpanan terpisah',
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
                        'subtitle' => 'Pengembangan Aplikasi',
                        'description' => 'Tim pengembang profesional kami siap membantu Anda membangun aplikasi web dan mobile yang responsif, modern, dan berkinerja tinggi menggunakan teknologi terkini.',
                        'features' => [
                            'Pengembangan aplikasi web',
                            'Pengembangan aplikasi mobile',
                            'Desain UI/UX modern',
                            'Pengembangan dan integrasi API',
                            'Pemeliharaan dan dukungan',
                            'Teknologi modern lintas platform',
                        ],
                        'gradient' => 'violet',
                    ],
                    [
                        'slug' => 'it-consulting',
                        'icon' => 'message-square',
                        'title' => 'Konsultasi IT',
                        'subtitle' => 'Konsultasi IT',
                        'description' => 'Pendampingan strategis dan teknis untuk transformasi digital, revitalisasi jaringan kantor, pembangunan jaringan gedung, dan penguatan fondasi operasional IT perusahaan.',
                        'features' => [
                            'Konsultasi infrastruktur IT',
                            'Transformasi digital',
                            'Revitalisasi jaringan kantor',
                            'Pembangunan jaringan gedung',
                            'Evaluasi keamanan sistem',
                            'Optimasi performa',
                            'Rencana IT strategis',
                        ],
                        'gradient' => 'cyan',
                    ],
                ],
                'reasonsBadge' => 'Pendekatan Kerja',
                'reasonsTitle' => 'Alasan perusahaan mempercayakan implementasi ke Banua Cloud Nusantara',
                'reasonsDescription' => 'Fokus kami bukan hanya menjual layanan, tetapi menyiapkan fondasi teknis yang bisa dipakai jangka panjang dan mudah dikembangkan.',
                'reasons' => [
                    ['icon' => 'Zap', 'title' => 'Super Cepat', 'description' => 'Infrastruktur performa tinggi dengan CDN global untuk kecepatan optimal.'],
                    ['icon' => 'Shield', 'title' => 'Aman Terjamin', 'description' => 'Enkripsi tingkat bank dan proteksi DDoS enterprise untuk keamanan data Anda.'],
                    ['icon' => 'Globe2', 'title' => 'Jaringan Global', 'description' => '15+ data center di berbagai wilayah dengan latensi rendah untuk pengguna lintas lokasi.'],
                    ['icon' => 'Star', 'title' => 'Dukungan 24/7', 'description' => 'Tim support profesional siap membantu kapan pun Anda membutuhkan respons teknis.'],
                ],
                'faqBadge' => 'FAQ',
                'faqTitle' => 'Pertanyaan Umum',
                'faqDescription' => 'Temukan jawaban untuk pertanyaan yang sering diajukan tentang layanan kami.',
                'faqs' => [
                    ['question' => 'Bagaimana cara memilih layanan yang tepat untuk bisnis saya?', 'answer' => 'Tim sales kami siap membantu Anda memilih layanan yang sesuai dengan kebutuhan. Anda dapat menghubungi kami melalui live chat, email, atau telepon untuk berkonsultasi secara gratis.'],
                    ['question' => 'Apakah saya bisa upgrade layanan di kemudian hari?', 'answer' => 'Tentu. Semua layanan kami dapat di-upgrade kapan saja. Anda cukup mengajukan upgrade melalui panel klien kami, dan tim kami akan memprosesnya dalam waktu singkat.'],
                    ['question' => 'Berapa lama waktu setup layanan?', 'answer' => 'Cloud VPS biasanya siap dalam 1-24 jam. Web hosting dan domain dapat aktif dalam hitungan menit setelah pembayaran terkonfirmasi.'],
                    ['question' => 'Apakah ada garansi uptime?', 'answer' => 'Ya, kami memberikan garansi uptime 99.9% untuk layanan infrastruktur utama. Jika uptime tidak terpenuhi, Anda berhak mendapatkan kompensasi sesuai SLA.'],
                    ['question' => 'Bagaimana sistem support Banua Cloud Nusantara?', 'answer' => 'Kami menyediakan support 24/7 melalui ticket system, live chat, dan telepon. Tim support kami terdiri dari teknisi berpengalaman yang siap membantu Anda kapan saja.'],
                ],
                'ctaTitle' => 'Butuh partner untuk cloud, jaringan, atau implementasi sistem?',
                'ctaDescription' => 'Diskusikan kebutuhan perusahaan Anda bersama tim Banua Cloud Nusantara, mulai dari konsultasi, pembangunan jaringan, sampai pengelolaan infrastruktur.',
                'ctaPrimary' => 'Jadwalkan Diskusi',
                'ctaSecondary' => 'Lihat Detail Layanan',
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
                'heroSecondaryCta' => 'Lihat Paket',
                'featureSectionTitle' => 'Mengapa Memilih Cloud VPS Kami?',
                'featureSectionDescription' => 'Dilengkapi dengan infrastruktur enterprise untuk performa maksimal dan keandalan.',
                'features' => [
                    ['icon' => 'Cpu', 'title' => 'Performa Tinggi', 'description' => 'Prosesor terbaru dengan clock speed tinggi untuk beban kerja intensif.'],
                    ['icon' => 'HardDrive', 'title' => 'Penyimpanan NVMe SSD', 'description' => 'Penyimpanan NVMe SSD ultra-cepat dengan kecepatan baca tulis tinggi.'],
                    ['icon' => 'Zap', 'title' => 'Resource Terjamin', 'description' => 'CPU, RAM, dan storage dedicated tidak berbagi dengan pengguna lain.'],
                    ['icon' => 'Shield', 'title' => 'Keamanan Terdepan', 'description' => 'Firewall hardware, DDoS protection, dan backup otomatis harian.'],
                    ['icon' => 'Globe', 'title' => 'Jaringan Global', 'description' => 'Pilihan data center dengan bandwidth tinggi dan latensi rendah untuk berbagai kebutuhan.'],
                    ['icon' => 'Terminal', 'title' => 'Akses Root Penuh', 'description' => 'Kontrol penuh dengan akses root untuk konfigurasi custom.'],
                ],
                'pricingTitle' => 'Rencana VPS yang Fleksibel',
                'pricingDescription' => 'Pilih paket yang sesuai dengan kebutuhan Anda. Semua paket mencakup dukungan 24/7.',
                'pricingCards' => [
                    [
                        'name' => 'Starter VPS',
                        'price' => '149.000',
                        'period' => '/bulan',
                        'specs' => ['CPU' => '2 vCPU', 'RAM' => '2 GB', 'Storage' => '30 GB NVMe', 'Bandwidth' => 'Unlimited', 'IP' => '1 IPv4'],
                        'features' => ['Panel kontrol opsional', 'Backup mingguan', 'Dukungan 24/7', 'Uptime 99.9%'],
                        'popular' => false,
                        'color' => 'sky',
                    ],
                    [
                        'name' => 'Professional VPS',
                        'price' => '299.000',
                        'period' => '/bulan',
                        'specs' => ['CPU' => '4 vCPU', 'RAM' => '4 GB', 'Storage' => '60 GB NVMe', 'Bandwidth' => 'Unlimited', 'IP' => '1 IPv4'],
                        'features' => ['Panel kontrol opsional', 'Backup harian', 'Dukungan prioritas', 'Uptime 99.9%', 'Proteksi DDoS'],
                        'popular' => true,
                        'color' => 'cyan',
                    ],
                    [
                        'name' => 'Enterprise VPS',
                        'price' => '599.000',
                        'period' => '/bulan',
                        'specs' => ['CPU' => '8 vCPU', 'RAM' => '8 GB', 'Storage' => '120 GB NVMe', 'Bandwidth' => 'Unlimited', 'IP' => '1 IPv4'],
                        'features' => ['Panel kontrol opsional', 'Backup harian', 'Dukungan khusus', 'Uptime 99.9%', 'Proteksi DDoS', 'SSL gratis'],
                        'popular' => false,
                        'color' => 'violet',
                    ],
                ],
                'extraSection' => [
                    'type' => 'badge-grid',
                    'title' => 'Pilihan Sistem Operasi',
                    'description' => 'Instal sistem operasi favorit Anda dengan satu klik atau gunakan ISO kustom.',
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
                'ctaSecondary' => 'Hubungi Tim Kami',
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
                'heroSecondaryCta' => 'Lihat Paket',
                'featureSectionTitle' => 'Keunggulan Web Hosting Kami',
                'featureSectionDescription' => 'Didesain untuk performa maksimal dengan teknologi terkini.',
                'features' => [
                    ['icon' => 'Zap', 'title' => 'Kecepatan Optimal', 'description' => 'Server dengan LiteSpeed Web Server dan SSD NVMe untuk waktu muat yang sangat cepat.'],
                    ['icon' => 'Shield', 'title' => 'SSL Gratis', 'description' => 'Auto-install SSL Let\'s Encrypt tanpa batas dengan renew otomatis.'],
                    ['icon' => 'Mail', 'title' => 'Email Tanpa Batas', 'description' => 'Buat email profesional tanpa batas dengan webmail Roundcube.'],
                    ['icon' => 'Database', 'title' => 'Database MySQL', 'description' => 'Database MySQL dan PostgreSQL dengan phpMyAdmin untuk akses mudah.'],
                    ['icon' => 'FileCode', 'title' => 'PHP 8.x Support', 'description' => 'Dukungan PHP terbaru dengan pilihan versi per domain.'],
                    ['icon' => 'Cpu', 'title' => 'Kontrol cPanel', 'description' => 'Panel kontrol intuitif untuk mengelola website dengan mudah.'],
                ],
                'pricingTitle' => 'Rencana Hosting Terjangkau',
                'pricingDescription' => 'Mulai dari harga ramah kantong dengan fitur lengkap.',
                'pricingCards' => [
                    [
                        'name' => 'Starter Hosting',
                        'price' => '29.000',
                        'period' => '/bulan',
                        'specs' => ['Disk Space' => '5 GB SSD', 'Bandwidth' => 'Unlimited', 'Email' => '5 Akun', 'Database' => '5 MySQL', 'Subdomain' => '5', 'Addon Domain' => '1'],
                        'features' => ['Akses cPanel', 'SSL gratis', 'Backup mingguan', 'Dukungan 24/7'],
                        'popular' => false,
                        'color' => 'sky',
                    ],
                    [
                        'name' => 'Business Hosting',
                        'price' => '59.000',
                        'period' => '/bulan',
                        'specs' => ['Disk Space' => '15 GB SSD', 'Bandwidth' => 'Unlimited', 'Email' => 'Unlimited', 'Database' => 'Unlimited MySQL', 'Subdomain' => 'Unlimited', 'Addon Domain' => '3'],
                        'features' => ['Akses cPanel', 'SSL gratis', 'Backup harian', 'Dukungan prioritas', 'LiteSpeed Cache'],
                        'popular' => true,
                        'color' => 'cyan',
                    ],
                    [
                        'name' => 'Ultimate Hosting',
                        'price' => '99.000',
                        'period' => '/bulan',
                        'specs' => ['Disk Space' => '30 GB SSD NVMe', 'Bandwidth' => 'Unlimited', 'Email' => 'Unlimited', 'Database' => 'Unlimited MySQL', 'Subdomain' => 'Unlimited', 'Addon Domain' => 'Unlimited'],
                        'features' => ['Akses cPanel', 'SSL gratis', 'Backup harian', 'Dukungan khusus', 'LiteSpeed Cache', 'Antrian prioritas'],
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
                'ctaSecondary' => 'Hubungi Tim Kami',
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
                    ['icon' => 'Search', 'title' => 'Cek Domain', 'description' => 'Periksa ketersediaan domain favorit Anda secara real time dengan cepat.'],
                    ['icon' => 'Shield', 'title' => 'Perlindungan Privasi', 'description' => 'Lindungi informasi pribadi Anda dengan WHOIS privacy gratis.'],
                    ['icon' => 'Globe', 'title' => 'Pengelolaan DNS', 'description' => 'Kelola DNS record dengan mudah melalui panel kontrol kami.'],
                    ['icon' => 'Lock', 'title' => 'SSL Gratis', 'description' => 'SSL terpasang otomatis untuk subdomain dengan integrasi Cloudflare.'],
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
                'ctaTitle' => 'Domain siap online untuk bisnis Anda',
                'ctaDescription' => 'Dapatkan domain terbaik untuk bisnis atau proyek Anda hari ini.',
                'ctaPrimary' => 'Cek Ketersediaan',
                'ctaSecondary' => 'Hubungi Tim Kami',
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
                    ['icon' => 'Cloud', 'title' => 'Penyimpanan Terpisah', 'description' => 'Data backup disimpan di data center terpisah untuk keamanan ekstra.'],
                    ['icon' => 'Lock', 'title' => 'Enkripsi End-to-End', 'description' => 'Data dienkripsi sebelum dikirim dan disimpan dengan aman.'],
                    ['icon' => 'Server', 'title' => 'Banyak Tujuan Backup', 'description' => 'Backup ke berbagai lokasi: lokal, cloud, FTP, maupun SFTP.'],
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
                        'features' => ['Backup otomatis', 'Restore 1 klik', 'Dukungan 24/7', 'Enkripsi AES-256'],
                        'popular' => false,
                        'color' => 'sky',
                    ],
                    [
                        'name' => 'Professional Backup',
                        'price' => '149.000',
                        'period' => '/bulan',
                        'specs' => ['Storage' => '50 GB', 'Retention' => '30 Hari', 'Frequency' => 'Harian + Manual', 'Destinations' => '5'],
                        'features' => ['Backup otomatis', 'Restore 1 klik', 'Dukungan prioritas', 'Enkripsi AES-256', 'Penyimpanan terpisah'],
                        'popular' => true,
                        'color' => 'cyan',
                    ],
                    [
                        'name' => 'Enterprise Backup',
                        'price' => '299.000',
                        'period' => '/bulan',
                        'specs' => ['Storage' => '200 GB', 'Retention' => '90 Hari', 'Frequency' => 'Custom', 'Destinations' => 'Unlimited'],
                        'features' => ['Backup otomatis', 'Restore 1 klik', 'Dukungan khusus', 'Enkripsi AES-256', 'Penyimpanan terpisah', 'Akses API'],
                        'popular' => false,
                        'color' => 'violet',
                    ],
                ],
                'ctaTitle' => 'Amankan Data Sekarang',
                'ctaDescription' => 'Jangan tunggu sampai data hilang. Lindungi bisnis Anda hari ini.',
                'ctaPrimary' => 'Pesan Sekarang',
                'ctaSecondary' => 'Hubungi Tim Kami',
            ],
            'service:app-development' => [
                'slug' => 'app-development',
                'name' => 'Pengembangan Aplikasi',
                'accent' => 'violet',
                'icon' => 'code',
                'breadcrumbLabel' => 'Pengembangan Aplikasi',
                'heroBadge' => 'Pengembangan Aplikasi',
                'heroTitlePrefix' => 'Wujudkan Aplikasi',
                'heroTitleHighlight' => 'Impian Anda',
                'heroDescription' => 'Tim pengembang profesional siap membangun aplikasi web dan mobile yang responsif, modern, dan berkinerja tinggi.',
                'heroPrimaryCta' => 'Konsultasi Gratis',
                'heroSecondaryCta' => 'Lihat Portofolio',
                'featureSectionTitle' => 'Layanan Pengembangan',
                'featureSectionDescription' => 'Solusi lengkap untuk semua kebutuhan pengembangan aplikasi Anda.',
                'features' => [
                    ['icon' => 'Globe', 'title' => 'Aplikasi Web', 'description' => 'Aplikasi web responsif dan modern dengan teknologi terkini.', 'tags' => ['Vue.js', 'React', 'Next.js', 'Laravel', 'Node.js']],
                    ['icon' => 'Smartphone', 'title' => 'Aplikasi Mobile', 'description' => 'Aplikasi mobile untuk iOS dan Android dengan performa optimal.', 'tags' => ['React Native', 'Flutter', 'Swift', 'Kotlin']],
                    ['icon' => 'Palette', 'title' => 'Desain UI/UX', 'description' => 'Desain antarmuka yang menarik dan mudah digunakan.', 'tags' => ['Figma', 'Adobe XD', 'Sketch', 'Prototyping']],
                    ['icon' => 'Server', 'title' => 'Pengembangan API', 'description' => 'API yang tangguh dan aman untuk integrasi aplikasi modern.', 'tags' => ['REST API', 'GraphQL', 'Node.js', 'Python']],
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
                'ctaSecondary' => 'Hubungi Tim Kami',
            ],
            'service:it-consulting' => [
                'slug' => 'it-consulting',
                'name' => 'Konsultasi IT',
                'accent' => 'cyan',
                'icon' => 'message-square',
                'breadcrumbLabel' => 'Konsultasi IT',
                'heroBadge' => 'Konsultasi IT',
                'heroTitlePrefix' => 'Strategi IT untuk',
                'heroTitleHighlight' => 'Bisnis Lebih Baik',
                'heroDescription' => 'Dapatkan panduan ahli untuk transformasi digital bisnis Anda. Kami membantu Anda merencanakan, mengimplementasikan, dan mengoptimalkan infrastruktur teknologi.',
                'heroPrimaryCta' => 'Jadwalkan Konsultasi',
                'heroSecondaryCta' => 'Pelajari Lebih Lanjut',
                'featureSectionTitle' => 'Layanan Konsultasi',
                'featureSectionDescription' => 'Solusi komprehensif untuk kebutuhan strategi IT, revitalisasi jaringan, dan implementasi infrastruktur bisnis Anda.',
                'features' => [
                    ['icon' => 'Briefcase', 'title' => 'Konsultasi Infrastruktur', 'description' => 'Evaluasi dan optimalisasi infrastruktur IT Anda untuk performa maksimal.'],
                    ['icon' => 'TrendingUp', 'title' => 'Transformasi Digital', 'description' => 'Panduan lengkap untuk transformasi bisnis di era digital.'],
                    ['icon' => 'Shield', 'title' => 'Keamanan Siber', 'description' => 'Evaluasi keamanan dan implementasi solusi perlindungan data.'],
                    ['icon' => 'Server', 'title' => 'Revitalisasi Jaringan Kantor', 'description' => 'Peremajaan topologi, perangkat, dan segmentasi jaringan agar operasional kantor lebih stabil dan aman.'],
                    ['icon' => 'Globe2', 'title' => 'Pembangunan Jaringan Gedung', 'description' => 'Perencanaan dan implementasi jaringan untuk kantor, gedung, maupun bangunan baru secara bertahap dan terstruktur.'],
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
                        ['text' => 'Laporan detail dan rekomendasi yang bisa langsung dijalankan'],
                        ['text' => 'Follow-up support setelah konsultasi'],
                        ['text' => 'Harga transparan tanpa biaya tersembunyi'],
                    ],
                ],
                'ctaTitle' => 'Siap Meningkatkan Bisnis Anda?',
                'ctaDescription' => 'Jadwalkan konsultasi gratis untuk mendiskusikan kebutuhan IT bisnis Anda.',
                'ctaPrimary' => 'Jadwalkan Konsultasi',
                'ctaSecondary' => 'Hubungi Tim Kami',
            ],
        ];

        foreach (array_keys($pages) as $pageKey) {
            $pages[$pageKey] = [
                ...$pages[$pageKey],
                ...$this->marketingPageCtaTargets($pageKey),
            ];
        }

        return $pages;
    }

    private function marketingPageCtaTargets(string $pageKey): array
    {
        return match ($pageKey) {
            'learn-more' => [
                'ctaPrimaryTarget' => '/services/cloud-vps',
                'ctaSecondaryTarget' => '#contact',
            ],
            'service:cloud-vps', 'service:web-hosting', 'service:backup' => [
                'heroPrimaryTarget' => '#service-contact',
                'heroSecondaryTarget' => '#service-pricing',
                'ctaPrimaryTarget' => '#service-contact',
                'ctaSecondaryTarget' => '#contact',
            ],
            'service:domain' => [
                'heroPrimaryTarget' => '#service-extra',
                'heroSecondaryTarget' => '#service-contact',
                'ctaPrimaryTarget' => '#service-contact',
                'ctaSecondaryTarget' => '#contact',
            ],
            'service:app-development', 'service:it-consulting' => [
                'heroPrimaryTarget' => '#service-contact',
                'heroSecondaryTarget' => '#service-extra',
                'ctaPrimaryTarget' => '#service-contact',
                'ctaSecondaryTarget' => '#contact',
            ],
            default => [
                'heroPrimaryTarget' => '#service-contact',
                'heroSecondaryTarget' => '#service-extra',
                'ctaPrimaryTarget' => '#service-contact',
                'ctaSecondaryTarget' => '#contact',
            ],
        };
    }
}
