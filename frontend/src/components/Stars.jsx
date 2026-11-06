import Icon from './Icon';

// Bintang rating (hanya tampilan). value = angka 0-5
export default function Stars({ value = 0, size = 14 }) {
    const bulat = Math.round(Number(value));
    return (
        <span className="inline-flex text-acc" role="img" aria-label={`Rating ${Number(value).toFixed(1)} dari 5`}>
            {[1, 2, 3, 4, 5].map((n) => (
                <Icon key={n} name="star" size={size} className={n <= bulat ? 'fill-current' : 'text-line'} />
            ))}
        </span>
    );
}
