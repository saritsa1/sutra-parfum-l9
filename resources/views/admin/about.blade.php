@php
    $stack = [
        'Laravel 9 dan Blade Template',
        'Tailwind CSS v3',
        'Vite',
        'Alpine.js',
        'MySQL dan Eloquent ORM',
        'LocalStorage untuk menyimpan keranjang, pesanan, dan ulasan',
    ];
@endphp
<x-admin-layout :title="$title">
    <div class="max-w-xl">
        <h1 class="mb-4 font-serif text-4xl">About</h1>
        <div class="rounded-xl border border-line bg-white p-6">
            <p class="mb-4">Sutra Parfum adalah prototipe toko parfum sederhana. Data produk berasal dari server (Laravel), sedangkan keranjang, pesanan, dan ulasan disimpan di browser.</p>
            <ul class="list-disc space-y-1 pl-5 text-mute">
                @foreach ($stack as $s)
                    <li>{{ $s }}</li>
                @endforeach
            </ul>
        </div>
    </div>
</x-admin-layout>