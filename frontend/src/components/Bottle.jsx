// Ilustrasi botol parfum (SVG), dipakai kalau foto produk tidak ada atau gagal dimuat.
// shape: 0 = kotak, 1 = tinggi, 2 = bulat (diambil dari id produk)
export default function Bottle({ color, shape = 0 }) {
    // warna datang dari database, jadi dicek dulu supaya hanya kode warna yang lolos
    const warna = /^#[0-9a-f]{3,8}$/i.test(color ?? '') ? color : '#2f5a47';
    const s = Math.abs(parseInt(shape) || 0) % 3;
    const g = 'g' + warna.slice(1) + s;

    const shapes = [
        { top: 92, hx: 63, body: <rect x="55" y="92" width="90" height="126" rx="10" /> },
        { top: 82, hx: 76, body: <rect x="68" y="82" width="64" height="136" rx="6" /> },
        { top: 93, hx: 64, body: <circle cx="100" cy="155" r="62" /> },
    ][s];

    return (
        <svg viewBox="0 0 200 250" className="block h-full w-full" role="img" aria-label="Botol parfum">
            <defs>
                <linearGradient id={g} x1="0" x2="1">
                    <stop offset="0" stopColor={warna} />
                    <stop offset="1" stopColor={warna} stopOpacity=".72" />
                </linearGradient>
                <radialGradient id={'b' + g} cx=".5" cy=".42" r=".7">
                    <stop offset="0" stopColor="#fff" />
                    <stop offset="1" stopColor="#ebe8e2" />
                </radialGradient>
            </defs>
            <rect width="200" height="250" fill={`url(#b${g})`} />
            <ellipse cx="100" cy="224" rx="56" ry="6" fill="#1f2326" opacity=".1" />
            <g fill={`url(#${g})`}>
                {shapes.body}
                <rect x="92" y={shapes.top - 8} width="16" height="10" />
            </g>
            <rect x="80" y={shapes.top - 48} width="40" height="34" rx="3" fill="#1f2326" />
            <rect x="84" y={shapes.top - 14} width="32" height="6" fill="#c9a66b" />
            <rect x="78" y="140" width="44" height="42" rx="2" fill="#fff" opacity=".6" />
            <rect x="86" y="152" width="28" height="2" fill="#1f2326" opacity=".5" />
            <rect x="90" y="160" width="20" height="2" fill="#1f2326" opacity=".3" />
            <rect x={shapes.hx} y={shapes.top + 16} width="7" height="70" rx="3.5" fill="#fff" opacity=".4" />
        </svg>
    );
}
