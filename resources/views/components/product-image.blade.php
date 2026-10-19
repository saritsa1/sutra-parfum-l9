{{-- Foto produk; kalau foto tidak ada/gagal dimuat, baru tampil ilustrasi botol svg --}}
@props(['p'])
@php
    $imageUrl = $p->img_url ?? $p->img ?? null;
    if ($imageUrl && !str_starts_with($imageUrl, 'http://') && !str_starts_with($imageUrl, 'https://')) {
        $imageUrl = asset($imageUrl);
    }
@endphp

@if (!empty($imageUrl))
    <div x-data="{ failed: false }" class="h-full w-full">
        <img x-show="!failed"
             src="{{ $imageUrl }}"
             alt="{{ $p->name }}"
             loading="lazy"
             x-on:error="failed = true"
             class="block h-full w-full bg-soft object-contain p-3">
        <div x-show="failed" x-cloak class="h-full w-full">
            <x-bottle :color="$p->color ?? '#2f5a47'" :shape="$p->id ?? 1" />
        </div>
    </div>
@else
    <div class="h-full w-full">
        <x-bottle :color="$p->color ?? '#2f5a47'" :shape="$p->id ?? 1" />
    </div>
@endif