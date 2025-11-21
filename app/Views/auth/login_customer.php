<?= $this->extend('layouts/customer_auth') ?>

<?= $this->section('content') ?>

<div class="bg-[var(--panel)] rounded-3xl shadow-xl shadow-slate-900/5 border border-slate-100/80 overflow-hidden">
  <div class="h-1.5 bg-gradient-to-r from-[var(--primary)] via-emerald-400 to-cyan-400"></div>

  <div class="px-6 pt-6 pb-7 sm:px-8 sm:pt-7 sm:pb-8">
    <h1 class="text-xl sm:text-2xl font-bold text-[var(--text)] mb-1">
      เข้าสู่ระบบ
    </h1>
    <p class="text-sm text-[var(--muted)] mb-6">
      จองสนามโปรดของคุณได้อย่างรวดเร็วในไม่กี่คลิก
    </p>

    <?php if (session('auth_error')): ?>
      <div class="mb-4 rounded-2xl bg-red-50 border border-red-100 px-4 py-3 text-sm text-red-700">
        <?= esc(session('auth_error')) ?>
      </div>
    <?php endif; ?>

    <form class="space-y-4" action="<?= site_url('/customer/login') ?>" method="post" autocomplete="off">
      <?= csrf_field() ?>

      <div>
        <label class="block text-sm font-medium text-[var(--text)] mb-1.5">อีเมล</label>
        <input type="email"
               name="email"
               value="<?= old('email') ?>"
               class="w-full rounded-2xl border border-[var(--line)] bg-white px-3 py-2.5 text-sm outline-none focus:border-[var(--primary)] focus:ring-2 focus:ring-[var(--primary)]/20"
               placeholder="you@example.com"
               required>
      </div>

      <div>
        <div class="flex items-center justify-between mb-1.5">
          <label class="block text-sm font-medium text-[var(--text)]">รหัสผ่าน</label>
        </div>
        <div class="relative">
          <input type="password"
                 id="password"
                 name="password"
                 class="w-full rounded-2xl border border-[var(--line)] bg-white px-3 py-2.5 text-sm outline-none focus:border-[var(--primary)] focus:ring-2 focus:ring-[var(--primary)]/20"
                 placeholder="••••••••"
                 required>
          <button type="button"
                  id="togglePassword"
                  class="absolute inset-y-0 right-3 flex items-center text-xs text-[var(--muted)] hover:text-[var(--primary)]">
            แสดง
          </button>
        </div>
      </div>

      <button type="submit"
              class="w-full mt-1 inline-flex items-center justify-center rounded-2xl bg-[var(--primary)] px-4 py-2.5 text-sm font-semibold text-[var(--primary-contrast)] shadow-md shadow-[var(--primary)]/30 hover:shadow-lg hover:shadow-[var(--primary)]/40 transition">
        เข้าสู่ระบบ
      </button>
    </form>

    <p class="mt-5 text-center text-xs text-[var(--muted)]">
      ยังไม่มีบัญชี?
      <a href="<?= route_to('customer/register') ?>" class="font-medium text-[var(--primary)] hover:underline">
        สมัครสมาชิก
      </a>
    </p>
  </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
  const pwdInput = document.getElementById('password');
  const toggleBtn = document.getElementById('togglePassword');

  if (pwdInput && toggleBtn) {
    toggleBtn.addEventListener('click', () => {
      const isPassword = pwdInput.type === 'password';
      pwdInput.type = isPassword ? 'text' : 'password';
      toggleBtn.textContent = isPassword ? 'ซ่อน' : 'แสดง';
    });
  }
</script>
<?= $this->endSection() ?>
