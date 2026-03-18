import {
  Code,
  Cpu,
  Database,
  Globe,
  HardDrive,
  Headphones,
  Lock,
  MessageSquare,
  Server,
  Shield,
  Star,
  Zap,
} from 'lucide-vue-next';

const featureIconMap = {
  Zap,
  Shield,
  Globe,
  Database,
  Lock,
  Cpu,
  Headphones,
  Star,
};

const serviceIconMap = {
  server: Server,
  globe: Globe,
  database: Database,
  'hard-drive': HardDrive,
  code: Code,
  'message-square': MessageSquare,
};

export function resolveFeatureIcon(icon: string) {
  return featureIconMap[icon as keyof typeof featureIconMap] || Star;
}

export function resolveServiceIcon(icon: string) {
  return serviceIconMap[icon as keyof typeof serviceIconMap] || Server;
}
