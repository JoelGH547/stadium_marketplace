<?php $siteName = $siteName ?? 'Stadium Marketplace'; ?>
<?php
$customerLoggedIn = (bool) session('customer_logged_in');
$customerUsername = session('customer_username') ?? 'ผู้ใช้';
$siteName = $siteName ?? 'Stadium Marketplace';
?>
<header class="relative z-40">
    <div class="border-b border-[var(--line)] bg-white">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 h-14 flex items-center justify-between">

            <!-- Logo -->
            <a href="<?= base_url('sport') ?>" class="flex items-center gap-2 group transition">
                <span
                    class="inline-flex h-8 w-8 rounded-lg bg-[var(--primary)] text-white items-center justify-center font-bold transition-transform group-hover:scale-110">
                    SA
                </span>
                <span
                    class="font-semibold text-[var(--primary)] group-hover:text-[var(--primary)]/80 transition-colors">
                    <?= esc($siteName) ?>
                </span>
            </a>

            <!-- NAV -->
            <nav class="hidden sm:flex items-center gap-6">

                <!-- หน้าแรก -->
                <a href="<?= base_url('sport') ?>"
                    class="flex items-center gap-2 text-[var(--primary)] hover:text-[var(--primary)]/80 transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                        stroke-width="1.5" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H18.375c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
                    </svg>
                    หน้าแรก
                </a>

                <!-- เกี่ยวกับเรา -->
                <a href="<?= base_url('arena#about') ?>"
                    class="flex items-center gap-2 text-[var(--primary)] hover:text-[var(--primary)]/80 transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                        stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" />

                    </svg>
                    เกี่ยวกับเรา
                </a>

                <!-- ติดต่อเรา -->
                <a href="<?= base_url('arena#contact') ?>"
                    class="flex items-center gap-2 text-[var(--primary)] hover:text-[var(--primary)]/80 transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                        stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" />
                    </svg>
                    ติดต่อเรา
                </a>
                <?php if (! $customerLoggedIn): ?>
                    <!-- ยังไม่ล็อกอิน: แสดงปุ่ม Login -->
                    <a href="<?= base_url('customer/login') ?>"
                        class="inline-flex items-center gap-1 rounded-full border border-[var(--primary)] px-3 py-1.5 text-xs font-medium text-[var(--primary)] hover:bg-[var(--primary)] hover:text-white transition">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6A2.25 2.25 0 005.25 5.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M18 12H9m0 0l3-3m-3 3l3 3" />
                        </svg>
                        Login
                    </a>
                <?php else: ?>
                    <div class="relative">
                        <button id="customerMenuButton" type="button"
                            class="inline-flex items-center justify-center h-9 w-9 rounded-full bg-gray-200 hover:bg-gray-300 text-gray-700 transition">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                stroke="currentColor" class="w-5 h-5">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 20.25a8.25 8.25 0 0115 0" />
                            </svg>
                        </button>

                        <div id="customerMenuDropdown"
                            class="hidden absolute right-0 mt-3 w-56 rounded-xl border border-gray-200 bg-white shadow-xl z-[999]">
                            <div class="px-4 py-3 border-b border-gray-100">
                                <p class="text-sm font-semibold text-gray-900 truncate">
                                    ชื่อผู้ใช้ <?= esc($customerUsername) ?>
                                </p>
                            </div>

                            <nav class="py-1 text-sm text-gray-700">

                                <a href="<?= base_url('sport/profile') ?>"
                                    class="flex items-center gap-3 px-4 py-2 hover:bg-gray-100 transition rounded-md">
                                    <svg class="w-5 h-5 text-gray-600" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                            d="M12 14c-4.418 0-8 2.015-8 4.5V20h16v-1.5c0-2.485-3.582-4.5-8-4.5z" />
                                    </svg>
                                    โปรไฟล์ของฉัน
                                </a>

                                <a href="<?= base_url('sport/booking_history') ?>"
                                    class="flex items-center gap-3 px-4 py-2 hover:bg-gray-100 transition rounded-md">
                                    <svg class="w-5 h-5 text-blue-500" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                                    </svg>
                                    รายการจองของฉัน
                                </a>

                                <a href="<?= base_url('sport/favorites') ?>"
                                    class="flex items-center gap-3 px-4 py-2 hover:bg-gray-100 transition rounded-md">
                                    <svg class="w-5 h-5 text-yellow-500" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                            d="M12 17l-5.447 2.863L7 13.118 2 8.736l6.276-.908L12 2l3.724 5.828L22 8.736l-5 4.382 1.447 6.745z" />
                                    </svg>
                                    สถานที่โปรด
                                </a>
                            </nav>

                        </div>
                    </div>
                    <!-- ปุ่ม Logout เดิม (ยังอยู่เหมือนเดิม) -->
                    <a href="<?= base_url('customer/logout') ?>"
                        class="inline-flex items-center gap-1 rounded-full border border-gray-300 px-3 py-1.5 text-xs font-medium text-gray-700 hover:bg-gray-100 transition">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6A2.25 2.25 0 005.25 5.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M18 12H9m0 0l3-3m-3 3l3 3" />
                        </svg>
                        Logout
                    </a>
                <?php endif; ?>
            </nav>
        </div>
    </div>
    <script>
        (function() {
            const btn = document.getElementById('customerMenuButton');
            const menu = document.getElementById('customerMenuDropdown');

            if (!btn || !menu) return;

            btn.addEventListener('click', function(e) {
                e.stopPropagation();
                menu.classList.toggle('hidden');
            });

            document.addEventListener('click', function() {
                menu.classList.add('hidden');
            });
        })();
    </script>


</header>