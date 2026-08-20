<aside x-data="{ collapsed: false }"
       :class="collapsed ? 'lg:w-20' : 'lg:w-72'"
       class="w-full shrink-0 border-b border-slate-200 bg-white text-slate-700 shadow-sm transition-[width] duration-200 lg:min-h-screen lg:border-b-0 lg:border-r">
    <div class="flex items-center justify-between border-b border-slate-100 px-5 py-5">
        <a href="{{ route('home') }}" class="flex min-w-0 items-center gap-3 text-xl font-black tracking-tight text-slate-900">
            <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-blue-600 text-sm font-black text-white">M</span>
            <span x-show="!collapsed" x-transition.opacity class="truncate">Marketplace</span>
        </a>
        <button type="button"
                @click="collapsed = !collapsed"
                :aria-label="collapsed ? 'Expandir menu' : 'Esconder menu'"
                class="ml-3 hidden h-9 w-9 shrink-0 items-center justify-center rounded-lg border border-slate-200 text-slate-500 transition hover:border-blue-200 hover:bg-blue-50 hover:text-blue-600 lg:flex">
            <svg x-show="!collapsed" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                <path d="M15 6 9 12l6 6" />
            </svg>
            <svg x-show="collapsed" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                <path d="m9 6 6 6-6 6" />
            </svg>
        </button>
    </div>

    <div class="flex items-center justify-between px-5 py-4 lg:block">
        <span x-show="!collapsed" class="text-[11px] font-bold uppercase tracking-[0.18em] text-slate-400">Navegação</span>
        <button type="button"
                @click="collapsed = !collapsed"
                class="flex h-9 w-9 items-center justify-center rounded-lg bg-slate-50 text-slate-500 hover:bg-blue-50 hover:text-blue-600 lg:hidden"
                aria-label="Expandir ou esconder menu">
            <span aria-hidden="true">☰</span>
        </button>
    </div>

    <nav class="flex gap-2 overflow-x-auto px-4 pb-4 lg:block lg:space-y-1" aria-label="Navegação principal">
        <a href="{{ route('home') }}"
           class="flex min-w-max items-center gap-3 rounded-xl px-4 py-3 text-sm font-semibold transition {{ request()->routeIs('home') ? 'bg-blue-600 text-white shadow-sm' : 'text-slate-600 hover:bg-blue-50 hover:text-blue-700' }}"
           :class="collapsed ? 'lg:justify-center lg:px-0' : ''"
           title="Home">
            <span class="text-base" aria-hidden="true">⌂</span>
            <span x-show="!collapsed">Home</span>
        </a>

        <a href="{{ route('products.index') }}"
           class="flex min-w-max items-center gap-3 rounded-xl px-4 py-3 text-sm font-semibold transition {{ request()->routeIs('products.*') ? 'bg-blue-600 text-white shadow-sm' : 'text-slate-600 hover:bg-blue-50 hover:text-blue-700' }}"
           :class="collapsed ? 'lg:justify-center lg:px-0' : ''"
           title="Gerenciamento de produtos">
            <span class="text-base" aria-hidden="true">▣</span>
            <span x-show="!collapsed">Produtos</span>
        </a>

        <a href="{{ route('purchases.index') }}"
           class="flex min-w-max items-center gap-3 rounded-xl px-4 py-3 text-sm font-semibold transition {{ request()->routeIs('purchases.*') ? 'bg-blue-600 text-white shadow-sm' : 'text-slate-600 hover:bg-blue-50 hover:text-blue-700' }}"
           :class="collapsed ? 'lg:justify-center lg:px-0' : ''"
           title="Histórico de compras">
            <span aria-hidden="true">↓</span>
            <span x-show="!collapsed">Compras</span>
        </a>

        <a href="{{ route('sales.index') }}"
           class="flex min-w-max items-center gap-3 rounded-xl px-4 py-3 text-sm font-semibold transition {{ request()->routeIs('sales.*') ? 'bg-blue-600 text-white shadow-sm' : 'text-slate-600 hover:bg-blue-50 hover:text-blue-700' }}"
           :class="collapsed ? 'lg:justify-center lg:px-0' : ''"
           title="Histórico de vendas">
            <span aria-hidden="true">↑</span>
            <span x-show="!collapsed">Vendas</span>
        </a>

        <div x-show="!collapsed" class="hidden px-4 pb-2 pt-5 text-[11px] font-bold uppercase tracking-[0.18em] text-slate-400 lg:block">
            Gerenciamento
        </div>

        <a href="{{ route('users.index') }}"
           class="flex min-w-max items-center justify-between gap-3 rounded-xl px-4 py-3 text-sm font-semibold transition {{ request()->routeIs('users.*') ? 'bg-blue-600 text-white shadow-sm' : 'text-slate-600 hover:bg-blue-50 hover:text-blue-700' }}"
           :class="collapsed ? 'lg:justify-center lg:px-0' : ''"
           title="Gerenciamento de usuários">
            <span class="flex items-center gap-3"><span aria-hidden="true">●</span><span x-show="!collapsed">Usuários</span></span>
            <span x-show="!collapsed" class="text-xs" aria-hidden="true">›</span>
        </a>

        @if (Auth::user()->is_admin)
            <a href="{{ route('admins.index') }}"
               class="flex min-w-max items-center justify-between gap-3 rounded-xl px-4 py-3 text-sm font-semibold transition {{ request()->routeIs('admins.*') ? 'bg-blue-600 text-white shadow-sm' : 'text-slate-600 hover:bg-blue-50 hover:text-blue-700' }}"
               :class="collapsed ? 'lg:justify-center lg:px-0' : ''"
               title="Gerenciamento de admins">
                <span class="flex items-center gap-3"><span aria-hidden="true">◆</span><span x-show="!collapsed">Admins</span></span>
                <span x-show="!collapsed" class="text-xs" aria-hidden="true">›</span>
            </a>
        @endif
    </nav>

    <div class="hidden border-t border-slate-100 px-5 py-5 lg:block">
        <div x-show="!collapsed" class="mb-3 truncate text-sm font-semibold text-slate-900">{{ Auth::user()->name }}</div>
        <a href="{{ route('profile.edit') }}" class="text-sm text-slate-500 hover:text-blue-600" title="Meu perfil">
            <span x-show="!collapsed">Meu perfil</span>
            <span x-show="collapsed" aria-hidden="true">●</span>
        </a>
        <form method="POST" action="{{ route('logout') }}" class="mt-3">
            @csrf
            <button type="submit" class="text-sm text-slate-500 hover:text-blue-600">Sair</button>
        </form>
    </div>
</aside>