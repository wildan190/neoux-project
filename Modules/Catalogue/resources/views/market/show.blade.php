@extends('catalogue::layouts.marketplace', [
    'title' => $product->name ?? 'Product Detail',
    'showBackButton' => true,
    'breadcrumbs' => [
        ['name' => 'Home', 'url' => url('/')],
        ['name' => 'Marketplace', 'url' => route('market.index')],
        ['name' => $product->name ?? 'Detail', 'url' => '#']
    ]
])

@section('market-content')
    @include('catalogue::marketplace.show_content')
@endsection
