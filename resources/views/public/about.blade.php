@extends('layouts.public')

@section('content')
<div class="mx-auto max-w-3xl px-4 py-8 sm:px-6 sm:py-12 lg:px-8">

    {{-- Header --}}
    <header class="text-center">
        <div class="mx-auto mb-4 flex size-20 items-center justify-center rounded-full bg-[#f7ebdf] text-2xl font-bold text-[#8b4a22] dark:bg-coffee-500/18 dark:text-coffee-100 sm:mb-6 sm:size-24 sm:text-3xl">
            {{ collect(explode(' ', $resume['name'] ?? 'R A'))->map(fn($w) => mb_strtoupper(mb_substr($w, 0, 1)))->take(2)->join('') }}
        </div>
        <h1 class="font-lora text-2xl font-bold text-[#2f1c12] dark:text-neutralwarm-50 sm:text-3xl md:text-4xl">
            {{ $resume['name'] ?? '' }}
        </h1>
        <p class="mt-2 text-base text-[#8b4a22] dark:text-coffee-100 sm:text-lg">{{ $resume['title'] ?? '' }}</p>
        @if(!empty($resume['location']))
            <p class="mt-3 text-sm leading-6 text-[#8a776b] dark:text-neutralwarm-100/70">{{ $resume['location'] }}</p>
        @endif
    </header>

    {{-- Summary --}}
    @if(!empty($resume['summary']))
    <section class="mt-8 sm:mt-12">
        <p class="text-sm leading-7 text-[#5a2e16] dark:text-neutralwarm-100/85 sm:text-base">{{ $resume['summary'] }}</p>
    </section>
    @endif

    {{-- Experience --}}
    @if(!empty($resume['experience']))
    <section class="mt-8 sm:mt-12">
        <h2 class="text-xs font-semibold uppercase tracking-[0.24em] text-[#8b4a22] dark:text-coffee-100">Pengalaman</h2>
        <div class="mt-4 space-y-3 sm:mt-5 sm:space-y-4">
            @foreach($resume['experience'] as $exp)
            <div class="rounded-2xl border border-[#ead8c8] bg-white p-4 dark:border-coffee-700/40 dark:bg-neutralwarm-900 sm:p-6">
                <div class="flex flex-col gap-1 sm:flex-row sm:items-center sm:justify-between">
                    <h3 class="text-sm font-semibold text-[#2f1c12] dark:text-neutralwarm-50 sm:text-base">{{ $exp['position'] ?? '' }}</h3>
                    <span class="text-sm text-[#8a776b] dark:text-neutralwarm-100/70">{{ $exp['period'] ?? '' }}</span>
                </div>
                <p class="mt-1 text-sm italic text-[#8a776b] dark:text-neutralwarm-100/70">{{ $exp['company'] ?? '' }}{{ !empty($exp['location']) ? ' — '.$exp['location'] : '' }}</p>
                @if(!empty($exp['items']))
                <ul class="mt-3 space-y-2 text-xs leading-6 text-[#5a2e16] dark:text-neutralwarm-100/85 sm:mt-4 sm:text-sm">
                    @foreach($exp['items'] as $item)
                    <li class="flex gap-2">
                        <span class="mt-2 block size-1.5 shrink-0 rounded-full bg-[#8b4a22] dark:bg-coffee-100"></span>
                        {{ $item }}
                    </li>
                    @endforeach
                </ul>
                @endif
            </div>
            @endforeach
        </div>
    </section>
    @endif

    {{-- Projects --}}
    @if(!empty($resume['projects']))
    <section class="mt-8 sm:mt-12">
        <h2 class="text-xs font-semibold uppercase tracking-[0.24em] text-[#8b4a22] dark:text-coffee-100">Proyek Web App</h2>
        <div class="mt-4 space-y-3 sm:mt-5 sm:space-y-4">
            @foreach($resume['projects'] as $project)
            <div class="rounded-2xl border border-[#ead8c8] bg-white p-4 dark:border-coffee-700/40 dark:bg-neutralwarm-900 sm:p-5">
                <div class="flex flex-col gap-1 sm:flex-row sm:items-center sm:justify-between">
                    <h3 class="text-sm font-semibold text-[#2f1c12] dark:text-neutralwarm-50 sm:text-base">{{ $project['name'] ?? '' }}</h3>
                    <span class="text-sm text-[#8a776b] dark:text-neutralwarm-100/70">{{ $project['period'] ?? '' }}</span>
                </div>
                @if(!empty($project['description']))
                <p class="mt-2 text-xs leading-6 text-[#5a2e16] dark:text-neutralwarm-100/85 sm:text-sm">{{ $project['description'] }}</p>
                @endif
                @if(!empty($project['tech']))
                <p class="mt-2 text-xs font-medium text-[#8b4a22] dark:text-coffee-100">{{ $project['tech'] }}</p>
                @endif
            </div>
            @endforeach
        </div>
    </section>
    @endif

    {{-- Skills --}}
    @if(!empty($resume['skills']))
    <section class="mt-8 sm:mt-12">
        <h2 class="text-xs font-semibold uppercase tracking-[0.24em] text-[#8b4a22] dark:text-coffee-100">Keahlian</h2>
        <div class="mt-4 flex flex-wrap gap-2 sm:mt-5">
            @foreach($resume['skills'] as $skill)
            <span class="rounded-full border border-[#ead8c8] bg-white px-3 py-1.5 text-xs font-medium text-[#5a2e16] dark:border-coffee-700/40 dark:bg-neutralwarm-900 dark:text-neutralwarm-100/85 sm:px-4 sm:py-2 sm:text-sm">{{ $skill }}</span>
            @endforeach
        </div>
    </section>
    @endif

    {{-- Education --}}
    @if(!empty($resume['education']))
    <section class="mt-8 sm:mt-12">
        <h2 class="text-xs font-semibold uppercase tracking-[0.24em] text-[#8b4a22] dark:text-coffee-100">Pendidikan</h2>
        <div class="mt-4 rounded-2xl border border-[#ead8c8] bg-white p-4 dark:border-coffee-700/40 dark:bg-neutralwarm-900 sm:mt-5 sm:p-6">
            <div class="flex flex-col gap-1 sm:flex-row sm:items-center sm:justify-between">
                <h3 class="text-sm font-semibold text-[#2f1c12] dark:text-neutralwarm-50 sm:text-base">{{ $resume['education']['degree'] ?? '' }}</h3>
                <span class="text-sm text-[#8a776b] dark:text-neutralwarm-100/70">{{ $resume['education']['period'] ?? '' }}</span>
            </div>
            <p class="mt-1 text-sm italic text-[#8a776b] dark:text-neutralwarm-100/70">{{ $resume['education']['institution'] ?? '' }}</p>
            @if(!empty($resume['education']['gpa']))
            <p class="mt-2 text-sm text-[#5a2e16] dark:text-neutralwarm-100/85">IPK: {{ $resume['education']['gpa'] }}</p>
            @endif
        </div>
    </section>
    @endif

    {{-- Certifications --}}
    @if(!empty($resume['certifications']))
    <section class="mt-8 sm:mt-12">
        <h2 class="text-xs font-semibold uppercase tracking-[0.24em] text-[#8b4a22] dark:text-coffee-100">Sertifikasi</h2>
        <ul class="mt-4 space-y-2 sm:mt-5 sm:space-y-3">
            @foreach($resume['certifications'] as $cert)
            <li class="flex items-start gap-2 text-xs leading-6 text-[#5a2e16] dark:text-neutralwarm-100/85 sm:gap-3 sm:text-sm">
                <span class="mt-2 block size-1.5 shrink-0 rounded-full bg-[#8b4a22] dark:bg-coffee-100"></span>
                {{ $cert }}
            </li>
            @endforeach
        </ul>
    </section>
    @endif

    {{-- Contact --}}
    @if(!empty($resume['email']))
    <section class="mt-8 text-center sm:mt-12">
        <h2 class="text-xs font-semibold uppercase tracking-[0.24em] text-[#8b4a22] dark:text-coffee-100">Kontak</h2>
        <p class="mt-4 text-sm leading-7 text-[#8a776b] dark:text-neutralwarm-100/70">{{ $resume['email'] }}</p>
    </section>
    @endif

</div>
@endsection
