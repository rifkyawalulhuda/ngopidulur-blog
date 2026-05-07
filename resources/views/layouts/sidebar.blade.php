@php
    use App\Helpers\MenuHelper;
    $menuGroups = MenuHelper::getMenuGroups();
@endphp

<aside
    class="fixed left-0 top-0 z-50 flex h-screen flex-col transition-all duration-300 ease-in-out"
    x-data="{
        isActive(path) {
            return window.location.pathname === path || window.location.pathname === path + '/';
        }
    }"
    :class="{
        'border-r border-gray-200 bg-white text-gray-800': $store.theme.theme !== 'dark',
        'border-r border-gray-800 bg-gray-900 text-white': $store.theme.theme === 'dark',
        'w-[280px]': $store.sidebar.isExpanded || $store.sidebar.isHovered || $store.sidebar.isMobileOpen,
        'w-[88px]': !$store.sidebar.isExpanded && !$store.sidebar.isHovered && !$store.sidebar.isMobileOpen,
        'translate-x-0': $store.sidebar.isMobileOpen,
        '-translate-x-full xl:translate-x-0': !$store.sidebar.isMobileOpen
    }"
    @mouseenter="if (!$store.sidebar.isExpanded) $store.sidebar.setHovered(true)"
    @mouseleave="$store.sidebar.setHovered(false)">
    <div
        class="flex h-20 items-center gap-3 px-5"
        :class="$store.theme.theme === 'dark' ? 'border-b border-gray-800' : 'border-b border-gray-200'">
        <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3">
            <div class="flex size-11 items-center justify-center rounded-2xl bg-brand-500 text-white shadow-theme-sm">
                <svg width="20" height="20" viewBox="0 0 20 20" fill="none" aria-hidden="true">
                    <path d="M5.83337 3.9585C5.83337 3.49826 6.20647 3.12516 6.66671 3.12516C7.12694 3.12516 7.50004 3.49826 7.50004 3.9585V16.0418C7.50004 16.5021 7.12694 16.8752 6.66671 16.8752C6.20647 16.8752 5.83337 16.5021 5.83337 16.0418V3.9585Z" fill="currentColor"/>
                    <path d="M9.16663 7.29183C9.16663 6.83159 9.53972 6.4585 9.99996 6.4585C10.4602 6.4585 10.8333 6.83159 10.8333 7.29183V16.0418C10.8333 16.5021 10.4602 16.8752 9.99996 16.8752C9.53972 16.8752 9.16663 16.5021 9.16663 16.0418V7.29183Z" fill="currentColor"/>
                    <path d="M12.5 10.6252C12.5 10.165 12.8731 9.79183 13.3333 9.79183C13.7936 9.79183 14.1667 10.165 14.1667 10.6252V16.0418C14.1667 16.5021 13.7936 16.8752 13.3333 16.8752C12.8731 16.8752 12.5 16.5021 12.5 16.0418V10.6252Z" fill="currentColor"/>
                </svg>
            </div>
            <div x-show="$store.sidebar.isExpanded || $store.sidebar.isHovered || $store.sidebar.isMobileOpen" class="leading-tight">
                <p class="text-xl font-semibold" :class="$store.theme.theme === 'dark' ? 'text-white' : 'text-gray-900'">TailAdmin</p>
                <p class="text-xs" :class="$store.theme.theme === 'dark' ? 'text-gray-400' : 'text-gray-500'">Admin Dashboard</p>
            </div>
        </a>
    </div>

    <div class="flex-1 overflow-y-auto px-4 py-5 no-scrollbar">
        @foreach ($menuGroups as $menuGroup)
            <div class="mb-6">
                <h2 class="mb-3 px-2 text-[11px] font-semibold uppercase tracking-[0.28em] text-gray-400"
                    :class="$store.theme.theme === 'dark' ? 'text-gray-400' : 'text-gray-400'"
                    x-show="$store.sidebar.isExpanded || $store.sidebar.isHovered || $store.sidebar.isMobileOpen">
                    {{ $menuGroup['title'] }}
                </h2>
                <ul class="space-y-2">
                    @foreach ($menuGroup['items'] as $item)
                        <li>
                            <a href="{{ $item['path'] }}"
                                class="group flex items-center gap-3 rounded-xl px-3 py-3 text-sm font-medium transition"
                                :class="isActive('{{ $item['path'] }}')
                                    ? 'bg-brand-50 text-brand-500 dark:bg-white/10 dark:text-white'
                                    : 'text-gray-700 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-white/5 dark:hover:text-white'">
                                <span :class="isActive('{{ $item['path'] }}')
                                    ? 'text-brand-500 dark:text-white'
                                    : 'text-gray-500 group-hover:text-gray-700 dark:text-gray-400 dark:group-hover:text-white'">
                                    {!! MenuHelper::getIconSvg($item['icon']) !!}
                                </span>
                                <span x-show="$store.sidebar.isExpanded || $store.sidebar.isHovered || $store.sidebar.isMobileOpen"
                                    class="truncate">
                                    {{ $item['name'] }}
                                </span>
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endforeach
    </div>

    <div
        class="p-4"
        :class="$store.theme.theme === 'dark' ? 'border-t border-gray-800' : 'border-t border-gray-200'">
        <form method="POST" action="{{ route('admin.api.logout') }}">
            @csrf
            <button type="submit"
                class="flex w-full items-center gap-3 rounded-xl px-3 py-3 text-sm font-medium transition"
                :class="$store.theme.theme === 'dark'
                    ? 'text-gray-300 hover:bg-white/5 hover:text-white'
                    : 'text-gray-700 hover:bg-gray-100 hover:text-gray-900'">
                <span :class="$store.theme.theme === 'dark' ? 'text-gray-400' : 'text-gray-500'">
                    {!! MenuHelper::getIconSvg('authentication') !!}
                </span>
                <span x-show="$store.sidebar.isExpanded || $store.sidebar.isHovered || $store.sidebar.isMobileOpen"
                    class="truncate">
                    Keluar
                </span>
            </button>
        </form>
    </div>
</aside>

<div x-show="$store.sidebar.isMobileOpen"
    @click="$store.sidebar.setMobileOpen(false)"
    class="fixed inset-0 z-40 bg-gray-900/60 xl:hidden"></div>
