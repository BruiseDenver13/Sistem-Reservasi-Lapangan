<?php

class Admin
{
    /** @var mysqli */
    private $koneksi;

    public function __construct(mysqli $koneksi)
    {
        $this->koneksi = $koneksi;
    }

    public function cariByUsername(string $username): ?array
    {
        $stmt = mysqli_prepare($this->koneksi, "SELECT id, username, password, nama, role FROM admin WHERE username = ? LIMIT 1");
        mysqli_stmt_bind_param($stmt, 's', $username);
        mysqli_stmt_execute($stmt);
        $hasil = mysqli_stmt_get_result($stmt);
        $data  = mysqli_fetch_assoc($hasil);
        mysqli_stmt_close($stmt);

        return $data ?: null;
    }

    public function cariById(int $id): ?array
    {
        $stmt = mysqli_prepare($this->koneksi, "SELECT id, username, nama, role FROM admin WHERE id = ? LIMIT 1");
        mysqli_stmt_bind_param($stmt, 'i', $id);
        mysqli_stmt_execute($stmt);
        $hasil = mysqli_stmt_get_result($stmt);
        $data  = mysqli_fetch_assoc($hasil);
        mysqli_stmt_close($stmt);

        return $data ?: null;
    }

    public function ambilSemua(): array
    {
        $hasil = mysqli_query($this->koneksi, "SELECT id, username, nama, role FROM admin ORDER BY id DESC");
        $data  = [];
        while ($baris = mysqli_fetch_assoc($hasil)) {
            $data[] = $baris;
        }
        return $data;
    }

    public function usernameSudahAda(string $username, ?int $kecualiId = null): bool
    {
        if ($kecualiId !== null) {
            $stmt = mysqli_prepare($this->koneksi, "SELECT id FROM admin WHERE username = ? AND id != ? LIMIT 1");
            mysqli_stmt_bind_param($stmt, 'si', $username, $kecualiId);
        } else {
            $stmt = mysqli_prepare($this->koneksi, "SELECT id FROM admin WHERE username = ? LIMIT 1");
            mysqli_stmt_bind_param($stmt, 's', $username);
        }
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);
        $ada = mysqli_stmt_num_rows($stmt) > 0;
        mysqli_stmt_close($stmt);

        return $ada;
    }

    public function tambah(string $username, string $passwordHash, string $nama, string $role): bool
    {
        $stmt = mysqli_prepare($this->koneksi, "INSERT INTO admin (username, password, nama, role) VALUES (?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt, 'ssss', $username, $passwordHash, $nama, $role);
        $berhasil = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        return $berhasil;
    }

    public function update(int $id, string $username, string $nama, string $role, ?string $passwordHash = null): bool
    {
        if ($passwordHash !== null) {
            $stmt = mysqli_prepare($this->koneksi, "UPDATE admin SET username = ?, password = ?, nama = ?, role = ? WHERE id = ?");
            mysqli_stmt_bind_param($stmt, 'ssssi', $username, $passwordHash, $nama, $role, $id);
        } else {
            $stmt = mysqli_prepare($this->koneksi, "UPDATE admin SET username = ?, nama = ?, role = ? WHERE id = ?");
            mysqli_stmt_bind_param($stmt, 'sssi', $username, $nama, $role, $id);
        }
        $berhasil = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        return $berhasil;
    }

    public function hapus(int $id): bool
    {
        $stmt = mysqli_prepare($this->koneksi, "DELETE FROM admin WHERE id = ?");
        mysqli_stmt_bind_param($stmt, 'i', $id);
        $berhasil = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        return $berhasil;
    }
}
