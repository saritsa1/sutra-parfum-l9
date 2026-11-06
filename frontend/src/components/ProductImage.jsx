import { useEffect, useState } from 'react';
import Bottle from './Bottle';

// Foto produk; kalau foto tidak ada atau gagal dimuat, tampil ilustrasi botol.
// item = { id, name, img_url, color }, pad = kelas padding, contoh: pad="p-3"
export default function ProductImage({ item, pad = '' }) {
    const [failed, setFailed] = useState(false);

    // produk berganti (misal foto baru diunggah): coba muat lagi
    useEffect(() => setFailed(false), [item.img_url]);

    if (!item.img_url || failed) {
        return (
            <div className="h-full w-full">
                <Bottle color={item.color} shape={item.id} />
            </div>
        );
    }

    return (
        <img
            src={item.img_url}
            alt={item.name}
            loading="lazy"
            onError={() => setFailed(true)}
            className={`block h-full w-full bg-soft object-contain ${pad}`}
        />
    );
}
