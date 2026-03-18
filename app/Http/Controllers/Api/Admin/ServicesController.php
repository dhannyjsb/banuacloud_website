<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\ServicePlan;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ServicesController extends Controller
{
    public function show(): JsonResponse
    {
        return response()->json([
            'services' => $this->formattedServices(),
        ]);
    }

    public function update(Request $request): JsonResponse
    {
        $data = $request->validate([
            'services' => ['required', 'array'],
            'services.*.name' => ['required', 'string', 'max:255'],
            'services.*.slug' => ['required', 'string', 'max:255'],
            'services.*.description' => ['required', 'string', 'max:500'],
            'services.*.icon' => ['required', 'string', 'max:100'],
            'services.*.enabled' => ['required', 'boolean'],
            'services.*.plans' => ['present', 'array'],
            'services.*.plans.*.name' => ['required', 'string', 'max:255'],
            'services.*.plans.*.price' => ['required', 'integer', 'min:0'],
            'services.*.plans.*.period' => ['required', 'string', 'max:50'],
            'services.*.plans.*.specs' => ['present', 'array'],
            'services.*.plans.*.features' => ['present', 'array'],
            'services.*.plans.*.popular' => ['required', 'boolean'],
            'services.*.plans.*.color' => ['required', 'string', 'max:50'],
        ]);

        DB::transaction(function () use ($data): void {
            ServicePlan::query()->delete();
            Service::query()->delete();

            foreach ($data['services'] as $serviceIndex => $service) {
                $serviceRecord = Service::query()->create([
                    'name' => $service['name'],
                    'slug' => $service['slug'],
                    'description' => $service['description'],
                    'icon_key' => $service['icon'],
                    'enabled' => $service['enabled'],
                    'sort_order' => $serviceIndex,
                ]);

                foreach ($service['plans'] as $planIndex => $plan) {
                    $serviceRecord->plans()->create([
                        'name' => $plan['name'],
                        'price' => $plan['price'],
                        'period' => $plan['period'],
                        'specs' => $plan['specs'],
                        'features' => $plan['features'],
                        'popular' => $plan['popular'],
                        'color' => $plan['color'],
                        'sort_order' => $planIndex,
                    ]);
                }
            }
        });

        return response()->json([
            'services' => $this->formattedServices(),
        ]);
    }

    private function formattedServices(): array
    {
        return Service::query()
            ->with('plans')
            ->orderBy('sort_order')
            ->get()
            ->map(fn(Service $service): array => [
                'id' => (string) $service->id,
                'name' => $service->name,
                'slug' => $service->slug,
                'description' => $service->description,
                'icon' => $service->icon_key,
                'enabled' => $service->enabled,
                'plans' => $service->plans->map(fn(ServicePlan $plan): array => [
                    'id' => (string) $plan->id,
                    'name' => $plan->name,
                    'price' => $plan->price,
                    'period' => $plan->period,
                    'specs' => $plan->specs,
                    'features' => $plan->features,
                    'popular' => $plan->popular,
                    'color' => $plan->color,
                ])->all(),
            ])
            ->all();
    }
}
