<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\TrackVisitorVisitRequest;
use App\Models\VisitorVisit;
use App\Support\TrafficSourceResolver;
use App\Support\VisitorLocationResolver;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class VisitorVisitController extends Controller
{
    public function __construct(
        private VisitorLocationResolver $visitorLocationResolver,
        private TrafficSourceResolver $trafficSourceResolver,
    ) {}

    public function store(TrackVisitorVisitRequest $request): JsonResponse
    {
        $data = $request->validated();
        $path = '/'.ltrim((string) $data['path'], '/');
        $clientIp = $this->resolveClientIp($request);

        if (str_starts_with($path, '/admin')) {
            return response()->json([
                'tracked' => false,
            ], 202);
        }

        $source = $this->trafficSourceResolver->resolve(
            $data['referrerUrl'] ?? null,
            $data['utmSource'] ?? null,
            $data['utmMedium'] ?? null,
            $request->getHost(),
        );

        $location = $this->visitorLocationResolver->resolve($clientIp);

        $visit = VisitorVisit::query()->create([
            'visitor_token' => $data['visitorToken'],
            'path' => $path,
            'route_name' => $data['routeName'] ?? null,
            'page_title' => $data['pageTitle'] ?? null,
            'referrer_url' => $data['referrerUrl'] ?? null,
            'referrer_host' => $source['referrerHost'],
            'source' => $source['source'],
            'medium' => $source['medium'],
            'utm_campaign' => $data['utmCampaign'] ?? null,
            'ip_address' => $clientIp,
            'user_agent' => $request->userAgent(),
            'country_code' => $location['countryCode'],
            'country_name' => $location['countryName'],
            'city_name' => $location['cityName'],
            'isp_name' => $location['ispName'],
            'organization_name' => $location['organizationName'],
            'autonomous_system_number' => $location['autonomousSystemNumber'],
            'autonomous_system_organization' => $location['autonomousSystemOrganization'],
            'visited_at' => now(),
        ]);

        return response()->json([
            'tracked' => true,
            'visitId' => (string) $visit->id,
        ], 201);
    }

    private function resolveClientIp(Request $request): ?string
    {
        foreach (['CF-Connecting-IP', 'True-Client-IP'] as $header) {
            $resolvedIp = $this->validIp($request->header($header));

            if ($resolvedIp !== null) {
                return $resolvedIp;
            }
        }

        $forwardedFor = $request->header('X-Forwarded-For');

        if (is_string($forwardedFor) && trim($forwardedFor) !== '') {
            foreach (explode(',', $forwardedFor) as $candidate) {
                $resolvedIp = $this->validIp($candidate);

                if ($resolvedIp !== null) {
                    return $resolvedIp;
                }
            }
        }

        return $this->validIp($request->ip());
    }

    private function validIp(string|array|null $value): ?string
    {
        if (! is_string($value) || trim($value) === '') {
            return null;
        }

        $candidate = trim($value);

        return filter_var($candidate, FILTER_VALIDATE_IP) !== false ? $candidate : null;
    }
}
