<?php
session_start();

// Check whether the user is logged in
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

// Allow only Admin users
if (strtolower($_SESSION["user_role"] ?? "") !== "admin") {
    http_response_code(403);
    exit("Access denied. Only Admin users can manage users.");
}

require_once "db.php";

// Retrieve registered users
$sql = "SELECT id, name, email, role FROM users ORDER BY id DESC";
$result = mysqli_query($conn, $sql);

if (!$result) {
    exit("Unable to load users. Please check the database.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Manage Users | NGO Fund Management</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            padding: 30px;
            font-family: Arial, sans-serif;
            background: #f3f7f4;
            color: #24352b;
        }

        .container {
            max-width: 1000px;
            margin: 0 auto;
        }

        .header {
            background: #14532d;
            color: white;
            padding: 25px;
            border-radius: 14px;
            margin-bottom: 25px;
        }

        .header h1 {
            margin: 0 0 8px;
        }

        .header p {
            margin: 0;
            color: #dcfce7;
        }

        .card {
            background: white;
            padding: 25px;
            border-radius: 14px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            padding: 14px;
            text-align: left;
            border-bottom: 1px solid #e5eee7;
        }

        th {
            background: #e8f5eb;
            color: #14532d;
        }

        .role {
            display: inline-block;
            padding: 5px 12px;
            border-radius: 20px;
            background: #dcfce7;
            color: #166534;
            font-size: 13px;
            font-weight: bold;
        }

        .back {
            display: inline-block;
            margin-top: 20px;
            padding: 11px 18px;
            border-radius: 8px;
            background: #14532d;
            color: white;
            text-decoration: none;
        }

        .back:hover {
            background: #166534;
        }
    </style>
</head>

<body>

<div class="container">

    <div class="header">
        <h1>Manage Users</h1>
        <p>View registered users and their access roles.</p>
    </div>

    <div class="card">
        <h2>Registered Users</h2>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                </tr>
            </thead>

            <tbody>
                <?php if (mysqli_num_rows($result) > 0): ?>

                    <?php while ($user = mysqli_fetch_assoc($result)): ?>
                        <tr>
                            <td>
                                <?= htmlspecialchars((string)$user["id"]) ?>
                            </td>

                            <td>
                                <?= htmlspecialchars($user["name"]) ?>
                            </td>

                            <td>
                                <?= htmlspecialchars($user["email"]) ?>
                            </td>

                            <td>
                                <span class="role">
                                    <?= htmlspecialchars($user["role"]) ?>
                                </span>
                            </td>
                        </tr>
                    <?php endwhile; ?>

                <?php else: ?>

                    <tr>
                        <td colspan="4">No registered users found.</td>
                    </tr>

                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <a class="back" href="dashboard.php">← Back to Dashboard</a>

</div>

</body>
</html>