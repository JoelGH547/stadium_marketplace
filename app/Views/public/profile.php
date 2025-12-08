<?= $this->extend('layouts/public') ?>

<?= $this->section('content') ?>

<?php
// เตรียมค่าพื้นฐานจาก customer
$displayName = $customer['full_name']
    ?? $customer['username']
    ?? $customer['email']
    ?? 'ผู้ใช้งาน';

$username = $customer['username']      ?? null;
$email    = $customer['email']        ?? null;
$phone    = $customer['phone_number'] ?? null;
$gender   = $customer['gender']       ?? null;
$birthday = $customer['birthday']     ?? null;
$joinedAt = $customer['created_at']   ?? null;

// avatar (เก็บ path รูป เช่น uploads/avatars/...)
$avatarPath = $customer['avatar'] ?? null;
$avatarUrl  = $avatarPath ? base_url($avatarPath) : null;

// ตัวอักษรย่อบน avatar ถ้าไม่มีรูป
$initial = mb_substr($displayName, 0, 1, 'UTF-8');
?>

<div class="bg-gray-100">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-10">
        <div class="mb-6 flex items-center justify-between gap-3">
            <h1 class="text-2xl sm:text-3xl font-semibold text-gray-900">
                โปรไฟล์ของฉัน
            </h1>
            <a href="<?= site_url('sport/profile/edit') ?>"
                class="inline-flex items-center rounded-full border border-[var(--primary)] px-4 py-1.5 text-sm font-medium text-[var(--primary)] hover:bg-[var(--primary)] hover:text-white transition">
                แก้ไขโปรไฟล์
            </a>
        </div>

        <div class="grid md:grid-cols-[260px,1fr] gap-6">
            <!-- การ์ดซ้าย: avatar + ข้อมูลสรุป -->
            <section class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="h-2 bg-[var(--primary)]"></div>

                <div class="p-6 flex flex-col items-center text-center">
                    <div class="relative mb-4">
                        <?php if ($avatarUrl): ?>
                            <img src="<?= esc($avatarUrl) ?>" alt="Avatar"
                                class="h-24 w-24 rounded-full object-cover ring-2 ring-[var(--primary)] ring-offset-2 ring-offset-white">
                        <?php else: ?>
                            <div
                                class="h-24 w-24 flex items-center justify-center rounded-full bg-teal-500/10 text-teal-700 text-3xl font-semibold ring-2 ring-[var(--primary)] ring-offset-2 ring-offset-white">
                                <?= esc($initial) ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <h2 class="text-lg font-semibold text-gray-900">
                        <?= esc($displayName) ?>
                    </h2>

                    <?php if ($email): ?>
                        <p class="mt-1 text-sm text-gray-500">
                            <?= esc($email) ?>
                        </p>
                    <?php endif; ?>

                    <?php if ($joinedAt): ?>
                        <p class="mt-2 text-xs text-gray-400">
                            สมาชิกตั้งแต่
                            <?= esc(date('d/m/Y', strtotime($joinedAt))) ?>
                        </p>
                    <?php endif; ?>
                </div>

                <div class="border-t border-gray-100 px-6 py-4 text-sm">
                    <dl class="space-y-2">
                        <?php if ($phone): ?>
                            <div class="flex items-center justify-between gap-3">
                                <dt class="text-gray-500">เบอร์โทร</dt>
                                <dd class="font-medium text-gray-900"><?= esc($phone) ?></dd>
                            </div>
                        <?php endif; ?>

                        <?php
                        $genderDisplay = 'ยังไม่ได้ระบุ';
                        if ($gender === 'male') {
                            $genderDisplay = 'ชาย';
                        } elseif ($gender === 'female') {
                            $genderDisplay = 'หญิง';
                        } elseif ($gender === 'other') {
                            $genderDisplay = 'อื่นๆ';
                        }
                        ?>
                        <div class="flex items-center justify-between gap-3">
                            <dt class="text-gray-500">เพศ</dt>
                            <dd class="font-medium text-gray-900">
                                <?= esc($genderDisplay) ?>
                            </dd>
                        </div>

                        <div class="flex items-center justify-between gap-3">
                            <dt class="text-gray-500">วันเกิด</dt>
                            <dd class="font-medium text-gray-900">
                                <?php if ($birthday): ?>
                                    <?= esc(date('d/m/Y', strtotime($birthday))) ?>
                                <?php else: ?>
                                    ยังไม่ได้ระบุ
                                <?php endif; ?>
                            </dd>
                        </div>

                        <div class="flex items-center justify-between gap-3">
                            <dt class="text-gray-500">อายุ</dt>
                            <dd class="font-medium text-gray-900">
                                <?php if (isset($age)): ?>
                                    <?= esc($age) ?> ปี
                                <?php else: ?>
                                    ยังไม่ได้ระบุ
                                <?php endif; ?>
                            </dd>
                        </div>
                    </dl>
                </div>
            </section>

            <!-- การ์ดขวา: รายละเอียดบัญชี + ความปลอดภัย -->
            <section class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 space-y-6">
                <div>
                    <h3 class="text-base font-semibold text-gray-900 mb-3">
                        ข้อมูลบัญชี
                    </h3>
                    <dl class="space-y-2 text-sm">
                        <div class="flex justify-between gap-3">
                            <dt class="text-gray-500">ชื่อที่แสดง</dt>
                            <dd class="font-medium text-right text-gray-900">
                                <?= esc($displayName) ?>
                            </dd>
                        </div>

                        <?php if ($username): ?>
                            <div class="flex justify-between gap-3">
                                <dt class="text-gray-500">ชื่อผู้ใช้ (Username)</dt>
                                <dd class="font-medium text-right text-gray-900">
                                    <?= esc($username) ?>
                                </dd>
                            </div>
                        <?php endif; ?>

                        <?php if ($email): ?>
                            <div class="flex justify-between gap-3">
                                <dt class="text-gray-500">อีเมล</dt>
                                <dd class="font-medium text-right text-gray-900">
                                    <?= esc($email) ?>
                                </dd>
                            </div>
                        <?php endif; ?>
                    </dl>
                </div>

                <div class="border-t border-dashed border-gray-200 pt-4">
                    <h3 class="text-base font-semibold text-gray-900 mb-3">
                        ความปลอดภัยของบัญชี
                    </h3>
                    <div class="flex flex-wrap items-center gap-3 text-sm">
                        <span
                            class="inline-flex items-center rounded-full bg-emerald-50 px-3 py-1 text-xs font-medium text-emerald-700">
                            ใช้งานได้ปกติ
                        </span>
                    </div>
                </div>
            </section>
        </div>
    </div>
</div>

<?= $this->endSection() ?>