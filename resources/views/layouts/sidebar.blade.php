@php
    use App\Helpers\MenuHelper;
    $menuGroups = MenuHelper::getMenuGroups();
@endphp

<aside
    class="fixed left-0 top-0 z-50 flex h-screen flex-col border-r border-coffee-800/40 bg-coffee-950 text-neutralwarm-50 transition-all duration-300 ease-in-out"
    x-data="{
        isActive(path) {
            return window.location.pathname === path || window.location.pathname === path + '/';
        }
    }"
    :class="{
        'w-[280px]': $store.sidebar.isExpanded || $store.sidebar.isHovered || $store.sidebar.isMobileOpen,
        'w-[88px]': !$store.sidebar.isExpanded && !$store.sidebar.isHovered && !$store.sidebar.isMobileOpen,
        'translate-x-0': $store.sidebar.isMobileOpen,
        '-translate-x-full xl:translate-x-0': !$store.sidebar.isMobileOpen
    }"
    @mouseenter="if (!$store.sidebar.isExpanded) $store.sidebar.setHovered(true)"
    @mouseleave="$store.sidebar.setHovered(false)">
    <div class="flex h-20 items-center gap-3 border-b border-coffee-800/40 px-5">
        <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3">
            <div class="flex size-11 items-center justify-center rounded-2xl bg-coffee-500 text-white shadow-soft shadow-coffee-950/20">
                <span class="text-sm font-bold tracking-[0.12em]">ND</span>
            </div>
            <div x-show="$store.sidebar.isExpanded || $store.sidebar.isHovered || $store.sidebar.isMobileOpen" class="leading-tight">
                <p class="font-lora text-lg font-semibold text-white">Ngopi Dulur</p>
                <p class="text-xs text-neutralwarm-100/80">Warm Coffee Meets Modern Tech</p>
            </div>
        </a>
    </div>

    <div class="flex-1 overflow-y-auto px-4 py-5 no-scrollbar">
        @foreach ($menuGroups as $menuGroup)
            <div class="mb-6">
                <h2 class="mb-3 px-2 text-[11px] font-semibold uppercase tracking-[0.28em] text-neutralwarm-100/60"
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

    <div class="border-t border-coffee-800/40 p-4">
        <form method="POST" action="{{ route('admin.api.logout') }}">
            @csrf
            <button type="submit"
                class="menu-item w-full justify-start bg-coffee-900/40 text-neutralwarm-50 hover:bg-coffee-800/60">
                <span class="menu-item-icon-inactive">
                    {!! MenuHelper::getIconSvg('authentication') !!}
                </span>
                <span x-show="$store.sidebar.isExpanded || $store.sidebar.isHovered || $store.sidebar.isMobileOpen"
                    class="menu-item-text">
                    Keluar
                </span>
            </button>
        </form>
    </div>
</aside>

<div x-show="$store.sidebar.isMobileOpen"
    @click="$store.sidebar.setMobileOpen(false)"
    class="fixed inset-0 z-40 bg-neutralwarm-950/50 xl:hidden"></div>
