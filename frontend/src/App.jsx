import { Route, Routes } from 'react-router-dom';
import ProtectedRoute from './components/ProtectedRoute';
import AdminLayout from './layouts/AdminLayout';
import ShopLayout from './layouts/ShopLayout';
import NotFound from './pages/NotFound';
import About from './pages/admin/About';
import Dashboard from './pages/admin/Dashboard';
import Login from './pages/admin/Login';
import Orders from './pages/admin/Orders';
import ProductForm from './pages/admin/ProductForm';
import Products from './pages/admin/Products';
import Cart from './pages/shop/Cart';
import Checkout from './pages/shop/Checkout';
import Home from './pages/shop/Home';
import Order from './pages/shop/Order';
import ProductDetail from './pages/shop/ProductDetail';

export default function App() {
    return (
        <Routes>
            {/* Toko (publik) */}
            <Route element={<ShopLayout />}>
                <Route index element={<Home />} />
                <Route path="produk/:slug" element={<ProductDetail />} />
                <Route path="keranjang" element={<Cart />} />
                <Route path="checkout" element={<Checkout />} />
                <Route path="pesanan/:orderNo" element={<Order />} />
                <Route path="*" element={<NotFound />} />
            </Route>

            {/* Admin: login terbuka, sisanya wajib login sebagai admin */}
            <Route path="admin/login" element={<Login />} />
            <Route path="admin" element={<ProtectedRoute />}>
                <Route element={<AdminLayout />}>
                    <Route index element={<Dashboard />} />
                    <Route path="produk" element={<Products />} />
                    <Route path="produk/baru" element={<ProductForm />} />
                    <Route path="produk/:id/edit" element={<ProductForm />} />
                    <Route path="pesanan" element={<Orders />} />
                    <Route path="about" element={<About />} />
                </Route>
            </Route>
        </Routes>
    );
}
