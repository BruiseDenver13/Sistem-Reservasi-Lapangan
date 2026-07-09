<?php

class Reservasi
{
    private $koneksi;

    public function __construct(mysqli $koneksi)
    {
        $this->koneksi = $koneksi;
    }

    public function ambilSemua(): array
    {
        $hasil = mysqli_query($this->koneksi, "
            SELECT r.id, r.kode, r.nama_pemesan, r.no_hp, r.total_bayar, r.status, r.keterangan, r.dibuat_pada,
                   j.tanggal, j.jam_mulai, j.jam_selesai,
                   l.nama AS nama_lapangan
            FROM reservasi r
            JOIN jadwal j ON r.id_jadwal = j.id
            JOIN lapangan l ON j.id_lapangan = l.id
            ORDER BY r.dibuat_pada DESC
        ");
        $data = [];
        while ($baris = mysqli_fetch_assoc($hasil)) {
            $data[] = $baris;
        }
        return $data;
    }

    public function cariById(int $id): ?array
    {
        $stmt = mysqli_prepare($this->koneksi, "SELECT * FROM reservasi WHERE id = ? LIMIT 1");
        mysqli_stmt_bind_param($stmt, 'i', $id);
        mysqli_stmt_execute($stmt);
        $data = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
        mysqli_stmt_close($stmt);
        return $data ?: null;
    }

    public function ambilJadwalTersedia(): array
    {
        $hasil = mysqli_query($this->koneksi, "
            SELECT j.id, j.tanggal, j.jam_mulai, j.jam_selesai, l.nama AS nama_lapangan, l.harga_per_jam
            FROM jadwal j
            JOIN lapangan l ON j.id_lapangan = l.id
            WHERE j.status = 'tersedia'
            ORDER BY j.tanggal ASC, j.jam_mulai ASC
        ");
        $data = [];
        while ($baris = mysqli_fetch_assoc($hasil)) {
            $data[] = $baris;
        }
        return $data;
    }

    public function ambilJadwalUntukReservasi(int $idJadwal): ?array
    {
        $stmt = mysqli_prepare($this->koneksi, "
            SELECT j.id, j.id_lapangan, j.tanggal, j.jam_mulai, j.jam_selesai, j.status, l.harga_per_jam
            FROM jadwal j
            JOIN lapangan l ON j.id_lapangan = l.id
            WHERE j.id = ?
            LIMIT 1
        ");
        mysqli_stmt_bind_param($stmt, 'i', $idJadwal);
        mysqli_stmt_execute($stmt);
        $data = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
        mysqli_stmt_close($stmt);
        return $data ?: null;
    }

    public function buatKode(): string
    {
        return 'RSV' . date('Ymd') . strtoupper(substr(bin2hex(random_bytes(3)), 0, 5));
    }

    public function tambah(int $idJadwal, string $namaPemesan, string $noHp, float $totalBayar, string $keterangan, int $idAdmin): bool
    {
        $kode = $this->buatKode();
        $stmt = mysqli_prepare($this->koneksi, "
            INSERT INTO reservasi (kode, id_jadwal, nama_pemesan, no_hp, total_bayar, status, keterangan, id_admin)
            VALUES (?, ?, ?, ?, ?, 'dikonfirmasi', ?, ?)
        ");
        mysqli_stmt_bind_param($stmt, 'sissdsi', $kode, $idJadwal, $namaPemesan, $noHp, $totalBayar, $keterangan, $idAdmin);
        $berhasil = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        return $berhasil;
    }

    public function updateStatus(int $id, string $status): bool
    {
        $stmt = mysqli_prepare($this->koneksi, "UPDATE reservasi SET status = ? WHERE id = ?");
        mysqli_stmt_bind_param($stmt, 'si', $status, $id);
        $berhasil = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        return $berhasil;
    }

    public function hapus(int $id): bool
    {
        $stmt = mysqli_prepare($this->koneksi, "DELETE FROM reservasi WHERE id = ?");
        mysqli_stmt_bind_param($stmt, 'i', $id);
        $berhasil = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        return $berhasil;
    }

    public function tambahPublik(int $idJadwal, string $namaPemesan, string $noHp, float $totalBayar, string $keterangan): string|false
{
    $kode = $this->buatKode();
    $stmt = mysqli_prepare($this->koneksi, "
        INSERT INTO reservasi (kode, id_jadwal, nama_pemesan, no_hp, total_bayar, status, keterangan, id_admin)
        VALUES (?, ?, ?, ?, ?, 'menunggu', ?, NULL)
    ");
    mysqli_stmt_bind_param($stmt, 'sissds', $kode, $idJadwal, $namaPemesan, $noHp, $totalBayar, $keterangan);
    $berhasil = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    return $berhasil ? $kode : false;
}

public function cariByKode(string $kode): ?array
{
    $stmt = mysqli_prepare($this->koneksi, "
        SELECT r.kode, r.nama_pemesan, r.no_hp, r.total_bayar, r.status,
               j.tanggal, j.jam_mulai, j.jam_selesai, l.nama AS nama_lapangan
        FROM reservasi r
        JOIN jadwal j ON r.id_jadwal = j.id
        JOIN lapangan l ON j.id_lapangan = l.id
        WHERE r.kode = ?
        LIMIT 1
    ");
    mysqli_stmt_bind_param($stmt, 's', $kode);
    mysqli_stmt_execute($stmt);
    $data = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    mysqli_stmt_close($stmt);
    return $data ?: null;
 }

}