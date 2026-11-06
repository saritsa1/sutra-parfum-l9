import { Link } from 'react-router-dom';

export default function NotFound() {
    return (
        <div className="mx-auto max-w-md py-20 text-center">
            <h1 className="mb-2 font-serif text-6xl">404</h1>
            <p className="mb-6 text-mute">Halaman yang Anda cari tidak ditemukan.</p>
            <Link to="/" className="btn btn-solid">Kembali ke beranda</Link>
        </div>
    );
}
