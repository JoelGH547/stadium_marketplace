<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= (isset($title) ? esc($title) . ' | ' : '') ?>Stadium Admin</title>
    
    
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            margin: 0;
            background-color: #f4f6f9; 
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        .auth-container {
            width: 100%;
            max-width: 400px;
            margin: 20px;
        }
        .auth-card {
            background-color: #ffffff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        .auth-card h1 {
            text-align: center;
            color: #333;
            margin-top: 0;
            margin-bottom: 25px;
        }
        
       
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 600;
        }
        .form-control {
            width: 100%;
            padding: 10px;
            box-sizing: border-box; 
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .btn {
            width: 100%;
            padding: 12px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            font-weight: 600;
        }
        .btn-primary {
            background-color: #007bff;
            color: white;
        }
        .auth-links {
            text-align: center;
            margin-top: 20px;
        }
        
        
        .message { padding: 10px; margin-bottom: 15px; border-radius: 4px; border: 1px solid transparent; }
        .success { background-color: #d4edda; color: #155724; border-color: #c3e6cb; }
        .error { background-color: #f8d7da; color: #721c24; border-color: #f5c6cb; }
        .error ul { margin: 0; padding-left: 20px; }
        
    </style>
</head>
<body>

    <div class="auth-container">
    
      
          
        <?= $this->renderSection('content') ?>
        
    </div>

</body>
</html>