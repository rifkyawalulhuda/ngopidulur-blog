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
                            class="inline-flex shrink-0 items-center gap-2 rounded-xl border border-coffee-100 bg-white px-3 py-2.5 text-sm font-semibold text-coffee-900 shadow-soft transition hover:bg-coffee-50 dark:border-coffee-800/50 dark:bg-neutralwarm-900 dark:text-neutralwarm-50 dark:hover:bg-white/5"
                            :aria-label="$store.theme.theme === 'dark' ? 'Ubah ke tema terang' : 'Ubah ke tema dark espresso'">
                            <span class="flex size-5 items-center justify-center">
                                <svg x-show="$store.theme.theme !== 'dark'" width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M9 2.25V3.75M9 14.25V15.75M4.22703 4.22703L5.28769 5.28769M12.7123 12.7123L13.773 13.773M2.25 9H3.75M14.25 9H15.75M4.22703 13.773L5.28769 12.7123M12.7123 5.28769L13.773 4.22703M11.625 9C11.625 10.4497 10.4497 11.625 9 11.625C7.55025 11.625 6.375 10.4497 6.375 9C6.375 7.55025 7.55025 6.375 9 6.375C10.4497 6.375 11.625 7.55025 11.625 9Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                <svg x-show="$store.theme.theme === 'dark'" width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M14.4375 10.1925C13.9358 10.4091 13.3953 10.5206 12.8488 10.52C10.5782 10.52 8.7375 8.67934 8.7375 6.40875C8.7375 5.82687 8.8575 5.27344 9.075 4.77187C7.00687 5.27 5.46875 7.13156 5.46875 9.36C5.46875 11.97 7.59 14.0913 10.2 14.0913C12.4294 14.0913 14.2909 12.5531 14.7891 10.485C14.673 10.3875 14.5556 10.2909 14.4375 10.1925Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </span>
                            <span x-text="$store.theme.theme === 'dark' ? 'Tema Terang' : 'Dark Espresso'"></span>
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
