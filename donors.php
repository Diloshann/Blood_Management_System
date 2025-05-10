
<?php
require_once 'includes/config.php';
require_once 'includes/auth.php';
require_once 'includes/functions.php';

if (!isLoggedIn()) {
    header("Location: login.php");
    exit();
}

$message = '';

// Handle donor addition
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_donor'])) {
    $name = trim($_POST['name']);
    $age = (int)$_POST['age'];
    $address = trim($_POST['address']);
    $phone = trim($_POST['phone']);
    $gender = $_POST['gender'];
    $blood_group = $_POST['blood_group'];
    $quantity = (float)$_POST['quantity'];
    
    if (addDonor($name, $age, $address, $phone, $gender, $blood_group, $quantity)) {
        $message = "Donor added successfully!";
    } else {
        $message = "Error adding donor.";
    }
}

// Handle donor deletion
if (isset($_GET['delete_donor'])) {
    $id = (int)$_GET['delete_donor'];
    if (deleteDonor($id)) {
        $message = "Donor deleted successfully!";
    } else {
        $message = "Error deleting donor.";
    }
}

$donors = getAllDonors();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blood Management System - Donors</title>
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
                <li class="active"><a href="donors.php">Donors</a></li>
                <li><a href="inventory.php">Inventory</a></li>
                <li><a href="blood_out.php">Blood Out</a></li>
            </ul>
        </nav>
        
        <main>
            <h2>Donor Management</h2>
            
            <?php if ($message): ?>
                <div class="alert"><?php echo htmlspecialchars($message); ?></div>
            <?php endif; ?>
            
            <section>
                <h3>Add New Donor</h3>
                <form method="POST" action="donors.php">
                    <input type="text" name="name" placeholder="Donor Name" required>
                    <input type="number" name="age" placeholder="Age" min="18" max="65" required>
                    <input type="text" name="address" placeholder="Address" required>
                    <input type="text" name="phone" placeholder="Phone Number" required>
                    
                    <select name="gender" required>
                        <option value="">Select Gender</option>
                        <option value="Male">Male</option>
                        <option value="Female">Female</option>
                        <option value="Other">Other</option>
                    </select>
                    
                    <select name="blood_group" required>
                        <option value="">Select Blood Group</option>
                        <?php foreach (getBloodGroups() as $group): ?>
                            <option value="<?php echo $group; ?>"><?php echo $group; ?></option>
                        <?php endforeach; ?>
                    </select>
                    
                    <input type="number" name="quantity" placeholder="Quantity (liters)" min="0.1" step="0.1" required>
                    <button type="submit" name="add_donor">Add Donor</button>
                </form>
            </section>
            
            <section>
                <h3>Donor List</h3>
                <table>
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Age</th>
                            <th>Address</th>
                            <th>Phone</th>
                            <th>Gender</th>
                            <th>Blood Group</th>
                            <th>Quantity (liters)</th>
                            <th>Donation Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($donors as $donor): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($donor['name']); ?></td>
                                <td><?php echo htmlspecialchars($donor['age']); ?></td>
                                <td><?php echo htmlspecialchars($donor['address']); ?></td>
                                <td><?php echo htmlspecialchars($donor['phone']); ?></td>
                                <td><?php echo htmlspecialchars($donor['gender']); ?></td>
                                <td><?php echo htmlspecialchars($donor['blood_group']); ?></td>
                                <td><?php echo htmlspecialchars($donor['quantity']); ?></td>
                                <td><?php echo date('M j, Y', strtotime($donor['donation_date'])); ?></td>
                                <td>
                                    <a href="donors.php?delete_donor=<?php echo $donor['id']; ?>" 
                                       onclick="return confirm('Are you sure you want to delete this donor?')" 
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