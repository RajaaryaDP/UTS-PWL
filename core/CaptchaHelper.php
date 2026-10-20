<?php

/**
 * CAPTCHA sesuai materi "Membuat Fungsi Login dan Captcha":
 * - kode acak 5 karakter (huruf besar/kecil + angka) disimpan di $_SESSION['code']
 * - gambar 173x50, background biru, teks abu-abu (imagestring), output JPEG
 * - input form bernama "kodecaptcha"
 */
class CaptchaHelper
{
    // fungsi acakCaptcha() pada materi
    public static function acakCaptcha(): string
    {
        $alphabet = 'abcdefghijklmnopqrstuwxyzABCDEFGHIJKLMNOPQRSTUWXYZ0123456789';
        $pass = [];
        $panjangAlpha = strlen($alphabet) - 1;
        for ($i = 0; $i < 5; $i++) {
            $n = random_int(0, $panjangAlpha);
            $pass[] = $alphabet[$n];
        }
        return implode($pass);
    }

    // isi captcha.php pada materi: buat kode, simpan ke session, gambar
    public static function render(): void
    {
        $code = self::acakCaptcha();
        $_SESSION['code'] = $code;

        // GD belum aktif di php.ini? Pakai gambar SVG sebagai cadangan agar captcha tetap tampil.
        if (!function_exists('imagecreatetruecolor') || !function_exists('imagejpeg')) {
            self::renderSvg($code);
            return;
        }

        $wh  = imagecreatetruecolor(173, 50);
        $bgc = imagecolorallocate($wh, 22, 86, 165);     // background biru
        $fc  = imagecolorallocate($wh, 223, 230, 233);   // teks abu-abu
        imagefill($wh, 0, 0, $bgc);

        // ( $image, $fontsize, $x, $y, $string, $fontcolor )
        imagestring($wh, 5, 50, 15, $code, $fc);

        while (ob_get_level() > 0) {
            ob_end_clean(); // buang output tersasar sebelum gambar
        }
        header('Content-Type: image/jpeg');
        header('Cache-Control: no-store, no-cache, must-revalidate');
        imagejpeg($wh);
    }

    // Cadangan tanpa GD: gambar SVG 173x50, warna sama (biru + teks abu-abu)
    public static function renderSvg(string $code): void
    {
        while (ob_get_level() > 0) {
            ob_end_clean();
        }
        $svg = '<svg xmlns="http://www.w3.org/2000/svg" width="173" height="50" viewBox="0 0 173 50">'
             . '<rect width="173" height="50" fill="rgb(22,86,165)"/>';
        for ($i = 0; $i < 5; $i++) { // garis pengganggu
            $svg .= sprintf('<line x1="%d" y1="%d" x2="%d" y2="%d" stroke="rgb(223,230,233)" stroke-opacity="0.35" stroke-width="1"/>',
                random_int(0, 173), random_int(0, 50), random_int(0, 173), random_int(0, 50));
        }
        $len = strlen($code);
        for ($i = 0; $i < $len; $i++) {
            $x = 16 + $i * 28;
            $y = random_int(30, 36);
            $svg .= sprintf('<text x="%d" y="%d" font-family="Courier New, monospace" font-size="%d" font-weight="bold" fill="rgb(223,230,233)" transform="rotate(%d %d %d)">%s</text>',
                $x, $y, random_int(24, 30), random_int(-20, 20), $x, $y, htmlspecialchars($code[$i]));
        }
        $svg .= '</svg>';

        header('Content-Type: image/svg+xml');
        header('Cache-Control: no-store, no-cache, must-revalidate');
        echo $svg;
    }

    // pengecekan seperti hasil.php: $_SESSION['code'] dibandingkan dengan $_POST['kodecaptcha']
    public static function verify(?string $input): bool
    {
        $code = $_SESSION['code'] ?? '';
        unset($_SESSION['code']); // sekali pakai
        return $code !== '' && hash_equals($code, (string) $input);
    }
}
