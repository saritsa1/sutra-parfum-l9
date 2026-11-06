import { getErrorMessage } from '../lib/axios';

// Kotak pesan error. error = objek error dari React Query/axios, onRetry = fungsi "Coba lagi"
export default function ErrorBox({ error, message, onRetry, className = '' }) {
    return (
        <div className={`rounded-lg bg-red-50 p-4 text-sm text-red-800 ${className}`} role="alert">
            {message ?? getErrorMessage(error)}
            {onRetry && (
                <button type="button" onClick={onRetry} className="ml-2 underline">
                    Coba lagi
                </button>
            )}
        </div>
    );
}
