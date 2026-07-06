<?php

class Lapangan
{
    private $koneksi;

    public function __construct(mysqli $koneksi)
    {
        $this->koneksi = $koneksi;
    }

    public function ambilSemua(): array
    {
        $hasil = mysqli_query($this->koneksi, "
            SELECT l.id, l.nama, l.harga_per_jam, l.foto, l.keterangan, l.status,
                   k.nama AS nama_kategori
            FROM lapangan l
            JOIN kategori_lapangan k ON l.id_kategori = k.id
            ORDER BY l.id DESC
        ");
        $data = [];
        while ($baris = mysqli_fetch_assoc($hasil)) {
            $data[] = $baris;
        }
        return $data;
    }

    public function cariById(int $id): ?array
    {
        $stmt = mysqli_prepare($this->koneksi, "SELECT * FROM lapangan WHERE id = ? LIMIT 1");
        mysqli_stmt_bind_param($stmt, 'i', $id);
        mysqli_stmt_execute($stmt);
        $data = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
        mysqli_stmt_close($stmt);
        return $data ?: null;
    }

    public function ambilYangAktif(): array
    {
        $hasil = mysqli_query($this->koneksi, "
            SELECT l.id, l.nama, l.harga_per_jam, l.foto, k.nama AS nama_kategori
            FROM lapangan l
            JOIN kategori_lapangan k ON l.id_kategori = k.id
            WHERE l.status = 'aktif'
            ORDER BY l.nama ASC
        ");
        $data = [];
        while ($baris = mysqli_fetch_assoc($hasil)) {
            $data[] = $baris;
        }
        return $data;
    }

    public function tambah(int $idKategori, string $nama, float $hargaPerJam, ?string $foto, string $keterangan, string $status): bool
    {
        $stmt = mysqli_prepare($this->koneksi, "
            INSERT INTO lapangan (id_kategori, nama, harga_per_jam, foto, keterangan, status)
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        mysqli_stmt_bind_param($stmt, 'isdsss', $idKategori, $nama, $hargaPerJam, $foto, $keterangan, $status);
        $berhasil = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        return $berhasil;
    }

    public function update(int $id, int $idKategori, string $nama, float $hargaPerJam, ?string $foto, string $keterangan, string $status): bool
    {
        if ($foto !== null) {
            $stmt = mysqli_prepare($this->koneksi, "
                UPDATE lapangan SET id_kategori=?, nama=?, harga_per_jam=?, foto=?, keterangan=?, status=? WHERE id=?
            ");
            mysqli_stmt_bind_param($stmt, 'isdsssi', $idKategori, $nama, $hargaPerJam, $foto, $keterangan, $status, $id);
        } else {
            $stmt = mysqli_prepare($this->koneksi, "
                UPDATE lapangan SET id_kategori=?, nama=?, harga_per_jam=?, keterangan=?, status=? WHERE id=?
            ");
            mysqli_stmt_bind_param($stmt, 'isdssi', $idKategori, $nama, $hargaPerJam, $keterangan, $status, $id);
        }
        $berhasil = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        return $berhasil;
    }

    public function sedangDipakai(int $id): bool
    {
        $stmt = mysqli_prepare($this->koneksi, "SELECT id FROM jadwal WHERE id_lapangan = ? LIMIT 1");
        mysqli_stmt_bind_param($stmt, 'i', $id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);
        $dipakai = mysqli_stmt_num_rows($stmt) > 0;
        mysqli_stmt_close($stmt);
        return $dipakai;
    }

    public function hapus(int $id): bool
    {
        $stmt = mysqli_prepare($this->koneksi, "DELETE FROM lapangan WHERE id = ?");
        mysqli_stmt_bind_param($stmt, 'i', $id);
        $berhasil = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        return $berhasil;
    }
}