<header
    class="sticky top-0 z-40 border-b border-gray-200 bg-white/90 backdrop-blur dark:border-gray-800 dark:bg-gray-900/90">
    <div class="mx-auto flex max-w-(--breakpoint-2xl) items-center justify-between gap-4 px-4 py-4 md:px-6">
        <div class="flex items-center gap-3">
            <button
                class="flex h-11 w-11 items-center justify-center rounded-xl border border-gray-200 bg-white text-gray-700 shadow-theme-xs dark:border-gray-800 dark:bg-gray-900 dark:text-gray-400 xl:hidden"
                @click="$store.sidebar.toggleMobileOpen()"
                aria-label="Buka sidebar">
                <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M3 5H17M3 10H17M3 15H17" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" />
                </svg>
            </button>
            <button
                class="hidden h-11 w-11 items-center justify-center rounded-xl border border-gray-200 bg-white text-gray-700 shadow-theme-xs dark:border-gray-800 dark:bg-gray-900 dark:text-gray-400 xl:flex"
                :class="{ 'bg-gray-100 dark:bg-gray-800': !$store.sidebar.isExpanded }"
                @click="$store.sidebar.toggleExpanded()"
                aria-label="Sembunyikan sidebar">
                <svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M3 3.75H15M3 9H15M3 14.25H15" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" />
                </svg>
            </button>

            <div class="hidden min-w-[320px] lg:block">
                <form class="relative">
                    <span class="pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 text-gray-400">
                        <svg width="20" height="20" viewBox="0 0 20 20" fill="none" aria-hidden="true">
                            <path d="M17.5 17.5L13.875 13.875M15.8333 9.16667C15.8333 12.8486 12.8486 15.8333 9.16667 15.8333C5.48477 15.8333 2.5 12.8486 2.5 9.16667C2.5 5.48477 5.48477 2.5 9.16667 2.5C12.8486 2.5 15.8333 5.48477 15.8333 9.16667Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                        </svg>
                    </span>
                    <input
                        type="text"
                        placeholder="Search or type command..."
                        class="h-11 w-full rounded-xl border border-gray-200 bg-white pl-12 pr-16 text-sm text-gray-700 outline-none transition focus:border-brand-300 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-200"
                    >
                    <span class="absolute right-3 top-1/2 inline-flex -translate-y-1/2 rounded-lg border border-gray-200 bg-gray-50 px-2 py-1 text-xs text-gray-400 dark:border-gray-800 dark:bg-gray-800">⌘ K</span>
                </form>
            </div>
        </div>

        @auth
            <div class="flex items-center gap-3">
                <button type="button"
                    @click="$store.theme.toggle()"
                    class="inline-flex size-11 items-center justify-center rounded-full border border-gray-200 bg-white text-gray-500 shadow-theme-xs transition hover:bg-gray-100 hover:text-gray-700 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-400 dark:hover:bg-gray-800 dark:hover:text-white"
                    :aria-label="$store.theme.theme === 'dark' ? 'Ubah ke tema terang' : 'Ubah ke tema gelap'">
                    <span class="sr-only" x-text="$store.theme.theme === 'dark' ? 'Ubah ke tema terang' : 'Ubah ke tema gelap'"></span>
                    <svg x-show="$store.theme.theme !== 'dark'" class="size-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <path d="M21 14.5C19.6 15.7 17.8 16.5 15.8 16.5C11.3 16.5 7.5 12.7 7.5 8.2C7.5 6.2 8.2 4.4 9.4 3C5.2 3.5 2 7.1 2 11.4C2 16.1 5.9 20 10.6 20C14.9 20 18.5 16.8 21 14.5Z" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    <svg x-show="$store.theme.theme === 'dark'" class="size-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <path d="M12 3V5M12 19V21M4.22 4.22L5.64 5.64M18.36 18.36L19.78 19.78M3 12H5M19 12H21M4.22 19.78L5.64 18.36M18.36 5.64L19.78 4.22" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" />
                        <circle cx="12" cy="12" r="4.25" stroke="currentColor" stroke-width="1.8" />
                    </svg>
                </button>
                <x-header.notification-dropdown />
                <div class="hidden items-center gap-3 sm:flex">
                    <span class="overflow-hidden rounded-full h-11 w-11 border border-gray-200 dark:border-gray-800">
                        <img src="/images/user/owner.png" alt="User" class="h-full w-full object-cover" />
                    </span>
                    <div class="text-right leading-tight">
                        <p class="text-sm font-medium text-gray-700 dark:text-gray-200">{{ auth()->user()->name }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Administrator</p>
                    </div>
                </div>
            </div>
        @endauth
    </div>
</header>
