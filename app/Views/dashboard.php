<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body { font-family: sans-serif; padding: 20px; }
        .welcome { font-size: 1.5em; }
        .logout-btn { 
            margin-top: 20px; 
            padding: 10px 15px; 
            background: #f44336; 
            color: white; 
            text-decoration: none; 
            border-radius: 5px;
        }
    </style>
</head>
<body>

    <h2>Welcome to the Dashboard!</h2>

    <hr>

    <?php 
        // ดึงข้อมูล session ที่เรา "set" ไว้ตอน Login
        $session = session(); 
    ?>

    <p class="welcome">
        Hello, <strong><?= $session->get('username') ?></strong>!
    </p>

    <p>Your User ID is: <?= $session->get('user_id') ?></p>
    <p>Your Role is: <strong><?= $session->get('role') ?></strong></p>

    <!-- 
    นี่คือปุ่ม Logout
    เราจะสร้าง Route /logout ในขั้นตอนต่อไป
    -->
    <a href="/logout" class="logout-btn">Logout</a>

</body>
</html>