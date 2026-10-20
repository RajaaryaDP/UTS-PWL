<?php

require_once __DIR__ . '/../core/CaptchaHelper.php';

class Captcha
{
    // GET /captcha  -> gambar captcha (JPEG via GD, atau SVG jika GD belum aktif)
    public function index(): void
    {
        CaptchaHelper::render();
    }
}
