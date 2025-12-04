<?php
require_once 'db_connection.php';

// Check if user is logged in as admin
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] != 'admin') {
    header('Location: index.php');
    exit();
}

// Handle Add Voter
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_voter'])) {
    $voter_id = mysqli_real_escape_string($conn, $_POST['voter_id']);
    $voter_name = mysqli_real_escape_string($conn, $_POST['voter_name']);
    $voter_password = $_POST['voter_password'];
    
    // Check if voter ID already exists
    $check = "SELECT * FROM pengundi WHERE idpengundi = '$voter_id'";
    $check_result = mysqli_query($conn, $check);
    
    if (mysqli_num_rows($check_result) > 0) {
        $error = "ID Pengundi sudah wujud!";
    } else {
        $insert = "INSERT INTO pengundi (idpengundi, namapengundi, password) VALUES ('$voter_id', '$voter_name', '$voter_password')";
        if (mysqli_query($conn, $insert)) {
            $success = "Pengundi berjaya ditambah!";
        } else {
            $error = "Gagal menambah pengundi!";
        }
    }
}

// Handle Delete Voter
if (isset($_GET['delete'])) {
    $delete_id = mysqli_real_escape_string($conn, $_GET['delete']);
    $delete_query = "DELETE FROM pengundi WHERE idpengundi = '$delete_id'";
    
    if (mysqli_query($conn, $delete_query)) {
        $success = "Pengundi berjaya dibuang!";
    } else {
        $error = "Gagal membuang pengundi!";
    }
}

// Get all voters
$voters_query = "SELECT * FROM pengundi ORDER BY idpengundi";
$voters_result = mysqli_query($conn, $voters_query);
?>
<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Urus Pengundi - Admin</title>
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
        
        .btn-delete {
            padding: 6px 15px;
            background: #dc3545;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            font-size: 13px;
            transition: background 0.3s;
        }
        
        .btn-delete:hover {
            background: #c82333;
        }
    </style>
    <script>
        function confirmDelete(name) {
            return confirm('Adakah anda pasti mahu membuang pengundi ' + name + '?');
        }
    </script>
</head>
<body>
    <div class="header">
        <h1>Urus Pengundi</h1>
        <div class="nav-links">
            <a href="admin_dashboard.php">Dashboard</a>
            <a href="logout.php">Log Keluar</a>
        </div>
    </div>
    
    <div class="container">
        <div class="page-header">
            <h2>Pengurusan Pengundi</h2>
            <p>Tambah pengundi baru atau urus pengundi sedia ada</p>
        </div>
        
        <?php if (isset($success)): ?>
            <div class="success-message"><?php echo $success; ?></div>
        <?php endif; ?>
        
        <?php if (isset($error)): ?>
            <div class="error-message"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <div class="card">
            <h3>Tambah Pengundi Baru</h3>
            <form method="POST">
                <div class="form-grid">
                    <div class="form-group">
                        <label>ID Pengundi:</label>
                        <input type="text" name="voter_id" required placeholder="Contoh: P006">
                    </div>
                    
                    <div class="form-group">
                        <label>Nama Pengundi:</label>
                        <input type="text" name="voter_name" required placeholder="Nama penuh">
                    </div>
                    
                    <div class="form-group">
                        <label>Kata Laluan:</label>
                        <input type="password" name="voter_password" required placeholder="Kata laluan">
                    </div>
                </div>
                
                <button type="submit" name="add_voter" class="btn-primary">Tambah Pengundi</button>
            </form>
        </div>
        
        <div class="card">
            <h3>Senarai Pengundi</h3>
            <table>
                <thead>
                    <tr>
                        <th>ID Pengundi</th>
                        <th>Nama Pengundi</th>
                        <th>Kata Laluan</th>
                        <th>Tindakan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($voter = mysqli_fetch_assoc($voters_result)): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($voter['idpengundi']); ?></td>
                            <td><?php echo htmlspecialchars($voter['namapengundi']); ?></td>
                            <td><?php echo htmlspecialchars($voter['password']); ?></td>
                            <td>
                                <a href="?delete=<?php echo $voter['idpengundi']; ?>" 
                                   class="btn-delete" 
                                   onclick="return confirmDelete('<?php echo htmlspecialchars($voter['namapengundi']); ?>')">
                                   Buang
                                </a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>