<?php

class Pembayaran
{
    private $koneksi;

    public function __construct(mysqli $koneksi)
    {
        $this->koneksi = $koneksi;
    }

    public function ambilSemua(): array
    {
        $hasil = mysqli_query($this->koneksi, "
            SELECT p.id, p.metode, p.bukti_bayar, p.jumlah_bayar, p.tanggal_bayar, p.status_verifikasi,
                   r.kode, r.nama_pemesan
            FROM pembayaran p
            JOIN reservasi r ON p.id_reservasi = r.id
            ORDER BY p.tanggal_bayar DESC
        ");
        $data = [];
        while ($baris = mysqli_fetch_assoc($hasil)) {
            $data[] = $baris;
        }
        return $data;
    }

    public function cariById(int $id): ?array
    {
        $stmt = mysqli_prepare($this->koneksi, "SELECT * FROM pembayaran WHERE id = ? LIMIT 1");
        mysqli_stmt_bind_param($stmt, 'i', $id);
        mysqli_stmt_execute($stmt);
        $data = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
        mysqli_stmt_close($stmt);
        return $data ?: null;
    }

    public function tambah(int $idReservasi, string $metode, ?string $buktiBayar, float $jumlahBayar, string $statusVerifikasi, ?int $idAdmin): bool
    {
        $stmt = mysqli_prepare($this->koneksi, "
            INSERT INTO pembayaran (id_reservasi, metode, bukti_bayar, jumlah_bayar, status_verifikasi, id_admin)
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        mysqli_stmt_bind_param($stmt, 'issdsi', $idReservasi, $metode, $buktiBayar, $jumlahBayar, $statusVerifikasi, $idAdmin);
        $berhasil = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        return $berhasil;
    }

    public function verifikasi(int $id, int $idAdmin): bool
    {
        $stmt = mysqli_prepare($this->koneksi, "
            UPDATE pembayaran SET status_verifikasi = 'diverifikasi', id_admin = ? WHERE id = ?
        ");
        mysqli_stmt_bind_param($stmt, 'ii', $idAdmin, $id);
        $berhasil = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        return $berhasil;
    }

    public function tolak(int $id, int $idAdmin): bool
    {
        $stmt = mysqli_prepare($this->koneksi, "
            UPDATE pembayaran SET status_verifikasi = 'ditolak', id_admin = ? WHERE id = ?
        ");
        mysqli_stmt_bind_param($stmt, 'ii', $idAdmin, $id);
        $berhasil = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        return $berhasil;
    }
}