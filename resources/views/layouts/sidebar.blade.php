<aside class="w-full shrink-0 border-b border-slate-800 bg-slate-950 text-slate-100 lg:min-h-screen lg:w-72 lg:border-b-0 lg:border-r">
    <div class="flex items-center justify-between px-6 py-5 lg:block">
        <a href="{{ route('home') }}" class="text-xl font-black tracking-tight text-white">
            Marketplace
        </a>
        <span class="hidden mt-1 text-xs uppercase tracking-[0.2em] text-slate-500 lg:block">Painel</span>
    </div>

    <nav class="flex gap-2 overflow-x-auto px-4 pb-4 lg:block lg:space-y-1 lg:px-4" aria-label="Navegação principal">
        <a href="{{ route('home') }}"
           class="flex min-w-max items-center gap-3 rounded-lg px-4 py-3 text-sm font-medium transition {{ request()->routeIs('home') ? 'bg-blue-600 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
            <span aria-hidden="true">⌂</span>
            <span>Home</span>
        </a>

        <a href="{{ route('products.index') }}"
           class="flex min-w-max items-center gap-3 rounded-lg px-4 py-3 text-sm font-medium transition {{ request()->routeIs('products.*') ? 'bg-blue-600 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
            <span aria-hidden="true">▣</span>
            <span>Gerenciamento de produtos</span>
        </a>

        <a href="{{ route('purchases.index') }}"
           class="flex min-w-max items-center gap-3 rounded-lg px-4 py-3 text-sm font-medium transition {{ request()->routeIs('purchases.*') ? 'bg-blue-600 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
            <span aria-hidden="true">↓</span>
            <span>Histórico de compras</span>
        </a>

        <a href="{{ route('sales.index') }}"
           class="flex min-w-max items-center gap-3 rounded-lg px-4 py-3 text-sm font-medium transition {{ request()->routeIs('sales.*') ? 'bg-blue-600 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
            <span aria-hidden="true">↑</span>
            <span>Histórico de vendas</span>
        </a>

        @if (Auth::user()->is_admin)
            <div class="hidden pt-5 pb-2 px-4 text-[11px] font-bold uppercase tracking-[0.18em] text-slate-500 lg:block">
                Administração
            </div>

            <a href="{{ route('users.index') }}"
               class="flex min-w-max items-center gap-3 rounded-lg px-4 py-3 text-sm font-medium transition {{ request()->routeIs('users.*') ? 'bg-blue-600 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                <span aria-hidden="true">●</span>
                <span>Gerenciamento de usuários</span>
            </a>

            <a href="{{ route('admins.index') }}"
               class="flex min-w-max items-center gap-3 rounded-lg px-4 py-3 text-sm font-medium transition {{ request()->routeIs('admins.*') ? 'bg-blue-600 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                <span aria-hidden="true">◆</span>
                <span>Gerenciamento de admins</span>
            </a>
        @endif
    </nav>

    <div class="hidden border-t border-slate-800 px-6 py-5 lg:block">
        <div class="mb-3 truncate text-sm font-semibold text-white">{{ Auth::user()->name }}</div>
        <a href="{{ route('profile.edit') }}" class="text-sm text-slate-400 hover:text-white">Meu perfil</a>
        <form method="POST" action="{{ route('logout') }}" class="mt-3">
            @csrf
            <button type="submit" class="text-sm text-slate-400 hover:text-white">Sair</button>
        </form>
    </div>
</aside>