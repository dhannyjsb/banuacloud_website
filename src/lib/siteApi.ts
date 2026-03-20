export interface SiteSettings {
  maintenanceMode: boolean;
  siteName: string;
  siteDescription: string;
  logoUrl: string;
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

export interface ChangePasswordPayload {
  currentPassword: string;
  newPassword: string;
  newPasswordConfirmation: string;
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

export interface MarketingStat {
  icon: string;
  value: string;
  label: string;
}

export interface MarketingReason {
  icon: string;
  title: string;
  description: string;
}

export interface MarketingFaq {
  question: string;
  answer: string;
}

export interface LearnMoreServiceCard {
  slug: string;
  icon: string;
  title: string;
  subtitle: string;
  description: string;
  features: string[];
  gradient: string;
}

export interface LearnMorePageData {
  heroBadge: string;
  heroTitlePrefix: string;
  heroTitleHighlight: string;
  heroDescription: string;
  stats: MarketingStat[];
  serviceSectionBadge: string;
  serviceSectionTitle: string;
  serviceSectionDescription: string;
  services: LearnMoreServiceCard[];
  reasonsBadge: string;
  reasonsTitle: string;
  reasonsDescription: string;
  reasons: MarketingReason[];
  faqBadge: string;
  faqTitle: string;
  faqDescription: string;
  faqs: MarketingFaq[];
  ctaTitle: string;
  ctaDescription: string;
  ctaPrimary: string;
  ctaSecondary: string;
  ctaPrimaryTarget: string;
  ctaSecondaryTarget: string;
}

export interface AdminMarketingCtaConfig {
  pageKey: string;
  pageTitle: string;
  supportsHeroCtas: boolean;
  heroPrimaryTarget: string;
  heroSecondaryTarget: string;
  ctaPrimaryTarget: string;
  ctaSecondaryTarget: string;
}

export interface ServicePricingCard {
  name: string;
  price: string;
  period: string;
  specs: Record<string, string>;
  features: string[];
  popular: boolean;
  color: string;
}

export interface ServiceFeatureCard {
  icon: string;
  title: string;
  description: string;
  tags?: string[];
}

export interface ServiceExtraItem {
  label?: string;
  text?: string;
  title?: string;
  description?: string;
  step?: string;
  price?: string;
  suffix?: string;
  popular?: boolean;
}

export interface ServiceExtraSection {
  type: 'badge-grid' | 'timeline-grid' | 'checklist' | 'price-grid';
  title: string;
  description: string;
  items: ServiceExtraItem[];
}

export interface ServiceDetailPageData {
  slug: string;
  name: string;
  accent: string;
  icon: string;
  breadcrumbLabel: string;
  heroBadge: string;
  heroTitlePrefix: string;
  heroTitleHighlight: string;
  heroDescription: string;
  heroPrimaryCta: string;
  heroSecondaryCta: string;
  heroPrimaryTarget: string;
  heroSecondaryTarget: string;
  featureSectionTitle: string;
  featureSectionDescription: string;
  features: ServiceFeatureCard[];
  pricingTitle?: string;
  pricingDescription?: string;
  pricingCards?: ServicePricingCard[];
  extraSection?: ServiceExtraSection;
  ctaTitle: string;
  ctaDescription: string;
  ctaPrimary: string;
  ctaSecondary: string;
  ctaPrimaryTarget: string;
  ctaSecondaryTarget: string;
}

export interface AdminContentPayload {
  heroContent: HeroContent;
  features: FeatureItem[];
  testimonials: TestimonialItem[];
  marketingCtas: AdminMarketingCtaConfig[];
}

export interface ContactMessagePayload {
  name: string;
  email: string;
  whatsapp: string;
  company?: string;
  message: string;
}

export interface InboxMessage {
  id: string;
  name: string;
  email: string;
  whatsapp: string;
  company?: string | null;
  message: string;
  isRead: boolean;
  submittedAt?: string | null;
  readAt?: string | null;
}

export interface AdminInboxPayload {
  stats: {
    total: number;
    unread: number;
  };
  messages: InboxMessage[];
}

const API_BASE_URL = (import.meta.env.VITE_API_BASE_URL || '/api').replace(/\/+$/, '');

export const defaultSiteSettings: SiteSettings = {
  maintenanceMode: import.meta.env.VITE_MAINTENANCE_MODE === 'true',
  siteName: import.meta.env.VITE_COMPANY_NAME || 'Banua Cloud Nusantara',
  siteDescription: import.meta.env.VITE_COMPANY_DESCRIPTOR || 'Mitra infrastruktur IT, cloud, dan jaringan untuk bisnis di Indonesia',
  logoUrl: import.meta.env.VITE_LOGO_URL || '',
  companyName: import.meta.env.VITE_COMPANY_FULL_NAME || import.meta.env.VITE_COMPANY_NAME || 'Banua Cloud Nusantara',
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
  title: 'Mitra Infrastruktur IT untuk Bisnis Modern',
  subtitle: 'Banua Cloud Nusantara membantu perusahaan merancang, membangun, dan mengelola cloud, jaringan kantor dan gedung, backup, serta pengembangan aplikasi dengan pendekatan yang rapi dan terukur.',
  ctaPrimary: 'Pelajari Profil Kami',
  ctaSecondary: 'Lihat Layanan',
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
    content: 'Banua Cloud Nusantara telah mengubah cara kami mendeploy aplikasi. Kecepatan dan keandalan tidak tertandingi, dan tim support mereka selalu siap membantu kapan saja.',
  },
  {
    id: 'testimonial-2',
    name: 'Siti Nurhaliza',
    role: 'Founder',
    company: 'DigitalAgency',
    content: 'Kami telah mencoba banyak penyedia cloud, tapi Banua Cloud Nusantara menonjol dengan uptime luar biasa dan harga kompetitif. Sangat direkomendasikan untuk bisnis di Indonesia.',
  },
  {
    id: 'testimonial-3',
    name: 'Budi Santoso',
    role: 'IT Manager',
    company: 'EcomStore',
    content: 'Migrasi ke Banua Cloud Nusantara berjalan mulus. Tim mereka menangani semuanya dengan profesional, dan performa website kami meningkat signifikan.',
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
        features: ['Panel', 'SSL Gratis', 'Backup Mingguan'],
        popular: false,
        color: 'sky',
      },
      {
        id: 'plan-2',
        name: 'Professional',
        price: 299000,
        period: '/bulan',
        specs: { cpu: '2 Core', ram: '2GB', storage: '50GB SSD', bandwidth: '2TB' },
        features: ['Panel', 'SSL Gratis', 'Backup Harian', 'Priority Support'],
        popular: true,
        color: 'sky',
      },
    ],
  },
  {
    id: 'service-2',
    name: 'Web Hosting',
    slug: 'web-hosting',
    description: 'Shared hosting cepat dan andal dengan Panel dan penyimpanan SSD untuk website Anda.',
    icon: 'globe',
    enabled: true,
    plans: [
      {
        id: 'plan-3',
        name: 'Starter',
        price: 29000,
        period: '/bulan',
        specs: { cpu: 'Shared', ram: 'Unlimited', storage: '5GB SSD', bandwidth: 'Unlimited' },
        features: ['Panel', 'SSL Gratis', 'Email Unlimited'],
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

function redirectToAdminLoginForUnauthorized() {
  if (typeof window === 'undefined') {
    return;
  }

  const { pathname, search } = window.location;

  if (!pathname.startsWith('/admin') || pathname.startsWith('/admin/login')) {
    return;
  }

  localStorage.removeItem('admin_token');
  localStorage.removeItem('admin_user');

  const redirectTarget = `${pathname}${search}`;
  window.location.replace(`/admin/login?redirect=${encodeURIComponent(redirectTarget)}`);
}

async function parseResponse<T>(response: Response): Promise<T> {
  const payload = (await response.json().catch(() => null)) as { message?: string } | null;

  if (!response.ok) {
    if (response.status === 401) {
      redirectToAdminLoginForUnauthorized();
    }

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

export async function fetchLearnMorePageFromApi(): Promise<LearnMorePageData> {
  return parseResponse<LearnMorePageData>(
    await fetch(`${API_BASE_URL}/site/learn-more`, {
      headers: authHeaders(),
    }),
  );
}

export async function fetchServiceDetailPageFromApi(slug: string): Promise<ServiceDetailPageData> {
  return parseResponse<ServiceDetailPageData>(
    await fetch(`${API_BASE_URL}/site/services/${slug}`, {
      headers: authHeaders(),
    }),
  );
}

export async function submitContactMessage(payload: ContactMessagePayload) {
  return parseResponse<{ message: string }>(
    await fetch(`${API_BASE_URL}/site/contact-messages`, {
      method: 'POST',
      headers: authHeaders(),
      body: JSON.stringify(payload),
    }),
  );
}

export async function fetchAdminContent(token: string) {
  return parseResponse<AdminContentPayload>(
    await fetch(`${API_BASE_URL}/admin/content`, {
      headers: authHeaders(token),
    }),
  );
}

export async function updateAdminContent(
  token: string,
  payload: AdminContentPayload,
) {
  return parseResponse<AdminContentPayload>(
    await fetch(`${API_BASE_URL}/admin/content`, {
      method: 'PUT',
      headers: authHeaders(token),
      body: JSON.stringify(payload),
    }),
  );
}

export async function fetchAdminInbox(token: string) {
  return parseResponse<AdminInboxPayload>(
    await fetch(`${API_BASE_URL}/admin/inbox`, {
      headers: authHeaders(token),
    }),
  );
}

export async function markAdminInboxMessageRead(token: string, messageId: string) {
  return parseResponse<{ message: InboxMessage }>(
    await fetch(`${API_BASE_URL}/admin/inbox/${messageId}/read`, {
      method: 'PATCH',
      headers: authHeaders(token),
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

export async function updateAdminLogo(token: string, logo: File) {
  const formData = new FormData();
  formData.append('logo', logo);

  return parseResponse<{ settings: SiteSettings }>(
    await fetch(`${API_BASE_URL}/admin/settings/logo`, {
      method: 'POST',
      headers: {
        Accept: 'application/json',
        Authorization: `Bearer ${token}`,
      },
      body: formData,
    }),
  );
}

export async function updateAdminPassword(token: string, payload: ChangePasswordPayload) {
  return parseResponse<{ message: string }>(
    await fetch(`${API_BASE_URL}/auth/change-password`, {
      method: 'PUT',
      headers: authHeaders(token),
      body: JSON.stringify({
        currentPassword: payload.currentPassword,
        newPassword: payload.newPassword,
        newPassword_confirmation: payload.newPasswordConfirmation,
      }),
    }),
  );
}
