<?php

require_once __DIR__ . '/../config/database.php';

use Ramsey\Uuid\Uuid;

class ActionsModel
{
    private $db;

    public function __construct()
    {
        global $conn;
        $this->db = $conn;
    }

    /**
     * Mengambil seluruh data jenis aksi yang belum dihapus (soft delete)
     * Mendukung pencarian berdasarkan nama dan deskripsi
     */
    public function getActions($search = null)
    {
        if (!empty($search)) {
            $keyword = '%' . trim($search) . '%';
            $stmt = $this->db->prepare(
                "SELECT * FROM actions 
                 WHERE deleted_at IS NULL 
                   AND (name LIKE ? OR description LIKE ?) 
                 ORDER BY created_at DESC"
            );
            $stmt->execute([$keyword, $keyword]);
            return $stmt->fetchAll();
        }

        $stmt = $this->db->query(
            "SELECT * FROM actions 
             WHERE deleted_at IS NULL 
             ORDER BY created_at DESC"
        );
        return $stmt->fetchAll();
    }

    public function getAll($search = null)
    {
        return $this->getActions($search);
    }

    public function getActionById($id)
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM actions 
             WHERE id = ? AND deleted_at IS NULL"
        );
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row === false ? null : $row;
    }

    public function getById($id)
    {
        return $this->getActionById($id);
    }

    public function createAction($data)
    {
        $id = Uuid::uuid4()->toString();

        $stmt = $this->db->prepare(
            "INSERT INTO actions (id, name, description, created_at, updated_at) 
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
        return $this->createAction($data);
    }

    public function updateAction($id, $data)
    {
        $stmt = $this->db->prepare(
            "UPDATE actions 
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
        return $this->updateAction($id, $data);
    }

    /**
     * Soft delete jenis aksi
     */
    public function deleteAction($id)
    {
        $stmt = $this->db->prepare(
            "UPDATE actions 
             SET deleted_at = NOW() 
             WHERE id = ? AND deleted_at IS NULL"
        );
        return $stmt->execute([$id]);
    }

    public function delete($id)
    {
        return $this->deleteAction($id);
    }
}
