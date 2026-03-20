<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TrackVisitorVisitRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'visitorToken' => ['required', 'string', 'max:80'],
            'path' => ['required', 'string', 'max:500'],
            'routeName' => ['nullable', 'string', 'max:120'],
            'pageTitle' => ['nullable', 'string', 'max:255'],
            'referrerUrl' => ['nullable', 'url', 'max:2000'],
            'utmSource' => ['nullable', 'string', 'max:120'],
            'utmMedium' => ['nullable', 'string', 'max:120'],
            'utmCampaign' => ['nullable', 'string', 'max:120'],
        ];
    }

    public function messages(): array
    {
        return [
            'visitorToken.required' => 'Token pengunjung wajib dikirim.',
            'path.required' => 'Path halaman wajib dikirim.',
            'referrerUrl.url' => 'Format referrer tidak valid.',
        ];
    }
}
