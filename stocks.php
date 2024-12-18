<?php      

include 'db.php';

$itemsPerPage = 8;
$pageNumber = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$offset = ($pageNumber - 1) * $itemsPerPage;


$totalItemsQuery = "SELECT COUNT(*) AS total FROM stocks";
$totalResult = mysqli_query($conn, $totalItemsQuery);
$totalItems = mysqli_fetch_assoc($totalResult)['total'];
$totalPages = ceil($totalItems / $itemsPerPage);


$sql = "SELECT * FROM stocks ORDER BY id DESC LIMIT $offset, $itemsPerPage";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>   
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" rel="stylesheet">
    <title>Stocks</title>
    <!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script> -->
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            background-color: #DADADA;
        }
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
        .sidebar a i {
            margin-right: 10px;
        }
        
        .sidebar a:hover, .Btn:hover {
            background-color: #555;
            transform: scale(1.05);
            color: #f0f0f0;
        }
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

        .container-background {
            max-width: 1300px;
            margin: auto;
            margin-top: 5px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 2rem;
        }
        .search-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            margin-right: 654px;
           
        }
        .search-bar input {
            padding: 0.5rem;
            width: 300px;
            font-size: 16px;
        }

        /* Table styling */
        .stock-table-container {
            width: 1100px;
            border-collapse: collapse;
            margin-bottom: 10px;
            padding: 10px;
          
        }

        .stock-table-container thead {
            background-color: grey;
           
        }

        .stock-table-container th, .stock-table-container td {
            padding: 12px;
            text-align: left;
            border: 1px solid #ddd;
        }

        .stock-table-container tbody tr:nth-child(even) {
            background-color: white;
        }

        th, td {
            padding: 12px;
            text-align: left;
        }

        th {
            background-color: #1C4E80;
            color:white;
        }

        /* Button Styling */
        .green-button, .add {
            padding: 8px 15px;
            font-size: 14px;
            border: none;
            color: white;
            cursor: pointer;
            border-radius: 5px;
            text-align: center;
            margin-left:0px;
            margin-bottom:-100px;
            margin-top:-100px;
        }

        .add {
            background-color: green;
        }

        .add:hover {
            background-color: #0056b3;
        }
        .Btn {
            padding:  5px 5px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            text-align: center;
            transition: background-color 0.3s ease, transform 0.3s ease;
            text-decoration: none; 
        }

        .Btn:hover {
            background-color: #0056b3;
            transform: scale(1.05);
        }

        .Btn-danger {
            background-color: #dc3545;
        }

        .Btn-danger:hover {
            background-color: #c82333;
        }

        .Btn-primary {
            background-color: #007bff;
        }

        .Btn-primary:hover {
            background-color: #0056b3;
        }

/* Pagination styling */
.pagination {
    margin-top: 20px;
    margin-right: 20px; /* Move pagination to the right corner */
    text-align: right; /* Align items to the right */
}

.pagination a {
    padding: 8px 15px;
    margin: 0 5px;
    text-decoration: none;
    background-color: transparent; /* Remove background when not hovered */
    color: #1C4E80; /* Set text color to match the pagination number */
    border-radius: 5px;
    transition: all 0.3s ease; /* Smooth transition for hover effects */
}

/* Hover effect */
.pagination a:hover {
    background-color: #0056b3; /* Change background on hover */
    transform: scale(1.1); /* Slightly enlarge the number on hover */
    color: white; /* Text color remains white when hovered */
}

.pagination .active {
    background-color: #007bff; /* Active page number background */
    color: white; /* Active page number text color */
}

/* Disabled state for pagination (for previous/next buttons) */
.pagination .disabled {
    padding: 8px 15px;
    margin: 0 5px;
    background-color: #ccc;
    color: #888;
    cursor: not-allowed;
}
    </style>
    <script>
        function filterTable() {
            const input = document.getElementById('search');
            const filter = input.value.toLowerCase();
            const rows = document.querySelectorAll('#stock-table tbody tr');
            rows.forEach(row => {
                const text = row.innerText.toLowerCase();
                row.style.display = text.includes(filter) ? '' : 'none';
            });
        }
    </script>
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
            <h2>Stocks</h2>
        </div>
        <div class="container-background">
            <div class="search-bar">
                <input type="text" id="search" placeholder="Search Stocks..." onkeyup="filterTable()">
                <button onclick="window.location.href='stocking.php'" class="add">Add New Stock</button>
            </div>
            <table class="stock-table-container" id="stock-table">
                <thead>
                    <tr>
                        <th>Item ID</th>
                        <th>Item Name</th>
                        <th>Category</th>
                        <th>Quantity</th>
                        <th>date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = mysqli_fetch_assoc($result)): ?>
                        <tr>
                            <td><?= $row['id'] ?></td>
                            <td><?= $row['itemname'] ?></td>
                            <td><?= $row['category'] ?></td>
                            <td><?= $row['quantity'] ?></td>
                            <td><?= $row['date'] ?></td>
                            <td>
                                        <a href="del.php?id=<?php echo$row['id']?>" class="Btn Btn-danger">DELETE</a>
                                        <button type="button" class="Btn Btn-primary" data-bs-toggle="modal" data-bs-target="#editModal" onclick="setEditData(<?= $row['itemid']?>, '<?= $row['itemname']?>', '<?= $row['category']?>', <?= $row['quantity']?>)">
                                            EDIT
                                        </button>
                                    </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
            <div class="pagination">
    <?php if ($pageNumber > 1): ?>
        <a href="?page=<?= $pageNumber - 1 ?>" class="Btn">Previous</a>
    <?php endif; ?>
    
    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
        <a href="?page=<?= $i ?>" class="Btn <?= $i == $pageNumber ? 'active' : '' ?>"><?= $i ?></a>
    <?php endfor; ?>

    <?php if ($pageNumber < $totalPages): ?>
        <a href="?page=<?= $pageNumber + 1 ?>" class="Btn">Next</a>
    <?php endif; ?>
</div>

        </div>
    </div>
    
</body>
</html>
