<?php
// Start session for login handling
session_start();

// Include the database connection file
include('../includes/db_config.php');

// Retrieve permissions from the `permissions` table
$permissions_query = "SELECT permission_id, permission_name FROM permissions";
$permissions_result = $conn->query($permissions_query);

if (!$permissions_result) {
    die("Error retrieving permissions: " . $conn->error);
}

// Handle login submission
if (isset($_POST['login_submit'])) {
    // Sanitize and retrieve input values
    $username = $_POST['login_username'];
    $password = $_POST['login_password'];
    $permission_id = $_POST['permission']; // Permission dropdown value

    // Query the database for user credentials
    $stmt = $conn->prepare("SELECT * FROM admin WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // User exists, now check the password
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password_hash'])) {
            // Successful login, set session variables
            $_SESSION['user_id'] = $user['admin_id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role_id'] = $user['role_id'];

            // Check permission and redirect
            if ($permission_id == 1) { // Replace '1' with the ID for 'view_dashboard'
                header("Location: ../admin_dashboard/index.php");  // Correct relative path
                exit();
            } else {
                header("Location: index.php");  // Redirect back to the default location
                exit();
            }                      
        } else {
            // Incorrect password
            $error_message = "Invalid password!";
        }
    } else {
        // User not found
        $error_message = "No user found with that username!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <style>
        /* General Reset */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Arial', sans-serif;
        }

        body {
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            color: #333;
        }

        .login-container {
            background: #fff;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0px 10px 20px rgba(0, 0, 0, 0.2);
            width: 100%;
            max-width: 400px;
        }

        .login-container h2 {
            font-size: 24px;
            font-weight: bold;
            text-align: center;
            margin-bottom: 20px;
            color: #4CAF50; /* Original green color for heading */
        }

        .input-group {
            margin-bottom: 15px;
        }

        .input-group label {
            font-size: 14px;
            color: #555;
            margin-bottom: 5px;
            display: block;
        }

        .input-group input,
        .input-group select {
            width: 100%;
            padding: 12px;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 5px;
            outline: none;
            transition: all 0.3s ease;
        }

        .input-group input:focus,
        .input-group select:focus {
            border-color: #4CAF50;
            box-shadow: 0 0 5px rgba(76, 175, 80, 0.5); /* Original green color glow */
        }

        .error-message {
            color: red;
            text-align: center;
            margin-bottom: 20px;
        }

        .login-btn {
            width: 100%;
            padding: 12px;
            background: #4CAF50; /* Original green color for button */
            color: white;
            font-size: 16px;
            font-weight: bold;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-transform: uppercase;
            transition: all 0.3s ease;
        }

        .login-btn:hover {
            background: #45a049; /* Darker green for hover effect */
            transform: translateY(-2px);
        }

        .back-btn {
            display: inline-block;
            margin-top: 15px;
            font-size: 14px;
            text-align: center;
            text-decoration: none;
            color: #4CAF50; /* Original green color for link */
            font-weight: bold;
            transition: all 0.3s ease;
        }

        .back-btn:hover {
            color: #45a049;
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h2>Admin Login</h2>
        <?php if (isset($error_message)): ?>
            <div class="error-message"><?= $error_message; ?></div>
        <?php endif; ?>
        <form action="" method="POST">
            <div class="input-group">
                <label for="login_username">Username</label>
                <input type="text" id="login_username" name="login_username" required placeholder="Enter your username">
            </div>

            <div class="input-group">
                <label for="login_password">Password</label>
                <input type="password" id="login_password" name="login_password" required placeholder="Enter your password">
            </div>

            <div class="input-group">
                <label for="permission">Select Permission</label>
                <select id="permission" name="permission" required>
                    <option value="">-- Select Permission --</option>
                    <?php while ($row = $permissions_result->fetch_assoc()): ?>
                        <option value="<?= $row['permission_id']; ?>"><?= $row['permission_name']; ?></option>
                    <?php endwhile; ?>
                </select>
            </div>

            <button type="submit" name="login_submit" class="login-btn">Login</button>
        </form>
        <a href="index.php" class="back-btn">Back to admin panel</a>
    </div>
</body>
</html>
