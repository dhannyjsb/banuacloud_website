export interface SiteSettings {
  maintenanceMode: boolean;
  siteName: string;
  siteDescription: string;
  companyName: string;
  companyEmail: string;
  companyPhone: string;
  companyWhatsapp: string;
  companyAddress: string;
  socialInstagram: string;
  socialLinkedin: string;
  socialTwitter: string;
  socialFacebook: string;
  emailNotifications: boolean;
  orderAlerts: boolean;
  supportAlerts: boolean;
  twoFactorEnabled: boolean;
  sessionTimeout: number;
}

export interface HeroContent {
  title: string;
  subtitle: string;
  ctaPrimary: string;
  ctaSecondary: string;
}

export interface FeatureItem {
  id: string;
  title: string;
  description: string;
  icon: string;
}

export interface TestimonialItem {
  id: string;
  name: string;
  role: string;
  company: string;
  content: string;
  avatar?: string | null;
}

export interface ServiceSpecs {
  cpu: string;
  ram: string;
  storage: string;
  bandwidth: string;
}

export interface ServicePlan {
  id: string;
  name: string;
  price: number;
  period: string;
  specs: ServiceSpecs;
  features: string[];
  popular: boolean;
  color: string;
}

export interface SiteService {
  id: string;
  name: string;
  slug: string;
  description: string;
  icon: string;
  enabled: boolean;
  plans: ServicePlan[];
}

export interface SiteBootstrap {
  settings: SiteSettings;
  heroContent: HeroContent;
  features: FeatureItem[];
  testimonials: TestimonialItem[];
  services: SiteService[];
}

const API_BASE_URL = (import.meta.env.VITE_API_BASE_URL || '/api').replace(/\/+$/, '');

export const defaultSiteSettings: SiteSettings = {
  maintenanceMode: import.meta.env.VITE_MAINTENANCE_MODE === 'true',
  siteName: import.meta.env.VITE_COMPANY_NAME || 'Banua Cloud Nusantara',
  siteDescription: import.meta.env.VITE_COMPANY_DESCRIPTOR || 'Trusted IT Solutions Partner in Indonesia',
  companyName: import.meta.env.VITE_COMPANY_FULL_NAME || 'Banua Cloud Nusantara',
  companyEmail: import.meta.env.VITE_SUPPORT_EMAIL || 'support@banuacloud.id',
  companyPhone: import.meta.env.VITE_SUPPORT_PHONE || '+62 812-3456-7890',
  companyWhatsapp: import.meta.env.VITE_SUPPORT_WHATSAPP_RAW || '6281234567890',
  companyAddress: 'Jakarta, Indonesia',
  socialInstagram: import.meta.env.VITE_SOCIAL_INSTAGRAM || '',
  socialLinkedin: import.meta.env.VITE_SOCIAL_LINKEDIN || '',
  socialTwitter: import.meta.env.VITE_SOCIAL_TWITTER || '',
  socialFacebook: '',
  emailNotifications: true,
  orderAlerts: true,
  supportAlerts: true,
  twoFactorEnabled: false,
  sessionTimeout: 30,
};

export const defaultHeroContent: HeroContent = {
  title: 'Solusi Cloud untuk Bisnis Modern',
  subtitle: 'Rasakan performa super cepat dengan infrastruktur cloud tingkat enterprise kami. Solusi yang skalabel, aman, dan terpercaya disesuaikan dengan kebutuhan bisnis Anda.',
  ctaPrimary: 'Mulai Sekarang',
  ctaSecondary: 'Lihat Harga',
};

export const defaultFeatures: FeatureItem[] = [
  {
    id: 'feature-1',
    icon: 'Zap',
    title: 'Super Cepat',
    description: 'Deploy aplikasi Anda dalam hitungan detik dengan infrastruktur berperforma tinggi dan jaringan CDN global kami.',
  },
  {
    id: 'feature-2',
    icon: 'Shield',
    title: 'Keamanan Enterprise',
    description: 'Enkripsi tingkat bank dan sertifikasi kepatuhan untuk menjaga data Anda tetap aman setiap saat.',
  },
  {
    id: 'feature-3',
    icon: 'Globe',
    title: 'Jaringan Global',
    description: 'Deploy di 15+ wilayah di seluruh dunia dengan koneksi latensi rendah untuk pengguna Anda.',
  },
  {
    id: 'feature-4',
    icon: 'Database',
    title: 'Backup Otomatis',
    description: 'Backup otomatis harian dengan restore satu klik memastikan data Anda selalu aman.',
  },
  {
    id: 'feature-5',
    icon: 'Lock',
    title: 'Perlindungan DDoS',
    description: 'Mitigasi DDoS tingkat enterprise untuk menjaga layanan Anda tetap berjalan saat serangan.',
  },
  {
    id: 'feature-6',
    icon: 'Cpu',
    title: 'Auto Scaling',
    description: 'Skalakan sumber daya secara otomatis berdasarkan pola traffic untuk performa optimal.',
  },
];

export const defaultTestimonials: TestimonialItem[] = [
  {
    id: 'testimonial-1',
    name: 'Ahmad Pratama',
    role: 'CTO',
    company: 'TechStart Indonesia',
    content: 'Banua Cloud telah mengubah cara kami mendeploy aplikasi. Kecepatan dan keandalan tidak tertandingi, dan tim support mereka selalu siap membantu kapan saja.',
  },
  {
    id: 'testimonial-2',
    name: 'Siti Nurhaliza',
    role: 'Founder',
    company: 'DigitalAgency',
    content: 'Kami telah mencoba banyak penyedia cloud, tapi Banua Cloud menonjol dengan uptime luar biasa dan harga kompetitif. Sangat direkomendasikan untuk bisnis di Indonesia.',
  },
  {
    id: 'testimonial-3',
    name: 'Budi Santoso',
    role: 'IT Manager',
    company: 'EcomStore',
    content: 'Migrasi ke Banua Cloud berjalan mulus. Tim mereka menangani semuanya dengan profesional, dan performa website kami meningkat signifikan.',
  },
];

export const defaultServices: SiteService[] = [
  {
    id: 'service-1',
    name: 'Cloud VPS',
    slug: 'cloud-vps',
    description: 'Server virtual private dengan performa tinggi dengan sumber daya khusus dan akses root.',
    icon: 'server',
    enabled: true,
    plans: [
      {
        id: 'plan-1',
        name: 'Starter',
        price: 149000,
        period: '/bulan',
        specs: { cpu: '1 Core', ram: '1GB', storage: '25GB SSD', bandwidth: '1TB' },
        features: ['cPanel', 'SSL Gratis', 'Backup Mingguan'],
        popular: false,
        color: 'sky',
      },
      {
        id: 'plan-2',
        name: 'Professional',
        price: 299000,
        period: '/bulan',
        specs: { cpu: '2 Core', ram: '2GB', storage: '50GB SSD', bandwidth: '2TB' },
        features: ['cPanel', 'SSL Gratis', 'Backup Harian', 'Priority Support'],
        popular: true,
        color: 'sky',
      },
    ],
  },
  {
    id: 'service-2',
    name: 'Web Hosting',
    slug: 'web-hosting',
    description: 'Shared hosting cepat dan andal dengan cPanel dan penyimpanan SSD untuk website Anda.',
    icon: 'globe',
    enabled: true,
    plans: [
      {
        id: 'plan-3',
        name: 'Starter',
        price: 29000,
        period: '/bulan',
        specs: { cpu: 'Shared', ram: 'Unlimited', storage: '5GB SSD', bandwidth: 'Unlimited' },
        features: ['cPanel', 'SSL Gratis', 'Email Unlimited'],
        popular: false,
        color: 'cyan',
      },
    ],
  },
  {
    id: 'service-3',
    name: 'Layanan Domain',
    slug: 'domain',
    description: 'Daftar domain sempurna Anda dengan manajemen DNS gratis dan sertifikat SSL.',
    icon: 'database',
    enabled: true,
    plans: [],
  },
  {
    id: 'service-4',
    name: 'Solusi Backup',
    slug: 'backup',
    description: 'Backup otomatis harian dengan restore satu klik untuk menjaga data Anda tetap aman.',
    icon: 'hard-drive',
    enabled: true,
    plans: [
      {
        id: 'plan-4',
        name: 'Basic',
        price: 99000,
        period: '/bulan',
        specs: { cpu: '-', ram: '-', storage: '10GB', bandwidth: '-' },
        features: ['Daily Backup', '30 Days Retention', 'Restore One-Click'],
        popular: false,
        color: 'violet',
      },
    ],
  },
  {
    id: 'service-5',
    name: 'Pengembangan Aplikasi',
    slug: 'app-development',
    description: 'Pengembangan aplikasi web dan mobile dengan teknologi modern dan responsif.',
    icon: 'code',
    enabled: true,
    plans: [],
  },
  {
    id: 'service-6',
    name: 'Konsultasi IT',
    slug: 'it-consulting',
    description: 'Konsultasi teknologi IT untuk transformasi digital dan optimalisasi bisnis Anda.',
    icon: 'message-square',
    enabled: true,
    plans: [],
  },
];

export const defaultSiteBootstrap: SiteBootstrap = {
  settings: defaultSiteSettings,
  heroContent: defaultHeroContent,
  features: defaultFeatures,
  testimonials: defaultTestimonials,
  services: defaultServices,
};

function cloneData<T>(value: T): T {
  return JSON.parse(JSON.stringify(value)) as T;
}

function authHeaders(token?: string): HeadersInit {
  return {
    Accept: 'application/json',
    'Content-Type': 'application/json',
    ...(token ? { Authorization: `Bearer ${token}` } : {}),
  };
}

async function parseResponse<T>(response: Response): Promise<T> {
  const payload = (await response.json().catch(() => null)) as { message?: string } | null;

  if (!response.ok) {
    throw new Error(payload?.message || 'Request failed.');
  }

  return payload as T;
}

export async function fetchSiteBootstrapFromApi(): Promise<SiteBootstrap> {
  const payload = await parseResponse<SiteBootstrap>(
    await fetch(`${API_BASE_URL}/site/bootstrap`, {
      headers: authHeaders(),
    }),
  );

  return {
    settings: payload.settings || cloneData(defaultSiteSettings),
    heroContent: payload.heroContent || cloneData(defaultHeroContent),
    features: payload.features?.length ? payload.features : cloneData(defaultFeatures),
    testimonials: payload.testimonials?.length ? payload.testimonials : cloneData(defaultTestimonials),
    services: payload.services?.length ? payload.services : cloneData(defaultServices),
  };
}

export async function fetchSiteSettingsFromApi(): Promise<SiteSettings> {
  const payload = await parseResponse<{ settings: SiteSettings }>(
    await fetch(`${API_BASE_URL}/site/settings`, {
      headers: authHeaders(),
    }),
  );

  return payload.settings || cloneData(defaultSiteSettings);
}

export async function fetchAdminContent(token: string) {
  return parseResponse<{ heroContent: HeroContent; features: FeatureItem[]; testimonials: TestimonialItem[] }>(
    await fetch(`${API_BASE_URL}/admin/content`, {
      headers: authHeaders(token),
    }),
  );
}

export async function updateAdminContent(
  token: string,
  payload: { heroContent: HeroContent; features: FeatureItem[]; testimonials: TestimonialItem[] },
) {
  return parseResponse<{ heroContent: HeroContent; features: FeatureItem[]; testimonials: TestimonialItem[] }>(
    await fetch(`${API_BASE_URL}/admin/content`, {
      method: 'PUT',
      headers: authHeaders(token),
      body: JSON.stringify(payload),
    }),
  );
}

export async function fetchAdminServices(token: string) {
  return parseResponse<{ services: SiteService[] }>(
    await fetch(`${API_BASE_URL}/admin/services`, {
      headers: authHeaders(token),
    }),
  );
}

export async function updateAdminServices(token: string, payload: { services: SiteService[] }) {
  return parseResponse<{ services: SiteService[] }>(
    await fetch(`${API_BASE_URL}/admin/services`, {
      method: 'PUT',
      headers: authHeaders(token),
      body: JSON.stringify(payload),
    }),
  );
}

export async function fetchAdminSettings(token: string) {
  return parseResponse<{ settings: SiteSettings }>(
    await fetch(`${API_BASE_URL}/admin/settings`, {
      headers: authHeaders(token),
    }),
  );
}

export async function updateAdminSettings(token: string, payload: { settings: SiteSettings }) {
  return parseResponse<{ settings: SiteSettings }>(
    await fetch(`${API_BASE_URL}/admin/settings`, {
      method: 'PUT',
      headers: authHeaders(token),
      body: JSON.stringify(payload.settings),
    }),
  );
}
