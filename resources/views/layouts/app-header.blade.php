<header
    class="sticky top-0 z-40 border-b border-coffee-100 bg-neutralwarm-50/90 backdrop-blur dark:border-coffee-800/40 dark:bg-neutralwarm-900/90">
    <div class="mx-auto flex max-w-(--breakpoint-2xl) items-center justify-between gap-4 px-4 py-4 md:px-6">
        <div class="flex items-center gap-3">
            <button
                class="flex h-11 w-11 items-center justify-center rounded-xl border border-coffee-100 bg-white text-coffee-800 shadow-soft dark:border-coffee-800/50 dark:bg-neutralwarm-900 dark:text-neutralwarm-50 xl:hidden"
                @click="$store.sidebar.toggleMobileOpen()"
                aria-label="Buka sidebar">
                <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M3 5H17M3 10H17M3 15H17" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" />
                </svg>
            </button>
            <button
                class="hidden h-11 w-11 items-center justify-center rounded-xl border border-coffee-100 bg-white text-coffee-800 shadow-soft dark:border-coffee-800/50 dark:bg-neutralwarm-900 dark:text-neutralwarm-50 xl:flex"
                :class="{ 'bg-coffee-50': !$store.sidebar.isExpanded }"
                @click="$store.sidebar.toggleExpanded()"
                aria-label="Sembunyikan sidebar">
                <svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M3 3.75H15M3 9H15M3 14.25H15" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" />
                </svg>
            </button>
            <div>
                <p class="text-[11px] font-semibold uppercase tracking-[0.3em] text-coffee-700 dark:text-coffee-200">Ngopi Dulur Admin</p>
                <h1 class="font-lora text-lg font-semibold text-coffee-900 dark:text-neutralwarm-50">Warm Coffee Meets Modern Tech</h1>
            </div>
        </div>

        @auth
            <div class="flex items-center gap-3">
                <button type="button"
                    @click="$store.theme.toggle()"
                    class="inline-flex size-11 items-center justify-center rounded-full border border-coffee-100 bg-white text-coffee-700 shadow-soft transition hover:bg-coffee-50 dark:border-coffee-700/50 dark:bg-neutralwarm-900 dark:text-coffee-100 dark:hover:bg-white/5"
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
                <span class="hidden rounded-full border border-coffee-100 bg-white px-4 py-2 text-sm font-medium text-coffee-900 shadow-soft dark:border-coffee-800/50 dark:bg-neutralwarm-900 dark:text-neutralwarm-50 sm:inline-flex">
                    {{ auth()->user()->name }}
                </span>
                <form method="POST" action="{{ route('admin.api.logout') }}">
                    @csrf
                    <button type="submit"
                        class="inline-flex items-center gap-2 rounded-xl bg-coffee-700 px-4 py-2.5 text-sm font-semibold text-white shadow-soft transition hover:bg-coffee-800">
                        <span>Keluar</span>
                    </button>
                </form>
            </div>
        @endauth
    </div>
</header>
