@php
    // Kelas tautan menu: aktif = garis bawah gelap, tidak aktif = abu-abu
    $link = fn ($active) => 'flex items-center gap-1.5 border-b py-1 transition-colors ' . ($active ? 'border-ink text-ink' : 'border-transparent text-mute hover:text-ink');
@endphp

<header class="sticky top-0 z-20 border-b border-line bg-paper/90 backdrop-blur">
    <nav aria-label="Menu utama" class="mx-auto flex max-w-5xl flex-wrap items-center justify-between gap-x-6 gap-y-3 px-5 py-4">
        {{-- Logo --}}
        <a href="{{ route('home') }}" class="flex items-center gap-2 font-serif text-3xl tracking-wide">
            <x-icon name="sparkles" :size="22" class="text-acc" />Sutra Parfum
        </a>

        {{-- Menu Navigasi --}}
        <div class="flex flex-wrap gap-x-5 gap-y-1 text-sm md:text-base">
            <a href="{{ route('home') }}" class="{{ $link(request()->routeIs('home')) }}"><x-icon name="layout-dashboard" :size="17" />Dashboard</a>
            <a href="{{ route('cart') }}" class="{{ $link(request()->routeIs('cart')) }}">
                <x-icon name="shopping-bag" :size="17" />Keranjang
                <span x-cloak x-show="$store.cart.totalQty > 0" x-text="$store.cart.totalQty"
                    x-effect="$store.cart.totalQty; $el.classList.remove('bump'); void $el.offsetWidth; $el.classList.add('bump')"
                    class="rounded-full bg-acc px-2 text-xs text-white"></span>
            </a>
            <a href="{{ route('checkout') }}" class="{{ $link(request()->routeIs('checkout')) }}"><x-icon name="credit-card" :size="17" />Checkout</a>
            {{-- Menu Admin: membuka halaman admin (perlu login) yang memakai AdminLayout --}}
            <a href="{{ route('admin.dashboard') }}" class="{{ $link(request()->routeIs('admin.*')) }}"><x-icon name="settings" :size="17" />Admin</a>
        </div>
    </nav>
</header>