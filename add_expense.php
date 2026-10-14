<?php
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

include "db.php";

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $expense_name = trim($_POST["expense_name"]);
    $amount = $_POST["amount"];
    $expense_date = $_POST["expense_date"];
    $category = trim($_POST["category"]);
    $vendor_name = trim($_POST["vendor_name"]);
    $description = trim($_POST["description"]);

    $sql = "INSERT INTO expenses
            (expense_name, amount, expense_date, category, vendor_name, description)
            VALUES (?, ?, ?, ?, ?, ?)";

    $stmt = mysqli_prepare($conn, $sql);

    if ($stmt) {
        mysqli_stmt_bind_param(
            $stmt,
            "sdssss",
            $expense_name,
            $amount,
            $expense_date,
            $category,
            $vendor_name,
            $description
        );

        if (mysqli_stmt_execute($stmt)) {
            $message = "Expense added successfully!";
        } else {
            $message = "Unable to save the expense. Please check your entries.";
        }

        mysqli_stmt_close($stmt);
    } else {
        $message = "Unable to prepare the expense record.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Expense - NGO Fund Management</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f4f7f5;
            color: #26352c;
        }

        .layout {
            display: flex;
            min-height: 100vh;
        }

        .sidebar {
            width: 245px;
            background: #123d2b;
            color: white;
            padding: 30px 18px;
            flex-shrink: 0;
        }

        .brand {
            font-size: 22px;
            font-weight: bold;
            text-align: center;
            margin-bottom: 8px;
        }

        .brand-subtitle {
            text-align: center;
            font-size: 12px;
            color: #c4d9cc;
            margin-bottom: 35px;
        }

        .sidebar a {
            display: block;
            text-decoration: none;
            color: #e6f0e9;
            padding: 13px 14px;
            margin-bottom: 8px;
            border-radius: 8px;
            font-size: 14px;
            transition: 0.2s;
        }

        .sidebar a:hover,
        .sidebar a.active {
            background: #26734d;
            color: white;
        }

        .main {
            flex: 1;
            padding: 35px;
            min-width: 0;
        }

        .topbar {
            margin-bottom: 28px;
        }

        .topbar h1 {
            margin: 0 0 8px;
            font-size: 28px;
            color: #173f2d;
        }

        .topbar p {
            margin: 0;
            color: #718077;
            font-size: 14px;
        }

        .form-card {
            max-width: 850px;
            background: white;
            padding: 30px;
            border-radius: 16px;
            box-shadow: 0 5px 20px rgba(25, 65, 43, 0.07);
            border: 1px solid #e8eee9;
        }

        .card-heading {
            margin-bottom: 25px;
            padding-bottom: 18px;
            border-bottom: 1px solid #edf1ee;
        }

        .card-heading h2 {
            margin: 0 0 7px;
            color: #204b34;
            font-size: 21px;
        }

        .card-heading p {
            margin: 0;
            color: #7a887f;
            font-size: 13px;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 20px;
        }

        .form-group {
            min-width: 0;
        }

        .form-group.full {
            grid-column: 1 / -1;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-size: 14px;
            font-weight: bold;
            color: #344a3b;
        }

        .optional {
            color: #849087;
            font-weight: normal;
            font-size: 12px;
        }

        input,
        select,
        textarea {
            display: block;
            width: 100%;
            padding: 12px 13px;
            border: 1px solid #d9e2db;
            border-radius: 8px;
            background: #fff;
            color: #26352c;
            font-family: inherit;
            font-size: 14px;
            outline: none;
            transition: border-color 0.2s, box-shadow 0.2s;
        }

        input:focus,
        select:focus,
        textarea:focus {
            border-color: #39845a;
            box-shadow: 0 0 0 3px rgba(57, 132, 90, 0.12);
        }

        textarea {
            resize: vertical;
        }

        .message {
            padding: 13px 15px;
            margin: 0 0 22px;
            border-radius: 8px;
            font-size: 14px;
            background: #e8f6ec;
            color: #176536;
            border: 1px solid #c9e8d1;
        }

        .actions {
            display: flex;
            gap: 12px;
            margin-top: 26px;
            flex-wrap: wrap;
        }

        .btn {
            display: inline-block;
            padding: 12px 22px;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: bold;
            text-decoration: none;
            text-align: center;
            cursor: pointer;
            transition: 0.2s;
        }

        .btn-primary {
            background: #216b43;
            color: white;
        }

        .btn-primary:hover {
            background: #174d30;
        }

        .btn-secondary {
            background: #edf3ee;
            color: #315640;
        }

        .btn-secondary:hover {
            background: #dfe9e1;
        }

        @media (max-width: 800px) {
            .layout {
                flex-direction: column;
            }

            .sidebar {
                width: 100%;
                padding: 20px;
            }

            .brand-subtitle {
                margin-bottom: 18px;
            }

            .sidebar a {
                display: inline-block;
                margin: 3px;
            }

            .main {
                padding: 22px 15px;
            }
        }

        @media (max-width: 520px) {
            .form-grid {
                grid-template-columns: 1fr;
            }

            .form-group.full {
                grid-column: auto;
            }

            .form-card {
                padding: 20px;
            }

            .topbar h1 {
                font-size: 24px;
            }

            .actions .btn {
                width: 100%;
            }
        }
    </style>
</head>

<body>

<div class="layout">

    <aside class="sidebar">
        <div class="brand">NGO Manager</div>
        <div class="brand-subtitle">Fund Management System</div>

        <a href="dashboard.php">Dashboard</a>
        <a href="add_donor.php">Add Donor</a>
        <a href="view_donors.php">View Donors</a>
        <a href="add_expense.php" class="active">Add Expense</a>
        <a href="add_project.php">Project Management</a>
        <a href="reports.php">Reports &amp; Analytics</a>
        <a href="logout.php">Logout</a>
    </aside>

    <main class="main">

        <div class="topbar">
            <h1>Add Expense</h1>
            <p>Record and manage your NGO's expenses.</p>
        </div>

        <section class="form-card">

            <div class="card-heading">
                <h2>Expense Details</h2>
                <p>Enter the details below to record a new expense.</p>
            </div>

            <?php if ($message !== "") { ?>
                <p class="message">
                    <?php echo htmlspecialchars($message); ?>
                </p>
            <?php } ?>

            <form method="POST" action="">

                <div class="form-grid">

                    <div class="form-group">
                        <label for="expense_name">Expense Name</label>
                        <input
                            type="text"
                            id="expense_name"
                            name="expense_name"
                            placeholder="e.g. Food supplies"
                            required
                        >
                    </div>

                    <div class="form-group">
                        <label for="amount">Amount (₹)</label>
                        <input
                            type="number"
                            id="amount"
                            name="amount"
                            min="0.01"
                            step="0.01"
                            placeholder="Enter amount"
                            required
                        >
                    </div>

                    <div class="form-group">
                        <label for="expense_date">Expense Date</label>
                        <input
                            type="date"
                            id="expense_date"
                            name="expense_date"
                            required
                        >
                    </div>

                    <div class="form-group">
                        <label for="category">Category</label>
                        <select id="category" name="category" required>
                            <option value="">Select Category</option>
                            <option value="Food">Food</option>
                            <option value="Education">Education</option>
                            <option value="Medical">Medical</option>
                            <option value="Transport">Transport</option>
                            <option value="Office">Office</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>

                    <div class="form-group full">
                        <label for="vendor_name">
                            Vendor Name <span class="optional">(Optional)</span>
                        </label>
                        <input
                            type="text"
                            id="vendor_name"
                            name="vendor_name"
                            placeholder="Enter vendor or supplier name"
                        >
                    </div>

                    <div class="form-group full">
                        <label for="description">
                            Description <span class="optional">(Optional)</span>
                        </label>
                        <textarea
                            id="description"
                            name="description"
                            rows="4"
                            placeholder="Add details about this expense..."
                        ></textarea>
                    </div>

                </div>

                <div class="actions">
                    <button type="submit" class="btn btn-primary">
                        Save Expense
                    </button>

                    <a href="dashboard.php" class="btn btn-secondary">
                        Back to Dashboard
                    </a>
                </div>

            </form>

        </section>

    </main>

</div>

</body>
</html>
