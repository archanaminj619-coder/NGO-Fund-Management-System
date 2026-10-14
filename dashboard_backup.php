php
<?php
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

$name = $_SESSION["user_name"];
$role = $_SESSION["user_role"];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NGO Fund Management Dashboard</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
    font-family: Arial, sans-serif;
    margin: 0;
    padding: 20px;
    background: #f2f5f7;
    color: #333;
}

header,
.container,
footer {
    max-width: 1000px;
    margin: 0 auto;
}

header {
    border-radius: 10px;
}

        /* Main centered box */
        .main-box {
            max-width: 1100px;
            width: 100%;
            margin: 0 auto;
            background: #f8faf9;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.15);
        }

        header {
            background: #198754;
            color: white;
            padding: 35px 20px;
            text-align: center;
        }

        header h1 {
            font-size: 32px;
            margin: 0 0 12px;
        }

        header p {
            font-size: 17px;
            margin: 5px 0;
        }

        .container {
            width: 100%;
            padding: 30px;
        }

        .welcome {
            background: white;
            padding: 25px;
            border-radius: 12px;
            margin-bottom: 30px;
            text-align: center;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.08);
        }

        .welcome h2 {
            margin-top: 0;
            color: #198754;
            font-size: 28px;
        }

        .welcome p {
            font-size: 17px;
        }

        .section-title {
            margin-bottom: 25px;
            text-align: center;
            font-size: 27px;
        }

        .cards {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 22px;
        }

        .card {
            background: white;
            padding: 25px 18px;
            border-radius: 12px;
            text-align: center;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.08);
        }

        .card h3 {
            margin-top: 0;
            color: #198754;
            font-size: 21px;
        }

        .card p {
            line-height: 1.6;
            font-size: 16px;
            color: #555;
        }

        .button {
            display: inline-block;
            padding: 11px 14px;
            margin: 6px 3px 0;
            background: #198754;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            font-size: 15px;
        }

        .button:hover {
            background: #146c43;
        }

        .logout-area {
            text-align: center;
            padding: 25px 0 5px;
        }

        .logout {
            display: inline-block;
            padding: 13px 30px;
            background: #dc3545;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            font-size: 16px;
        }

        .logout:hover {
            background: #b02a37;
        }

        footer {
            text-align: center;
            padding: 20px;
            background: #e9f3ed;
            color: #555;
            font-size: 14px;
        }

        @media (max-width: 700px) {
            body {
                padding: 15px 10px;
            }

            .container {
                padding: 20px 15px;
            }

            .cards {
                grid-template-columns: 1fr;
            }

            header h1 {
                font-size: 25px;
            }

            .welcome h2 {
                font-size: 23px;
            }
        }
    </style>
</head>

<body>

<div class="main-box">

    <header>
        <h1>NGO Fund Management System</h1>
        <p>Manage donations, expenses, projects, and reports in one place.</p>
    </header>

    <div class="container">

        <div class="welcome">
            <h2>Welcome, <?php echo htmlspecialchars($name); ?>!</h2>

            <p>
                Your Role:
                <strong><?php echo htmlspecialchars($role); ?></strong>
            </p>

            <p>Use the sections below to manage your NGO activities.</p>
        </div>

        <h2 class="section-title">Dashboard Modules</h2>

        <div class="cards">

            <div class="card">
                <h3>Donor Management</h3>
                <p>Add donors and view their registered details.</p>
                <a class="button" href="add_donor.php">Add Donor</a>
                <a class="button" href="view_donors.php">View Donors</a>
            </div>

            <div class="card">
                <h3>Donation Management</h3>
                <p>Record donations and view donation information.</p>
                <a class="button" href="add_donor.php">Record Donation</a>
                <a class="button" href="view_donors.php">View Donations</a>
            </div>

            <div class="card">
                <h3>Expense &amp; Vendor Tracker</h3>
                <p>Record expenses, categories, and vendor details.</p>
                <a class="button" href="add_expense.php">Add Expense</a>
                <a class="button" href="view_expenses.php">View Expenses</a>
            </div>

            <div class="card">
                <h3>Project &amp; Cause Budgeting</h3>
                <p>Create projects and track their allocated budgets.</p>
                <a class="button" href="add_project.php">Add Project</a>
                <a class="button" href="view_projects.php">View Projects</a>
            </div>

            <div class="card">
                <h3>Reporting &amp; Analytics</h3>
                <p>Review donation totals, expenses, funds, and budgets.</p>
                <a class="button" href="reports.php">View Reports</a>
            </div>

        </div>

        <div class="logout-area">
            <a class="logout" href="logout.php">Logout</a>
        </div>

    </div>

    <footer>
        <p>NGO Fund Management System </p>
    </footer>

</div>

</body>
</html>

