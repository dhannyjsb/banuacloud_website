import {
  ArrowRight,
  BarChart3,
  Briefcase,
  Clock,
  Cloud,
  Code,
  Cpu,
  Database,
  FileCode,
  Globe,
  Globe2,
  GraduationCap,
  HardDrive,
  Headphones,
  Lock,
  MessageSquare,
  Palette,
  RefreshCw,
  Search,
  Server,
  Shield,
  Smartphone,
  Star,
  Terminal,
  TrendingUp,
  Users,
  Zap,
} from 'lucide-vue-next';

const marketingIconMap = {
  ArrowRight,
  BarChart3,
  Briefcase,
  Clock,
  Cloud,
  Code,
  Cpu,
  Database,
  FileCode,
  Globe,
  Globe2,
  GraduationCap,
  HardDrive,
  Headphones,
  Lock,
  MessageSquare,
  Palette,
  RefreshCw,
  Search,
  Server,
  Shield,
  Smartphone,
  Star,
  Terminal,
  TrendingUp,
  Users,
  Zap,
  server: Server,
  globe: Globe,
  database: Database,
  'hard-drive': HardDrive,
  code: Code,
  'message-square': MessageSquare,
  shield: Shield,
  zap: Zap,
  cpu: Cpu,
  search: Search,
  star: Star,
  clock: Clock,
  cloud: Cloud,
};

export function resolveMarketingIcon(icon: string) {
  return marketingIconMap[icon as keyof typeof marketingIconMap] || Star;
}

export function resolveFeatureIcon(icon: string) {
  return resolveMarketingIcon(icon);
}

export function resolveServiceIcon(icon: string) {
  return resolveMarketingIcon(icon) || Server;
}
