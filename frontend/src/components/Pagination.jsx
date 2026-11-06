// Paginasi dari respons Laravel: meta.current_page, meta.last_page, meta.total
export default function Pagination({ meta, onPage, label = 'data' }) {
    if (!meta || meta.last_page <= 1) return null;
    const { current_page: page, last_page: last, total } = meta;

    return (
        <div className="mt-4 flex flex-wrap items-center justify-between gap-3 text-sm">
            <p className="text-mute">{`Halaman ${page} dari ${last} (${total} ${label})`}</p>
            <div className="flex flex-wrap gap-1">
                <button type="button" onClick={() => onPage(page - 1)} disabled={page <= 1} className="rounded-lg border border-line bg-white px-3 py-1.5 hover:bg-soft disabled:opacity-40">
                    &laquo; Sebelumnya
                </button>
                {Array.from({ length: last }, (_, i) => i + 1).map((n) => (
                    <button
                        key={n}
                        type="button"
                        onClick={() => onPage(n)}
                        className={`rounded-lg border px-3 py-1.5 ${n === page ? 'border-ink bg-ink text-white' : 'border-line bg-white hover:bg-soft'}`}
                    >
                        {n}
                    </button>
                ))}
                <button type="button" onClick={() => onPage(page + 1)} disabled={page >= last} className="rounded-lg border border-line bg-white px-3 py-1.5 hover:bg-soft disabled:opacity-40">
                    Berikutnya &raquo;
                </button>
            </div>
        </div>
    );
}
