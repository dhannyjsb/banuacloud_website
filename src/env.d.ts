/// <reference types="vite/client" />

interface ImportMetaEnv {
  readonly VITE_MAINTENANCE_MODE: string;
  readonly VITE_COMPANY_NAME: string;
  readonly VITE_COMPANY_TAGLINE: string;
  readonly VITE_COMPANY_FULL_NAME: string;
  readonly VITE_COMPANY_DESCRIPTOR: string;
  readonly VITE_SUPPORT_EMAIL: string;
  readonly VITE_SUPPORT_PHONE: string;
  readonly VITE_SUPPORT_PHONE_RAW: string;
  readonly VITE_SUPPORT_WHATSAPP_RAW: string;
  readonly VITE_LOGO_URL: string;
  readonly VITE_SOCIAL_INSTAGRAM: string;
  readonly VITE_SOCIAL_LINKEDIN: string;
  readonly VITE_SOCIAL_TWITTER: string;
}

interface ImportMeta {
  readonly env: ImportMetaEnv;
}

declare module '*.vue' {
  import type { DefineComponent } from 'vue';
  const component: DefineComponent<Record<string, never>, Record<string, never>, unknown>;
  export default component;
}