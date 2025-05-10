
<?php
require_once 'includes/config.php';
require_once 'includes/auth.php';
require_once 'includes/functions.php';

if (!isLoggedIn()) {
    header("Location: login.php");
    exit();
}

$inventory = getBloodInventory();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blood Management System - Inventory</title>
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
                <li class="active"><a href="inventory.php">Inventory</a></li>
                <li><a href="blood_out.php">Blood Out</a></li>
            </ul>
        </nav>
        
        <main>
            <h2>Blood Inventory</h2>
            
            <section>
                <table>
                    <thead>
                        <tr>
                            <th>Blood Group</th>
                            <th>Available Liters</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($inventory as $item): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($item['blood_group']); ?></td>
                                <td><?php echo htmlspecialchars($item['quantity']); ?></td>
                                <td>
                                    <?php if ($item['quantity'] < 1): ?>
                                        <span class="status-low">Low Stock</span>
                                    <?php elseif ($item['quantity'] < 3): ?>
                                        <span class="status-medium">Medium Stock</span>
                                    <?php else: ?>
                                        <span class="status-high">High Stock</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </section>
        </main>
    </div>
</body>
</html>