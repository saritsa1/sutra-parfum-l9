import { rupiah } from '../lib/format';

// Rincian harga: dipakai di checkout (hitungan sementara) dan halaman pesanan (angka dari server)
export default function PriceSummary({ subtotal, ongkir, layanan, biayaMetode = 0, total }) {
    return (
        <dl className="text-sm">
            <div className="flex justify-between py-1">
                <dt className="text-mute">Subtotal produk</dt>
                <dd>{rupiah(subtotal)}</dd>
            </div>
            <div className="flex justify-between py-1">
                <dt className="text-mute">Ongkos kirim</dt>
                <dd>{rupiah(ongkir)}</dd>
            </div>
            <div className="flex justify-between py-1">
                <dt className="text-mute">Biaya layanan</dt>
                <dd>{rupiah(layanan)}</dd>
            </div>
            {biayaMetode > 0 && (
                <div className="flex justify-between py-1">
                    <dt className="text-mute">Biaya metode bayar</dt>
                    <dd>{rupiah(biayaMetode)}</dd>
                </div>
            )}
            <div className="mt-2 flex justify-between border-t border-line pt-3 text-base font-medium">
                <dt>Total pembayaran</dt>
                <dd>{rupiah(total)}</dd>
            </div>
        </dl>
    );
}
