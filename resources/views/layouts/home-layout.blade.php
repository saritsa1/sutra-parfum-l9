<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ? $title . ' - ' : '' }}Sutra Parfum</title>

    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@400;500&family=Jost:wght@400;500&display=swap">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @include('layouts.sutra-routes')
</head>
<body class="sutra flex min-h-screen flex-col" x-data>

    {{-- Header/Navbar --}}
    @include('layouts.navigationhome')

    {{-- Search & Filter --}}
    @php
        $q = request('q', '');
        $cat = request()->routeIs('home') ? request('cat', 'Semua') : 'Semua';
    @endphp
    <header class="border-b border-line bg-soft">
        <div class="mx-auto flex max-w-5xl flex-col items-center justify-between gap-2 px-5 py-4 md:flex-row">
            <form action="{{ route('home') }}" method="GET" role="search" class="relative w-full md:w-1/3">
                @if ($cat !== 'Semua')
                    <input type="hidden" name="cat" value="{{ $cat }}">
                @endif
                <x-icon name="search" :size="17" class="pointer-events-none absolute left-3.5 top-1/2 -translate-y-1/2 text-mute" />
                {{-- Mengetik: hasil dicari otomatis setelah berhenti mengetik; Enter juga bisa --}}
                <input type="text" name="q" value="{{ $q }}" placeholder="Cari nama, aroma, golongan" aria-label="Cari parfum"
                    class="w-full rounded-lg border border-line bg-white py-2 pl-10 pr-9 focus:border-line"
                    x-init="if (new URLSearchParams(location.search).has('q')) { $el.focus(); $el.setSelectionRange($el.value.length, $el.value.length) }"
                    x-on:input.debounce.500ms="$el.form.submit()">
                @if ($q !== '')
                    <a href="{{ route('home', $cat !== 'Semua' ? ['cat' => $cat] : []) }}" aria-label="Hapus pencarian" class="absolute right-3 top-1/2 -translate-y-1/2 text-mute hover:text-ink">
                        <x-icon name="x" :size="16" />
                    </a>
                @endif
            </form>
        </div>
    </header>

    {{-- Main Section --}}
    <main class="page mx-auto w-full max-w-5xl flex-1 px-5 py-10">
        {{ $slot }}
    </main>

    {{-- Footer --}}
    <footer class="border-t border-line py-6 text-center text-sm text-mute">
        &copy; 2025 Sutra Parfum | Version 1.0
    </footer>

    @include('layouts.toast')
</body>
</html>