<?= $this->extend('layouts/customer_auth') ?>

<?= $this->section('content') ?>

<div class="bg-[var(--panel)] rounded-3xl shadow-xl shadow-slate-900/5 border border-slate-100/80 overflow-hidden">
  <div class="h-1.5 bg-gradient-to-r from-[var(--primary)] via-emerald-400 to-cyan-400"></div>

  <div class="px-6 pt-6 pb-7 sm:px-8 sm:pt-7 sm:pb-8">
    <h1 class="text-xl sm:text-2xl font-bold text-[var(--text)] mb-1">
      สมัครสมาชิก
    </h1>
    <p class="text-sm text-[var(--muted)] mb-6">
      สร้างบัญชีเพื่อเริ่มจองสนาม และจัดการการจองของคุณได้ง่าย ๆ
    </p>

    <?php if (session('auth_error')): ?>
      <div class="mb-4 rounded-2xl bg-red-50 border border-red-100 px-4 py-3 text-sm text-red-700">
        <?= esc(session('auth_error')) ?>
      </div>
    <?php endif; ?>

    <form class="space-y-4" action="<?= site_url('/customer/register') ?>" method="post" autocomplete="off">
      <?= csrf_field() ?>

      <div>
        <label class="block text-sm font-medium text-[var(--text)] mb-1.5">ชื่อผู้ใช้ (Username)</label>
        <input type="text" name="username" value="<?= old('username') ?>"
          class="w-full rounded-2xl border border-[var(--line)] bg-white px-3 py-2.5 text-sm outline-none focus:border-[var(--primary)] focus:ring-2 focus:ring-[var(--primary)]/20"
          placeholder="เช่น sportlover01" required>
      </div>

      <div>
        <label class="block text-sm font-medium text-[var(--text)] mb-1.5">อีเมล</label>
        <input type="email" name="email" value="<?= old('email') ?>"
          class="w-full rounded-2xl border border-[var(--line)] bg-white px-3 py-2.5 text-sm outline-none focus:border-[var(--primary)] focus:ring-2 focus:ring-[var(--primary)]/20"
          placeholder="you@example.com" required>
      </div>

      <div>
        <label class="block text-sm font-medium text-[var(--text)] mb-1.5">รหัสผ่าน</label>
        <div class="relative">
          <input type="password" id="password" name="password"
            class="w-full rounded-2xl border border-[var(--line)] bg-white px-3 py-2.5 text-sm outline-none focus:border-[var(--primary)] focus:ring-2 focus:ring-[var(--primary)]/20"
            placeholder="อย่างน้อย 8 ตัวอักษร" required>
          <button type="button" id="togglePassword"
            class="absolute inset-y-0 right-3 flex items-center text-xs text-[var(--muted)] hover:text-[var(--primary)]">
            แสดง
          </button>
        </div>
      </div>

      <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
        <div>
          <label class="block text-sm font-medium text-[var(--text)] mb-1.5">ชื่อ</label>
          <input type="text" name="firstname" value="<?= old('firstname') ?>"
            class="w-full rounded-2xl border border-[var(--line)] bg-white px-3 py-2.5 text-sm outline-none focus:border-[var(--primary)] focus:ring-2 focus:ring-[var(--primary)]/20"
            placeholder="ชื่อจริง">
        </div>
        <div>
          <label class="block text-sm font-medium text-[var(--text)] mb-1.5">นามสกุล</label>
          <input type="text" name="lastname" value="<?= old('lastname') ?>"
            class="w-full rounded-2xl border border-[var(--line)] bg-white px-3 py-2.5 text-sm outline-none focus:border-[var(--primary)] focus:ring-2 focus:ring-[var(--primary)]/20"
            placeholder="นามสกุล">
        </div>
      </div>

      <div>
        <div class="flex items-center justify-between mb-1.5">
          <label class="block text-sm font-medium text-[var(--text)]">เบอร์โทรศัพท์</label>
          <span class="text-[10px] text-[var(--muted)]">ต้องเป็นตัวเลข 10 หลัก</span>
        </div>
        <input type="tel" id="phone" name="phone" inputmode="numeric" maxlength="10" value="<?= old('phone') ?>"
          class="w-full rounded-2xl border border-[var(--line)] bg-white px-3 py-2.5 text-sm outline-none focus:border-[var(--primary)] focus:ring-2 focus:ring-[var(--primary)]/20"
          placeholder="เช่น 0812345678">
      </div>

      <button type="submit"
        class="w-full mt-1 inline-flex items-center justify-center rounded-3xl bg-[var(--primary)] px-4 py-2.5 text-sm font-semibold text-[var(--primary-contrast)] shadow-md shadow-[var(--primary)]/30 hover:shadow-lg hover:shadow-[var(--primary)]/40 transition">
        สมัครสมาชิก
      </button>
    </form>

    <p class="mt-5 text-center text-xs text-[var(--muted)]">
      มีบัญชีแล้ว?
      <a href="<?= route_to('customer/login') ?>" class="font-medium text-[var(--primary)] hover:underline">
        เข้าสู่ระบบ
      </a>
    </p>
  </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
  const pwdInput2 = document.getElementById('password');
  const toggleBtn2 = document.getElementById('togglePassword');

  if (pwdInput2 && toggleBtn2) {
    toggleBtn2.addEventListener('click', () => {
      const isPassword = pwdInput2.type === 'password';
      pwdInput2.type = isPassword ? 'text' : 'password';
      toggleBtn2.textContent = isPassword ? 'ซ่อน' : 'แสดง';
    });
  }
</script>
<?= $this->endSection() ?>