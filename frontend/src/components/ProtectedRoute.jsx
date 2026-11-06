import { Navigate, Outlet, useLocation } from 'react-router-dom';
import { useAuth } from '../lib/auth';

// Pembungkus halaman admin: belum login atau bukan admin -> arahkan ke halaman login.
// Saat token kedaluwarsa (401), interceptor axios mengosongkan auth sehingga komponen ini ikut mengalihkan.
export default function ProtectedRoute() {
    const { token, user, manual } = useAuth();
    const location = useLocation();

    if (!token || !user?.is_admin) {
        return <Navigate to="/admin/login" replace state={manual ? undefined : { from: location.pathname }} />;
    }

    return <Outlet />;
}
