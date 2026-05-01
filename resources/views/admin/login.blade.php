@extends('layouts.fullscreen-layout')

@section('content')
    <main class="min-h-screen bg-neutralwarm-50 px-4 py-10 dark:bg-neutralwarm-900">
        <div class="mx-auto flex min-h-[calc(100vh-5rem)] max-w-6xl items-center">
            <div class="grid w-full gap-8 lg:grid-cols-[1.1fr_0.9fr]">
                <section class="space-y-6 rounded-[28px] border border-coffee-100 bg-white p-8 shadow-soft dark:border-coffee-800/40 dark:bg-neutralwarm-900">
                    <div class="flex items-start justify-between gap-4">
                        <div class="space-y-3">
                            <span class="inline-flex items-center rounded-full border border-coffee-100 bg-coffee-50 px-3 py-1 text-xs font-semibold uppercase tracking-[0.24em] text-coffee-700 dark:border-coffee-700/40 dark:bg-coffee-500/10 dark:text-coffee-100">
                                Ngopi Dulur Admin
                            </span>
                            <h1 class="font-lora text-4xl font-semibold text-coffee-900 dark:text-neutralwarm-50">
                                Masuk ke ruang tulis.
                            </h1>
                            <p class="max-w-xl text-sm leading-6 text-neutralwarm-500 dark:text-neutralwarm-100/70">
                                Gunakan akun admin untuk membuka dashboard, menulis, dan menjaga ritme publikasi tetap hangat.
                            </p>
                        </div>

                        <button type="button"
                            @click="$store.theme.toggle()"
                            class="inline-flex size-11 shrink-0 items-center justify-center rounded-full border border-coffee-100 bg-white text-coffee-700 shadow-soft transition hover:bg-coffee-50 dark:border-coffee-700/50 dark:bg-neutralwarm-900 dark:text-coffee-100 dark:hover:bg-white/5"
                            :aria-label="$store.theme.theme === 'dark' ? 'Ubah ke tema terang' : 'Ubah ke tema dark espresso'">
                            <span class="sr-only" x-text="$store.theme.theme === 'dark' ? 'Ubah ke tema terang' : 'Ubah ke tema dark espresso'"></span>
                            <svg x-show="$store.theme.theme !== 'dark'" class="size-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <path d="M12 3V5M12 19V21M4.22 4.22L5.64 5.64M18.36 18.36L19.78 19.78M3 12H5M19 12H21M4.22 19.78L5.64 18.36M18.36 5.64L19.78 4.22" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" />
                                <circle cx="12" cy="12" r="4.25" stroke="currentColor" stroke-width="1.8" />
                            </svg>
                            <svg x-show="$store.theme.theme === 'dark'" class="size-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <path d="M21 14.5C19.6 15.7 17.8 16.5 15.8 16.5C11.3 16.5 7.5 12.7 7.5 8.2C7.5 6.2 8.2 4.4 9.4 3C5.2 3.5 2 7.1 2 11.4C2 16.1 5.9 20 10.6 20C14.9 20 18.5 16.8 21 14.5Z" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </button>
                    </div>

                    <form method="POST" action="{{ route('admin.api.login') }}" class="space-y-4">
                        @csrf

                        <div class="space-y-2">
                            <label for="email" class="text-sm font-medium text-neutralwarm-900 dark:text-neutralwarm-50">Email</label>
                            <input
                                id="email"
                                name="email"
                                type="email"
                                value="{{ old('email') }}"
                                required
                                autocomplete="email"
                                class="w-full rounded-2xl border border-coffee-100 bg-white px-4 py-3 text-sm text-neutralwarm-900 outline-none ring-0 transition placeholder:text-neutralwarm-500 focus:border-coffee-300 focus:shadow-soft dark:border-coffee-800/50 dark:bg-neutralwarm-900 dark:text-neutralwarm-50"
                                placeholder="admin@ngopidulur.test"
                            >
                            @error('email')
                                <p class="text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="space-y-2">
                            <label for="password" class="text-sm font-medium text-neutralwarm-900 dark:text-neutralwarm-50">Kata sandi</label>
                            <input
                                id="password"
                                name="password"
                                type="password"
                                required
                                autocomplete="current-password"
                                class="w-full rounded-2xl border border-coffee-100 bg-white px-4 py-3 text-sm text-neutralwarm-900 outline-none ring-0 transition placeholder:text-neutralwarm-500 focus:border-coffee-300 focus:shadow-soft dark:border-coffee-800/50 dark:bg-neutralwarm-900 dark:text-neutralwarm-50"
                                placeholder="password"
                            >
                        </div>

                        <label class="flex items-center gap-3 text-sm text-neutralwarm-500 dark:text-neutralwarm-100/70">
                            <input
                                type="checkbox"
                                name="remember"
                                class="size-4 rounded border-coffee-300 text-coffee-700 focus:ring-coffee-300"
                            >
                            Ingat saya
                        </label>

                        @if ($errors->any())
                            <div class="rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                                {{ $errors->first() }}
                            </div>
                        @endif

                        <button
                            type="submit"
                            class="inline-flex w-full items-center justify-center rounded-2xl bg-coffee-700 px-4 py-3 text-sm font-semibold text-white shadow-soft transition hover:bg-coffee-800">
                            Masuk
                        </button>
                    </form>
                </section>

                <aside class="rounded-[28px] border border-coffee-100 bg-coffee-50 p-8 shadow-soft dark:border-coffee-800/40 dark:bg-coffee-500/10">
                    <div class="space-y-4">
                        <p class="text-xs font-semibold uppercase tracking-[0.24em] text-coffee-700 dark:text-coffee-100">
                            Warm Coffee Meets Modern Tech
                        </p>
                        <h2 class="font-lora text-3xl font-semibold text-coffee-900 dark:text-neutralwarm-50">
                            Fondasi sudah siap, cerita tinggal diisi.
                        </h2>
                        <p class="text-sm leading-6 text-neutralwarm-500 dark:text-neutralwarm-100/70">
                            TailAdmin tetap menjadi dasar tampilan admin, sementara bahasa visual Ngopi Dulur masuk lewat warna, typography, dan suasana.
                        </p>
                    </div>
                </aside>
            </div>
        </div>
    </main>
@endsection
