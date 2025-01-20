<?php
// Include database connection file
include '../../includes/config/db_config.php';

// Start the session
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the form data
    $username = isset($_POST['username']) ? $_POST['username'] : '';
    $email = isset($_POST['email']) ? $_POST['email'] : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    $address = isset($_POST['address']) ? $_POST['address'] : '';
    $phone = isset($_POST['phone']) ? $_POST['phone'] : '';

    // Validate form fields
    if (!empty($username) && !empty($email) && !empty($password)) {
        // Check if email already exists
        $sql = "SELECT * FROM users WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $error_message = "Email is already taken.";
        } else {
            // Hash the password
            $password_hash = password_hash($password, PASSWORD_DEFAULT);

            // Insert the new user into the database
            $sql = "INSERT INTO users (username, email, password_hash, address, phone) 
                    VALUES (?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssss", $username, $email, $password_hash, $address, $phone);
            $stmt->execute();

            // Redirect to login page after successful registration
            header("Location: login.php");
            exit;
        }

        $stmt->close();
    } else {
        $error_message = "All fields are required.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Nike Shoes Store</title>
    <link rel="stylesheet" href="../css/styles.css">
    <style>
        /* General Styles */
        body {
            font-family: 'Arial', sans-serif;
            background: linear-gradient(120deg, #f6d365, #fda085);
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            flex-direction: column;
        }

        /* Register Form */
        .register-form {
            background-color: white;
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 450px;
            text-align: center;
        }

        .register-form h2 {
            margin-bottom: 20px;
            color: #333;
        }

        .register-form label {
            font-weight: bold;
            margin-bottom: 5px;
            display: block;
            color: #555;
        }

        .register-form input {
            width: 100%;
            padding: 12px;
            margin: 12px 0;
            border-radius: 5px;
            border: 1px solid #ccc;
            font-size: 16px;
            box-sizing: border-box;
        }

        .register-form input:focus {
            border-color: #fda085;
            outline: none;
        }

        .register-form button {
            background-color: #fda085;
            color: white;
            padding: 15px 20px;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            width: 100%;
            cursor: pointer;
        }

        .register-form button:hover {
            background-color: #f6d365;
        }

        .error {
            color: red;
            margin-bottom: 15px;
        }

        .register-form p {
            margin-top: 20px;
            color: #333;
        }

        .register-form p a {
            color: #fda085;
            text-decoration: none;
        }

        .register-form p a:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>

    <!-- Include Header -->
    <?php include '../include/nav.php'; ?>

    <!-- Register Form -->
    <div class="register-form">
        <h2>Register</h2>

        <?php if (isset($error_message)) {
            echo "<p class='error'>$error_message</p>";
        } ?>

        <form method="POST" action="">
            <label for="username">Username:</label>
            <input type="text" name="username" id="username" required>

            <label for="email">Email:</label>
            <input type="email" name="email" id="email" required>

            <label for="password">Password:</label>
            <input type="password" name="password" id="password" required>

            <label for="address">Address:</label>
            <input type="text" name="address" id="address">

            <label for="phone">Phone:</label>
            <input type="text" name="phone" id="phone">

            <button type="submit">Register</button>
        </form>

        <p>Already have an account? <a href="login.php">Login</a></p>
    </div>

    <!-- Include Footer -->
    <footer>
        <p>&copy; 2025 Nike Shoes Store. All Rights Reserved.</p>
    </footer>

</body>

</html>