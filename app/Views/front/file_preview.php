<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>معاينة ملف: <?php echo escape($file->title); ?></title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.rtl.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;800&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Tajawal', sans-serif;
            background-color: #0f172a;
            color: #f8fafc;
            margin: 0;
            padding: 0;
            height: 100vh;
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }
        .preview-header {
            background-color: #1e293b;
            border-bottom: 1px solid #334155;
            padding: 10px 20px;
            z-index: 1000;
        }
        .preview-container {
            flex: 1;
            width: 100%;
            height: calc(100vh - 65px);
            background-color: #0f172a;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: auto;
        }
        .preview-iframe {
            width: 100%;
            height: 100%;
            border: none;
        }
        .preview-media {
            max-width: 90%;
            max-height: calc(100vh - 100px);
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.5);
        }
    </style>
</head>
<body>

    <header class="preview-header d-flex align-items-center justify-content-between flex-wrap gap-2">
        <div class="d-flex align-items-center gap-3">
            <a href="<?php echo escape($backUrl); ?>" class="btn btn-outline-light btn-sm rounded-pill px-3 shadow-sm">
                <i class="fas fa-arrow-right ms-1"></i>العودة إلى المادة
            </a>
            <div class="d-flex align-items-center gap-2">
                <i class="fas fa-file-pdf text-danger fs-5"></i>
                <h6 class="mb-0 text-white fw-bold me-2"><?php echo escape($file->title); ?></h6>
            </div>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="<?php echo url('/files/' . escape($file->id) . '/download'); ?>" class="btn btn-success btn-sm rounded-pill px-4 shadow-sm">
                <i class="fas fa-download ms-1"></i>تحميل الملف
            </a>
        </div>
    </header>

    <main class="preview-container">
        <?php
            $ext = strtolower($fileExtension ?? '');
            $rawUrl = url('/files/' . $file->id . '/preview?raw=1');
        ?>

        <?php if (in_array($ext, ['png', 'jpg', 'jpeg', 'gif', 'webp'])): ?>
            <img src="<?php echo $rawUrl; ?>" alt="<?php echo escape($file->title); ?>" class="preview-media img-fluid">
        <?php elseif (in_array($ext, ['mp4', 'webm'])): ?>
            <video controls src="<?php echo $rawUrl; ?>" class="preview-media"></video>
        <?php elseif (in_array($ext, ['mp3', 'wav', 'ogg'])): ?>
            <div class="card bg-dark text-white p-4 rounded-4 shadow text-center" style="max-width: 500px; width: 90%;">
                <i class="fas fa-music fa-4x text-primary mb-3"></i>
                <h5 class="fw-bold mb-3"><?php echo escape($file->title); ?></h5>
                <audio controls src="<?php echo $rawUrl; ?>" class="w-100"></audio>
            </div>
        <?php else: ?>
            <iframe src="<?php echo $rawUrl; ?>" class="preview-iframe" title="<?php echo escape($file->title); ?>"></iframe>
        <?php endif; ?>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
