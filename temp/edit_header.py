import re

file_path = r'D:\Github\ngopidulur-blog\resources\views\layouts\app-header.blade.php'
with open(file_path, 'r', encoding='utf-8') as f:
    content = f.read()

pattern = r'<div class="hidden min-w-\[320px\] lg:block">.*?</div>'

new_search = '''<div class="hidden min-w-[320px] lg:block">
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
            </div>'''

result = re.sub(pattern, new_search, content, count=1, flags=re.DOTALL)

if result == content:
    print('WARNING: No replacement made!')
else:
    with open(file_path, 'w', encoding='utf-8') as f:
        f.write(result)
    print('File updated successfully')
