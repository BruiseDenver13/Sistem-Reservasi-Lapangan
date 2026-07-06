<?php

class KategoriLapangan
{
    private $koneksi;

    public function __construct(mysqli $koneksi)
    {
        $this->koneksi = $koneksi;
    }

    public function ambilSemua(): array
    {
        $hasil = mysqli_query($this->koneksi, "SELECT id, nama, keterangan FROM kategori_lapangan ORDER BY id DESC");
        $data  = [];
        while ($baris = mysqli_fetch_assoc($hasil)) {
            $data[] = $baris;
        }
        return $data;
    }

    public function ambilUntukDropdown(): array
    {
        $hasil    = mysqli_query($this->koneksi, "SELECT id, nama FROM kategori_lapangan ORDER BY nama ASC");
        $dropdown = [];
        while ($baris = mysqli_fetch_assoc($hasil)) {
            $dropdown[$baris['id']] = $baris['nama'];
        }
        return $dropdown;
    }

    public function cariById(int $id): ?array
    {
        $stmt = mysqli_prepare($this->koneksi, "SELECT id, nama, keterangan FROM kategori_lapangan WHERE id = ? LIMIT 1");
        mysqli_stmt_bind_param($stmt, 'i', $id);
        mysqli_stmt_execute($stmt);
        $data = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
        mysqli_stmt_close($stmt);
        return $data ?: null;
    }

    public function tambah(string $nama, string $keterangan): bool
    {
        $stmt = mysqli_prepare($this->koneksi, "INSERT INTO kategori_lapangan (nama, keterangan) VALUES (?, ?)");
        mysqli_stmt_bind_param($stmt, 'ss', $nama, $keterangan);
        $berhasil = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        return $berhasil;
    }

    public function update(int $id, string $nama, string $keterangan): bool
    {
        $stmt = mysqli_prepare($this->koneksi, "UPDATE kategori_lapangan SET nama = ?, keterangan = ? WHERE id = ?");
        mysqli_stmt_bind_param($stmt, 'ssi', $nama, $keterangan, $id);
        $berhasil = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        return $berhasil;
    }

    public function sedangDipakai(int $id): bool
    {
        $stmt = mysqli_prepare($this->koneksi, "SELECT id FROM lapangan WHERE id_kategori = ? LIMIT 1");
        mysqli_stmt_bind_param($stmt, 'i', $id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);
        $dipakai = mysqli_stmt_num_rows($stmt) > 0;
        mysqli_stmt_close($stmt);
        return $dipakai;
    }

    public function hapus(int $id): bool
    {
        $stmt = mysqli_prepare($this->koneksi, "DELETE FROM kategori_lapangan WHERE id = ?");
        mysqli_stmt_bind_param($stmt, 'i', $id);
        $berhasil = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        return $berhasil;
    }
}