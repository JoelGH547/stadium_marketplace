<?php 
    $outsideImages = json_decode($stadium['outside_images'] ?? '[]', true);
    $heroImage = (!empty($outsideImages)) ? base_url('assets/uploads/stadiums/' . $outsideImages[0]) : base_url('assets/uploads/no-image.png');
?>

<!-- Hero Banner -->
<div class="hero-banner" style="background-image: url('<?= $heroImage ?>');">
    <div class="hero-overlay"></div>
    <div class="hero-content">
        <h1 class="fw-bold mb-0"><?= esc($stadium['name']) ?></h1>
        <p class="mb-0 text-white-50">📍 <?= esc($stadium['province']) ?></p>
    </div>
</div>
