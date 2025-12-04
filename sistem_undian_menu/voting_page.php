<?php
require_once 'db_connection.php';

// Check if user is logged in as voter
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] != 'pengundi') {
    header('Location: index.php');
    exit();
}

$voter_id = $_SESSION['user_id'];
$today = date('Y-m-d');

// Check if already voted today
$check_vote = "SELECT * FROM undian WHERE idpengundi = '$voter_id' AND tarikh = '$today'";
$check_result = mysqli_query($conn, $check_vote);
$already_voted = mysqli_num_rows($check_result) > 0;

// Handle vote submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['vote'])) {
    $selected_food = $_POST['food_id'];
    
    // Check if already voted today
    if (!$already_voted) {
        $vote_query = "INSERT INTO undian (idpengundi, idmakanan, tarikh) VALUES ('$voter_id', '$selected_food', '$today')";
        
        if (mysqli_query($conn, $vote_query)) {
            $success = "Undian anda telah berjaya direkodkan!";
            $already_voted = true;
        } else {
            $error = "Gagal merekod undian. Sila cuba lagi.";
        }
    } else {
        $error = "Anda telah mengundi untuk hari ini!";
    }
}

// Get all food items
$food_query = "SELECT * FROM makanan ORDER BY namamakanan";
$food_result = mysqli_query($conn, $food_query);
?>
<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Halaman Undian - SMK Schuwenzel Paul</title>
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
        
        .voting-header {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 30px;
            text-align: center;
        }
        
        .voting-header h2 {
            color: #667eea;
            margin-bottom: 10px;
        }
        
        .success-message {
            background: #d4edda;
            color: #155724;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            text-align: center;
        }
        
        .error-message {
            background: #f8d7da;
            color: #721c24;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            text-align: center;
        }
        
        .food-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 25px;
        }
        
        .food-card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            overflow: hidden;
            transition: transform 0.3s, box-shadow 0.3s;
            cursor: pointer;
        }
        
        .food-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 20px rgba(0,0,0,0.15);
        }
        
        .food-card.selected {
            border: 3px solid #667eea;
        }
        
        .food-image {
            width: 100%;
            height: 200px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 48px;
        }
        
        .food-info {
            padding: 20px;
        }
        
        .food-info h3 {
            color: #333;
            margin-bottom: 10px;
        }
        
        .food-info p {
            color: #666;
            font-size: 14px;
        }
        
        .radio-container {
            display: none;
        }
        
        .submit-container {
            text-align: center;
            margin-top: 30px;
        }
        
        .btn-submit {
            padding: 15px 50px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.2s;
        }
        
        .btn-submit:hover {
            transform: translateY(-2px);
        }
        
        .btn-submit:disabled {
            background: #ccc;
            cursor: not-allowed;
        }
        
        .already-voted {
            background: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            text-align: center;
        }
        
        .already-voted h3 {
            color: #667eea;
            margin-bottom: 15px;
        }
        
        .already-voted p {
            color: #666;
            margin-bottom: 20px;
        }
    </style>
    <script>
        function selectFood(foodId) {
            // Remove all selected classes
            document.querySelectorAll('.food-card').forEach(card => {
                card.classList.remove('selected');
            });
            
            // Add selected class to clicked card
            event.currentTarget.classList.add('selected');
            
            // Check the radio button
            document.getElementById('food_' + foodId).checked = true;
            
            // Enable submit button
            document.getElementById('submitBtn').disabled = false;
        }
    </script>
</head>
<body>
    <div class="header">
        <h1>Halaman Undian Menu Harian</h1>
        <div class="nav-links">
            <a href="voter_dashboard.php">Dashboard</a>
            <a href="logout.php">Log Keluar</a>
        </div>
    </div>
    
    <div class="container">
        <div class="voting-header">
            <h2>Undi Menu untuk Hari Esok</h2>
            <p>Pilih satu menu yang anda inginkan untuk hari esok</p>
        </div>
        
        <?php if (isset($success)): ?>
            <div class="success-message"><?php echo $success; ?></div>
        <?php endif; ?>
        
        <?php if (isset($error)): ?>
            <div class="error-message"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <?php if ($already_voted && !isset($success)): ?>
            <div class="already-voted">
                <h3>✓ Anda Telah Mengundi</h3>
                <p>Anda telah mengundi untuk hari ini. Terima kasih atas penyertaan anda!</p>
                <a href="voter_dashboard.php" class="btn-submit">Kembali ke Dashboard</a>
            </div>
        <?php else: ?>
            <form method="POST">
                <div class="food-grid">
                    <?php while ($food = mysqli_fetch_assoc($food_result)): ?>
                        <div class="food-card" onclick="selectFood('<?php echo $food['idmakanan']; ?>')">
                            <div class="food-image">
                                🍽️
                            </div>
                            <div class="food-info">
                                <h3><?php echo htmlspecialchars($food['namamakanan']); ?></h3>
                                <p>ID: <?php echo htmlspecialchars($food['idmakanan']); ?></p>
                            </div>
                            <input type="radio" name="food_id" value="<?php echo $food['idmakanan']; ?>" 
                                   id="food_<?php echo $food['idmakanan']; ?>" class="radio-container" required>
                        </div>
                    <?php endwhile; ?>
                </div>
                
                <div class="submit-container">
                    <button type="submit" name="vote" id="submitBtn" class="btn-submit" disabled>
                        Hantar Undian
                    </button>
                </div>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>