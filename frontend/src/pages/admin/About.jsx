export default function About() {
    return (
        <div className="max-w-xl">
            <h1 className="mb-4 font-serif text-4xl">About</h1>
            <div className="rounded-xl border border-line bg-white p-6">
                <p className="mb-4">
                    Sutra Parfum adalah sistem informasi toko parfum dengan arsitektur terpisah (decoupled): data produk, kategori,
                    pesanan, ulasan, dan login admin dikelola oleh API Laravel, sedangkan tampilannya dibangun sebagai React SPA.
                    Keranjang belanja tersimpan di browser pembeli.
                </p>
                <p className="mb-1 font-medium">Backend</p>
                <ul className="mb-4 list-disc space-y-1 pl-5 text-mute">
                    <li>Laravel 9 (REST API)</li>
                    <li>Laravel Sanctum (Bearer token) dan role admin</li>
                    <li>MySQL dan Eloquent ORM</li>
                </ul>
                <p className="mb-1 font-medium">Frontend</p>
                <ul className="list-disc space-y-1 pl-5 text-mute">
                    <li>React 18 dan React Router (SPA)</li>
                    <li>Axios untuk komunikasi dengan API</li>
                    <li>TanStack React Query untuk pengambilan dan cache data</li>
                    <li>React Hot Toast untuk notifikasi</li>
                    <li>Tailwind CSS v3 dan Vite</li>
                </ul>
            </div>
        </div>
    );
}
