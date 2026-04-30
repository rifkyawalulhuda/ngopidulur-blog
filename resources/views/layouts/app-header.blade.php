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
                    class="inline-flex items-center gap-2 rounded-xl border border-coffee-100 bg-white px-3 py-2.5 text-sm font-semibold text-coffee-900 shadow-soft transition hover:bg-coffee-50 dark:border-coffee-800/50 dark:bg-neutralwarm-900 dark:text-neutralwarm-50 dark:hover:bg-white/5"
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
