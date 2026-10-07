<?php

require_once __DIR__ . '/../config/database.php';

use Ramsey\Uuid\Uuid;

class AccountsModel
{
    private $db;

    public function __construct()
    {
        global $conn;
        $this->db = $conn;
    }

    /**
     * Mengambil daftar akun yang belum dihapus (soft delete)
     * Dilengkapi pencarian: nama, email, nomor identitas (NIM/NIP), dan nama tipe akun
     */
    public function getAccounts($search = null)
    {
        $sql = "SELECT accounts.*, account_type.name AS account_type_name 
                FROM accounts 
                LEFT JOIN account_type ON accounts.account_type_id = account_type.id 
                WHERE accounts.deleted_at IS NULL";

        if (!empty($search)) {
            $keyword = '%' . trim($search) . '%';
            $sql .= " AND (accounts.name LIKE ? 
                           OR accounts.email LIKE ? 
                           OR accounts.identification_number LIKE ? 
                           OR account_type.name LIKE ?)";
            $sql .= " ORDER BY accounts.created_at DESC";

            $stmt = $this->db->prepare($sql);
            $stmt->execute([$keyword, $keyword, $keyword, $keyword]);
            return $stmt->fetchAll();
        }

        $sql .= " ORDER BY accounts.created_at DESC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }

    public function getAll($search = null)
    {
        return $this->getAccounts($search);
    }

    public function getAccountsByType($accountTypeName, $excludeAccountId, $search = null)
    {
        $sql = "SELECT accounts.*, account_type.name AS account_type_name
                FROM accounts
                INNER JOIN account_type ON accounts.account_type_id = account_type.id
                WHERE accounts.deleted_at IS NULL
                  AND account_type.deleted_at IS NULL
                  AND account_type.name = ?
                  AND accounts.id != ?";
        $params = [$accountTypeName, $excludeAccountId];

        if (!empty($search)) {
            $keyword = '%' . trim($search) . '%';
            $sql .= " AND (accounts.name LIKE ?
                           OR accounts.email LIKE ?
                           OR accounts.identification_number LIKE ?)";
            $params = array_merge($params, [$keyword, $keyword, $keyword]);
        }

        $sql .= " ORDER BY accounts.created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function getAccountById($id)
    {
        $stmt = $this->db->prepare(
            "SELECT accounts.*, account_type.name AS account_type_name 
             FROM accounts 
             LEFT JOIN account_type ON accounts.account_type_id = account_type.id 
             WHERE accounts.id = ? AND accounts.deleted_at IS NULL"
        );
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row === false ? null : $row;
    }

    public function getById($id)
    {
        return $this->getAccountById($id);
    }

    /**
     * Mencari akun berdasarkan email untuk proses autentikasi (login)
     */
    public function getByEmail($email)
    {
        $stmt = $this->db->prepare(
            "SELECT accounts.*, account_type.name AS account_type_name 
             FROM accounts 
             LEFT JOIN account_type ON accounts.account_type_id = account_type.id 
             WHERE accounts.email = ? AND accounts.deleted_at IS NULL"
        );
        $stmt->execute([trim($email)]);
        $row = $stmt->fetch();
        return $row === false ? null : $row;
    }

    /**
     * Cek apakah email sudah terdaftar sebelumnya
     */
    public function isEmailExists($email, $excludeId = null)
    {
        if ($excludeId) {
            $stmt = $this->db->prepare(
                "SELECT id FROM accounts 
                 WHERE email = ? AND id != ? AND deleted_at IS NULL"
            );
            $stmt->execute([trim($email), $excludeId]);
        } else {
            $stmt = $this->db->prepare(
                "SELECT id FROM accounts 
                 WHERE email = ? AND deleted_at IS NULL"
            );
            $stmt->execute([trim($email)]);
        }
        return (bool) $stmt->fetch();
    }

    public function createAccount($data)
    {
        $id = Uuid::uuid4()->toString();
        $passwordHash = password_hash($data['password'], PASSWORD_BCRYPT);

        $stmt = $this->db->prepare(
            "INSERT INTO accounts 
             (id, name, email, password, account_type_id, status, identification_number, identification_type, created_at, updated_at) 
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())"
        );

        $stmt->execute([
            $id,
            $data['name'],
            trim($data['email']),
            $passwordHash,
            $data['account_type_id'],
            $data['status'] ?? 'Aktif',
            $data['identification_number'],
            $data['identification_type'],
        ]);

        return $id;
    }

    public function create($data)
    {
        return $this->createAccount($data);
    }

    public function updateAccount($id, $data)
    {
        if (!empty($data['password'])) {
            $passwordHash = password_hash($data['password'], PASSWORD_BCRYPT);
            $stmt = $this->db->prepare(
                "UPDATE accounts 
                 SET name = ?, email = ?, password = ?, account_type_id = ?, status = ?, 
                     identification_number = ?, identification_type = ?, updated_at = NOW() 
                 WHERE id = ? AND deleted_at IS NULL"
            );
            return $stmt->execute([
                $data['name'],
                trim($data['email']),
                $passwordHash,
                $data['account_type_id'],
                $data['status'] ?? 'Aktif',
                $data['identification_number'],
                $data['identification_type'],
                $id
            ]);
        }

        $stmt = $this->db->prepare(
            "UPDATE accounts 
             SET name = ?, email = ?, account_type_id = ?, status = ?, 
                 identification_number = ?, identification_type = ?, updated_at = NOW() 
             WHERE id = ? AND deleted_at IS NULL"
        );
        return $stmt->execute([
            $data['name'],
            trim($data['email']),
            $data['account_type_id'],
            $data['status'] ?? 'Aktif',
            $data['identification_number'],
            $data['identification_type'],
            $id
        ]);
    }

    public function update($id, $data)
    {
        return $this->updateAccount($id, $data);
    }

    /**
     * Soft delete akun
     */
    public function deleteAccount($id)
    {
        $stmt = $this->db->prepare(
            "UPDATE accounts 
             SET deleted_at = NOW() 
             WHERE id = ? AND deleted_at IS NULL"
        );
        return $stmt->execute([$id]);
    }

    public function delete($id)
    {
        return $this->deleteAccount($id);
    }
}
