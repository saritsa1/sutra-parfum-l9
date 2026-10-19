{{-- Alamat halaman untuk JavaScript (dipakai tombol "Beli sekarang", checkout, dll) --}}
<script>
    window.sutraRoutes = @js([
        'home' => route('home'),
        'cart' => route('cart'),
        'checkout' => route('checkout'),
        'order' => route('order', ['id' => '__ID__']),
    ]);
</script>