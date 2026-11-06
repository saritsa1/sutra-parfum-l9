import React from 'react';
import ReactDOM from 'react-dom/client';
import { BrowserRouter } from 'react-router-dom';
import { MutationCache, QueryClient, QueryClientProvider } from '@tanstack/react-query';
import toast, { Toaster } from 'react-hot-toast';
import App from './App.jsx';
import { getErrorMessage } from './lib/axios';
import { CategoryProvider } from './context/CategoryContext';
import { CartProvider } from './lib/cart';
import './css/app.css';

const queryClient = new QueryClient({
    // Semua mutation (simpan, hapus, checkout, dst.) yang gagal otomatis memunculkan toast error.
    mutationCache: new MutationCache({
        onError: (error) => toast.error(getErrorMessage(error), { id: 'mutation-error' }),
    }),
    defaultOptions: {
        queries: { retry: 1, refetchOnWindowFocus: false },
    },
});

ReactDOM.createRoot(document.getElementById('root')).render(
    <React.StrictMode>
        <QueryClientProvider client={queryClient}>
            {/* CategoryProvider satu kali di sini: toko dan admin memakai daftar kategori yang sama */}
            <CategoryProvider>
                <BrowserRouter future={{ v7_startTransition: true, v7_relativeSplatPath: true }}>
                    <CartProvider>
                        <App />
                        {/* Toaster harus di dalam BrowserRouter: toast keranjang berisi <Link> */}
                        <Toaster
                            position="bottom-center"
                            toastOptions={{
                                duration: 3000,
                                style: { background: '#1f2326', color: '#fff', borderRadius: '9999px', padding: '10px 20px', fontSize: '14px' },
                                success: { iconTheme: { primary: '#c9a66b', secondary: '#1f2326' } },
                            }}
                        />
                    </CartProvider>
                </BrowserRouter>
            </CategoryProvider>
        </QueryClientProvider>
    </React.StrictMode>
);
