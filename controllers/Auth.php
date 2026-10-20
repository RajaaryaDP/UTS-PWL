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
            $captchaId = $_POST['captcha_id'] ?? '';
            $captcha  = is_string($captchaId)
                ? ($_SESSION['login_captchas'][$captchaId] ?? null)
                : null;
            $submittedCaptcha = $_POST['captcha'] ?? '';
            $answer = is_string($submittedCaptcha)
                ? strtoupper(trim($submittedCaptcha))
                : '';

            if (is_string($captchaId) && $captchaId !== '') {
                unset($_SESSION['login_captchas'][$captchaId]);
            }

            if (
                !$this->isCaptchaActive($captcha)
                || !hash_equals(strtoupper($captcha['answer']), $answer)
            ) {
                $errors['captcha'] = 'Kode CAPTCHA tidak sesuai atau telah kedaluwarsa.';
            }

            if (empty($email)) {
                $errors['email'] = 'Email wajib diisi.';
            }
            if (empty($password)) {
                $errors['password'] = 'Password wajib diisi.';
            }

            if (!empty($errors)) {
                $this->showLogin($errors, ['email' => $email]);
                return;
            }

            $user = $this->model->getByEmail($email);

            if (!$user || !password_verify($password, $user['password'])) {
                $errors['general'] = 'Email atau password salah.';
                $this->showLogin($errors, ['email' => $email]);
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
        $this->showLogin();
    }

    public function captcha()
    {

        $captchaId = $_GET['id'] ?? '';
        $captcha = is_string($captchaId)
            ? ($_SESSION['login_captchas'][$captchaId] ?? null)
            : null;
        if (!$this->isCaptchaActive($captcha)) {
            http_response_code(404);
            exit;
        }

        $image = imagecreatetruecolor(160, 52);
        $background = imagecolorallocate($image, 22, 86, 165);
        imagefill($image, 0, 0, $background);
        $textColor = imagecolorallocate($image, 255, 255, 255);

        foreach (str_split($captcha['answer']) as $index => $character) {
            imagestring(
                $image,
                5,
                12 + ($index * 23),
                17 + random_int(0, 6),
                $character,
                $textColor
            );
        }

        header('Content-Type: image/png');
        header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
        imagepng($image);
        imagedestroy($image);
        exit;
    }

    private function showLogin($errors = [], $old = [])
    {
        $captchaId = $this->createCaptchaChallenge();
        $this->load->view('views/auth/login.php', [
            'errors'    => $errors,
            'old'       => $old,
            'captchaId' => $captchaId,
        ]);
    }

    private function createCaptchaChallenge()
    {
        $characters = 'ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz23456789';
        $answer = '';

        for ($i = 0; $i < 6; $i++) {
            $answer .= $characters[random_int(0, strlen($characters) - 1)];
        }

        if (!isset($_SESSION['login_captchas']) || !is_array($_SESSION['login_captchas'])) {
            $_SESSION['login_captchas'] = [];
        }

        foreach ($_SESSION['login_captchas'] as $id => $captcha) {
            if (!$this->isCaptchaActive($captcha)) {
                unset($_SESSION['login_captchas'][$id]);
            }
        }

        $id = bin2hex(random_bytes(16));
        $_SESSION['login_captchas'][$id] = [
            'answer'     => $answer,
            'expires_at' => time() + 500,
        ];

        return $id;
    }

    private function isCaptchaActive($captcha)
    {
        return is_array($captcha)
            && isset($captcha['answer'], $captcha['expires_at'])
            && is_string($captcha['answer'])
            && is_int($captcha['expires_at'])
            && $captcha['expires_at'] >= time();
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
