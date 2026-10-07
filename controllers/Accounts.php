<?php

require_once __DIR__ . '/../models/AccountsModel.php';
require_once __DIR__ . '/../models/AccountTypeModel.php';

class Accounts
{
    private $model;
    private $accountTypeModel;
    private $load;

    public function __construct()
    {
        $this->model            = new AccountsModel();
        $this->accountTypeModel = new AccountTypeModel();
        $this->load             = new Loader();
    }

    private function validate($data, $isEdit = false, $id = null)
    {
        $errors = [];

        // Validasi Nama
        if (empty(trim($data['name'] ?? ''))) {
            $errors['name'] = 'Nama wajib diisi.';
        } elseif (strlen($data['name']) > 128) {
            $errors['name'] = 'Nama maksimal 128 karakter.';
        }

        // Validasi Email
        $email = trim($data['email'] ?? '');
        if (empty($email)) {
            $errors['email'] = 'Email wajib diisi.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Format email tidak valid.';
        } elseif ($this->model->isEmailExists($email, $id)) {
            $errors['email'] = 'Email sudah digunakan oleh akun lain.';
        }

        // Validasi Password
        $password = $data['password'] ?? '';
        if (!$isEdit) {
            if (empty($password)) {
                $errors['password'] = 'Password wajib diisi.';
            } elseif (strlen($password) < 6) {
                $errors['password'] = 'Password minimal 6 karakter.';
            }
        } else {
            if (!empty($password) && strlen($password) < 6) {
                $errors['password'] = 'Password baru minimal 6 karakter.';
            }
        }

        // Validasi Tipe Akun
        if (empty($data['account_type_id'] ?? '')) {
            $errors['account_type_id'] = 'Tipe akun wajib dipilih.';
        } else {
            $type = $this->accountTypeModel->getById($data['account_type_id']);
            if (!$type) {
                $errors['account_type_id'] = 'Tipe akun yang dipilih tidak valid.';
            }
        }

        // Validasi Status
        $allowedStatus = ['Aktif', 'Nonaktif'];
        if (!in_array($data['status'] ?? '', $allowedStatus, true)) {
            $errors['status'] = 'Status akun tidak valid.';
        }

        // Validasi Identitas
        $allowedIdentTypes = ['NIM', 'NIP'];
        if (!in_array($data['identification_type'] ?? '', $allowedIdentTypes, true)) {
            $errors['identification_type'] = 'Jenis identitas wajib dipilih (NIM / NIP).';
        }

        if (empty(trim($data['identification_number'] ?? ''))) {
            $errors['identification_number'] = 'Nomor identitas wajib diisi.';
        }

        return $errors;
    }

    private function requireAdmin()
    {
        if (strcasecmp(trim($_SESSION['user']['account_type_name'] ?? ''), 'Admin') === 0) {
            return true;
        }

        http_response_code(403);
        echo '403 - Akses hanya tersedia untuk admin.';
        return false;
    }

    public function index()
    {
        $search = $_GET['q'] ?? '';
        $role = trim($_SESSION['user']['account_type_name'] ?? '');
        $isAdmin = strcasecmp($role, 'Admin') === 0;

        if ($isAdmin) {
            $accounts = $this->model->getAll($search);
        } elseif (strcasecmp($role, 'Dosen') === 0 || strcasecmp($role, 'Mahasiswa') === 0) {
            $accounts = $this->model->getAccountsByType($role, $_SESSION['user']['id'], $search);
        } else {
            http_response_code(403);
            echo '403 - Role akun tidak memiliki akses ke halaman ini.';
            return;
        }

        $this->load->view('views/accounts/index.php', [
            'accounts'      => $accounts,
            'search'        => $search,
            'dashboardRole' => $role,
            'isAdmin'       => $isAdmin,
        ]);
    }

    public function create()
    {
        if (!$this->requireAdmin()) {
            return;
        }

        $accountTypes = $this->accountTypeModel->getAll();

        $this->load->view('views/accounts/form.php', [
            'isEdit'       => false,
            'accountTypes' => $accountTypes,
            'values'       => [
                'status'              => 'Aktif',
                'identification_type' => 'NIM',
            ],
        ]);
    }

    public function store()
    {
        if (!$this->requireAdmin()) {
            return;
        }

        $errors = $this->validate($_POST, false);

        if (!empty($errors)) {
            $accountTypes = $this->accountTypeModel->getAll();
            $this->load->view('views/accounts/form.php', [
                'isEdit'       => false,
                'accountTypes' => $accountTypes,
                'values'       => $_POST,
                'errors'       => $errors,
            ]);
            return;
        }

        $this->model->create([
            'name'                  => trim($_POST['name']),
            'email'                 => trim($_POST['email']),
            'password'              => $_POST['password'],
            'account_type_id'       => $_POST['account_type_id'],
            'status'                => $_POST['status'] ?? 'Aktif',
            'identification_type'   => $_POST['identification_type'],
            'identification_number' => trim($_POST['identification_number']),
        ]);

        header('Location: ' . BASE_URL . '/accounts');
        exit;
    }

    public function edit($id)
    {
        if (!$this->requireAdmin()) {
            return;
        }

        $account = $this->model->getById($id);

        if ($account === null) {
            http_response_code(404);
            echo '404 - Akun tidak ditemukan';
            return;
        }

        $accountTypes = $this->accountTypeModel->getAll();

        $this->load->view('views/accounts/form.php', [
            'isEdit'       => true,
            'id'           => $id,
            'accountTypes' => $accountTypes,
            'values'       => $account,
        ]);
    }

    public function update($id)
    {
        if (!$this->requireAdmin()) {
            return;
        }

        $existing = $this->model->getById($id);
        if ($existing === null) {
            http_response_code(404);
            echo '404 - Akun tidak ditemukan';
            return;
        }

        $errors = $this->validate($_POST, true, $id);

        if (!empty($errors)) {
            $accountTypes = $this->accountTypeModel->getAll();
            $this->load->view('views/accounts/form.php', [
                'isEdit'       => true,
                'id'           => $id,
                'accountTypes' => $accountTypes,
                'values'       => array_merge($existing, $_POST),
                'errors'       => $errors,
            ]);
            return;
        }

        $updateData = [
            'name'                  => trim($_POST['name']),
            'email'                 => trim($_POST['email']),
            'account_type_id'       => $_POST['account_type_id'],
            'status'                => $_POST['status'] ?? 'Aktif',
            'identification_type'   => $_POST['identification_type'],
            'identification_number' => trim($_POST['identification_number']),
        ];

        if (!empty($_POST['password'])) {
            $updateData['password'] = $_POST['password'];
        }

        $this->model->update($id, $updateData);

        // Jika user yang diupdate adalah user yang sedang login, update sesi juga
        if (isset($_SESSION['user']) && $_SESSION['user']['id'] === $id) {
            $_SESSION['user']['name']                  = $updateData['name'];
            $_SESSION['user']['email']                 = $updateData['email'];
            $_SESSION['user']['account_type_id']       = $updateData['account_type_id'];
            $_SESSION['user']['identification_number'] = $updateData['identification_number'];
            $_SESSION['user']['identification_type']   = $updateData['identification_type'];
        }

        header('Location: ' . BASE_URL . '/accounts');
        exit;
    }

    public function delete($id)
    {
        if (!$this->requireAdmin()) {
            return;
        }

        $existing = $this->model->getById($id);
        if ($existing === null) {
            http_response_code(404);
            echo '404 - Akun tidak ditemukan';
            return;
        }

        // Cegah menghapus akun sendiri saat sedang login
        if (isset($_SESSION['user']) && $_SESSION['user']['id'] === $id) {
            http_response_code(400);
            echo 'Tidak dapat menghapus akun yang sedang aktif digunakan.';
            return;
        }

        $this->model->delete($id);
        header('Location: ' . BASE_URL . '/accounts');
        exit;
    }
}
