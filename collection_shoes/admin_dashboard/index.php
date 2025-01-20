<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- FontAwesome for Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Custom Styles -->
    <style>
    body {
        font-family: 'Poppins', sans-serif;
        background-color: #f4f7fc;
    }

    .sidebar {
        width: 260px;
        background: #1a202c;
        color: #fff;
        height: 100vh;
        position: fixed;
        top: 0;
        left: 0;
        padding-top: 30px;
        transition: width 0.3s ease;
    }

    .sidebar a {
        color: #bfc7d5;
        text-decoration: none;
        padding: 16px 24px;
        display: flex;
        align-items: center;
        font-size: 15px;
        font-weight: 500;
        border-radius: 8px;
        margin-bottom: 10px;
        transition: background 0.3s, padding-left 0.3s;
    }

    .sidebar a:hover {
        background: #2d3748;
        padding-left: 32px;
        color: #fff;
    }

    .sidebar a i {
        margin-right: 12px;
        font-size: 18px;
    }

    .main-content {
        margin-left: 260px;
        padding: 30px;
        transition: margin-left 0.3s ease;
    }

    .dashboard-header {
        background: #fff;
        padding: 20px 30px;
        border-radius: 10px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        display: flex;
        justify-content: space-between;
        align-items: center;
        transition: all 0.3s ease;
    }

    .dashboard-header h1 {
        font-size: 26px;
        color: #2d3748;
        font-weight: 700;
        margin-bottom: 0;
    }

    .dashboard-header p {
        font-size: 16px;
        color: #4a5568;
        font-weight: 500;
    }

    .logout-btn {
        background-color: #38a169;
        /* Green for logout button */
        color: #fff;
        border: none;
        padding: 10px 20px;
        border-radius: 8px;
        cursor: pointer;
        font-size: 15px;
        font-weight: 600;
        transition: background-color 0.3s, transform 0.3s;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    }

    .logout-btn:hover {
        background-color: #2f855a;
        transform: translateY(-2px);
    }

    .logout-btn:active {
        transform: translateY(0);
    }

    .stat-card {
        background: #fff;
        border-radius: 12px;
        padding: 20px;
        text-align: center;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
    }

    .stat-card i {
        font-size: 28px;
        color: #38a169;
        margin-bottom: 15px;
    }

    .stat-card h3 {
        font-size: 22px;
        font-weight: 700;
        margin-bottom: 8px;
    }

    .stat-card p {
        font-size: 14px;
        color: #4a5568;
    }

    .quick-links {
        margin-top: 30px;
    }

    .quick-links h3 {
        font-size: 20px;
        font-weight: 600;
        margin-bottom: 20px;
        color: #2d3748;
    }

    .quick-links a {
        display: flex;
        align-items: center;
        padding: 15px;
        margin-bottom: 12px;
        background: #fff;
        border-radius: 12px;
        color: #2d3748;
        text-decoration: none;
        transition: all 0.3s ease;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .quick-links a:hover {
        background: #f7fafc;
        transform: translateY(-4px);
    }

    .quick-links a i {
        margin-right: 12px;
        color: #38a169;
    }

    @media (max-width: 768px) {
        .sidebar {
            width: 100%;
            height: auto;
            position: relative;
            padding-top: 20px;
        }

        .main-content {
            margin-left: 0;
        }
    }
    </style>
</head>

<body>
    <!-- Sidebar -->
    <!-- <div class="sidebar">
        <h2 class="text-center text-white mb-4">Admin Panel</h2>
        <a href="index.php"><i class="fas fa-home"></i> Dashboard</a>
        <a href="manage_users.php"><i class="fas fa-users"></i> Manage Users</a>
        <a href="manage_products.php"><i class="fas fa-box-open"></i> Manage Products</a>
        <a href="manage_orders.php"><i class="fas fa-shopping-cart"></i> Manage Orders</a>
    </div> -->
    <?php include '../includes/partials/sidebar.php'; ?>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Dashboard Header -->
        <div class="dashboard-header">
            <div>
                <h1>Admin Dashboard</h1>
                <p>Welcome, Admin!</p>
            </div>
            <div style="display: flex; align-items: center;">
                <form action="logout.php" method="POST" style="display: inline;">
                    <button type="submit" class="logout-btn">Logout <i class="fas fa-sign-out-alt"></i></button>
                </form>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="row mt-4">
            <?php
                include '../includes/config/db_config.php';
                $stats = [
                    ['title' => 'Total Users', 'icon' => 'fa-users', 'query' => 'SELECT COUNT(*) AS count FROM users', 'link' => 'manage_users.php'],
                    ['title' => 'Total Products', 'icon' => 'fa-box-open', 'query' => 'SELECT COUNT(*) AS count FROM products', 'link' => '/collection_shoes/admin/manage_products/manage_products.php'],
                    ['title' => 'Pending Orders', 'icon' => 'fa-shopping-cart', 'query' => "SELECT COUNT(*) AS count FROM orders WHERE status = 'Pending'", 'link' => 'manage_orders.php'],
                    ['title' => 'Active Admins', 'icon' => 'fa-user-shield', 'query' => "SELECT COUNT(*) AS count FROM admin WHERE status = 'Active'", 'link' => 'manage_admins.php'],
                    ['title' => 'Pending Reviews', 'icon' => 'fa-comments', 'query' => "SELECT COUNT(*) AS count FROM reviews WHERE rating IS NULL", 'link' => 'manage_reviews.php'],
                    ['title' => 'Return Requests', 'icon' => 'fa-undo-alt', 'query' => "SELECT COUNT(*) AS count FROM returns WHERE status = 'Requested'", 'link' => 'manage_returns.php'],
                    ['title' => 'Coupons', 'icon' => 'fa-tags', 'query' => 'SELECT COUNT(*) AS count FROM coupons', 'link' => 'manage_coupons.php'],
                    ['title' => 'Transactions', 'icon' => 'fa-wallet', 'query' => 'SELECT COUNT(*) AS count FROM transactions', 'link' => 'manage_transactions.php'],
                ];

                foreach ($stats as $stat) {
                    $result = $conn->query($stat['query']);
                    $count = $result->fetch_assoc()['count'] ?? 0;
                    echo "
                    <div class='col-md-3'>
                        <div class='stat-card'>
                            <i class='fas {$stat['icon']}'></i>
                            <h3>{$count}</h3>
                            <p>{$stat['title']}</p>
                            <a href='{$stat['link']}'>View</a>
                        </div>
                    </div>";
                }
            ?>
        </div>

        <!-- Quick Links -->
        <div class="quick-links">
            <h3>Quick Links</h3>
            <a href="manage_roles.php"><i class="fas fa-user-tag"></i> Manage Roles</a>
            <a href="reports.php"><i class="fas fa-chart-line"></i> Generate Reports</a>
            <a href="manage_settings.php"><i class="fas fa-cogs"></i> Manage Settings</a>
            <a href="user_activity.php"><i class="fas fa-activity"></i> User Activity</a>
            <a href="email_templates.php"><i class="fas fa-envelope"></i> Email Templates</a>
            <a href="manage_discounts.php"><i class="fas fa-percent"></i> Manage Discounts</a>
            <a href="system_logs.php"><i class="fas fa-file-alt"></i> System Logs</a>
            <a href="system_backups.php"><i class="fas fa-hdd"></i> System Backups</a>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>