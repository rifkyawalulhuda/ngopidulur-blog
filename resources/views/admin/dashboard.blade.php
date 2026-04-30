@extends('layouts.app')

@push('head')
    <meta name="robots" content="noindex,nofollow">
@endpush

@section('content')
    <div id="ngopi-dulur-admin-app" data-user-name="{{ auth()->user()->name }}"></div>
@endsection
