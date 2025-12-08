<?= $this->extend('layouts/public') ?>

<?= $this->section('content') ?>

<?php
$displayName = $customer['full_name']
    ?? $customer['username']
    ?? $customer['email']
    ?? 'ผู้ใช้งาน';

$email    = $customer['email']        ?? null;
$phone    = $customer['phone_number'] ?? null;
$gender   = $customer['gender']       ?? null;
$birthday = $customer['birthday']     ?? null;

$avatarPath = $customer['avatar'] ?? null;
$avatarUrl  = $avatarPath ? base_url($avatarPath) : null;

$initial = mb_substr($displayName, 0, 1, 'UTF-8');
?>

<div class="bg-gray-100">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-10">
        <div class="mb-6 flex items-center justify-between gap-3">
            <div>
                <h1 class="text-2xl sm:text-3xl font-semibold text-gray-900">
                    แก้ไขโปรไฟล์
                </h1>
                <p class="mt-1 text-sm text-gray-500">
                    ปรับข้อมูลส่วนตัวและรูปโปรไฟล์ของคุณ
                </p>
            </div>
            <a href="<?= site_url('sport/profile') ?>"
                class="inline-flex items-center rounded-full border border-gray-200 px-4 py-1.5 text-sm font-medium text-gray-600 hover:border-[var(--primary)] hover:text-[var(--primary)] transition">
                ย้อนกลับ
            </a>
        </div>

        <div class="grid md:grid-cols-[260px,1fr] gap-6">
            <!-- การ์ดซ้าย: preview avatar -->
            <section class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="h-2 bg-[var(--primary)]"></div>

                <div class="p-6 flex flex-col items-center text-center">
                    <div
                        class="relative mb-4 h-24 w-24 rounded-full overflow-hidden bg-teal-500/5 flex items-center justify-center">
                        <?php if ($avatarUrl): ?>
                            <img id="avatarPreview" src="<?= esc($avatarUrl) ?>" alt="Avatar preview"
                                class="h-full w-full object-cover">
                        <?php else: ?>
                            <span id="avatarPreviewInitial" class="text-3xl font-semibold text-teal-700">
                                <?= esc($initial) ?>
                            </span>
                            <img id="avatarPreview" src="" alt="" class="hidden h-full w-full object-cover">
                        <?php endif; ?>
                    </div>

                    <h2 class="text-lg font-semibold text-gray-900">
                        <?= esc($displayName) ?>
                    </h2>

                    <?php if ($email): ?>
                        <p class="mt-1 text-sm text-gray-500"><?= esc($email) ?></p>
                    <?php endif; ?>
                </div>
            </section>

            <!-- การ์ดขวา: ฟอร์มแก้ไข -->
            <section class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <form id="profileForm" method="post" action="<?= site_url('sport/profile/update') ?>"
                    enctype="multipart/form-data" class="space-y-6">
                    <?= csrf_field() ?>

                    <!-- ชื่อ -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            ชื่อที่แสดง
                        </label>
                        <input type="text" name="full_name" value="<?= esc($displayName) ?>"
                            class="block w-full rounded-xl border-gray-300 text-sm shadow-sm focus:border-[var(--primary)] focus:ring-[var(--primary)]">
                    </div>

                    <!-- เบอร์โทร -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            เบอร์โทรศัพท์
                        </label>
                        <input type="tel" name="phone_number" value="<?= esc($phone) ?>"
                            maxlength="10"
                            pattern="[0-9]{10}"
                            oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                            title="กรุณากรอกเบอร์โทรศัพท์ 10 หลัก"
                            class="block w-full rounded-xl border-gray-300 text-sm shadow-sm focus:border-[var(--primary)] focus:ring-[var(--primary)]">
                    </div>

                    <!-- เพศ + วันเกิด -->
                    <div class="grid sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                เพศ
                            </label>
                            <select name="gender"
                                class="block w-full rounded-xl border-gray-300 text-sm shadow-sm focus:border-[var(--primary)] focus:ring-[var(--primary)]">
                                <option value="">-- ไม่ระบุ --</option>
                                <option value="male" <?= $gender === 'male'   ? 'selected' : '' ?>>ชาย</option>
                                <option value="female" <?= $gender === 'female' ? 'selected' : '' ?>>หญิง</option>
                                <option value="other" <?= $gender === 'other'  ? 'selected' : '' ?>>อื่น ๆ</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                วันเกิด
                            </label>
                            <input type="date" name="birthday" value="<?= $birthday ? esc($birthday) : '' ?>"
                                class="block w-full rounded-xl border-gray-300 text-sm shadow-sm focus:border-[var(--primary)] focus:ring-[var(--primary)]">
                        </div>
                    </div>

                    <!-- อัปโหลด + ครอปรูป -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            รูปโปรไฟล์
                        </label>

                        <div class="flex flex-col gap-3">
                            <div class="flex flex-wrap items-center gap-3">
                                <input type="file" id="avatarInput" accept="image/*" class="text-sm text-gray-600">
                                <p class="text-xs text-gray-500">
                                    เลือกภาพใหม่ จากนั้นครอปในกรอบด้านล่างก่อนกดบันทึก
                                </p>
                            </div>

                            <div class="border border-dashed border-gray-300 rounded-xl p-3 bg-gray-50">
                                <div
                                    class="aspect-square max-h-80 mx-auto bg-white rounded-lg overflow-hidden flex items-center justify-center">
                                    <img id="avatarCropper" src="<?= $avatarUrl ? esc($avatarUrl) : '' ?>" alt="Cropper"
                                        class="<?= $avatarUrl ? 'max-w-full' : 'hidden' ?>">
                                    <?php if (!$avatarUrl): ?>
                                        <span class="text-xs text-gray-400">
                                            ยังไม่มีภาพโปรไฟล์ เลือกรูปเพื่อเริ่มครอป
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <input type="hidden" name="avatar_cropped" id="avatarCropped">
                    </div>

                    <!-- ปุ่ม -->
                    <div class="flex justify-end gap-3 pt-2">
                        <a href="<?= site_url('sport/profile') ?>"
                            class="inline-flex items-center rounded-full border border-gray-200 px-4 py-2 text-sm font-medium text-gray-600 hover:border-gray-300">
                            ยกเลิก
                        </a>
                        <button type="submit"
                            class="inline-flex items-center rounded-full bg-[var(--primary)] px-5 py-2 text-sm font-semibold text-white shadow-sm hover:bg-teal-600 transition">
                            บันทึกการเปลี่ยนแปลง
                        </button>
                    </div>
                </form>
            </section>
        </div>
    </div>
</div>

<!-- CropperJS (ใช้ CDN) -->
<link rel="stylesheet" href="https://unpkg.com/cropperjs@1.6.2/dist/cropper.min.css">
<script src="https://unpkg.com/cropperjs@1.6.2/dist/cropper.min.js"></script>

<script>
    (function() {
        const avatarInput = document.getElementById('avatarInput');
        const avatarCropper = document.getElementById('avatarCropper');
        const avatarCropped = document.getElementById('avatarCropped');
        const avatarPreview = document.getElementById('avatarPreview');
        const previewInitial = document.getElementById('avatarPreviewInitial');
        const form = document.getElementById('profileForm');

        let cropper = null;

        avatarInput.addEventListener('change', function(e) {
            const [file] = e.target.files;
            if (!file) return;

            const reader = new FileReader();
            reader.onload = function(ev) {
                avatarCropper.src = ev.target.result;
                avatarCropper.classList.remove('hidden');

                if (previewInitial) {
                    previewInitial.classList.add('hidden');
                }
                if (avatarPreview) {
                    avatarPreview.src = ev.target.result;
                    avatarPreview.classList.remove('hidden');
                }

                if (cropper) {
                    cropper.destroy();
                }
                cropper = new Cropper(avatarCropper, {
                    aspectRatio: 1,
                    viewMode: 1,
                    autoCropArea: 1,
                });
            };
            reader.readAsDataURL(file);
        });

        form.addEventListener('submit', function() {
            if (cropper) {
                const canvas = cropper.getCroppedCanvas({
                    width: 400,
                    height: 400,
                });
                avatarCropped.value = canvas.toDataURL('image/webp', 0.9);
            }
        });
    })();
</script>

<?= $this->endSection() ?>