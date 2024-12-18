<?php
include 'db.php';

// Check if the form is submitted
if (isset($_POST['status']) && isset($_POST['id'])) {
    $status = $_POST['status'];
    $id = $_POST['id'];

    // Update the status in the database
    $updateQuery = "UPDATE borrow SET status = ? WHERE id = ?";
    $stmt = mysqli_prepare($conn, $updateQuery);
    mysqli_stmt_bind_param($stmt, 'si', $status, $id);

    if (mysqli_stmt_execute($stmt)) {
        // Redirect back to the inventory page
        header("Location: inventory.php");
        exit();
    } else {
        echo "Error updating status: " . mysqli_error($conn);
    }

    mysqli_stmt_close($stmt);
}

mysqli_close($conn);
?>
