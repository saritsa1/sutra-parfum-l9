{{-- Rincian harga. Semua nilai berupa ekspresi Alpine, contoh: subtotal="$store.cart.totalPrice" --}}
@props(['subtotal', 'ongkir', 'layanan', 'biaya', 'total'])
<dl class="text-sm">
    <div class="flex justify-between py-1">
        <dt class="text-mute">Subtotal produk</dt>
        <dd x-text="sutra.rupiah({{ $subtotal }})"></dd>
    </div>
    <div class="flex justify-between py-1">
        <dt class="text-mute">Ongkos kirim</dt>
        <dd x-text="sutra.rupiah({{ $ongkir }})"></dd>
    </div>
    <div class="flex justify-between py-1">
        <dt class="text-mute">Biaya layanan</dt>
        <dd x-text="sutra.rupiah({{ $layanan }})"></dd>
    </div>
    <template x-if="{{ $biaya }} > 0">
        <div class="flex justify-between py-1">
            <dt class="text-mute">Biaya metode bayar</dt>
            <dd x-text="sutra.rupiah({{ $biaya }})"></dd>
        </div>
    </template>
    <div class="mt-2 flex justify-between border-t border-line pt-3 text-base font-medium">
        <dt>Total pembayaran</dt>
        <dd x-text="sutra.rupiah({{ $total }})"></dd>
    </div>
</dl>