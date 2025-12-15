<?php $siteName = $siteName ?? 'Stadium Marketplace'; ?>
<footer class="border-t border-[var(--line)] bg-[var(--panel)] text-[var(--primary)] mt-12">

  <div class="border-t border-[var(--line)] py-4 text-center text-sm">
    © <?= date('Y') ?> <?= esc($siteName) ?>. All rights reserved.
  </div>
</footer>