<header class="relative">
  <div class="border-b border-[var(--line)] bg-[var(--panel)]/90 backdrop-blur">
    <div class="accent-bar"></div>
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 h-14 flex items-center justify-between">
      <a href="<?= base_url('arena#top') ?>" class="flex items-center gap-2 group transition">
        <span class="inline-flex h-8 w-8 rounded-lg bg-[var(--primary)] text-white items-center justify-center font-bold transition-transform group-hover:scale-110">
          SA
        </span>
        <span class="font-semibold text-[var(--primary)] group-hover:text-[var(--primary)]/80 transition-colors">
          <?= esc($siteName) ?>
        </span>
      </a>

      <nav class="hidden sm:flex items-center gap-6">
        <a href="<?= base_url('arena#home') ?>" class="text-[var(--primary)] hover:text-[var(--primary)]/80 transition-colors">หน้าแรก</a>
        <a href="<?= base_url('arena#search') ?>" class="text-[var(--primary)] hover:text-[var(--primary)]/80 transition-colors">ค้นหาสนาม</a>
        <a href="<?= base_url('arena#about') ?>" class="text-[var(--primary)] hover:text-[var(--primary)]/80 transition-colors">เกี่ยวกับเรา</a>
        <a href="<?= base_url('arena#contact') ?>" class="text-[var(--primary)] hover:text-[var(--primary)]/80 transition-colors">ติดต่อเรา</a>
        <a href="<?= base_url('arena#faq') ?>" class="text-[var(--primary)] hover:text-[var(--primary)]/80 transition-colors">คำถามที่พบบ่อย</a>
      </nav>
    </div>
  </div>
</header>
