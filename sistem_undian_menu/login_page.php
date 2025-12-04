<?php
require_once 'db_connection.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_type = $_POST['user_type'];
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password'];
    
    if ($user_type == 'admin') {
        // Admin login
        $query = "SELECT * FROM admin WHERE idadmin = '$username' AND password_admin = '$password'";
        $result = mysqli_query($conn, $query);
        
        if (mysqli_num_rows($result) == 1) {
            $admin = mysqli_fetch_assoc($result);
            $_SESSION['user_type'] = 'admin';
            $_SESSION['user_id'] = $admin['idadmin'];
            $_SESSION['user_name'] = $admin['namaadmin'];
            header('Location: admin_dashboard.php');
            exit();
        } else {
            $error = 'ID Admin atau kata laluan salah!';
        }
    } else {
        // Voter login
        $query = "SELECT * FROM pengundi WHERE idpengundi = '$username' AND password = '$password'";
        $result = mysqli_query($conn, $query);
        
        if (mysqli_num_rows($result) == 1) {
            $pengundi = mysqli_fetch_assoc($result);
            $_SESSION['user_type'] = 'pengundi';
            $_SESSION['user_id'] = $pengundi['idpengundi'];
            $_SESSION['user_name'] = $pengundi['namapengundi'];
            header('Location: voter_dashboard.php');
            exit();
        } else {
            $error = 'ID Pengundi atau kata laluan salah!';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Undian Menu Harian - SMK Schuwenzel Paul</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        
        .login-container {
            background: white;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            width: 100%;
            max-width: 450px;
        }
        
        .school-header {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .school-header h1 {
            color: #667eea;
            font-size: 24px;
            margin-bottom: 5px;
        }
        
        .school-header h2 {
            color: #764ba2;
            font-size: 18px;
            margin-bottom: 10px;
        }
        
        .school-header p {
            color: #666;
            font-size: 14px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: 600;
        }
        
        input[type="text"],
        input[type="password"],
        select {
            width: 100%;
            padding: 12px;
            border: 2px solid #ddd;
            border-radius: 8px;
            font-size: 14px;
            transition: border-color 0.3s;
        }
        
        input[type="text"]:focus,
        input[type="password"]:focus,
        select:focus {
            outline: none;
            border-color: #667eea;
        }
        
        .btn-login {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.2s;
        }
        
        .btn-login:hover {
            transform: translateY(-2px);
        }
        
        .error-message {
            background: #fee;
            color: #c33;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            text-align: center;
        }
        
        .user-type-selector {
            display: flex;
            gap: 10px;
            margin-bottom: 25px;
        }
        
        .user-type-btn {
            flex: 1;
            padding: 12px;
            border: 2px solid #ddd;
            background: white;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .user-type-btn.active {
            background: #667eea;
            color: white;
            border-color: #667eea;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="school-header">
            <h1>SMK Schuwenzel Paul</h1>
            <h2>Sistem Undian Menu Harian</h2>
            <p>Sila log masuk untuk meneruskan</p>
        </div>
        
        <?php if ($error): ?>
            <div class="error-message"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <form method="POST" action="">
            <div class="form-group">
                <label>Jenis Pengguna:</label>
                <select name="user_type" required>
                    <option value="pengundi">Pengundi</option>
                    <option value="admin">Admin</option>
                </select>
            </div>
            
            <div class="form-group">
                <label>ID Pengguna:</label>
                <input type="text" name="username" required placeholder="Masukkan ID anda">
            </div>
            
            <div class="form-group">
                <label>Kata Laluan:</label>
                <input type="password" name="password" required placeholder="Masukkan kata laluan">
            </div>
            
            <button type="submit" class="btn-login">Log Masuk</button>
        </form>
    </div>
</body>
</html>