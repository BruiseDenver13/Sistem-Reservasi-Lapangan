<?php

class Jadwal
{
    private $koneksi;

    public function __construct(mysqli $koneksi)
    {
        $this->koneksi = $koneksi;
    }

    public function ambilSemua(): array
    {
        $hasil = mysqli_query($this->koneksi, "
            SELECT j.id, j.id_lapangan, j.tanggal, j.jam_mulai, j.jam_selesai, j.status,
                   l.nama AS nama_lapangan
            FROM jadwal j
            JOIN lapangan l ON j.id_lapangan = l.id
            ORDER BY j.tanggal DESC, j.jam_mulai ASC
        ");
        $data = [];
        while ($baris = mysqli_fetch_assoc($hasil)) {
            $data[] = $baris;
        }
        return $data;
    }

    public function cariById(int $id): ?array
    {
        $stmt = mysqli_prepare($this->koneksi, "SELECT * FROM jadwal WHERE id = ? LIMIT 1");
        mysqli_stmt_bind_param($stmt, 'i', $id);
        mysqli_stmt_execute($stmt);
        $data = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
        mysqli_stmt_close($stmt);
        return $data ?: null;
    }

    public function ambilByLapanganTanggal(int $idLapangan, string $tanggal): array
    {
        $stmt = mysqli_prepare($this->koneksi, "
            SELECT * FROM jadwal WHERE id_lapangan = ? AND tanggal = ? ORDER BY jam_mulai ASC
        ");
        mysqli_stmt_bind_param($stmt, 'is', $idLapangan, $tanggal);
        mysqli_stmt_execute($stmt);
        $hasil = mysqli_stmt_get_result($stmt);
        $data  = [];
        while ($baris = mysqli_fetch_assoc($hasil)) {
            $data[] = $baris;
        }
        mysqli_stmt_close($stmt);
        return $data;
    }

    public function cekBentrok(int $idLapangan, string $tanggal, string $jamMulai, string $jamSelesai, ?int $kecualiId = null): bool
    {
        if ($kecualiId !== null) {
            $stmt = mysqli_prepare($this->koneksi, "
                SELECT id FROM jadwal
                WHERE id_lapangan = ? AND tanggal = ? AND id != ?
                AND jam_mulai < ? AND jam_selesai > ?
                LIMIT 1
            ");
            mysqli_stmt_bind_param($stmt, 'isiss', $idLapangan, $tanggal, $kecualiId, $jamSelesai, $jamMulai);
        } else {
            $stmt = mysqli_prepare($this->koneksi, "
                SELECT id FROM jadwal
                WHERE id_lapangan = ? AND tanggal = ?
                AND jam_mulai < ? AND jam_selesai > ?
                LIMIT 1
            ");
            mysqli_stmt_bind_param($stmt, 'isss', $idLapangan, $tanggal, $jamSelesai, $jamMulai);
        }
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);
        $bentrok = mysqli_stmt_num_rows($stmt) > 0;
        mysqli_stmt_close($stmt);
        return $bentrok;
    }

    public function tambah(int $idLapangan, string $tanggal, string $jamMulai, string $jamSelesai, string $status): bool
    {
        $stmt = mysqli_prepare($this->koneksi, "
            INSERT INTO jadwal (id_lapangan, tanggal, jam_mulai, jam_selesai, status) VALUES (?, ?, ?, ?, ?)
        ");
        mysqli_stmt_bind_param($stmt, 'issss', $idLapangan, $tanggal, $jamMulai, $jamSelesai, $status);
        $berhasil = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        return $berhasil;
    }

    public function update(int $id, int $idLapangan, string $tanggal, string $jamMulai, string $jamSelesai, string $status): bool
    {
        $stmt = mysqli_prepare($this->koneksi, "
            UPDATE jadwal SET id_lapangan=?, tanggal=?, jam_mulai=?, jam_selesai=?, status=? WHERE id=?
        ");
        mysqli_stmt_bind_param($stmt, 'issssi', $idLapangan, $tanggal, $jamMulai, $jamSelesai, $status, $id);
        $berhasil = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        return $berhasil;
    }

    public function sedangDipakai(int $id): bool
    {
        $stmt = mysqli_prepare($this->koneksi, "SELECT id FROM reservasi WHERE id_jadwal = ? LIMIT 1");
        mysqli_stmt_bind_param($stmt, 'i', $id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);
        $dipakai = mysqli_stmt_num_rows($stmt) > 0;
        mysqli_stmt_close($stmt);
        return $dipakai;
    }

    public function hapus(int $id): bool
    {
        $stmt = mysqli_prepare($this->koneksi, "DELETE FROM jadwal WHERE id = ?");
        mysqli_stmt_bind_param($stmt, 'i', $id);
        $berhasil = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        return $berhasil;
    }
}