@php
    $menu = [
        ['label' => 'Beranda', 'route' => 'home', 'match' => 'home'],
        ['label' => 'Produk', 'route' => 'produk', 'match' => 'produk'],
        ['label' => 'Tentang', 'route' => 'tentang', 'match' => 'tentang'],
        ['label' => 'Kontak', 'route' => 'kontak', 'match' => 'kontak'],
    ];
@endphp

<header x-data="{ open: false }" class="sticky top-0 z-20 border-b border-[#e4e2dd] bg-[#faf9f7]/95 backdrop-blur">
    <div class="mx-auto flex max-w-5xl items-center justify-between px-5 py-4">
        <a href="{{ route('home') }}" class="text-xl text-[#5a2d3f]" style="font-family: Georgia, 'Times New Roman', serif;">
            ✦ Sutra Parfum
        </a>

        {{-- Menu desktop --}}
        <nav class="hidden items-center gap-6 md:flex">
            @foreach ($menu as $item)
                <a href="{{ route($item['route']) }}"
                   class="text-sm transition {{ request()->routeIs($item['match']) ? 'font-medium text-[#5a2d3f]' : 'text-[#5f6469] hover:text-[#1f2326]' }}">
                    {{ $item['label'] }}
                </a>
            @endforeach

            @auth
                <a href="{{ route('dashboard') }}" class="text-sm text-[#5f6469] hover:text-[#1f2326]">Dashboard</a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="rounded-full border border-[#e4e2dd] px-4 py-1.5 text-sm text-[#1f2326] hover:bg-white">
                        Keluar
                    </button>
                </form>
            @else
                <a href="{{ route('login') }}" class="rounded-full border border-[#5a2d3f] px-4 py-1.5 text-sm text-[#5a2d3f] hover:bg-white">Masuk</a>
                <a href="{{ route('register') }}" class="rounded-full bg-[#5a2d3f] px-4 py-1.5 text-sm text-white hover:opacity-90">Daftar</a>
            @endauth
        </nav>

        {{-- Tombol hamburger (mobile) --}}
        <button @click="open = !open" class="md:hidden" aria-label="Menu">
            <svg class="h-6 w-6 text-[#1f2326]" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                <path x-show="!open" stroke-linecap="round" d="M4 7h16M4 12h16M4 17h16" />
                <path x-show="open" stroke-linecap="round" d="M6 6l12 12M18 6L6 18" />
            </svg>
        </button>
    </div>

    {{-- Menu mobile --}}
    <div x-show="open" x-cloak class="border-t border-[#e4e2dd] px-5 pb-4 md:hidden">
        @foreach ($menu as $item)
            <a href="{{ route($item['route']) }}"
               class="block py-2 text-sm {{ request()->routeIs($item['match']) ? 'font-medium text-[#5a2d3f]' : 'text-[#5f6469]' }}">
                {{ $item['label'] }}
            </a>
        @endforeach

        @auth
            <a href="{{ route('dashboard') }}" class="block py-2 text-sm text-[#5f6469]">Dashboard</a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="py-2 text-sm text-[#5f6469]">Keluar</button>
            </form>
        @else
            <div class="mt-2 flex gap-3">
                <a href="{{ route('login') }}" class="rounded-full border border-[#5a2d3f] px-4 py-1.5 text-sm text-[#5a2d3f]">Masuk</a>
                <a href="{{ route('register') }}" class="rounded-full bg-[#5a2d3f] px-4 py-1.5 text-sm text-white">Daftar</a>
            </div>
        @endauth
    </div>
</header>