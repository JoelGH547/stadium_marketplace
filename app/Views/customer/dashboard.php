<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'Customer Dashboard') ?></title>
    
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">

    <style>
        .stadium-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 20px;
        }
        .stadium-card {
            border: 1px solid #ddd;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .stadium-card-image {
            width: 100%;
            height: 180px;
            background-color: #eee;
            
        }
        .stadium-card-content {
            padding: 15px;
        }
        .stadium-card h3 {
            margin-top: 0;
        }
        .stadium-card .price {
            font-size: 1.2rem;
            font-weight: bold;
            color: #e67e22;
        }
        .stadium-card .category {
            display: inline-block;
            background-color: #3498db;
            color: white;
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 0.8rem;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <a class="navbar-brand" href="<?= base_url('customer/dashboard') ?>">Stadium Booking</a>
        <div class="collapse navbar-collapse">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <span class="navbar-text">
                        สวัสดี, <?= esc(session()->get('username')) ?>
                    </span>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= base_url('logout') ?>">Logout</a>
                </li>
            </ul>
        </div>
    </nav>

    <div class="container" style="margin-top: 30px;">

        <h1><?= esc($title) ?></h1>
        <p>ยินดีต้อนรับ! เลือกสนามที่คุณต้องการจอง:</p>

        <div class="stadium-grid">
            <?php if (! empty($stadiums) && is_array($stadiums)): ?>
                <?php foreach ($stadiums as $stadium): ?>
                    
                    <div class="stadium-card">
                        <div class="stadium-card-image">
                            </div>
                        <div class="stadium-card-content">
                            <span class="category"><?= esc($stadium['category_name'] ?? 'N/A') ?></span>
                            
                            <h3><?= esc($stadium['name']) ?></h3>
                            <p><?= esc($stadium['description']) ?></p>
                            
                            <div classs="price">
                                ราคา: <?= esc(number_format($stadium['price'], 2)) ?> บาท/ชั่วโมง
                            </div>
                            
                            <a href="<?= base_url('customer/booking/stadium/' . $stadium['id']) ?>" class="btn btn-primary" style="margin-top: 15px;">
                                ดูรายละเอียด และ จอง
                            </a>
                        </div>
                    </div>

                <?php endforeach; ?>
            <?php else: ?>
                <p>ขออภัย, ยังไม่มีสนามกีฬาที่เปิดให้จองในขณะนี้</p>
            <?php endif; ?>
        </div>

    </div> <footer class="text-center" style="margin-top: 50px; padding: 20px; background-color: #f8f9fa;">
        <p>&copy; <?= date('Y') ?> Stadium Marketplace. All rights reserved.</p>
    </footer>

</body>
</html>