<?php

require_once __DIR__ . '/../config/database.php';

use Ramsey\Uuid\Uuid;

class AccountTypeModel
{
    private $db;

    public function __construct()
    {
        global $conn;
        $this->db = $conn;
    }

    /**
     * Mengambil semua tipe akun yang belum dihapus (soft delete)
     * Mendukung fitur pencarian berdasarkan nama dan deskripsi
     */
    public function getAccountType($search = null)
    {
        if (!empty($search)) {
            $keyword = '%' . trim($search) . '%';
            $stmt = $this->db->prepare(
                "SELECT * FROM account_type 
                 WHERE deleted_at IS NULL 
                   AND (name LIKE ? OR description LIKE ?) 
                 ORDER BY created_at DESC"
            );
            $stmt->execute([$keyword, $keyword]);
            return $stmt->fetchAll();
        }

        $stmt = $this->db->query(
            "SELECT * FROM account_type 
             WHERE deleted_at IS NULL 
             ORDER BY created_at DESC"
        );
        return $stmt->fetchAll();
    }

    public function getAll($search = null)
    {
        return $this->getAccountType($search);
    }

    public function getAccountTypeById($id)
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM account_type 
             WHERE id = ? AND deleted_at IS NULL"
        );
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row === false ? null : $row;
    }

    public function getById($id)
    {
        return $this->getAccountTypeById($id);
    }

    public function createAccountType($data)
    {
        $id = Uuid::uuid4()->toString();

        $stmt = $this->db->prepare(
            "INSERT INTO account_type (id, name, description, created_at, updated_at) 
             VALUES (?, ?, ?, NOW(), NOW())"
        );
        $stmt->execute([
            $id,
            $data['name'],
            $data['description'] ?? null,
        ]);

        return $id;
    }

    public function create($data)
    {
        return $this->createAccountType($data);
    }

    public function updateAccountType($id, $data)
    {
        $stmt = $this->db->prepare(
            "UPDATE account_type 
             SET name = ?, description = ?, updated_at = NOW() 
             WHERE id = ? AND deleted_at IS NULL"
        );
        return $stmt->execute([
            $data['name'],
            $data['description'] ?? null,
            $id
        ]);
    }

    public function update($id, $data)
    {
        return $this->updateAccountType($id, $data);
    }

    /**
     * Soft delete tipe akun
     */
    public function deleteAccountType($id)
    {
        $stmt = $this->db->prepare(
            "UPDATE account_type 
             SET deleted_at = NOW() 
             WHERE id = ? AND deleted_at IS NULL"
        );
        return $stmt->execute([$id]);
    }

    public function delete($id)
    {
        return $this->deleteAccountType($id);
    }
}
