<?= $this->extend('layouts/public') ?>

<?= $this->section('content') ?>

<?php
// Prepare user data
$displayName = $customer['full_name'] ?: $customer['username'] ?: $customer['email'] ?: 'ผู้ใช้งาน';
$username = $customer['username'] ?? null;
$email = $customer['email'] ?? null;
$phone = $customer['phone_number'] ?? null;
$gender = $customer['gender'] ?? null;
$birthday = $customer['birthday'] ?? null;
$avatarPath = $customer['avatar'] ?? null;
$avatarUrl = $avatarPath ? base_url($avatarPath) : null;
$initial = mb_substr($displayName, 0, 1, 'UTF-8');
?>

<div class="bg-gray-50 min-h-screen py-8 sm:py-12">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">

        <!-- Page Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-800">
                    แก้ไขโปรไฟล์
                </h1>
                <p class="mt-1 text-sm text-gray-500">
                    ปรับข้อมูลส่วนตัวและรูปโปรไฟล์ของคุณ
                </p>
            </div>
            <a href="<?= site_url('sport/profile') ?>"
                class="inline-flex items-center justify-center gap-2 rounded-xl bg-white border border-gray-200 px-5 py-2.5 text-sm font-bold text-gray-600 shadow-sm hover:bg-gray-100 hover:-translate-y-0.5 transition-all duration-200">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                </svg>
                <span>ย้อนกลับ</span>
            </a>
        </div>

        <!-- Validation Errors -->
        <?php if (session()->has('errors')) : ?>
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-lg mb-6" role="alert">
                <p class="font-bold">เกิดข้อผิดพลาด</p>
                <ul>
                    <?php foreach (session('errors') as $error) : ?>
                        <li class="ml-4 list-disc"><?= esc($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <!-- Main Grid Layout -->
        <form id="profileForm" method="post" action="<?= site_url('sport/profile/update') ?>" enctype="multipart/form-data" class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <?= csrf_field() ?>

            <!-- Left Column: User Card -->
            <aside class="lg:col-span-1">
                <div class="bg-white rounded-2xl shadow-md border border-gray-100/80 overflow-hidden text-center p-6">
                    <div class="relative inline-block mb-4">
                        <div class="h-28 w-28 rounded-full bg-teal-100 flex items-center justify-center ring-4 ring-offset-4 ring-offset-white ring-[var(--primary)]">
                            <?php if ($avatarUrl): ?>
                                <img id="avatarPreview" src="<?= esc($avatarUrl) ?>" alt="Avatar preview" class="h-full w-full object-cover rounded-full">
                                <span id="avatarPreviewInitial" class="hidden text-4xl font-bold text-[var(--primary)]"><?= esc($initial) ?></span>
                            <?php else: ?>
                                <img id="avatarPreview" src="" alt="Avatar preview" class="hidden h-full w-full object-cover rounded-full">
                                <span id="avatarPreviewInitial" class="text-4xl font-bold text-[var(--primary)]"><?= esc($initial) ?></span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <h2 class="text-xl font-bold text-gray-900">
                        <?= esc($displayName) ?>
                    </h2>

                    <?php if ($email): ?>
                        <p class="mt-1 text-sm text-gray-500"><?= esc($email) ?></p>
                    <?php endif; ?>

                    <label for="avatarInput" class="mt-4 inline-block cursor-pointer rounded-lg bg-gray-100 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-200 transition">
                        เปลี่ยนรูปภาพ
                    </label>
                    <input type="file" id="avatarInput" name="avatar" accept="image/*" class="hidden">
                    <input type="hidden" name="avatar_cropped" id="avatarCropped">
                </div>
            </aside>

            <!-- Right Column: Profile Form -->
            <main class="lg:col-span-2">
                <div class="bg-white rounded-2xl shadow-md border border-gray-100/80 p-6 md:p-8">
                    <h3 class="text-xl font-bold text-gray-900 mb-6 pb-4 border-b border-gray-200">
                        ข้อมูลส่วนตัว
                    </h3>

                    <div class="space-y-6">
                        <!-- Full Name -->
                        <div>
                            <label for="full_name" class="block text-sm font-medium text-gray-700 mb-1.5">ชื่อ-นามสกุล</label>
                            <input type="text" id="full_name" name="full_name" value="<?= esc($customer['full_name'] ?? '') ?>" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-[var(--primary)] focus:ring-[var(--primary)] sm:text-sm">
                        </div>

                        <!-- Username -->
                        <div>
                            <label for="username" class="block text-sm font-medium text-gray-700 mb-1.5">ชื่อผู้ใช้ (Username)</label>
                            <input type="text" id="username" name="username" value="<?= esc($username) ?>" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-[var(--primary)] focus:ring-[var(--primary)] sm:text-sm" placeholder="ตัวอักษรภาษาอังกฤษหรือตัวเลขเท่านั้น">
                        </div>

                        <!-- Phone Number -->
                        <div>
                            <label for="phone_number" class="block text-sm font-medium text-gray-700 mb-1.5">เบอร์โทรศัพท์</label>
                            <input type="tel" id="phone_number" name="phone_number" value="<?= esc($phone) ?>" maxlength="10" pattern="[0-9]{10}" oninput="this.value = this.value.replace(/[^0-9]/g, '')" title="กรุณากรอกเบอร์โทรศัพท์ 10 หลัก" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-[var(--primary)] focus:ring-[var(--primary)] sm:text-sm">
                        </div>

                        <!-- Gender & Birthday -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                            <div>
                                <label for="gender" class="block text-sm font-medium text-gray-700 mb-1.5">เพศ</label>
                                <select id="gender" name="gender" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-[var(--primary)] focus:ring-[var(--primary)] sm:text-sm">
                                    <option value="">-- ไม่ระบุ --</option>
                                    <option value="male" <?= $gender === 'male' ? 'selected' : '' ?>>ชาย</option>
                                    <option value="female" <?= $gender === 'female' ? 'selected' : '' ?>>หญิง</option>
                                    <option value="other" <?= $gender === 'other' ? 'selected' : '' ?>>อื่นๆ</option>
                                </select>
                            </div>
                            <div>
                                <label for="birthday" class="block text-sm font-medium text-gray-700 mb-1.5">วันเกิด</label>
                                <input type="date" id="birthday" name="birthday" value="<?= $birthday ? esc($birthday) : '' ?>" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-[var(--primary)] focus:ring-[var(--primary)] sm:text-sm">
                            </div>
                        </div>

                        <!-- Avatar Cropper -->
                        <div id="cropperContainer" class="hidden">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                ครอปรูปโปรไฟล์
                            </label>
                            <div class="border border-dashed border-gray-300 rounded-xl p-3 bg-gray-50">
                                <div class="aspect-square max-h-80 mx-auto bg-white rounded-lg overflow-hidden flex items-center justify-center">
                                    <img id="avatarCropper" src="" alt="Cropper">
                                </div>
                            </div>
                             <p class="text-xs text-gray-500 mt-2">
                                перетащите, чтобы настроить область обрезки, затем нажмите «Сохранить изменения».
                            </p>
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="flex justify-end gap-3 pt-8 mt-8 border-t border-gray-200">
                        <a href="<?= site_url('sport/profile') ?>" class="inline-flex items-center rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition">
                            ยกเลิก
                        </a>
                        <button type="submit" class="inline-flex items-center justify-center rounded-lg bg-[var(--primary)] px-5 py-2 text-sm font-bold text-white shadow-lg shadow-[var(--primary)]/30 hover:bg-emerald-600 hover:-translate-y-0.5 transition-all duration-200">
                            บันทึกการเปลี่ยนแปลง
                        </button>
                    </div>
                </div>
            </main>
        </form>
    </div>
</div>

<!-- CropperJS Modal and Script -->
<div id="cropperModal" class="fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center p-4 z-50 hidden">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-md p-6">
        <h3 class="text-lg font-bold text-gray-900 mb-4">ตัดรูปโปรไฟล์</h3>
        <div class="w-full h-64 bg-gray-100 rounded-lg">
            <img id="imageToCrop" src="" alt="Image to crop">
        </div>
        <div class="flex justify-end gap-3 mt-6">
            <button type="button" id="cancelCrop" class="inline-flex items-center rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50">ยกเลิก</button>
            <button type="button" id="confirmCrop" class="inline-flex items-center justify-center rounded-lg bg-[var(--primary)] px-5 py-2 text-sm font-bold text-white shadow-sm hover:bg-emerald-600">ยืนยันและใช้รูปนี้</button>
        </div>
    </div>
</div>


<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.2/cropper.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.2/cropper.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const avatarInput = document.getElementById('avatarInput');
    const avatarCroppedInput = document.getElementById('avatarCropped');
    const avatarPreview = document.getElementById('avatarPreview');
    const avatarPreviewInitial = document.getElementById('avatarPreviewInitial');
    const form = document.getElementById('profileForm');

    const modal = document.getElementById('cropperModal');
    const imageToCrop = document.getElementById('imageToCrop');
    const cancelCropBtn = document.getElementById('cancelCrop');
    const confirmCropBtn = document.getElementById('confirmCrop');

    let cropper = null;
    let originalFile = null;

    avatarInput.addEventListener('change', function(e) {
        const files = e.target.files;
        if (files && files.length > 0) {
            originalFile = files[0];
            const reader = new FileReader();
            reader.onload = function (event) {
                imageToCrop.src = event.target.result;
                modal.classList.remove('hidden');

                if (cropper) {
                    cropper.destroy();
                }

                cropper = new Cropper(imageToCrop, {
                    aspectRatio: 1,
                    viewMode: 1,
                    background: false,
                    autoCropArea: 1,
                    movable: true,
                    zoomable: true,
                    rotatable: true,
                    scalable: true,
                });
            };
            reader.readAsDataURL(originalFile);
        }
    });

    cancelCropBtn.addEventListener('click', () => {
        modal.classList.add('hidden');
        if (cropper) {
            cropper.destroy();
            cropper = null;
        }
        avatarInput.value = ''; // Reset file input
    });

    confirmCropBtn.addEventListener('click', () => {
        if (cropper) {
            const canvas = cropper.getCroppedCanvas({
                width: 400,
                height: 400,
                imageSmoothingQuality: 'high',
            });

            canvas.toBlob((blob) => {
                const reader = new FileReader();
                reader.onloadend = function() {
                    avatarCroppedInput.value = reader.result;

                    // Update preview
                    avatarPreview.src = reader.result;
                    avatarPreview.classList.remove('hidden');
                    if(avatarPreviewInitial) avatarPreviewInitial.classList.add('hidden');

                    modal.classList.add('hidden');
                    cropper.destroy();
                    cropper = null;
                };
                reader.readAsDataURL(blob);
            }, 'image/webp', 0.9);
        }
    });
});
</script>

<?= $this->endSection() ?>