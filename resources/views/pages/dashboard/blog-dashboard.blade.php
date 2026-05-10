@extends('layouts.app')

@section('content')
    <div
        id="ngopi-dulur-blog-dashboard"
        data-endpoint="{{ route('admin.api.dashboard') }}"
        class="min-h-[70vh]">
        <div class="grid grid-cols-12 gap-4 md:gap-6">
            @for ($i = 0; $i < 4; $i++)
                <div class="col-span-12 sm:col-span-6 xl:col-span-3">
                    <div class="h-[146px] animate-pulse rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900"></div>
                </div>
            @endfor
            <div class="col-span-12 xl:col-span-4">
                <div class="h-[220px] animate-pulse rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900"></div>
            </div>
            <div class="col-span-12 xl:col-span-8">
                <div class="grid grid-cols-1 gap-4 xl:grid-cols-2">
                    <div class="h-[340px] animate-pulse rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900"></div>
                    <div class="h-[340px] animate-pulse rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900"></div>
                </div>
            </div>
            <div class="col-span-12 xl:col-span-8">
                <div class="h-[420px] animate-pulse rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900"></div>
            </div>
            <div class="col-span-12 xl:col-span-4">
                <div class="grid grid-cols-1 gap-4">
                    <div class="h-[220px] animate-pulse rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900"></div>
                    <div class="h-[220px] animate-pulse rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900"></div>
                </div>
            </div>
        </div>
    </div>
@endsection
