<?php
require_once 'db_connection.php';

// Check if user is logged in as admin
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] != 'admin') {
    header('Location: index.php');
    exit();
}

$admin_id = $_SESSION['user_id'];

// Get statistics
$total_voters_query = "SELECT COUNT(*) as total FROM pengundi";
$total_voters = mysqli_fetch_assoc(mysqli_query($conn, $total_voters_query))['total'];

$total_food_query = "SELECT COUNT(*) as total FROM makanan";
$total_food = mysqli_fetch_assoc(mysqli_query($conn, $total_food_query))['total'];

$today = date('Y-m-d');
$today_votes_query = "SELECT COUNT(*) as total FROM undian WHERE tarikh = '$today'";
$today_votes = mysqli_fetch_assoc(mysqli_query($conn, $today_votes_query))['total'];

// Get recent votes
$recent_votes_query = "SELECT p.namapengundi, m.namamakanan, u.tarikh 
                       FROM undian u
                       JOIN pengundi p ON u.idpengundi = p.idpengundi
                       JOIN makanan m ON u.idmakanan = m.idmakanan
                       ORDER BY u.tarikh DESC LIMIT 5";
$recent_votes_result = mysqli_query($conn, $recent_votes_query);
?>
<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - SMK Schuwenzel Paul</title>
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
            max-width: 1400px;
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
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            text-align: center;
        }
        
        .stat-card h3 {
            color: #666;
            font-size: 14px;
            margin-bottom: 10px;
        }
        
        .stat-card .number {
            font-size: 48px;
            font-weight: bold;
            color: #667eea;
        }
        
        .menu-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .menu-card {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            text-align: center;
            cursor: pointer;
            transition: transform 0.2s;
            text-decoration: none;
            color: inherit;
            display: block;
        }
        
        .menu-card:hover {
            transform: translateY(-5px);
        }
        
        .menu-card-icon {
            font-size: 48px;
            margin-bottom: 15px;
        }
        
        .menu-card h3 {
            color: #333;
            margin-bottom: 5px;
        }
        
        .menu-card p {
            color: #666;
            font-size: 14px;
        }
        
        .card {
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        
        .card h3 {
            color: #333;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #667eea;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
        }
        
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        
        th {
            background: #f8f9fa;
            color: #333;
            font-weight: 600;
        }
        
        tr:hover {
            background: #f8f9fa;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>SMK Schuwenzel Paul - Panel Admin</h1>
        <div class="user-info">
            <span>Admin: <?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
            <a href="logout.php" class="btn-logout">Log Keluar</a>
        </div>
    </div>
    
    <div class="container">
        <div class="welcome-card">
            <h2>Dashboard Pentadbir</h2>
            <p>Selamat datang ke panel pentadbiran sistem undian menu harian.</p>
        </div>
        
        <div class="stats-grid">
            <div class="stat-card">
                <h3>Jumlah Pengundi</h3>
                <div class="number"><?php echo $total_voters; ?></div>
            </div>
            <div class="stat-card">
                <h3>Jumlah Menu</h3>
                <div class="number"><?php echo $total_food; ?></div>
            </div>
            <div class="stat-card">
                <h3>Undian Hari Ini</h3>
                <div class="number"><?php echo $today_votes; ?></div>
            </div>
        </div>
        
        <h2 style="margin-bottom: 20px; color: #333;">Menu Pentadbiran</h2>
        <div class="menu-grid">
            <a href="admin_voters.php" class="menu-card">
                <div class="menu-card-icon">👥</div>
                <h3>Urus Pengundi</h3>
                <p>Tambah, lihat & buang pengundi</p>
            </a>
            
            <a href="admin_food.php" class="menu-card">
                <div class="menu-card-icon">🍽️</div>
                <h3>Urus Menu</h3>
                <p>Tambah & buang item menu</p>
            </a>
            
            <a href="admin_results.php" class="menu-card">
                <div class="menu-card-icon">📊</div>
                <h3>Keputusan Undian</h3>
                <p>Lihat & cetak keputusan</p>
            </a>
            
            <a href="admin_reports.php" class="menu-card">
                <div class="menu-card-icon">📄</div>
                <h3>Laporan</h3>
                <p>Jana laporan undian</p>
            </a>
        </div>
        
        <div class="card">
            <h3>Undian Terkini</h3>
            <?php if (mysqli_num_rows($recent_votes_result) > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Pengundi</th>
                            <th>Menu Dipilih</th>
                            <th>Tarikh</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($vote = mysqli_fetch_assoc($recent_votes_result)): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($vote['namapengundi']); ?></td>
                                <td><?php echo htmlspecialchars($vote['namamakanan']); ?></td>
                                <td><?php echo date('d/m/Y', strtotime($vote['tarikh'])); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p style="text-align: center; color: #999; padding: 20px;">Tiada rekod undian</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>