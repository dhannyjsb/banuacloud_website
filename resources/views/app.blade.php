<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ $seo['title'] ?? config('app.name', 'Banua Cloud Nusantara') }}</title>
        <meta name="description" content="{{ $seo['description'] ?? 'Mitra infrastruktur IT, cloud, dan jaringan untuk bisnis di Indonesia' }}">
        <meta name="robots" content="{{ $seo['robots'] ?? 'index, follow' }}">
        <meta name="author" content="{{ $seo['siteName'] ?? config('app.name', 'Banua Cloud Nusantara') }}">
        <meta property="og:locale" content="id_ID">
        <meta property="og:type" content="{{ $seo['type'] ?? 'website' }}">
        <meta property="og:site_name" content="{{ $seo['siteName'] ?? config('app.name', 'Banua Cloud Nusantara') }}">
        <meta property="og:title" content="{{ $seo['title'] ?? config('app.name', 'Banua Cloud Nusantara') }}">
        <meta property="og:description" content="{{ $seo['description'] ?? 'Mitra infrastruktur IT, cloud, dan jaringan untuk bisnis di Indonesia' }}">
        <meta property="og:url" content="{{ $seo['canonical'] ?? config('app.url') }}">
        <meta property="og:image" content="{{ $seo['image'] ?? url('/favicon.svg') }}">
        <meta name="twitter:card" content="summary_large_image">
        <meta name="twitter:title" content="{{ $seo['title'] ?? config('app.name', 'Banua Cloud Nusantara') }}">
        <meta name="twitter:description" content="{{ $seo['description'] ?? 'Mitra infrastruktur IT, cloud, dan jaringan untuk bisnis di Indonesia' }}">
        <meta name="twitter:image" content="{{ $seo['image'] ?? url('/favicon.svg') }}">
        <link rel="canonical" href="{{ $seo['canonical'] ?? config('app.url') }}">
        <link rel="sitemap" type="application/xml" title="Sitemap" href="{{ route('sitemap') }}">
        <link rel="icon" type="image/svg+xml" href="/favicon.svg">
        <script>
            window.__INITIAL_SEO__ = @json($seo ?? []);
        </script>
        @if (! empty($seo['jsonLd']))
            @foreach ($seo['jsonLd'] as $schema)
                <script type="application/ld+json">@json($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)</script>
            @endforeach
        @endif
        @vite('src/main.ts')
    </head>
    <body>
        <div id="root"></div>
    </body>
</html>
