<?= $this->extend('customer/layout') ?>
<?= $this->section('content') ?>

<style>
    /* (CSS ของการ์ด... เราจะใช้ .table แทน) */
</style>

<h1>Customer Dashboard</h1>

<?php if (isset($customer)): ?>
    <p>สวัสดี, **<?= esc($customer['full_name'] ?? $customer['username']) ?>**!</p>
    <p>คุณล็อคอินในฐานะ **ลูกค้า (Customer)** กรุณาเลือกสนามที่คุณต้องการจอง จากรายการด้านล่าง:</p>
<?php endif; ?>


<h3 style="margin-top: 30px;">Available Stadiums (สนามที่เปิดให้จอง)</h3>
<table class="table">
    <thead>
        <tr>
            <th>Name (ชื่อสนาม)</th>
            <th>Category (ประเภท)</th>
            <th>Price (ราคา/ชม.)</th>
            <th>Description (รายละเอียด)</th>
            <th>Action (จอง)</th>
        </tr>
    </thead>
    <tbody>
        <?php if (! empty($stadiums) && is_array($stadiums)): ?>
            
            <?php foreach ($stadiums as $stadium): ?>
                <tr>
                    <td><?= esc($stadium['name']) ?></td>
                    <td><?= esc($stadium['category_name'] ?? 'N/A') ?></td>
                    <td><?= esc(number_format($stadium['price'], 2)) ?></td>
                    <td><?= esc($stadium['description']) ?></td>
                    <td>
                        <a href="<?= base_url('customer/book/' . $stadium['id']) ?>" 
                           class="btn btn-primary btn-sm">
                           Book Now (จองเลย)
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="5" style="text-align: center;">
                    Sorry, there are no stadiums available for booking right now.
                </td>
            </tr>
        <?php endif ?>
    </tbody>
</table>

<?= $this->endSection() ?>