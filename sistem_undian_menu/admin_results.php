<?php
require_once 'db_connection.php';

// Check if user is logged in as admin
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] != 'admin') {
    header('Location: index.php');
    exit();
}

// Get selected date or use today
$selected_date = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');

// Get total votes for selected date
$total_votes_query = "SELECT COUNT(*) as total FROM undian WHERE tarikh = '$selected_date'";
$total_votes = mysqli_fetch_assoc(mysqli_query($conn, $total_votes_query))['total'];

// Get vote results with percentages
$results_query = "SELECT m.idmakanan, m.namamakanan, m.gambar, COUNT(u.idmakanan) as vote_count
                  FROM makanan m
                  LEFT JOIN undian u ON m.idmakanan = u.idmakanan AND u.tarikh = '$selected_date'
                  GROUP BY m.idmakanan, m.namamakanan, m.gambar
                  ORDER BY vote_count DESC";
$results = mysqli_query($conn, $results_query);
?>
<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Keputusan Undian - Admin</title>
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
        
        .date-selector {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-top: 20px;
        }
        
        .date-selector label {
            font-weight: 600;
            color: #555;
        }
        
        .date-selector input[type="date"] {
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
            margin-left: 10px;
        }
        
        .stats-card {
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 30px;
            text-align: center;
        }
        
        .stats-card h3 {
            color: #666;
            margin-bottom: 10px;
        }
        
        .stats-card .number {
            font-size: 48px;
            font-weight: bold;
            color: #667eea;
        }
        
        .results-card {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .results-card h3 {
            color: #333;
            margin-bottom: 25px;
            padding-bottom: 10px;
            border-bottom: 2px solid #667eea;
        }
        
        .result-item {
            display: flex;
            align-items: center;
            padding: 20px;
            margin-bottom: 15px;
            border: 2px solid #ddd;
            border-radius: 10px;
            transition: all 0.3s;
        }
        
        .result-item:hover {
            border-color: #667eea;
            box-shadow: 0 2px 10px rgba(102, 126, 234, 0.1);
        }
        
        .result-item.winner {
            border-color: #28a745;
            background: #f0fff4;
        }
        
        .result-rank {
            font-size: 32px;
            font-weight: bold;
            color: #667eea;
            margin-right: 20px;
            min-width: 50px;
        }
        
        .result-rank.winner {
            color: #28a745;
        }
        
        .result-icon {
            font-size: 48px;
            margin-right: 20px;
        }
        
        .result-info {
            flex: 1;
        }
        
        .result-info h4 {
            color: #333;
            margin-bottom: 10px;
            font-size: 18px;
        }
        
        .result-info p {
            color: #666;
            font-size: 14px;
        }
        
        .result-stats {
            text-align: right;
        }
        
        .result-votes {
            font-size: 32px;
            font-weight: bold;
            color: #667eea;
            margin-bottom: 5px;
        }
        
        .result-percentage {
            font-size: 24px;
            font-weight: bold;
            color: #28a745;
        }
        
        .progress-bar {
            width: 100%;
            height: 10px;
            background: #e9ecef;
            border-radius: 5px;
            margin-top: 10px;
            overflow: hidden;
        }
        
        .progress-fill {
            height: 100%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            transition: width 0.5s ease;
        }
        
        .no-votes {
            text-align: center;
            padding: 40px;
            color: #999;
        }
        
        .winner-badge {
            display: inline-block;
            background: #28a745;
            color: white;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            margin-left: 10px;
        }
        
        @media print {
            .header, .nav-links, .date-selector, .btn-print, .btn-primary {
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
            
            .page-header {
                box-shadow: none;
                border: 1px solid #ddd;
            }
        }
    </style>
    <script>
        function printResults() {
            window.print();
        }
    </script>
</head>
<body>
    <div class="header">
        <h1>Keputusan Undian</h1>
        <div class="nav-links">
            <a href="admin_dashboard.php">Dashboard</a>
            <a href="logout.php">Log Keluar</a>
        </div>
    </div>
    
    <div class="container">
        <div class="page-header">
            <h2>Keputusan Undian Menu Harian</h2>
            <p>SMK Schuwenzel Paul</p>
            
            <form method="GET" class="date-selector">
                <label>Pilih Tarikh:</label>
                <input type="date" name="date" value="<?php echo $selected_date; ?>" required>
                <button type="submit" class="btn-primary">Papar Keputusan</button>
                <button type="button" onclick="printResults()" class="btn-print">🖨️ Cetak Laporan</button>
            </form>
        </div>
        
        <div class="stats-card">
            <h3>Jumlah Undian pada <?php echo date('d/m/Y', strtotime($selected_date)); ?></h3>
            <div class="number"><?php echo $total_votes; ?></div>
            <p>undian diterima</p>
        </div>
        
        <div class="results-card">
            <h3>Keputusan Mengikut Peratus</h3>
            
            <?php if ($total_votes > 0): ?>
                <?php 
                $rank = 1;
                mysqli_data_seek($results, 0);
                while ($result = mysqli_fetch_assoc($results)): 
                    $percentage = $total_votes > 0 ? ($result['vote_count'] / $total_votes) * 100 : 0;
                    $is_winner = $rank == 1 && $result['vote_count'] > 0;
                ?>
                    <div class="result-item <?php echo $is_winner ? 'winner' : ''; ?>">
                        <div class="result-rank <?php echo $is_winner ? 'winner' : ''; ?>">
                            <?php echo $is_winner ? '🏆' : '#' . $rank; ?>
                        </div>
                        <div class="result-icon">🍽️</div>
                        <div class="result-info">
                            <h4>
                                <?php echo htmlspecialchars($result['namamakanan']); ?>
                                <?php if ($is_winner): ?>
                                    <span class="winner-badge">PEMENANG</span>
                                <?php endif; ?>
                            </h4>
                            <p>ID: <?php echo htmlspecialchars($result['idmakanan']); ?></p>
                            <div class="progress-bar">
                                <div class="progress-fill" style="width: <?php echo $percentage; ?>%"></div>
                            </div>
                        </div>
                        <div class="result-stats">
                            <div class="result-votes"><?php echo $result['vote_count']; ?></div>
                            <div class="result-percentage"><?php echo number_format($percentage, 1); ?>%</div>
                        </div>
                    </div>
                <?php 
                    $rank++;
                endwhile; 
                ?>
            <?php else: ?>
                <div class="no-votes">
                    <p>Tiada undian untuk tarikh ini.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>