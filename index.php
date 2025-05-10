
<?php
require_once 'includes/config.php';
require_once 'includes/auth.php';
require_once 'includes/functions.php';

if (!isLoggedIn()) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blood Management System - Dashboard</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>Blood Management System</h1>
            <div class="user-info">
                Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!
                <a href="logout.php">Logout</a>
            </div>
        </header>
        
        <nav>
            <ul>
                <li><a href="index.php">Dashboard</a></li>
                <li><a href="donors.php">Donors</a></li>
                <li><a href="inventory.php">Inventory</a></li>
                <li><a href="blood_out.php">Blood Out</a></li>
            </ul>
        </nav>
        
        <main>
            <h2>Dashboard</h2>
            
            <div class="stats">
                <div class="stat-card">
                    <h3>Total Donors</h3>
                    <p><?php 
                        $donors = getAllDonors();
                        echo count($donors);
                    ?></p>
                </div>
                
                <div class="stat-card">
                    <h3>Total Blood Available</h3>
                    <p><?php 
                        $inventory = getBloodInventory();
                        $total = 0;
                        foreach ($inventory as $item) {
                            $total += $item['quantity'];
                        }
                        echo $total . ' liters';
                    ?></p>
                </div>
                
                <div class="stat-card">
                    <h3>Recent Donations</h3>
                    <ul>
                        <?php 
                        $recent_donors = array_slice($donors, 0, 3);
                        foreach ($recent_donors as $donor): ?>
                            <li><?php echo htmlspecialchars($donor['name']); ?> - <?php echo htmlspecialchars($donor['blood_group']); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </main>
    </div>
</body>
</html>