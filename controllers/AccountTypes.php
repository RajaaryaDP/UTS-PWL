<?php

require_once __DIR__ . '/../models/AccountTypeModel.php';

class AccountTypes
{
    private $model;
    private $load;

    public function __construct()
    {
        $this->model = new AccountTypeModel();
        $this->load  = new Loader();
    }

    private function validate($data)
    {
        $errors = [];

        if (empty(trim($data['name'] ?? ''))) {
            $errors['name'] = 'Nama tipe akun wajib diisi.';
        } elseif (strlen($data['name']) > 128) {
            $errors['name'] = 'Nama tipe akun maksimal 128 karakter.';
        }

        return $errors;
    }

    public function index()
    {
        $search = $_GET['q'] ?? '';
        $types  = $this->model->getAll($search);

        $this->load->view('views/account_types/index.php', [
            'types'  => $types,
            'search' => $search,
        ]);
    }

    public function create()
    {
        $this->load->view('views/account_types/form.php', [
            'isEdit' => false,
            'values' => [],
        ]);
    }

    public function store()
    {
        $errors = $this->validate($_POST);

        if (!empty($errors)) {
            $this->load->view('views/account_types/form.php', [
                'isEdit' => false,
                'values' => $_POST,
                'errors' => $errors,
            ]);
            return;
        }

        $this->model->create([
            'name'        => trim($_POST['name']),
            'description' => trim($_POST['description'] ?? ''),
        ]);

        header('Location: ' . BASE_URL . '/account-types');
        exit;
    }

    public function edit($id)
    {
        $type = $this->model->getById($id);

        if ($type === null) {
            http_response_code(404);
            echo '404 - Tipe Akun tidak ditemukan';
            return;
        }

        $this->load->view('views/account_types/form.php', [
            'isEdit' => true,
            'id'     => $id,
            'values' => $type,
        ]);
    }

    public function update($id)
    {
        $existing = $this->model->getById($id);
        if ($existing === null) {
            http_response_code(404);
            echo '404 - Tipe Akun tidak ditemukan';
            return;
        }

        $errors = $this->validate($_POST);

        if (!empty($errors)) {
            $this->load->view('views/account_types/form.php', [
                'isEdit' => true,
                'id'     => $id,
                'values' => array_merge($existing, $_POST),
                'errors' => $errors,
            ]);
            return;
        }

        $this->model->update($id, [
            'name'        => trim($_POST['name']),
            'description' => trim($_POST['description'] ?? ''),
        ]);

        header('Location: ' . BASE_URL . '/account-types');
        exit;
    }

    public function delete($id)
    {
        $existing = $this->model->getById($id);
        if ($existing === null) {
            http_response_code(404);
            echo '404 - Tipe Akun tidak ditemukan';
            return;
        }

        $this->model->delete($id);
        header('Location: ' . BASE_URL . '/account-types');
        exit;
    }
}
