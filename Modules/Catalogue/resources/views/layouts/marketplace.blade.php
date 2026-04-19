@php
    $isBuyer = auth()->check() && session('procurement_mode', 'buyer') === 'buyer';
    $customLayout = $isBuyer ? 'layouts.app' : 'layouts.guest';
    $title = $title ?? 'Marketplace';
@endphp

@extends($customLayout, [
    'title' => $title,
    'hide_sidebar' => false,
    'hide_header' => false,
    'breadcrumbs' => $breadcrumbs ?? [
        ['name' => 'Home', 'url' => url('/')],
        ['name' => 'Marketplace', 'url' => '#']
    ]
])

@section('content')
    @if($isBuyer)
        <div class="max-w-[1440px] w-full mx-auto px-4 md:px-6 py-4 md:py-8">
            @yield('market-content')
        </div>
    @else
        <div class="flex flex-col">
            @include('layouts.partials.guest-navbar', ['showBackButton' => $showBackButton ?? false])

            <div class="flex-1 pt-8 md:pt-12 pb-12">
                <div class="max-w-7xl w-full mx-auto px-4 md:px-6">
                    @yield('market-content')
                </div>
            </div>

            @include('layouts.partials.guest-footer')
        </div>
    @endif
@endsection
