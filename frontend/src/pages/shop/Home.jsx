import ErrorBox from '../../components/ErrorBox';
import Icon from '../../components/Icon';
import ProductCard from '../../components/ProductCard';
import { useCategoryContext } from '../../context/CategoryContext';
import { useProductContext } from '../../context/ProductContext';

export default function Home() {
    // Produk dan filter dari ProductContext, kategori dari CategoryContext (dua context yang terpisah)
    const { products, filters, setFilters, resetFilters, isLoading, error: productError, refetch: refetchProducts } = useProductContext();
    const { categories, error: categoryError, refetch: refetchCategories } = useCategoryContext();
    const q = filters.s;
    const error = productError || categoryError;
    const refetch = () => { refetchProducts(); refetchCategories(); };

    return (
        <div>
            {q === '' && (
                <section className="grid items-center gap-10 pb-14 pt-4 md:grid-cols-[1.3fr_.7fr]">
                    <div>
                        <h1 className="rise mb-4 font-serif text-5xl leading-tight md:text-6xl md:leading-tight">
                            Wangi yang tenang, bertahan sepanjang hari.
                        </h1>
                        <p className="rise max-w-[46ch] text-lg text-mute" style={{ animationDelay: '.15s' }}>
                            Enam aroma pilihan dari bahan yang diracik perlahan. Tanpa ramai, hanya yang perlu.
                        </p>
                    </div>
                </section>
            )}

            <h2 className="mb-4 font-serif text-3xl">{q !== '' ? `Hasil untuk "${q}"` : 'Dashboard Produk'}</h2>

            {/* Filter golongan aroma */}
            <div className="mb-8 flex flex-wrap gap-2" role="group" aria-label="Filter golongan aroma">
                {[{ id: '', name: 'Semua' }, ...categories].map((c) => (
                    <button
                        key={c.id || 'semua'}
                        type="button"
                        onClick={() => setFilters({ category_id: c.id })}
                        aria-pressed={filters.category_id === c.id}
                        className={`rounded-full border px-4 py-1 text-sm transition-colors ${filters.category_id === c.id ? 'border-ink bg-ink text-white' : 'border-line text-mute hover:border-ink hover:text-ink'}`}
                    >
                        {c.name}
                    </button>
                ))}
            </div>

            {isLoading && <p className="py-16 text-center text-mute" aria-live="polite">Memuat produk...</p>}

            {error && <ErrorBox error={error} onRetry={refetch} />}

            {!isLoading && !error && products.length === 0 && (
                <div className="py-16 text-center text-mute">
                    <Icon name="search" size={40} className="mx-auto mb-3" />
                    <p>Tidak ada parfum yang cocok. Coba kata lain atau golongan lain.</p>
                    <button type="button" onClick={resetFilters} className="btn btn-ghost mt-5">Tampilkan semua</button>
                </div>
            )}

            {products.length > 0 && (
                <div className="grid grid-cols-[repeat(auto-fill,minmax(210px,1fr))] gap-6">
                    {products.map((item) => (
                        <ProductCard key={item.id} item={item} />
                    ))}
                </div>
            )}
        </div>
    );
}
