<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Sedang Gangguan - 503</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; background: #f8fafc; color: #334155; display: flex; justify-content: center; align-items: center; min-height: 100vh; }
        .container { text-align: center; padding: 2rem; max-width: 500px; }
        .icon { font-size: 4rem; margin-bottom: 1rem; }
        h1 { font-size: 1.5rem; font-weight: 600; color: #1e293b; margin-bottom: 0.5rem; }
        p { color: #64748b; line-height: 1.6; }
        .action { margin-top: 1.5rem; }
        .action a { display: inline-block; padding: 0.75rem 1.5rem; background: #f59e0b; color: white; text-decoration: none; border-radius: 0.5rem; font-weight: 500; transition: background 0.2s; }
        .action a:hover { background: #d97706; }
    </style>
</head>
<body>
    <div class="container">
        <div class="icon">⚠️</div>
        <h1>Sistem Sedang Gangguan</h1>
        <p>{{ $message ?? 'Sistem sedang mengalami gangguan. Tim teknis sedang bekerja untuk memperbaikinya.' }}</p>
        <div class="action">
            <a href="javascript:location.reload()">Muat Ulang</a>
        </div>
    </div>
</body>
</html>

