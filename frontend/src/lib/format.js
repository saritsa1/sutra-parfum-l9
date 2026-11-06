export const rupiah = (n) => 'Rp ' + Number(n || 0).toLocaleString('id-ID');

export const tanggal = (value) =>
    value ? new Date(value).toLocaleString('id-ID', { dateStyle: 'medium', timeStyle: 'short' }) : '-';
