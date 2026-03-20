<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\CaseStudy;
use App\Models\ContactMessage;
use App\Models\FaqItem;
use App\Models\Service;
use App\Models\SiteSetting;
use App\Models\Testimonial;
use App\Models\VisitorVisit;
use App\Support\VisitorLocationResolver;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    public function __construct(private VisitorLocationResolver $visitorLocationResolver) {}

    public function show(Request $request): JsonResponse
    {
        $messages = ContactMessage::query()
            ->orderByDesc('created_at')
            ->get();

        $trafficRange = $this->resolveTrafficRange($request);
        $trafficQuery = VisitorVisit::query()
            ->whereBetween('visited_at', [$trafficRange['start'], $trafficRange['end']]);

        $todayTrafficQuery = VisitorVisit::query()
            ->where('visited_at', '>=', now()->startOfDay());

        $workflow = collect(ContactMessage::WORKFLOW_STATUSES)
            ->map(fn(string $status): array => [
                'key' => $status,
                'count' => $messages->where('status', $status)->count(),
            ])
            ->values()
            ->all();

        $categories = collect(ContactMessage::CATEGORY_OPTIONS)
            ->map(fn(string $category): array => [
                'key' => $category,
                'count' => $messages->where('category', $category)->count(),
            ])
            ->values()
            ->all();

        $settings = SiteSetting::query()->first();
        $todayVisitors = (clone $todayTrafficQuery)
            ->select('visitor_token')
            ->distinct()
            ->count('visitor_token');
        $todayPageViews = (clone $todayTrafficQuery)->count();

        return response()->json([
            'stats' => [
                'totalMessages' => $messages->count(),
                'unreadMessages' => $messages->where('is_read', false)->count(),
                'followUpMessages' => $messages->whereIn('status', ['new', 'in_progress'])->count(),
                'activeServices' => Service::query()->where('enabled', true)->count(),
                'testimonials' => Testimonial::query()->count(),
                'faqs' => FaqItem::query()->count(),
                'caseStudies' => CaseStudy::query()->count(),
                'maintenanceMode' => (bool) $settings?->maintenance_mode,
                'todayVisitors' => $todayVisitors,
                'todayPageViews' => $todayPageViews,
            ],
            'traffic' => [
                'period' => $trafficRange['period'],
                'rangeStart' => $trafficRange['start']->toDateString(),
                'rangeEnd' => $trafficRange['end']->toDateString(),
                'rangeLabel' => $trafficRange['rangeLabel'],
                'summaryLabel' => $trafficRange['summaryLabel'],
                'rangeDescription' => $trafficRange['rangeDescription'],
                'todayVisitors' => (clone $trafficQuery)
                    ->select('visitor_token')
                    ->distinct()
                    ->count('visitor_token'),
                'todayPageViews' => (clone $trafficQuery)->count(),
                'geolocationEnabled' => $this->visitorLocationResolver->isEnabled(),
                'geolocationMode' => $this->visitorLocationResolver->lookupMode(),
                'topSources' => $this->topTrafficItems($trafficQuery, 'source', 'Direct'),
                'topBrowsers' => $this->topBrowsers($trafficQuery),
                'topPages' => $this->topTrafficItems($trafficQuery, 'path', '/'),
                'topCountries' => $this->topTrafficItems($trafficQuery, 'country_name', 'Tidak diketahui'),
                'topCities' => $this->topCities($trafficQuery),
                'dailyTrend' => $this->dailyTrend($trafficRange['start'], $trafficRange['end']),
                'mostVisitedIps' => $this->mostVisitedIps($trafficQuery),
                'recentVisits' => (clone $trafficQuery)
                    ->latest('visited_at')
                    ->limit(8)
                    ->get()
                    ->map(fn(VisitorVisit $visit): array => [
                        'id' => (string) $visit->id,
                        'visitorToken' => $visit->visitor_token,
                        'path' => $visit->path,
                        'routeName' => $visit->route_name,
                        'pageTitle' => $visit->page_title,
                        'referrerUrl' => $visit->referrer_url,
                        'referrerHost' => $visit->referrer_host,
                        'source' => $visit->source ?: 'Direct',
                        'medium' => $visit->medium,
                        'utmCampaign' => $visit->utm_campaign,
                        'ipAddress' => $visit->ip_address,
                        'browser' => $this->detectBrowser($visit->user_agent),
                        'userAgent' => $visit->user_agent,
                        'countryCode' => $visit->country_code,
                        'countryName' => $visit->country_name,
                        'cityName' => $visit->city_name,
                        'location' => collect([$visit->city_name, $visit->country_name])
                            ->filter()
                            ->implode(', ') ?: 'Tidak diketahui',
                        'visitedAt' => $visit->visited_at?->toIso8601String(),
                    ])
                    ->values()
                    ->all(),
            ],
            'workflow' => $workflow,
            'categories' => $categories,
            'recentMessages' => $messages
                ->take(6)
                ->map(fn(ContactMessage $message): array => [
                    'id' => (string) $message->id,
                    'name' => $message->name,
                    'company' => $message->company,
                    'category' => $message->category,
                    'status' => $message->status ?: 'new',
                    'isRead' => $message->is_read,
                    'submittedAt' => $message->created_at?->toIso8601String(),
                ])
                ->values()
                ->all(),
            'recentAuditLogs' => AuditLog::query()
                ->latest()
                ->limit(8)
                ->get()
                ->map(fn(AuditLog $log): array => [
                    'id' => (string) $log->id,
                    'actorName' => $log->actor_name,
                    'actorEmail' => $log->actor_email,
                    'action' => $log->action,
                    'target' => $log->target,
                    'summary' => $log->summary,
                    'createdAt' => $log->created_at?->toIso8601String(),
                ])
                ->values()
                ->all(),
        ]);
    }

    private function topTrafficItems(Builder $query, string $column, string $fallbackLabel, int $limit = 5): array
    {
        return (clone $query)
            ->selectRaw($column . ' as label, COUNT(*) as aggregate')
            ->groupBy($column)
            ->orderByDesc('aggregate')
            ->limit($limit)
            ->get()
            ->map(fn(object $row): array => [
                'label' => filled($row->label) ? (string) $row->label : $fallbackLabel,
                'count' => (int) $row->aggregate,
            ])
            ->values()
            ->all();
    }

    private function topCities(Builder $query, int $limit = 5): array
    {
        return (clone $query)
            ->whereNotNull('city_name')
            ->selectRaw('city_name, country_name, COUNT(*) as aggregate')
            ->groupBy('city_name', 'country_name')
            ->orderByDesc('aggregate')
            ->limit($limit)
            ->get()
            ->map(fn(object $row): array => [
                'label' => collect([$row->city_name, $row->country_name])->filter()->implode(', '),
                'count' => (int) $row->aggregate,
            ])
            ->values()
            ->all();
    }

    private function dailyTrend(Carbon $start, Carbon $end): array
    {
        $trendRows = VisitorVisit::query()
            ->whereBetween('visited_at', [$start, $end])
            ->selectRaw('DATE(visited_at) as visit_date, COUNT(*) as page_views, COUNT(DISTINCT visitor_token) as visitors')
            ->groupBy('visit_date')
            ->orderBy('visit_date')
            ->get()
            ->keyBy('visit_date');

        $dayCount = $start->diffInDays($end);
        $trend = [];

        for ($offset = 0; $offset <= $dayCount; $offset++) {
            $date = $start->copy()->addDays($offset);
            $key = $date->toDateString();
            $row = $trendRows->get($key);

            $trend[] = [
                'date' => $key,
                'label' => $date->translatedFormat('d M'),
                'pageViews' => (int) ($row->page_views ?? 0),
                'visitors' => (int) ($row->visitors ?? 0),
            ];
        }

        return $trend;
    }

    private function mostVisitedIps(Builder $query, int $limit = 10): array
    {
        $rows = (clone $query)
            ->whereNotNull('ip_address')
            ->where('ip_address', '!=', '')
            ->selectRaw('ip_address, COUNT(*) as aggregate, COUNT(DISTINCT visitor_token) as unique_visitors, MIN(visited_at) as first_visited_at, MAX(visited_at) as last_visited_at')
            ->groupBy('ip_address')
            ->orderByDesc('aggregate')
            ->limit($limit)
            ->get();

        $latestVisitsByIp = (clone $query)
            ->whereIn('ip_address', $rows->pluck('ip_address')->all())
            ->orderByDesc('visited_at')
            ->get()
            ->unique('ip_address')
            ->keyBy('ip_address');

        return $rows
            ->map(function (object $row) use ($latestVisitsByIp): array {
                /** @var VisitorVisit|null $latestVisit */
                $latestVisit = $latestVisitsByIp->get($row->ip_address);

                return [
                    'ipAddress' => (string) $row->ip_address,
                    'totalVisits' => (int) $row->aggregate,
                    'uniqueVisitors' => (int) $row->unique_visitors,
                    'browser' => $this->detectBrowser($latestVisit?->user_agent),
                    'countryName' => $latestVisit?->country_name,
                    'cityName' => $latestVisit?->city_name,
                    'firstVisitedAt' => $row->first_visited_at ? Carbon::parse($row->first_visited_at)->toIso8601String() : null,
                    'lastVisitedAt' => $latestVisit?->visited_at?->toIso8601String(),
                ];
            })
            ->values()
            ->all();
    }

    private function topBrowsers(Builder $query, int $limit = 5): array
    {
        $browserCounts = (clone $query)
            ->selectRaw('user_agent, COUNT(*) as aggregate')
            ->groupBy('user_agent')
            ->orderByDesc('aggregate')
            ->get()
            ->reduce(function (array $carry, object $row): array {
                $label = $this->detectBrowser($row->user_agent ?? null);

                if (! array_key_exists($label, $carry)) {
                    $carry[$label] = 0;
                }

                $carry[$label] += (int) $row->aggregate;

                return $carry;
            }, []);

        return collect($browserCounts)
            ->map(fn(int $count, string $label): array => [
                'label' => $label,
                'count' => $count,
            ])
            ->sortByDesc('count')
            ->take($limit)
            ->values()
            ->all();
    }

    private function resolveTrafficRange(Request $request): array
    {
        $period = $request->string('period')->toString();
        $now = now();

        return match ($period) {
            '7d' => [
                'period' => '7d',
                'start' => $now->copy()->subDays(6)->startOfDay(),
                'end' => $now->copy()->endOfDay(),
                'rangeLabel' => '7 Hari Terakhir',
                'summaryLabel' => '7 Hari',
                'rangeDescription' => '7 hari terakhir',
            ],
            '30d' => [
                'period' => '30d',
                'start' => $now->copy()->subDays(29)->startOfDay(),
                'end' => $now->copy()->endOfDay(),
                'rangeLabel' => '30 Hari Terakhir',
                'summaryLabel' => '30 Hari',
                'rangeDescription' => '30 hari terakhir',
            ],
            'custom' => $this->resolveCustomTrafficRange($request, $now),
            default => [
                'period' => 'today',
                'start' => $now->copy()->startOfDay(),
                'end' => $now->copy()->endOfDay(),
                'rangeLabel' => 'Hari Ini',
                'summaryLabel' => 'Hari Ini',
                'rangeDescription' => 'hari ini',
            ],
        };
    }

    private function resolveCustomTrafficRange(Request $request, Carbon $fallbackDate): array
    {
        $start = $this->parseDateInput($request->string('startDate')->toString()) ?? $fallbackDate->copy()->startOfDay();
        $end = $this->parseDateInput($request->string('endDate')->toString()) ?? $start->copy();

        if ($start->gt($end)) {
            [$start, $end] = [$end, $start];
        }

        if ($start->diffInDays($end) > 30) {
            $end = $start->copy()->addDays(30);
        }

        return [
            'period' => 'custom',
            'start' => $start->copy()->startOfDay(),
            'end' => $end->copy()->endOfDay(),
            'rangeLabel' => sprintf('%s - %s', $start->translatedFormat('d M Y'), $end->translatedFormat('d M Y')),
            'summaryLabel' => 'Custom',
            'rangeDescription' => 'rentang tanggal pilihan',
        ];
    }

    private function parseDateInput(?string $value): ?Carbon
    {
        if (blank($value)) {
            return null;
        }

        try {
            return Carbon::createFromFormat('Y-m-d', (string) $value);
        } catch (\Throwable) {
            return null;
        }
    }

    private function detectBrowser(?string $userAgent): string
    {
        if (blank($userAgent)) {
            return 'Tidak diketahui';
        }

        $agent = strtolower($userAgent);

        return match (true) {
            str_contains($agent, 'edg/') => 'Microsoft Edge',
            str_contains($agent, 'opr/'), str_contains($agent, 'opera') => 'Opera',
            str_contains($agent, 'samsungbrowser/') => 'Samsung Internet',
            str_contains($agent, 'ucbrowser/') => 'UC Browser',
            str_contains($agent, 'chrome/') && ! str_contains($agent, 'edg/') && ! str_contains($agent, 'opr/') => 'Google Chrome',
            str_contains($agent, 'firefox/') => 'Mozilla Firefox',
            str_contains($agent, 'safari/') && ! str_contains($agent, 'chrome/') => 'Safari',
            str_contains($agent, 'trident/'), str_contains($agent, 'msie') => 'Internet Explorer',
            default => 'Browser lain',
        };
    }
}
