<?php
// Database connection
include 'db.php'; 

$itemsPerPage = 8; // Set number of items per page
$pageNumber = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1; // Current page, default is 1
$offset = ($pageNumber - 1) * $itemsPerPage; // Calculate offset

// Get total item count for pagination
$totalItemsQuery = "SELECT COUNT(*) AS total FROM borrow";
$totalResult = mysqli_query($conn, $totalItemsQuery);
$totalItems = mysqli_fetch_assoc($totalResult)['total'];
$totalPages = ceil($totalItems / $itemsPerPage); // Calculate total pages

// Fetch paginated data
$sql = "SELECT * FROM borrow ORDER BY id DESC LIMIT $offset, $itemsPerPage";
$result = mysqli_query($conn, $sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css"/>
    <title>Borrow</title>
    <style>
        /* Base Styles */
        body {
            font-family: Arial, sans-serif;
            background-color: #DADADA;
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        /* Sidebar */
        .sidebar {
            width: 250px;
            height: 100vh;
            position: fixed;
            background-color: #333;
            display: flex;
            flex-direction: column;
            padding-top: 20px;
            padding-bottom: 260px;
        }

        .sidebar a {
            padding: 15px 20px;
            text-decoration: none;
            font-size: 18px;
            color: #fff;
            display: block;
            transition: all 0.3s ease;
            margin-bottom: 5px;
        }

        .sidebar a:hover {
            background-color: #555;
            transform: scale(1.05);
        }

        /* Main Content */
        .main-content {
            margin-left: 260px;
            padding: 20px;
            flex-grow: 1;
        }

        .navbar {
            background-color: #333;
            padding: 15px;
            color: white;
            text-align: center;
        }

        .navbar h2 {
            margin: 0;
        }

        .container {
            max-width: 1300px;
            margin: auto;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 2rem;
            margin-top: 5px;
        }

        .search-bar {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }

        .search-bar input {
            padding: 0.5rem;
            width: 300px;
            font-size: 16px;
        }

        /* Table Styling */
        .inventory-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .inventory-table th, .inventory-table td {
            padding: 12px;
            text-align: left;
            border: 1px solid #ddd;
        }

        .inventory-table thead {
            background-color: grey;
        }

        .inventory-table tbody tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        th {
            background-color: #1C4E80;
            color: white;
        }

        /* Button Styling */
        .green-button {
            padding: 8px 15px;
            font-size: 14px;
            background-color: green;
            border: none;
            color: white;
            cursor: pointer;
            border-radius: 5px;
            text-align: center;
            transition: background-color 0.3s ease;
        }

        .green-button:hover {
            background-color: #0056b3;
        }

        /* Pagination Styling */
        .pagination {
            margin-top: 20px;
            text-align: right;
        }

        .pagination a {
            padding: 8px 15px;
            margin: 0 5px;
            text-decoration: none;
            background-color: transparent;
            color: #1C4E80;
            border-radius: 5px;
            transition: all 0.3s ease;
        }

        .pagination a:hover {
            background-color: #0056b3;
            transform: scale(1.1);
            color: white;
        }

        .pagination .active {
            background-color: #007bff;
            color: white;
        }

        .pagination .disabled {
            padding: 8px 15px;
            margin: 0 5px;
            background-color: #ccc;
            color: #888;
            cursor: not-allowed;
        }

        /* Status color classes */
.status-pending {
    color: blue;
    font-weight: bold;
}

.status-returned {
    color: green;
    font-weight: bold;
}

.status-lost {
    color: red;
    font-weight: bold;
}

    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <a href="dashboard.php"><i class="fa-solid fa-tachometer-alt"></i> Dashboard</a>
        <a href="inventory.php"><i class="fa-solid fa-file-alt"></i> Borrow</a>
        <a href="stocks.php"><i class="fa-solid fa-boxes"></i> Stocks</a>    
        <a href="tracker.php"><i class="fa-solid fa-map-marker-alt"></i> Transaction Details</a>
        <a href="login.php"><i class="fa fa-sign-out-alt"></i> Logout</a>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <div class="navbar">
            <h2>Borrow</h2>
        </div>
        <div class="container">
            <!-- Search and Add New Record Button -->
            <div class="search-bar">
                <input type="text" id="searchInput" placeholder="Search Inventory...">
                <form method="post" action="addstock.php">
                    <button type="submit" name="myButton" class="green-button">Add New Record</button>
                </form>
            </div>

            <!-- Table for Borrowed Items -->
            <table class="inventory-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Item ID</th>
                        <th>Item Name</th>
                        <th>Category</th>
                        <th>Quantity</th>
                        <th>Employee ID</th>
                        <th>Employee Name</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result && mysqli_num_rows($result) > 0): ?>
                        <?php while ($row = mysqli_fetch_assoc($result)): ?>
                            <tr>
                                <td><?= htmlspecialchars($row['id']) ?></td>
                                <td><?= htmlspecialchars($row['item_id']) ?></td>
                                <td><?= htmlspecialchars($row['item_name']) ?></td>
                                <td><?= htmlspecialchars($row['cate']) ?></td>
                                <td><?= htmlspecialchars($row['quan']) ?></td>
                                <td><?= htmlspecialchars($row['emp_id']) ?></td>
                                <td><?= htmlspecialchars($row['emp_name']) ?></td>
                                <td>
    <form action="update_status.php" method="POST">
        <select name="status" onchange="this.form.submit()">
            <option value="Pending" <?= $row['status'] == 'Pending' ? 'selected' : '' ?>>Pending</option>
            <option value="Returned" <?= $row['status'] == 'Returned' ? 'selected' : '' ?>>Returned</option>
            <option value="Lost" <?= $row['status'] == 'Lost' ? 'selected' : '' ?>>Lost</option>
        </select>
        <input type="hidden" name="id" value="<?= $row['id'] ?>"> <!-- Hidden field for item id -->
    </form>
</td>

                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" style="text-align: center;">No records found</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>

            <!-- Pagination -->
            <div class="pagination">
                <?php if ($pageNumber > 1): ?>
                    <a href="?page=<?= $pageNumber - 1 ?>">Previous</a>
                <?php else: ?>
                    <span class="disabled">Previous</span>
                <?php endif; ?>

                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <a href="?page=<?= $i ?>" class="<?= $i == $pageNumber ? 'active' : '' ?>"><?= $i ?></a>
                <?php endfor; ?>

                <?php if ($pageNumber < $totalPages): ?>
                    <a href="?page=<?= $pageNumber + 1 ?>">Next</a>
                <?php else: ?>
                    <span class="disabled">Next</span>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>
