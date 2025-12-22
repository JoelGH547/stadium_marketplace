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
$joinedAt = $customer['created_at'] ?? null;
$avatarPath = $customer['avatar'] ?? null;
$avatarUrl = $avatarPath ? base_url($avatarPath) : null;
$initial = mb_substr($displayName, 0, 1, 'UTF-8');

// Helper function for rendering profile items
function render_profile_item($label, $value, $default = 'ยังไม่ได้ระบุ', $icon = null)
{
    $displayValue = !empty($value) ? esc($value) : "<span class='text-gray-400'>{$default}</span>";
    $iconHtml = $icon ? "<div class='w-5 text-gray-400'>{$icon}</div>" : "";

    echo "
        <div class='flex items-start gap-4'>
            {$iconHtml}
            <div class='flex-1'>
                <dt class='text-sm font-medium text-gray-500'>{$label}</dt>
                <dd class='mt-1 text-base text-gray-800 font-semibold'>{$displayValue}</dd>
            </div>
        </div>
    ";
}
?>

<div class="bg-gray-50 min-h-screen py-8 sm:py-12">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">

        <!-- Page Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
            <h1 class="text-3xl font-bold text-gray-800">
                โปรไฟล์ของฉัน
            </h1>
            <a href="<?= site_url('sport/profile/edit') ?>"
                class="inline-flex items-center justify-center gap-2 rounded-xl bg-[var(--primary)] px-5 py-2.5 text-sm font-bold text-white shadow-lg shadow-[var(--primary)]/30 hover:bg-emerald-600 hover:-translate-y-0.5 transition-all duration-200">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.5L14.732 5.232z">
                    </path>
                </svg>
                <span>แก้ไขโปรไฟล์</span>
            </a>
        </div>

        <!-- Main Grid Layout -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

            <!-- Left Column: User Card -->
            <aside class="lg:col-span-1">
                <div class="bg-white rounded-2xl shadow-md border border-gray-100/80 overflow-hidden text-center p-6">
                    <div class="relative inline-block mb-4">
                        <?php if ($avatarUrl) : ?>
                        <img src="<?= esc($avatarUrl) ?>" alt="Avatar"
                            class="h-28 w-28 rounded-full object-cover ring-4 ring-offset-4 ring-offset-white ring-[var(--primary)]">
                        <?php else : ?>
                        <div
                            class="h-28 w-28 flex items-center justify-center rounded-full bg-teal-100 text-[var(--primary)] text-4xl font-bold ring-4 ring-offset-4 ring-offset-white ring-[var(--primary)]">
                            <?= esc($initial) ?>
                        </div>
                        <?php endif; ?>
                    </div>

                    <h2 class="text-xl font-bold text-gray-900">
                        <?= esc($displayName) ?>
                    </h2>

                    <?php if ($email) : ?>
                    <p class="mt-1 text-sm text-gray-500">
                        <?= esc($email) ?>
                    </p>
                    <?php endif; ?>

                    <?php if ($joinedAt) : ?>
                    <p class="mt-4 text-xs text-gray-400 border-t border-gray-100 pt-4">
                        สมาชิกตั้งแต่ <?= esc(date('j F Y', strtotime($joinedAt))) ?>
                    </p>
                    <?php endif; ?>
                </div>
            </aside>

            <!-- Right Column: Profile Details -->
            <main class="lg:col-span-2">
                <div class="bg-white rounded-2xl shadow-md border border-gray-100/80 p-6 md:p-8">
                    <h3 class="text-xl font-bold text-gray-900 mb-6 pb-4 border-b border-gray-200">
                        ข้อมูลส่วนตัว
                    </h3>

                    <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-8">
                        <?php
                        $userIcon = '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-full h-full"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" /></svg>';
                        $atIcon = '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-full h-full"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 12a4.5 4.5 0 11-9 0 4.5 4.5 0 019 0zm0 0c0 1.657 1.007 3 2.25 3S21 13.657 21 12a9 9 0 10-2.636 6.364M16.5 12V8.25" /></svg>';
                        $phoneIcon = '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-full h-full"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 01-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 6.75z" /></svg>';
                        $genderIcon = '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-full h-full"><path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 01-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 013.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 013.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 01-3.09 3.09zM18.259 8.715L18 9.75l-.259-1.035a3.375 3.375 0 00-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 002.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 002.456 2.456L21.75 6l-1.035.259a3.375 3.375 0 00-2.456 2.456zM16.898 20.553L16.5 21.75l-.398-1.197a3.375 3.375 0 00-2.456-2.456L12.5 17.25l1.197-.398a3.375 3.375 0 002.456-2.456L16.5 13.5l.398 1.197a3.375 3.375 0 002.456 2.456L20.5 17.25l-1.197.398a3.375 3.375 0 00-2.456 2.456z" /></svg>';
                        $cakeIcon = '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-full h-full"><path stroke-linecap="round" stroke-linejoin="round" d="M21 15.75a.75.75 0 01-.75.75H3.75a.75.75 0 01-.75-.75V14.25m18 1.5v.188c0 .533-.424 1.01-1.012 1.012H3.762A1.013 1.013 0 012.75 16.188V15.75m18-1.5V6a2.25 2.25 0 00-2.25-2.25H5.25A2.25 2.25 0 003 6v8.25m18-1.5h-2.25m-13.5 0H3M15 5.25v.01M12 5.25v.01M9 5.25v.01M6 5.25v.01M3 9.75h18" /></svg>';
                        $ageIcon = '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-full h-full"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>';

                        render_profile_item('ชื่อจริง-นามสกุล', $displayName, 'N/A', $userIcon);
                        if ($username) render_profile_item('ชื่อผู้ใช้', $username, 'N/A', $atIcon);
                        if ($phone) render_profile_item('เบอร์โทร', $phone, 'ยังไม่ได้ระบุ', $phoneIcon);

                        $genderDisplay = 'ยังไม่ได้ระบุ';
                        if ($gender === 'male') $genderDisplay = 'ชาย';
                        elseif ($gender === 'female') $genderDisplay = 'หญิง';
                        elseif ($gender === 'other') $genderDisplay = 'อื่นๆ';
                        render_profile_item('เพศ', $genderDisplay, 'ยังไม่ได้ระบุ', $genderIcon);

                        render_profile_item('วันเกิด', $birthday ? date('d F Y', strtotime($birthday)) : null, 'ยังไม่ได้ระบุ', $cakeIcon);

                        $age = null;
                        if ($birthday) {
                            try {
                                $age = (new DateTime($birthday))->diff(new DateTime())->y . " ปี";
                            } catch (Exception $e) {
                                $age = null;
                            }
                        }
                        render_profile_item('อายุ', $age, 'ยังไม่ได้ระบุ', $ageIcon);
                        ?>
                    </dl>
                </div>
            </main>

        </div>
    </div>
</div>

<?= $this->endSection() ?>