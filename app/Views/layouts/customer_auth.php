<!doctype html>
<html lang="th">
<head>
  <meta charset="utf-8">
  <title><?= isset($title) ? esc($title) . ' — Stadium Marketplace' : 'Stadium Marketplace' ?></title>
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- Tailwind CDN -->
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    :root{
      --primary: #0ea5a4;
      --primary-contrast: #ffffff;
      --panel: #ffffff;
      --line: #e5e7eb;
      --muted: #6b7280;
      --text: #111827;
    }
    body{
      font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
      background: radial-gradient(circle at top left, #ecfeff, #f3f4f6);
    }
  </style>
</head>
<body class="min-h-screen flex items-center justify-center px-4">

  <!-- ปุ่มกลับหน้าหลัก (มุมซ้ายบน) -->
  <a href="<?= base_url('sport') ?>"
     class="fixed top-4 left-4 z-50 inline-flex items-center gap-2 bg-white/90 backdrop-blur-sm border border-gray-200 px-3 py-1.5 rounded-full shadow-sm hover:bg-white transition text-sm font-medium text-gray-700">
    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
         viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
      <path stroke-linecap="round" stroke-linejoin="round"
            d="M15.75 19.5L8.25 12l7.5-7.5" />
    </svg>
    กลับหน้าหลัก
  </a>

  <div class="max-w-md w-full">
    <?= $this->renderSection('content') ?>
  </div>

  <?= $this->renderSection('scripts') ?>
</body>
</html>
