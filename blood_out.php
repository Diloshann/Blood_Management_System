
<?php
require_once 'includes/config.php';
require_once 'includes/auth.php';
require_once 'includes/functions.php';

if (!isLoggedIn()) {
    header("Location: login.php");
    exit();
}

$message = '';

// Handle blood out record addition
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_blood_out'])) {
    $buyer_name = trim($_POST['buyer_name']);
    $blood_group = $_POST['blood_group'];
    $quantity = (float)$_POST['quantity'];
    $contact = trim($_POST['contact']);
    
    if (recordBloodOut($buyer_name, $blood_group, $quantity, $contact)) {
        $message = "Blood out record added successfully!";
    } else {
        $message = "Error adding blood out record. Not enough blood available.";
    }
}

// Handle blood out record deletion
if (isset($_GET['delete_record'])) {
    $id = (int)$_GET['delete_record'];
    if (deleteBloodOutRecord($id)) {
        $message = "Record deleted successfully!";
    } else {
        $message = "Error deleting record.";
    }
}

$blood_out_records = getBloodOutRecords();
$inventory = getBloodInventory();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blood Management System - Blood Out</title>
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
                <li class="active"><a href="blood_out.php">Blood Out</a></li>
            </ul>
        </nav>
        
        <main>
            <h2>Blood Going Out Report</h2>
            
            <?php if ($message): ?>
                <div class="alert"><?php echo htmlspecialchars($message); ?></div>
            <?php endif; ?>
            
            <section>
                <h3>Record Blood Out</h3>
                <form method="POST" action="blood_out.php">
                    <input type="text" name="buyer_name" placeholder="Buyer Name" required>
                    
                    <select name="blood_group" required>
                        <option value="">Select Blood Group</option>
                        <?php foreach (getBloodGroups() as $group): 
                            // Only show blood groups with available stock
                            foreach ($inventory as $item) {
                                if ($item['blood_group'] === $group && $item['quantity'] > 0) {
                                    echo "<option value=\"$group\">$group</option>";
                                    break;
                                }
                            }
                        endforeach; ?>
                    </select>
                    
                    <input type="number" name="quantity" placeholder="Quantity (liters)" min="0.1" step="0.1" required>
                    <input type="text" name="contact" placeholder="Contact Number" required>
                    <button type="submit" name="add_blood_out">Record Blood Out</button>
                </form>
            </section>
            
            <section>
                <h3>Blood Out Records</h3>
                <table>
                    <thead>
                        <tr>
                            <th>Buyer Name</th>
                            <th>Blood Group</th>
                            <th>Quantity (liters)</th>
                            <th>Contact</th>
                            <th>Transaction Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($blood_out_records as $record): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($record['buyer_name']); ?></td>
                                <td><?php echo htmlspecialchars($record['blood_group']); ?></td>
                                <td><?php echo htmlspecialchars($record['quantity']); ?></td>
                                <td><?php echo htmlspecialchars($record['contact']); ?></td>
                                <td><?php echo date('M j, Y', strtotime($record['transaction_date'])); ?></td>
                                <td>
                                    <a href="blood_out.php?delete_record=<?php echo $record['id']; ?>" 
                                       onclick="return confirm('Are you sure you want to delete this record?')" 
                                       class="btn-delete">Delete</a>
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