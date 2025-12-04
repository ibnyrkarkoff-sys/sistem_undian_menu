<?php
require_once 'db_connection.php';

// Check if user is logged in as voter
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] != 'pengundi') {
    header('Location: index.php');
    exit();
}

// Get voter information
$voter_id = $_SESSION['user_id'];
$query = "SELECT * FROM pengundi WHERE idpengundi = '$voter_id'";
$result = mysqli_query($conn, $query);
$voter = mysqli_fetch_assoc($result);

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_profile'])) {
    $new_name = mysqli_real_escape_string($conn, $_POST['nama']);
    $new_password = $_POST['password'];
    
    $update_query = "UPDATE pengundi SET namapengundi = '$new_name', password = '$new_password' WHERE idpengundi = '$voter_id'";
    
    if (mysqli_query($conn, $update_query)) {
        $_SESSION['user_name'] = $new_name;
        $success = "Profil berjaya dikemaskini!";
        $voter['namapengundi'] = $new_name;
        $voter['password'] = $new_password;
    } else {
        $error = "Gagal mengemaskini profil!";
    }
}

// Get today's votes
$today = date('Y-m-d');
$vote_query = "SELECT m.namamakanan, u.tarikh FROM undian u 
               JOIN makanan m ON u.idmakanan = m.idmakanan 
               WHERE u.idpengundi = '$voter_id' 
               ORDER BY u.tarikh DESC LIMIT 5";
$vote_result = mysqli_query($conn, $vote_query);
?>
<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Pengundi - SMK Schuwenzel Paul</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f5f7fa;
        }
        
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .header h1 {
            font-size: 24px;
        }
        
        .user-info {
            display: flex;
            align-items: center;
            gap: 20px;
        }
        
        .btn-logout {
            padding: 10px 20px;
            background: rgba(255,255,255,0.2);
            color: white;
            text-decoration: none;
            border-radius: 5px;
            transition: background 0.3s;
        }
        
        .btn-logout:hover {
            background: rgba(255,255,255,0.3);
        }
        
        .container {
            max-width: 1200px;
            margin: 30px auto;
            padding: 0 20px;
        }
        
        .welcome-card {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }
        
        .welcome-card h2 {
            color: #667eea;
            margin-bottom: 10px;
        }
        
        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .card {
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .card h3 {
            color: #333;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #667eea;
        }
        
        .form-group {
            margin-bottom: 15px;
        }
        
        label {
            display: block;
            margin-bottom: 5px;
            color: #555;
            font-weight: 600;
        }
        
        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 10px;
            border: 2px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
        }
        
        input[type="text"]:focus,
        input[type="password"]:focus {
            outline: none;
            border-color: #667eea;
        }
        
        .btn-primary {
            padding: 12px 24px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
            transition: transform 0.2s;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
        }
        
        .btn-vote {
            display: inline-block;
            padding: 15px 30px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-weight: 600;
            transition: transform 0.2s;
        }
        
        .btn-vote:hover {
            transform: translateY(-2px);
        }
        
        .success-message {
            background: #d4edda;
            color: #155724;
            padding: 12px;
            border-radius: 5px;
            margin-bottom: 15px;
        }
        
        .error-message {
            background: #f8d7da;
            color: #721c24;
            padding: 12px;
            border-radius: 5px;
            margin-bottom: 15px;
        }
        
        .vote-history {
            list-style: none;
        }
        
        .vote-history li {
            padding: 10px;
            border-bottom: 1px solid #eee;
            display: flex;
            justify-content: space-between;
        }
        
        .vote-history li:last-child {
            border-bottom: none;
        }
        
        .no-votes {
            text-align: center;
            color: #999;
            padding: 20px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>SMK Schuwenzel Paul - Sistem Undian Menu</h1>
        <div class="user-info">
            <span>Selamat datang, <?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
            <a href="logout.php" class="btn-logout">Log Keluar</a>
        </div>
    </div>
    
    <div class="container">
        <div class="welcome-card">
            <h2>Dashboard Pengundi</h2>
            <p>Selamat datang ke sistem pengundian menu harian. Anda boleh mengundi untuk menu esok atau mengemaskini profil anda.</p>
        </div>
        
        <div class="dashboard-grid">
            <div class="card">
                <h3>Kemaskini Profil</h3>
                
                <?php if (isset($success)): ?>
                    <div class="success-message"><?php echo $success; ?></div>
                <?php endif; ?>
                
                <?php if (isset($error)): ?>
                    <div class="error-message"><?php echo $error; ?></div>
                <?php endif; ?>
                
                <form method="POST">
                    <div class="form-group">
                        <label>ID Pengundi:</label>
                        <input type="text" value="<?php echo htmlspecialchars($voter['idpengundi']); ?>" readonly>
                    </div>
                    
                    <div class="form-group">
                        <label>Nama:</label>
                        <input type="text" name="nama" value="<?php echo htmlspecialchars($voter['namapengundi']); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Kata Laluan:</label>
                        <input type="password" name="password" value="<?php echo htmlspecialchars($voter['password']); ?>" required>
                    </div>
                    
                    <button type="submit" name="update_profile" class="btn-primary">Kemaskini Profil</button>
                </form>
            </div>
            
            <div class="card">
                <h3>Sejarah Undian Terkini</h3>
                <?php if (mysqli_num_rows($vote_result) > 0): ?>
                    <ul class="vote-history">
                        <?php while ($vote = mysqli_fetch_assoc($vote_result)): ?>
                            <li>
                                <span><?php echo htmlspecialchars($vote['namamakanan']); ?></span>
                                <span><?php echo date('d/m/Y', strtotime($vote['tarikh'])); ?></span>
                            </li>
                        <?php endwhile; ?>
                    </ul>
                <?php else: ?>
                    <p class="no-votes">Tiada rekod undian</p>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="card" style="text-align: center; padding: 40px;">
            <h3>Undi Menu Esok</h3>
            <p style="margin: 20px 0;">Klik butang di bawah untuk mengundi menu yang anda inginkan untuk hari esok.</p>
            <a href="voting_page.php" class="btn-vote">Pergi ke Halaman Undian</a>
        </div>
    </div>
</body>
</html>