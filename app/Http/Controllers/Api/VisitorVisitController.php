<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\TrackVisitorVisitRequest;
use App\Models\VisitorVisit;
use App\Support\TrafficSourceResolver;
use App\Support\VisitorLocationResolver;
use Illuminate\Http\JsonResponse;

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

        $location = $this->visitorLocationResolver->resolve($request->ip());

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
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'country_code' => $location['countryCode'],
            'country_name' => $location['countryName'],
            'city_name' => $location['cityName'],
            'visited_at' => now(),
        ]);

        return response()->json([
            'tracked' => true,
            'visitId' => (string) $visit->id,
        ], 201);
    }
}
