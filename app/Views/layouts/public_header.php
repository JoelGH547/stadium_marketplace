<?php
// public_header.php — Heroicons Outline Version
?>
<header class="relative">
  <div class="border-b border-[var(--line)] bg-[var(--panel)]/90 backdrop-blur">
    <div class="accent-bar"></div>
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 h-14 flex items-center justify-between">

      <!-- Logo -->
      <a href="<?= base_url('sport') ?>" class="flex items-center gap-2 group transition">
        <span class="inline-flex h-8 w-8 rounded-lg bg-[var(--primary)] text-white items-center justify-center font-bold transition-transform group-hover:scale-110">
          SA
        </span>
        <span class="font-semibold text-[var(--primary)] group-hover:text-[var(--primary)]/80 transition-colors">
          <?= esc($siteName) ?>
        </span>
      </a>

      <!-- NAV -->
      <nav class="hidden sm:flex items-center gap-6">

        <!-- หน้าแรก -->
        <a href="<?= base_url('sport') ?>" class="flex items-center gap-2 text-[var(--primary)] hover:text-[var(--primary)]/80 transition">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"  viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round"
              d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125A1.125 1.125 0 005.625 21h12.75A1.125 
              1.125 0 0019.5 19.875V9.75" />
          </svg>
          หน้าแรก
        </a>

        <!-- ค้นหาสนาม -->
        <a href="<?= base_url('arena#search') ?>" class="flex items-center gap-2 text-[var(--primary)] hover:text-[var(--primary)]/80 transition">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"  viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round"
              d="M21 21l-4.35-4.35m0 0A7.5 7.5 0 1010.5 18a7.5 7.5 0 006.15-3.35z" />
          </svg>
          ค้นหาสนาม
        </a>

        <!-- เกี่ยวกับเรา -->
        <a href="<?= base_url('arena#about') ?>" class="flex items-center gap-2 text-[var(--primary)] hover:text-[var(--primary)]/80 transition">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round"
              d="M11.25 11.25h1.5v5.25h-1.5m.75-7.5h.007v.008H12v-.008z" />
            <circle cx="12" cy="12" r="9" />
          </svg>
          เกี่ยวกับเรา
        </a>

        <!-- ติดต่อเรา -->
        <a href="<?= base_url('arena#contact') ?>" class="flex items-center gap-2 text-[var(--primary)] hover:text-[var(--primary)]/80 transition">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round"
              d="M2.25 4.5l8.954 6.716c.44.33 1.102.33 1.542 0L21.75 4.5M4.5 19.5h15A1.5 1.5 0 0021 18V6M3 6v12a1.5 1.5 0 001.5 1.5z" />
          </svg>
          ติดต่อเรา
        </a>

        <!-- คำถามที่พบบ่อย -->
        <a href="<?= base_url('arena#faq') ?>" class="flex items-center gap-2 text-[var(--primary)] hover:text-[var(--primary)]/80 transition">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round"
              d="M12 18h.01M12 6v4.5l2 2"/>
            <circle cx="12" cy="12" r="9" />
          </svg>
          FAQ
        </a>

      </nav>
    </div>
  </div>
</header>
