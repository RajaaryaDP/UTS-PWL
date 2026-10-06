<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Error</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body style="min-height: 100vh; background: #f4f6fb;" class="d-flex align-items-center justify-content-center">

    <div class="text-center p-4" style="max-width: 480px;">
        <img src="https://i.pinimg.com/736x/ff/41/63/ff416364fd7c064230b9d1d1e7fe3c8b.jpg"
             alt="error cat" class="img-fluid rounded-3 shadow-sm mb-3">

        <h4 class="mb-1">Ada yang error, bro.</h4>
        <p class="text-muted small mb-3">Ini bukan 404 -- ini beneran ada yang salah di kode.</p>

        <div class="text-start bg-white border rounded-3 p-3 small font-monospace text-break">
            <?= htmlspecialchars($exception->getMessage()) ?><br>
            <span class="text-muted"><?= htmlspecialchars($exception->getFile()) ?>:<?= $exception->getLine() ?></span>
        </div>
    </div>

</body>
</html>
