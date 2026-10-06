<?php

require_once __DIR__ . '/../models/AccountsModel.php';

class Auth
{
    private $model;
    private $load;

    public function __construct()
    {
        $this->model = new AccountsModel();
        $this->load  = new Loader();
    }

    /**
     * Menampilkan form login (GET) atau memproses kredensial pengguna (POST)
     */
    public function login()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email    = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';
            $errors   = [];

            if (empty($email)) {
                $errors['email'] = 'Email wajib diisi.';
            }
            if (empty($password)) {
                $errors['password'] = 'Password wajib diisi.';
            }

            if (!empty($errors)) {
                $this->load->view('views/auth/login.php', [
                    'errors' => $errors,
                    'old'    => ['email' => $email],
                ]);
                return;
            }

            $user = $this->model->getByEmail($email);

            if (!$user || !password_verify($password, $user['password'])) {
                $errors['general'] = 'Email atau password salah.';
                $this->load->view('views/auth/login.php', [
                    'errors' => $errors,
                    'old'    => ['email' => $email],
                ]);
                return;
            }

            // Simpan data autentikasi ke dalam session
            $_SESSION['user'] = [
                'id'                    => $user['id'],
                'name'                  => $user['name'],
                'email'                 => $user['email'],
                'account_type_id'       => $user['account_type_id'],
                'account_type_name'     => $user['account_type_name'],
                'status'                => $user['status'],
                'identification_number' => $user['identification_number'],
                'identification_type'   => $user['identification_type'],
            ];

            header('Location: ' . BASE_URL . '/accounts');
            exit;
        }

        // Tampilan form login (GET)
        $this->load->view('views/auth/login.php');
    }

    /**
     * Mengakhiri sesi login pengguna
     */
    public function logout()
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            unset($_SESSION['user']);
            session_destroy();
        }

        header('Location: ' . BASE_URL . '/auth');
        exit;
    }
}
