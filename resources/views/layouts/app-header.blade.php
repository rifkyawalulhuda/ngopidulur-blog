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
                <div class="relative" x-data="adminGlobalSearch()" @keydown.escape.window="close()" @click.outside="close()">
                    <span class="pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 text-gray-400">
                        <svg x-show="!loading" width="20" height="20" viewBox="0 0 20 20" fill="none" aria-hidden="true"><path d="M17.5 17.5L13.875 13.875M15.8333 9.16667C15.8333 12.8486 12.8486 15.8333 9.16667 15.8333C5.48477 15.8333 2.5 12.8486 2.5 9.16667C2.5 5.48477 5.48477 2.5 9.16667 2.5C12.8486 2.5 15.8333 5.48477 15.8333 9.16667Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
                        <svg x-show="loading" class="animate-spin" width="20" height="20" viewBox="0 0 24 24" fill="none" aria-hidden="true"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                    </span>
                    <input x-model="query" @input.debounce.300ms="search()" @focus="if (query.length >= 2) open = true" type="text" placeholder="Cari tulisan, kategori, tag..." autocomplete="off" class="h-11 w-full rounded-xl border border-gray-200 bg-white pl-12 pr-16 text-sm text-gray-700 outline-none transition focus:border-brand-300 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-200">
                    <span x-show="!query" class="pointer-events-none absolute right-3 top-1/2 inline-flex -translate-y-1/2 rounded-lg border border-gray-200 bg-gray-50 px-2 py-1 text-xs text-gray-400 dark:border-gray-800 dark:bg-gray-800">&#8984; K</span>
                    <button x-show="query" @click="clear()" type="button" class="absolute right-3 top-1/2 -translate-y-1/2 rounded-lg p-1 text-gray-400 transition hover:text-gray-600 dark:hover:text-gray-200" aria-label="Hapus pencarian">
                        <svg width="16" height="16" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 0 1 1.414 0L10 8.586l4.293-4.293a1 1 0 1 1 1.414 1.414L11.414 10l4.293 4.293a1 1 0 0 1-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 0 1-1.414-1.414L8.586 10 4.293 5.707a1 1 0 0 1 0-1.414Z" clip-rule="evenodd" /></svg>
                    </button>
                    <div x-show="open" x-transition:enter="transition ease-out duration-100" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95" class="absolute left-0 top-full z-50 mt-2 w-full min-w-[420px] overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-theme-md dark:border-gray-800 dark:bg-gray-900" role="listbox" style="display:none;">
                        <div x-show="total === 0 && !loading" class="px-4 py-8 text-center">
                            <p class="text-sm text-gray-500 dark:text-gray-400">Tidak ada hasil untuk <span class="font-medium text-gray-700 dark:text-gray-200" x-text="'&quot;' + query + '&quot;'"></span></p>
                        </div>
                        <div class="max-h-[480px] overflow-y-auto">
                            <template x-if="results.posts && results.posts.length > 0">
                                <div class="px-2 pt-3">
                                    <p class="mb-1 px-2 text-xs font-semibold uppercase tracking-wide text-gray-400 dark:text-gray-500">Tulisan</p>
                                    <template x-for="item in results.posts" :key="'post-' + item.id">
                                        <a :href="item.url" class="flex items-start gap-3 rounded-xl px-3 py-2.5 transition hover:bg-gray-50 dark:hover:bg-white/5" role="option">
                                            <span class="mt-0.5 flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-brand-50 text-brand-600 dark:bg-brand-500/10 dark:text-brand-400"><svg width="16" height="16" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd" /></svg></span>
                                            <div class="min-w-0 flex-1">
                                                <p class="truncate text-sm font-medium text-gray-900 dark:text-white" x-text="item.title"></p>
                                                <p class="mt-0.5 flex items-center gap-2 text-xs text-gray-500 dark:text-gray-400">
                                                    <span x-text="item.category_name || 'Tanpa kategori'"></span>
                                                    <span class="inline-flex rounded-full px-1.5 py-0.5 text-xs font-medium capitalize" :class="item.status === 'published' ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/15 dark:text-emerald-300' : item.status === 'draft' ? 'bg-amber-100 text-amber-700 dark:bg-amber-500/15 dark:text-amber-300' : 'bg-gray-100 text-gray-600 dark:bg-white/10 dark:text-gray-400'" x-text="item.status"></span>
                                                </p>
                                            </div>
                                        </a>
                                    </template>
                                </div>
                            </template>
                            <template x-if="results.categories && results.categories.length > 0">
                                <div class="px-2 pt-3">
                                    <p class="mb-1 px-2 text-xs font-semibold uppercase tracking-wide text-gray-400 dark:text-gray-500">Kategori</p>
                                    <template x-for="item in results.categories" :key="'cat-' + item.id">
                                        <a :href="item.url" class="flex items-center gap-3 rounded-xl px-3 py-2.5 transition hover:bg-gray-50 dark:hover:bg-white/5" role="option">
                                            <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-violet-50 text-violet-600 dark:bg-violet-500/10 dark:text-violet-400"><svg width="16" height="16" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path d="M2 6a2 2 0 012-2h5l2 2h5a2 2 0 012 2v6a2 2 0 01-2 2H4a2 2 0 01-2-2V6z" /></svg></span>
                                            <div class="min-w-0 flex-1">
                                                <p class="truncate text-sm font-medium text-gray-900 dark:text-white" x-text="item.name"></p>
                                                <p class="mt-0.5 text-xs text-gray-500 dark:text-gray-400" x-text="item.slug"></p>
                                            </div>
                                        </a>
                                    </template>
                                </div>
                            </template>
                            <template x-if="results.tags && results.tags.length > 0">
                                <div class="px-2 py-3">
                                    <p class="mb-1 px-2 text-xs font-semibold uppercase tracking-wide text-gray-400 dark:text-gray-500">Tag</p>
                                    <template x-for="item in results.tags" :key="'tag-' + item.id">
                                        <a :href="item.url" class="flex items-center gap-3 rounded-xl px-3 py-2.5 transition hover:bg-gray-50 dark:hover:bg-white/5" role="option">
                                            <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-emerald-50 text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-400"><svg width="16" height="16" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M17.707 9.293a1 1 0 010 1.414l-7 7a1 1 0 01-1.414 0l-7-7A.997.997 0 012 10V5a3 3 0 013-3h5c.256 0 .512.098.707.293l7 7zM5 6a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" /></svg></span>
                                            <div class="min-w-0 flex-1">
                                                <p class="truncate text-sm font-medium text-gray-900 dark:text-white" x-text="item.name"></p>
                                                <p class="mt-0.5 text-xs text-gray-500 dark:text-gray-400" x-text="item.slug"></p>
                                            </div>
                                        </a>
                                    </template>
                                </div>
                            </template>
                        </div>
                        <div x-show="total > 0" class="border-t border-gray-100 px-4 py-2.5 dark:border-gray-800">
                            <p class="text-xs text-gray-400 dark:text-gray-500" x-text="total + ' hasil ditemukan'"></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @auth
            @php
                $adminUser = auth()->user();
                $adminProfile = [
                    'name' => $adminUser->name,
                    'email' => $adminUser->email,
                    'role_label' => $adminUser->role === 'admin' ? 'Administrator' : $adminUser->role,
                    'avatar_url' => $adminUser->avatar ? \App\Support\PublicAssetUrl::fromPublicDisk($adminUser->avatar) : '/images/user/owner.png',
                ];
            @endphp

            <div class="flex items-center gap-3" x-data="adminProfileEditor(@js($adminProfile))" @keydown.escape.window="closeAll()">
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

                <div class="relative hidden sm:block" @click.outside="profileDropdownOpen = false">
                    <button
                        type="button"
                        @click="profileDropdownOpen = ! profileDropdownOpen"
                        class="flex items-center gap-3 rounded-xl px-2 py-1.5 transition hover:bg-gray-100 dark:hover:bg-white/5"
                        :aria-expanded="profileDropdownOpen ? 'true' : 'false'"
                        aria-haspopup="menu"
                    >
                        <span class="overflow-hidden rounded-full h-11 w-11 border border-gray-200 bg-gray-100 dark:border-gray-800 dark:bg-gray-800">
                            <img :src="profile.avatar_url" alt="User" class="h-full w-full object-cover" />
                        </span>
                        <span class="text-right leading-tight">
                            <span class="block text-sm font-medium text-gray-700 dark:text-gray-200" x-text="profile.name"></span>
                            <span class="block text-xs text-gray-500 dark:text-gray-400" x-text="profile.role_label"></span>
                        </span>
                        <svg class="size-4 text-gray-400 transition" :class="{ 'rotate-180': profileDropdownOpen }" viewBox="0 0 20 20" fill="none" aria-hidden="true">
                            <path d="M5 7.5L10 12.5L15 7.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </button>

                    <div
                        x-cloak
                        x-show="profileDropdownOpen"
                        x-transition.origin.top.right
                        class="absolute right-0 top-full z-50 mt-3 w-64 rounded-2xl border border-gray-200 bg-white p-2 shadow-theme-lg dark:border-gray-800 dark:bg-gray-900"
                        role="menu"
                    >
                        <button
                            type="button"
                            @click="openProfileModal()"
                            class="flex w-full items-center gap-3 rounded-xl px-3 py-3 text-left text-sm font-medium text-gray-700 transition hover:bg-gray-50 hover:text-gray-900 dark:text-gray-300 dark:hover:bg-white/5 dark:hover:text-white"
                            role="menuitem"
                        >
                            <span class="inline-flex size-9 items-center justify-center rounded-lg bg-brand-50 text-brand-600 dark:bg-brand-500/15 dark:text-brand-300">
                                <svg class="size-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <path d="M12 12.25C14.4853 12.25 16.5 10.2353 16.5 7.75C16.5 5.26472 14.4853 3.25 12 3.25C9.51472 3.25 7.5 5.26472 7.5 7.75C7.5 10.2353 9.51472 12.25 12 12.25Z" stroke="currentColor" stroke-width="1.7" />
                                    <path d="M4.75 20.25C5.875 16.9 8.55 15.25 12 15.25C15.45 15.25 18.125 16.9 19.25 20.25" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" />
                                </svg>
                            </span>
                            <span>
                                <span class="block">Profile</span>
                                <span class="mt-0.5 block text-xs font-normal text-gray-500 dark:text-gray-400">Foto, nama, email, password</span>
                            </span>
                        </button>
                    </div>
                </div>

                <template x-teleport="body">
                    <div
                        x-cloak
                        x-show="profileModalOpen"
                        x-transition.opacity
                        class="fixed inset-0 z-[80] flex items-center justify-center bg-gray-950/50 px-4 py-8 backdrop-blur-sm"
                        role="dialog"
                        aria-modal="true"
                    >
                        <div
                            x-show="profileModalOpen"
                            x-transition.scale.origin.center
                            @click.outside="closeProfileModal()"
                            class="max-h-[calc(100vh-4rem)] w-full max-w-2xl overflow-y-auto rounded-2xl border border-gray-200 bg-white shadow-theme-xl dark:border-gray-800 dark:bg-gray-900"
                        >
                            <div class="flex items-start justify-between gap-4 border-b border-gray-200 px-6 py-5 dark:border-gray-800">
                                <div>
                                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-brand-500 dark:text-brand-300">TailAdmin Profile</p>
                                    <h2 class="mt-1 text-2xl font-semibold text-gray-900 dark:text-white">Pengaturan Profile</h2>
                                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Ubah foto profil, nama, email login, dan password admin.</p>
                                </div>
                                <button
                                    type="button"
                                    @click="closeProfileModal()"
                                    class="inline-flex size-10 items-center justify-center rounded-xl border border-gray-200 bg-white text-gray-500 transition hover:bg-gray-50 hover:text-gray-700 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-400 dark:hover:bg-white/5 dark:hover:text-white"
                                    aria-label="Tutup profile"
                                >
                                    <svg class="size-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                        <path d="M6 6L18 18M18 6L6 18" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" />
                                    </svg>
                                </button>
                            </div>

                            <form class="space-y-6 px-6 py-6" @submit.prevent="saveProfile($event)">
                                <div class="flex flex-col gap-4 rounded-2xl border border-gray-200 p-4 dark:border-gray-800 sm:flex-row sm:items-center">
                                    <span class="overflow-hidden rounded-full size-20 border border-gray-200 bg-gray-100 dark:border-gray-800 dark:bg-gray-800">
                                        <img :src="profilePreviewUrl || profile.avatar_url" alt="Foto profil" class="h-full w-full object-cover" />
                                    </span>
                                    <div class="flex-1">
                                        <p class="text-sm font-medium text-gray-900 dark:text-white">Foto profil</p>
                                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Gunakan JPG, PNG, atau WebP maksimal 2 MB.</p>
                                        <div class="mt-3 flex flex-wrap gap-2">
                                            <input x-ref="avatarInput" type="file" name="avatar" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp" class="hidden" @change="previewAvatar($event)">
                                            <button type="button" @click="$refs.avatarInput.click()" class="rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 dark:hover:bg-white/5">Pilih foto</button>
                                            <button type="button" @click="removeAvatar()" class="rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 dark:hover:bg-white/5">Hapus foto</button>
                                        </div>
                                        <input type="hidden" name="remove_avatar" :value="removeAvatarFlag ? '1' : '0'">
                                    </div>
                                </div>

                                <template x-if="profileError">
                                    <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700 dark:border-red-500/20 dark:bg-red-500/10 dark:text-red-200" x-text="profileError"></div>
                                </template>

                                <div class="grid gap-5 md:grid-cols-2">
                                    <label class="block">
                                        <span class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Nama</span>
                                        <input type="text" name="name" x-model="form.name" class="h-11 w-full rounded-lg border border-gray-300 bg-white px-4 text-sm text-gray-700 outline-none transition focus:border-brand-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200" required>
                                    </label>
                                    <label class="block">
                                        <span class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Email / Username Login</span>
                                        <input type="email" name="email" x-model="form.email" class="h-11 w-full rounded-lg border border-gray-300 bg-white px-4 text-sm text-gray-700 outline-none transition focus:border-brand-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200" required>
                                    </label>
                                </div>

                                <div class="rounded-2xl border border-gray-200 p-4 dark:border-gray-800">
                                    <p class="text-sm font-medium text-gray-900 dark:text-white">Ganti password login</p>
                                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Kosongkan bagian ini bila password tidak ingin diubah.</p>
                                    <div class="mt-4 grid gap-5 md:grid-cols-3">
                                        <label class="block">
                                            <span class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Password saat ini</span>
                                            <input type="password" name="current_password" x-model="form.current_password" class="h-11 w-full rounded-lg border border-gray-300 bg-white px-4 text-sm text-gray-700 outline-none transition focus:border-brand-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200">
                                        </label>
                                        <label class="block">
                                            <span class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Password baru</span>
                                            <input type="password" name="password" x-model="form.password" class="h-11 w-full rounded-lg border border-gray-300 bg-white px-4 text-sm text-gray-700 outline-none transition focus:border-brand-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200">
                                        </label>
                                        <label class="block">
                                            <span class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Konfirmasi password</span>
                                            <input type="password" name="password_confirmation" x-model="form.password_confirmation" class="h-11 w-full rounded-lg border border-gray-300 bg-white px-4 text-sm text-gray-700 outline-none transition focus:border-brand-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200">
                                        </label>
                                    </div>
                                </div>

                                <div class="flex flex-col-reverse gap-3 border-t border-gray-200 pt-5 dark:border-gray-800 sm:flex-row sm:justify-end">
                                    <button type="button" @click="closeProfileModal()" class="rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 transition hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 dark:hover:bg-white/5">Batal</button>
                                    <button type="submit" class="rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-brand-600 disabled:cursor-not-allowed disabled:opacity-70" :disabled="profileSaving">
                                        <span x-text="profileSaving ? 'Menyimpan...' : 'Simpan Profile'"></span>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </template>
            </div>
        @endauth
    </div>
</header>

@auth
    <script>
        function adminGlobalSearch() {
            return {
                query: '',
                open: false,
                loading: false,
                results: { posts: [], categories: [], tags: [] },
                total: 0,
                abortController: null,

                async search() {
                    if (this.query.length < 2) {
                        this.close();
                        return;
                    }

                    if (this.abortController) {
                        this.abortController.abort();
                    }

                    this.abortController = new AbortController();
                    this.loading = true;
                    this.open = true;

                    try {
                        const response = await fetch(`/admin/api/search?q=${encodeURIComponent(this.query)}`, {
                            credentials: 'same-origin',
                            signal: this.abortController.signal,
                            headers: {
                                Accept: 'application/json',
                                'X-Requested-With': 'XMLHttpRequest',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                            },
                        });

                        const payload = await response.json();
                        this.results = payload.results || { posts: [], categories: [], tags: [] };
                        this.total = payload.total || 0;
                    } catch (error) {
                        if (error.name !== 'AbortError') {
                            this.results = { posts: [], categories: [], tags: [] };
                            this.total = 0;
                        }
                    } finally {
                        this.loading = false;
                    }
                },

                close() {
                    this.open = false;
                },

                clear() {
                    this.query = '';
                    this.results = { posts: [], categories: [], tags: [] };
                    this.total = 0;
                    this.open = false;
                },
            };
        }

        function adminProfileEditor(initialProfile) {
            return {
                profile: { ...initialProfile },
                form: {
                    name: initialProfile.name || '',
                    email: initialProfile.email || '',
                    current_password: '',
                    password: '',
                    password_confirmation: '',
                },
                profileDropdownOpen: false,
                profileModalOpen: false,
                profileSaving: false,
                profileError: '',
                profilePreviewUrl: '',
                removeAvatarFlag: false,
                openProfileModal() {
                    this.profileDropdownOpen = false;
                    this.profileModalOpen = true;
                    this.profileError = '';
                    this.form.name = this.profile.name || '';
                    this.form.email = this.profile.email || '';
                    this.form.current_password = '';
                    this.form.password = '';
                    this.form.password_confirmation = '';
                    this.removeAvatarFlag = false;
                    this.profilePreviewUrl = '';
                    this.$nextTick(() => {
                        if (this.$refs.avatarInput) {
                            this.$refs.avatarInput.value = '';
                        }
                    });
                },
                closeProfileModal() {
                    if (this.profileSaving) {
                        return;
                    }

                    this.profileModalOpen = false;
                    this.profileError = '';
                    this.profilePreviewUrl = '';
                    this.removeAvatarFlag = false;
                },
                closeAll() {
                    this.profileDropdownOpen = false;
                    this.closeProfileModal();
                },
                previewAvatar(event) {
                    const [file] = Array.from(event.target.files || []);

                    if (! file) {
                        return;
                    }

                    this.removeAvatarFlag = false;
                    this.profilePreviewUrl = URL.createObjectURL(file);
                },
                removeAvatar() {
                    this.removeAvatarFlag = true;
                    this.profilePreviewUrl = '/images/user/owner.png';

                    if (this.$refs.avatarInput) {
                        this.$refs.avatarInput.value = '';
                    }
                },
                async saveProfile(event) {
                    this.profileSaving = true;
                    this.profileError = '';

                    const formData = new FormData(event.target);

                    // Hapus field password dari request bila semua kosong
                    // agar user bisa update nama/foto tanpa harus isi password
                    if (!formData.get('password') && !formData.get('current_password') && !formData.get('password_confirmation')) {
                        formData.delete('current_password');
                        formData.delete('password');
                        formData.delete('password_confirmation');
                    }

                    try {
                        const response = await fetch('/admin/api/profile', {
                            method: 'POST',
                            credentials: 'same-origin',
                            headers: {
                                Accept: 'application/json',
                                'X-Requested-With': 'XMLHttpRequest',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                            },
                            body: formData,
                        });

                        const payload = await response.json();

                        if (! response.ok) {
                            const firstError = payload?.errors ? Object.values(payload.errors).flat()[0] : null;
                            throw new Error(firstError || payload?.message || 'Profil belum bisa disimpan.');
                        }

                        this.profile = payload.item || this.profile;
                        this.form.current_password = '';
                        this.form.password = '';
                        this.form.password_confirmation = '';
                        this.profilePreviewUrl = '';
                        this.removeAvatarFlag = false;
                        this.profileModalOpen = false;

                        if (this.$refs.avatarInput) {
                            this.$refs.avatarInput.value = '';
                        }
                    } catch (error) {
                        this.profileError = error.message || 'Profil belum bisa disimpan.';
                    } finally {
                        this.profileSaving = false;
                    }
                },
            };
        }
    </script>
@endauth
