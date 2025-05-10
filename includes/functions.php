
<?php
require_once 'config.php';

function getBloodGroups() {
    return ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'];
}

function getAllDonors() {
    global $conn;
    $stmt = $conn->prepare("SELECT * FROM donors ORDER BY donation_date DESC");
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

function getBloodInventory() {
    global $conn;
    $stmt = $conn->prepare("SELECT * FROM blood_inventory");
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

function getBloodOutRecords() {
    global $conn;
    $stmt = $conn->prepare("SELECT * FROM blood_out ORDER BY transaction_date DESC");
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

function addDonor($name, $age, $address, $phone, $gender, $blood_group, $quantity) {
    global $conn;
    
    // Add to donors table
    $stmt = $conn->prepare("INSERT INTO donors (name, age, address, phone, gender, blood_group, quantity, added_by) 
                           VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sissssdi", $name, $age, $address, $phone, $gender, $blood_group, $quantity, $_SESSION['user_id']);
    $donor_result = $stmt->execute();
    
    // Update inventory
    if ($donor_result) {
        $stmt = $conn->prepare("UPDATE blood_inventory SET quantity = quantity + ? WHERE blood_group = ?");
        $stmt->bind_param("ds", $quantity, $blood_group);
        $inventory_result = $stmt->execute();
        
        return $donor_result && $inventory_result;
    }
    
    return false;
}

function recordBloodOut($buyer_name, $blood_group, $quantity, $contact) {
    global $conn;
    
    // Check if enough blood is available
    $stmt = $conn->prepare("SELECT quantity FROM blood_inventory WHERE blood_group = ?");
    $stmt->bind_param("s", $blood_group);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    
    if ($result['quantity'] < $quantity) {
        return false; // Not enough blood
    }
    
    // Record blood out
    $stmt = $conn->prepare("INSERT INTO blood_out (buyer_name, blood_group, quantity, contact, processed_by) 
                           VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("ssdsi", $buyer_name, $blood_group, $quantity, $contact, $_SESSION['user_id']);
    $blood_out_result = $stmt->execute();
    
    // Update inventory
    if ($blood_out_result) {
        $stmt = $conn->prepare("UPDATE blood_inventory SET quantity = quantity - ? WHERE blood_group = ?");
        $stmt->bind_param("ds", $quantity, $blood_group);
        $inventory_result = $stmt->execute();
        
        return $blood_out_result && $inventory_result;
    }
    
    return false;
}

function deleteDonor($id) {
    global $conn;
    
    // First get donor details to adjust inventory
    $stmt = $conn->prepare("SELECT blood_group, quantity FROM donors WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $donor = $stmt->get_result()->fetch_assoc();
    
    if ($donor) {
        // Delete donor
        $stmt = $conn->prepare("DELETE FROM donors WHERE id = ?");
        $stmt->bind_param("i", $id);
        $delete_result = $stmt->execute();
        
        // Adjust inventory
        if ($delete_result) {
            $stmt = $conn->prepare("UPDATE blood_inventory SET quantity = quantity - ? WHERE blood_group = ?");
            $stmt->bind_param("ds", $donor['quantity'], $donor['blood_group']);
            $update_result = $stmt->execute();
            
            return $delete_result && $update_result;
        }
    }
    
    return false;
}

function deleteBloodOutRecord($id) {
    global $conn;
    
    // First get record details to adjust inventory
    $stmt = $conn->prepare("SELECT blood_group, quantity FROM blood_out WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $record = $stmt->get_result()->fetch_assoc();
    
    if ($record) {
        // Delete record
        $stmt = $conn->prepare("DELETE FROM blood_out WHERE id = ?");
        $stmt->bind_param("i", $id);
        $delete_result = $stmt->execute();
        
        // Adjust inventory
        if ($delete_result) {
            $stmt = $conn->prepare("UPDATE blood_inventory SET quantity = quantity + ? WHERE blood_group = ?");
            $stmt->bind_param("ds", $record['quantity'], $record['blood_group']);
            $update_result = $stmt->execute();
            
            return $delete_result && $update_result;
        }
    }
    
    return false;
}
?>