<?php
require_once 'db_connection.php';

// Check if user is logged in as admin
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] != 'admin') {
    header('Location: index.php');
    exit();
}

$admin_id = $_SESSION['user_id'];

// Handle Add Food Item
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_food'])) {
    $food_id = mysqli_real_escape_string($conn, $_POST['food_id']);
    $food_name = mysqli_real_escape_string($conn, $_POST['food_name']);
    $food_image = mysqli_real_escape_string($conn, $_POST['food_image']);
    
    // Check if food ID already exists
    $check = "SELECT * FROM makanan WHERE idmakanan = '$food_id'";
    $check_result = mysqli_query($conn, $check);
    
    if (mysqli_num_rows($check_result) > 0) {
        $error = "ID Makanan sudah wujud!";
    } else {
        $insert = "INSERT INTO makanan (idmakanan, namamakanan, gambar, idadmin) VALUES ('$food_id', '$food_name', '$food_image', '$admin_id')";
        if (mysqli_query($conn, $insert)) {
            $success = "Item menu berjaya ditambah!";
        } else {
            $error = "Gagal menambah item menu!";
        }
    }
}

// Handle Delete Food Item
if (isset($_GET['delete'])) {
    $delete_id = mysqli_real_escape_string($conn, $_GET['delete']);
    $delete_query = "DELETE FROM makanan WHERE idmakanan = '$delete_id'";
    
    if (mysqli_query($conn, $delete_query)) {
        $success = "Item menu berjaya dibuang!";
    } else {
        $error = "Gagal membuang item menu!";
    }
}

// Get all food items
$food_query = "SELECT m.*, a.namaadmin FROM makanan m 
               LEFT JOIN admin a ON m.idadmin = a.idadmin 
               ORDER BY m.idmakanan";
$food_result = mysqli_query($conn, $food_query);
?>
<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Urus Menu - Admin</title>
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
            max-width: 1400px;
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
            margin-bottom: 10px;
        }
        
        .success-message {
            background: #d4edda;
            color: #155724;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        
        .error-message {
            background: #f8d7da;
            color: #721c24;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        
        .card {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }
        
        .card h3 {
            color: #333;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #667eea;
        }
        
        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 20px;
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
        
        input[type="text"] {
            width: 100%;
            padding: 10px;
            border: 2px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
        }
        
        input[type="text"]:focus {
            outline: none;
            border-color: #667eea;
        }
        
        .btn-primary {
            padding: 12px 30px;
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
        
        .food-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 20px;
        }
        
        .food-item {
            background: white;
            border: 2px solid #ddd;
            border-radius: 10px;
            padding: 20px;
            text-align: center;
            transition: border-color 0.3s;
        }
        
        .food-item:hover {
            border-color: #667eea;
        }
        
        .food-icon {
            font-size: 64px;
            margin-bottom: 15px;
        }
        
        .food-item h4 {
            color: #333;
            margin-bottom: 10px;
        }
        
        .food-item p {
            color: #666;
            font-size: 14px;
            margin-bottom: 5px;
        }
        
        .btn-delete {
            margin-top: 15px;
            padding: 8px 20px;
            background: #dc3545;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 13px;
            transition: background 0.3s;
        }
        
        .btn-delete:hover {
            background: #c82333;
        }
        
        .info-note {
            background: #e7f3ff;
            border-left: 4px solid #2196F3;
            padding: 15px;
            margin-top: 15px;
            border-radius: 4px;
        }
        
        .info-note p {
            color: #0c5c9e;
            font-size: 14px;
            margin: 0;
        }
    </style>
    <script>
        function confirmDelete(name) {
            return confirm('Adakah anda pasti mahu membuang menu ' + name + '?');
        }
    </script>
</head>
<body>
    <div class="header">
        <h1>Urus Menu Makanan</h1>
        <div class="nav-links">
            <a href="admin_dashboard.php">Dashboard</a>
            <a href="logout.php">Log Keluar</a>
        </div>
    </div>
    
    <div class="container">
        <div class="page-header">
            <h2>Pengurusan Menu Makanan</h2>
            <p>Tambah item menu baru atau urus menu sedia ada</p>
        </div>
        
        <?php if (isset($success)): ?>
            <div class="success-message"><?php echo $success; ?></div>
        <?php endif; ?>
        
        <?php if (isset($error)): ?>
            <div class="error-message"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <div class="card">
            <h3>Tambah Item Menu Baru</h3>
            <form method="POST">
                <div class="form-grid">
                    <div class="form-group">
                        <label>ID Makanan:</label>
                        <input type="text" name="food_id" required placeholder="Contoh: M006">
                    </div>
                    
                    <div class="form-group">
                        <label>Nama Makanan:</label>
                        <input type="text" name="food_name" required placeholder="Contoh: Nasi Goreng">
                    </div>
                    
                    <div class="form-group">
                        <label>Nama Fail Gambar:</label>
                        <input type="text" name="food_image" required placeholder="Contoh: M006.jpg">
                    </div>
                </div>
                
                <button type="submit" name="add_food" class="btn-primary">Tambah Menu</button>
                
                <div class="info-note">
                    <p><strong>Nota:</strong> Gambar perlu disimpan dalam folder 'images' dengan nama fail yang betul.</p>
                </div>
            </form>
        </div>
        
        <div class="card">
            <h3>Senarai Menu Sedia Ada</h3>
            <div class="food-grid">
                <?php while ($food = mysqli_fetch_assoc($food_result)): ?>
                    <div class="food-item">
                        <div class="food-icon">🍽️</div>
                        <h4><?php echo htmlspecialchars($food['namamakanan']); ?></h4>
                        <p><strong>ID:</strong> <?php echo htmlspecialchars($food['idmakanan']); ?></p>
                        <p><strong>Gambar:</strong> <?php echo htmlspecialchars($food['gambar']); ?></p>
                        <p><strong>Ditambah oleh:</strong> <?php echo htmlspecialchars($food['namaadmin'] ?? 'N/A'); ?></p>
                        
                        <form method="GET" style="display: inline;">
                            <input type="hidden" name="delete" value="<?php echo $food['idmakanan']; ?>">
                            <button type="submit" 
                                    class="btn-delete" 
                                    onclick="return confirmDelete('<?php echo htmlspecialchars($food['namamakanan']); ?>')">
                                Buang Menu
                            </button>
                        </form>
                    </div>
                <?php endwhile; ?>
            </div>
        </div>
    </div>
</body>
</html>