<?php
/* Sisipkan di dalam <form> login DAN form tambah akun, sebelum tombol submit:
   <?php require __DIR__ . '/captcha_field.php'; ?>          (views/auth/login.php)
   <?php require __DIR__ . '/../auth/captcha_field.php'; ?>  (views/accounts/create.php, sesuaikan path)
   Nama input harus "kodecaptcha". */
$captchaError = $_SESSION['captcha_error'] ?? '';
unset($_SESSION['captcha_error']);
?>
<?php if ($captchaError !== ''): ?>
    <div class="alert alert-danger" role="alert"><?= htmlspecialchars($captchaError) ?></div>
<?php endif; ?>
<div class="form-group mb-3">
    <label>Captcha</label><br>
    <img id="captcha-img" src="<?= BASE_URL ?>/captcha?t=<?= time() ?>" alt="Captcha" width="173" height="50">
    <a href="#" onclick="document.getElementById('captcha-img').src='<?= BASE_URL ?>/captcha?t='+Date.now();return false;">Muat ulang</a>
</div>
<div class="form-group mb-3">
    <label for="kodecaptcha">Isikan captcha</label>
    <input type="text" class="form-control" id="kodecaptcha" name="kodecaptcha"
           placeholder="Ketik kode pada gambar" autocomplete="off" required>
</div>
