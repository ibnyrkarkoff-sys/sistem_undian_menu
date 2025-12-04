<?php
require_once 'db_connection.php';

// Check if user is logged in as admin
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] != 'admin') {
    header('Location: index.php');
    exit();
}

// Get date range
$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-01');
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-d');

// Get summary statistics
$total_votes_query = "SELECT COUNT(*) as total FROM undian WHERE tarikh BETWEEN '$start_date' AND '$end_date'";
$total_votes = mysqli_fetch_assoc(mysqli_query($conn, $total_votes_query))['total'];

$unique_voters_query = "SELECT COUNT(DISTINCT idpengundi) as total FROM undian WHERE tarikh BETWEEN '$start_date' AND '$end_date'";
$unique_voters = mysqli_fetch_assoc(mysqli_query($conn, $unique_voters_query))['total'];

// Get most popular food
$popular_food_query = "SELECT m.namamakanan, COUNT(u.idmakanan) as vote_count
                       FROM undian u
                       JOIN makanan m ON u.idmakanan = m.idmakanan
                       WHERE u.tarikh BETWEEN '$start_date' AND '$end_date'
                       GROUP BY u.idmakanan, m.namamakanan
                       ORDER BY vote_count DESC
                       LIMIT 1";
$popular_result = mysqli_query($conn, $popular_food_query);
$most_popular = mysqli_num_rows($popular_result) > 0 ? mysqli_fetch_assoc($popular_result) : null;

// Get daily vote counts
$daily_votes_query = "SELECT tarikh, COUNT(*) as vote_count
                      FROM undian
                      WHERE tarikh BETWEEN '$start_date' AND '$end_date'
                      GROUP BY tarikh
                      ORDER BY tarikh DESC";
$daily_votes_result = mysqli_query($conn, $daily_votes_query);

// Get food popularity breakdown
$food_breakdown_query = "SELECT m.namamakanan, COUNT(u.idmakanan) as vote_count
                         FROM makanan m
                         LEFT JOIN undian u ON m.idmakanan = u.idmakanan AND u.tarikh BETWEEN '$start_date' AND '$end_date'
                         GROUP BY m.idmakanan, m.namamakanan
                         ORDER BY vote_count DESC";
$food_breakdown_result = mysqli_query($conn, $food_breakdown_query);
?>
<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan - Admin</title>
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
        
        .nav-links {
            display: flex;
            gap: 15px;
        }
        
        .nav-links a {
            padding: 10px 20px;
            background: rgba(255,255,255,0.2);
            color: white;
            text-decoration: none;
            border-radius: 5px;
            transition: background 0.3s;
        }
        
        .nav-links a:hover {
            background: rgba(255,255,255,0.3);
        }
        
        .container {
            max-width: 1200px;
            margin: 30px auto;
            padding: 0 20px;
        }
        
        .page-header {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }
        
        .page-header h2 {
            color: #667eea;
            margin-bottom: 15px;
        }
        
        .date-range-selector {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-top: 20px;
            flex-wrap: wrap;
        }
        
        .date-range-selector label {
            font-weight: 600;
            color: #555;
        }
        
        .date-range-selector input[type="date"] {
            padding: 10px;
            border: 2px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
        }
        
        .btn-primary {
            padding: 10px 25px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
        }
        
        .btn-print {
            padding: 10px 25px;
            background: #28a745;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
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
            margin-bottom: 5px;
        }
        
        .stat-card .label {
            color: #999;
            font-size: 13px;
        }
        
        .card {
            background: white;
            padding: 30px;
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
        
        @media print {
            .header, .nav-links, .date-range-selector, .btn-print, .btn-primary {
                display: none !important;
            }
            
            body {
                background: white;
            }
            
            .container {
                max-width: 100%;
                margin: 0;
                padding: 20px;
            }
        }
    </style>
    <script>
        function printReport() {
            window.print();
        }
    </script>
</head>
<body>
    <div class="header">
        <h1>Laporan Undian</h1>
        <div class="nav-links">
            <a href="admin_dashboard.php">Dashboard</a>
            <a href="logout.php">Log Keluar</a>
        </div>
    </div>
    
    <div class="container">
        <div class="page-header">
            <h2>Laporan Analisis Undian</h2>
            <p>SMK Schuwenzel Paul</p>
            
            <form method="GET" class="date-range-selector">
                <label>Dari:</label>
                <input type="date" name="start_date" value="<?php echo $start_date; ?>" required>
                
                <label>Hingga:</label>
                <input type="date" name="end_date" value="<?php echo $end_date; ?>" required>
                
                <button type="submit" class="btn-primary">Jana Laporan</button>
                <button type="button" onclick="printReport()" class="btn-print">🖨️ Cetak Laporan</button>
            </form>
        </div>
        
        <div class="stats-grid">
            <div class="stat-card">
                <h3>Jumlah Undian</h3>
                <div class="number"><?php echo $total_votes; ?></div>
                <div class="label">undian diterima</div>
            </div>
            
            <div class="stat-card">
                <h3>Pengundi Unik</h3>
                <div class="number"><?php echo $unique_voters; ?></div>
                <div class="label">pengundi aktif</div>
            </div>
            
            <div class="stat-card">
                <h3>Menu Paling Popular</h3>
                <div class="number" style="font-size: 24px;">
                    <?php echo $most_popular ? htmlspecialchars($most_popular['namamakanan']) : 'N/A'; ?>
                </div>
                <div class="label">
                    <?php echo $most_popular ? $most_popular['vote_count'] . ' undian' : 'Tiada data'; ?>
                </div>
            </div>
        </div>
        
        <div class="card">
            <h3>Undian Harian</h3>
            <?php if (mysqli_num_rows($daily_votes_result) > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Tarikh</th>
                            <th>Jumlah Undian</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($daily = mysqli_fetch_assoc($daily_votes_result)): ?>
                            <tr>
                                <td><?php echo date('d/m/Y', strtotime($daily['tarikh'])); ?></td>
                                <td><?php echo $daily['vote_count']; ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p style="text-align: center; color: #999; padding: 20px;">Tiada data undian untuk tempoh ini.</p>
            <?php endif; ?>
        </div>
        
        <div class="card">
            <h3>Analisis Populariti Menu</h3>
            <?php if (mysqli_num_rows($food_breakdown_result) > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Nama Menu</th>
                            <th>Jumlah Undian</th>
                            <th>Peratus</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($food = mysqli_fetch_assoc($food_breakdown_result)): 
                            $percentage = $total_votes > 0 ? ($food['vote_count'] / $total_votes) * 100 : 0;
                        ?>
                            <tr>
                                <td><?php echo htmlspecialchars($food['namamakanan']); ?></td>
                                <td><?php echo $food['vote_count']; ?></td>
                                <td><?php echo number_format($percentage, 1); ?>%</td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p style="text-align: center; color: #999; padding: 20px;">Tiada data menu.</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>