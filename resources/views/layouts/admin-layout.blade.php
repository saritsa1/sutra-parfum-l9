<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ? $title . ' - ' : '' }}Sutra Admin</title>

    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@400;500&family=Jost:wght@400;500&display=swap">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @include('layouts.sutra-routes')
</head>
{{-- font-serif = Times New Roman untuk seluruh area admin, lining-nums = bentuk angka seragam --}}
<body class="sutra relative flex h-screen bg-paper font-serif lining-nums" x-data="{ sidebarOpen: false }">

    {{-- Sidebar --}}
    @php
        $cls = fn ($active) => 'flex items-center gap-2 rounded-md px-3 py-2 transition-colors ' . ($active ? 'bg-soft text-ink' : 'text-mute hover:bg-soft');
    @endphp
    <aside :class="sidebarOpen ? 'block' : 'hidden'" class="w-64 shrink-0 border-r border-line bg-white max-md:absolute max-md:inset-y-0 max-md:z-10 max-md:shadow-lg md:block">
        <div class="flex items-center gap-2 p-5 font-serif text-2xl">
            <x-icon name="sparkles" :size="20" class="text-acc" />Sutra Admin
        </div>
        <nav aria-label="Menu admin" class="flex flex-col gap-1 px-3">
            <a href="{{ route('admin.dashboard') }}" class="{{ $cls(request()->routeIs('admin.dashboard')) }}" x-on:click="sidebarOpen = false"><x-icon name="layout-dashboard" :size="17" />Dashboard</a>
            <a href="{{ route('product.index') }}" class="{{ $cls(request()->routeIs('product.*')) }}" x-on:click="sidebarOpen = false"><x-icon name="package" :size="17" />Produk</a>
            <a href="{{ route('admin.about') }}" class="{{ $cls(request()->routeIs('admin.about')) }}" x-on:click="sidebarOpen = false"><x-icon name="info" :size="17" />About</a>
            <a href="{{ route('home') }}" class="mt-4 flex items-center gap-2 rounded-md px-3 py-2 text-mute hover:bg-soft"><x-icon name="store" :size="17" />Lihat toko</a>
            {{-- Keluar: tambahan karena halaman admin dilindungi login (Breeze) --}}
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="flex w-full items-center gap-2 rounded-md px-3 py-2 text-left text-mute hover:bg-soft"><x-icon name="log-out" :size="17" />Keluar</button>
            </form>
        </nav>
    </aside>

    <div class="flex flex-1 flex-col overflow-hidden">
        {{-- Top bar khusus mobile --}}
        <div class="flex items-center justify-between border-b border-line bg-white p-4 md:hidden">
            <h1 class="font-serif text-xl">Sutra Admin</h1>
            <button type="button" x-on:click="sidebarOpen = !sidebarOpen" aria-label="Buka menu" class="rounded border border-line p-2"><x-icon name="menu" :size="18" /></button>
        </div>
        <main class="flex-1 overflow-y-auto p-6">{{ $slot }}</main>
        <footer class="border-t border-line bg-white p-4 text-center text-sm text-mute">&copy; 2025 Sutra Admin v1.0.0</footer>
    </div>
</body>
</html>