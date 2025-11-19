<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>

<h1><?= esc($title ?? 'Stadiums') ?></h1>

<p>
    <a href="<?= base_url('admin/stadiums/create') ?>" class="btn btn-primary">
        Add New Stadium
    </a>
</p>

<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success">
        <?= esc(session()->getFlashdata('success')) ?>
    </div>
<?php endif; ?>

<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger">
        <?= esc(session()->getFlashdata('error')) ?>
    </div>
<?php endif; ?>

<table class="table table-bordered table-striped">
    <thead>
        <tr>
            <th>ID</th>
            <th>Cover</th>
            <th>Name</th>
            <th>Category</th>
            <th>Vendor</th>
            <th>Province</th>
            <th>Price/Hour (฿)</th>
            <th>Map</th>
            <th style="width: 150px;">Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php if (!empty($stadiums)): ?>
            <?php foreach ($stadiums as $index => $stadium): ?>
                <?php
                    $outsideArr = json_decode($stadium['outside_images'] ?? '[]', true) ?: [];
                    $cover      = $outsideArr[0] ?? null;
                ?>
                <tr>
                    <td><?= $index + 1 ?></td>
                    <td>
                        <?php if ($cover): ?>
                            <img src="<?= base_url('assets/uploads/stadiums/' . $cover) ?>"
                                 alt="Cover"
                                 style="width: 90px; height: 60px; object-fit: cover; border-radius: 4px;">
                        <?php else: ?>
                            <span class="text-muted">No image</span>
                        <?php endif; ?>
                    </td>
                    <td><?= esc($stadium['name']) ?></td>
                    <td>
                    <span class="me-1"><?= $stadium['category_emoji'] ?></span> 
                    <?= esc($stadium['category_name']) ?> </td>
                    <td><?= esc($stadium['vendor_name'] ?? '-') ?></td>
                    <td><?= esc($stadium['province'] ?? '-') ?></td>
                    <td><?= number_format((float) $stadium['price'], 2) ?></td>
                    <td>
                        <?php if (!empty($stadium['lat']) && !empty($stadium['lng'])): ?>
                            <button type="button"
                                    class="btn btn-sm btn-outline-secondary js-open-map"
                                    data-lat="<?= esc($stadium['lat']) ?>"
                                    data-lng="<?= esc($stadium['lng']) ?>">
                                🗺
                            </button>
                        <?php elseif (!empty($stadium['map_link'])): ?>
                            <a href="<?= esc($stadium['map_link']) ?>" target="_blank" class="btn btn-sm btn-outline-secondary">
                                Link
                            </a>
                        <?php else: ?>
                            <span class="text-muted">-</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <a href="<?= base_url('admin/stadiums/edit/' . $stadium['id']) ?>"
                           class="btn btn-sm btn-warning">Edit</a>

                        <a href="<?= base_url('admin/stadiums/delete/' . $stadium['id']) ?>"
                           class="btn btn-sm btn-danger"
                           onclick="return confirm('Are you sure you want to delete this stadium?');">
                            Delete
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="9" style="text-align: center;">No stadiums found.</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>

<!-- Modal แผนที่แบบ custom (ไม่พึ่ง Bootstrap JS) -->
<div id="mapModal" class="stadium-map-modal" style="display:none;">
    <div class="stadium-map-backdrop"></div>
    <div class="stadium-map-dialog">
        <button type="button" class="close stadium-map-close" aria-label="Close">&times;</button>
        <h5>Stadium Location</h5>
        <div class="stadium-map-frame-wrapper">
            <iframe id="stadiumMapFrame"
                    src=""
                    width="100%"
                    height="350"
                    style="border:0;"
                    allowfullscreen=""
                    loading="lazy"
                    referrerpolicy="no-referrer-when-downgrade"></iframe>
        </div>
    </div>
</div>

<style>
    .stadium-map-modal {
        position: fixed;
        z-index: 1050;
        inset: 0;
        display: none;
    }
    .stadium-map-backdrop {
        position: absolute;
        inset: 0;
        background: rgba(0, 0, 0, 0.5);
    }
    .stadium-map-dialog {
        position: relative;
        max-width: 700px;
        margin: 60px auto;
        background: #fff;
        border-radius: 6px;
        padding: 16px 20px 20px;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.3);
        z-index: 1051;
    }
    .stadium-map-close {
        border: none;
        background: transparent;
        font-size: 24px;
        float: right;
        cursor: pointer;
    }
    .stadium-map-frame-wrapper {
        margin-top: 10px;
    }
</style>

<script>
    (function () {
        const modal   = document.getElementById('mapModal');
        const iframe  = document.getElementById('stadiumMapFrame');
        const closeBtn = modal ? modal.querySelector('.stadium-map-close') : null;
        const backdrop = modal ? modal.querySelector('.stadium-map-backdrop') : null;

        function openMapModal(lat, lng) {
            if (!lat || !lng) {
                alert('ไม่พบพิกัดแผนที่ของสนามนี้');
                return;
            }
            const url = 'https://www.google.com/maps?q=' + encodeURIComponent(lat + ',' + lng) + '&hl=th&z=16&output=embed';
            iframe.src = url;
            modal.style.display = 'block';
        }

        function closeMapModal() {
            modal.style.display = 'none';
            iframe.src = '';
        }

        document.querySelectorAll('.js-open-map').forEach(function (btn) {
            btn.addEventListener('click', function () {
                const lat = this.getAttribute('data-lat');
                const lng = this.getAttribute('data-lng');
                openMapModal(lat, lng);
            });
        });

        if (closeBtn) closeBtn.addEventListener('click', closeMapModal);
        if (backdrop) backdrop.addEventListener('click', closeMapModal);
    })();
</script>

<?= $this->endSection() ?>
