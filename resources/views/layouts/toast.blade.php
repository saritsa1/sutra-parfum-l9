{{-- Notifikasi "... masuk keranjang" --}}
<div aria-live="polite" class="pointer-events-none fixed inset-x-0 bottom-6 z-30 flex justify-center px-4">
    <template x-if="$store.cart.toast">
        <div class="toast pointer-events-auto flex items-center gap-3 rounded-full bg-ink py-2.5 pl-4 pr-5 text-sm text-white shadow-lg">
            <x-icon name="check" :size="17" class="text-[#c9a66b]" />
            <span x-text="$store.cart.toast.text"></span>
            <a href="{{ route('cart') }}" class="underline underline-offset-4">Lihat keranjang</a>
        </div>
    </template>
</div>