@extends('catalogue::layouts.marketplace', [
    'title' => 'Marketplace Catalogue',
    'categories' => $categories ?? [],
    'breadcrumbs' => [
        ['name' => 'Home', 'url' => url('/')],
        ['name' => 'Marketplace', 'url' => '#']
    ]
])

@section('market-content')
    @include('catalogue::marketplace.content')
@endsection