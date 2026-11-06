import { useState } from 'react';
import { Link, Navigate, useLocation, useNavigate } from 'react-router-dom';
import Icon from '../../components/Icon';
import { useLogin } from '../../hooks/useAuthActions';
import { getErrorMessage, getFieldErrors } from '../../lib/axios';
import { useAuth } from '../../lib/auth';

export default function Login() {
    const navigate = useNavigate();
    const location = useLocation();
    const { token, user } = useAuth();
    const login = useLogin();

    const [email, setEmail] = useState('');
    const [password, setPassword] = useState('');
    const [error, setError] = useState('');

    const tujuan = location.state?.from && location.state.from.startsWith('/admin') ? location.state.from : '/admin';

    // sudah login? langsung ke dashboard
    if (token && user?.is_admin) return <Navigate to={tujuan} replace />;

    const submit = (e) => {
        e.preventDefault();
        setError('');
        if (!email || !password) {
            setError('Email dan password wajib diisi.');
            return;
        }
        login.mutate(
            { email, password },
            {
                onSuccess: () => navigate(tujuan, { replace: true }),
                // error validasi Laravel (422) ada di errors.email
                onError: (err) => setError(getFieldErrors(err).email?.[0] ?? getErrorMessage(err)),
            }
        );
    };

    return (
        <div className="flex min-h-screen items-center justify-center bg-paper px-5">
            <div className="w-full max-w-sm">
                <Link to="/" className="mb-6 flex items-center justify-center gap-2 font-serif text-3xl tracking-wide">
                    <Icon name="sparkles" size={22} className="text-acc" />Sutra Admin
                </Link>

                <form onSubmit={submit} noValidate className="rounded-xl border border-line bg-white p-6 shadow-sm">
                    <h1 className="mb-4 font-serif text-3xl">Masuk</h1>

                    <label htmlFor="email" className="mb-1 block font-medium">Email</label>
                    <input id="email" type="email" value={email} onChange={(e) => setEmail(e.target.value)} autoComplete="username" autoFocus className="field mb-4" />

                    <label htmlFor="password" className="mb-1 block font-medium">Password</label>
                    <input id="password" type="password" value={password} onChange={(e) => setPassword(e.target.value)} autoComplete="current-password" className="field mb-2" />

                    <small className="mb-3 block min-h-[1.25rem] text-red-700" role="alert">{error}</small>

                    <button type="submit" disabled={login.isPending} className="btn btn-solid w-full disabled:opacity-60">
                        {login.isPending ? 'Memproses...' : 'Masuk'}
                    </button>
                </form>

                <p className="mt-4 text-center text-sm text-mute"><Link to="/" className="underline">Kembali ke toko</Link></p>
            </div>
        </div>
    );
}
