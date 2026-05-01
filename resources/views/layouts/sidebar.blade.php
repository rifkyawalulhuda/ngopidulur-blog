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
        'border-r border-coffee-100 bg-neutralwarm-50 text-neutralwarm-900': $store.theme.theme !== 'dark',
        'border-r border-coffee-800/40 bg-coffee-950 text-neutralwarm-50': $store.theme.theme === 'dark',
        'w-[280px]': $store.sidebar.isExpanded || $store.sidebar.isHovered || $store.sidebar.isMobileOpen,
        'w-[88px]': !$store.sidebar.isExpanded && !$store.sidebar.isHovered && !$store.sidebar.isMobileOpen,
        'translate-x-0': $store.sidebar.isMobileOpen,
        '-translate-x-full xl:translate-x-0': !$store.sidebar.isMobileOpen
    }"
    @mouseenter="if (!$store.sidebar.isExpanded) $store.sidebar.setHovered(true)"
    @mouseleave="$store.sidebar.setHovered(false)">
    <div
        class="flex h-20 items-center gap-3 px-5"
        :class="$store.theme.theme === 'dark' ? 'border-b border-coffee-800/40' : 'border-b border-coffee-100'">
        <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3">
            <div class="flex size-11 items-center justify-center rounded-2xl bg-coffee-500 text-white shadow-soft shadow-coffee-950/20">
                <span class="text-sm font-bold tracking-[0.12em]">ND</span>
            </div>
            <div x-show="$store.sidebar.isExpanded || $store.sidebar.isHovered || $store.sidebar.isMobileOpen" class="leading-tight">
                <p class="font-lora text-lg font-semibold" :class="$store.theme.theme === 'dark' ? 'text-white' : 'text-coffee-900'">Ngopi Dulur</p>
                <p class="text-xs" :class="$store.theme.theme === 'dark' ? 'text-coffee-100/85' : 'text-neutralwarm-500'">Warm Coffee Meets Modern Tech</p>
            </div>
        </a>
    </div>

    <div class="flex-1 overflow-y-auto px-4 py-5 no-scrollbar">
        @foreach ($menuGroups as $menuGroup)
            <div class="mb-6">
                <h2 class="mb-3 px-2 text-[11px] font-semibold uppercase tracking-[0.28em] text-coffee-500/70 dark:text-neutralwarm-100/60"
                    :class="$store.theme.theme === 'dark' ? 'text-neutralwarm-100/60' : 'text-coffee-500/70'"
                    x-show="$store.sidebar.isExpanded || $store.sidebar.isHovered || $store.sidebar.isMobileOpen">
                    {{ $menuGroup['title'] }}
                </h2>
                <ul class="space-y-2">
                    @foreach ($menuGroup['items'] as $item)
                        <li>
                            <a href="{{ $item['path'] }}"
                                class="menu-item group"
                                :class="isActive('{{ $item['path'] }}') ? 'menu-item-active' : 'menu-item-inactive'">
                                <span :class="isActive('{{ $item['path'] }}') ? 'menu-item-icon-active' : 'menu-item-icon-inactive'">
                                    {!! MenuHelper::getIconSvg($item['icon']) !!}
                                </span>
                                <span x-show="$store.sidebar.isExpanded || $store.sidebar.isHovered || $store.sidebar.isMobileOpen"
                                    class="menu-item-text">
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
        :class="$store.theme.theme === 'dark' ? 'border-t border-coffee-800/40' : 'border-t border-coffee-100'">
        <form method="POST" action="{{ route('admin.api.logout') }}">
            @csrf
            <button type="submit"
                class="menu-item w-full justify-start"
                :class="$store.theme.theme === 'dark'
                    ? 'bg-coffee-900/40 text-neutralwarm-50 hover:bg-coffee-800/60'
                    : 'bg-coffee-100 text-coffee-900 hover:bg-coffee-200'">
                <span class="menu-item-icon-inactive">
                    {!! MenuHelper::getIconSvg('authentication') !!}
                </span>
                <span x-show="$store.sidebar.isExpanded || $store.sidebar.isHovered || $store.sidebar.isMobileOpen"
                    class="menu-item-text"
                    :class="$store.theme.theme === 'dark' ? 'text-neutralwarm-50' : 'text-coffee-900'">
                    Keluar
                </span>
            </button>
        </form>
    </div>
</aside>

<div x-show="$store.sidebar.isMobileOpen"
    @click="$store.sidebar.setMobileOpen(false)"
    class="fixed inset-0 z-40 bg-neutralwarm-950/50 xl:hidden"></div>
