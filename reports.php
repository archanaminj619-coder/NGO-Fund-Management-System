<?php
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

if (strtolower($_SESSION["user_role"] ?? "") !== "admin") {
    http_response_code(403);
    exit("Only Admin can access this page.");
}

include "db.php";

// Total donations received
$donation_sql = "SELECT COALESCE(SUM(amount), 0) AS total FROM donations";
$donation_result = mysqli_query($conn, $donation_sql);
$donation_row = mysqli_fetch_assoc($donation_result);
$total_donations = $donation_row["total"] ?? 0;

// Total expenses
$expense_sql = "SELECT COALESCE(SUM(amount), 0) AS total FROM expenses";
$expense_result = mysqli_query($conn, $expense_sql);
$expense_row = mysqli_fetch_assoc($expense_result);
$total_expenses = $expense_row["total"] ?? 0;

// Remaining funds
$remaining_funds = $total_donations - $total_expenses;

// Total donors
$donor_sql = "SELECT COUNT(*) AS total FROM donors";
$donor_result = mysqli_query($conn, $donor_sql);
$donor_row = mysqli_fetch_assoc($donor_result);
$total_donors = $donor_row["total"] ?? 0;

// Total projects
$project_sql = "SELECT COUNT(*) AS total FROM projects";
$project_result = mysqli_query($conn, $project_sql);
$project_row = mysqli_fetch_assoc($project_result);
$total_projects = $project_row["total"] ?? 0;

// Total project budget
$budget_sql = "SELECT COALESCE(SUM(budget), 0) AS total FROM projects";
$budget_result = mysqli_query($conn, $budget_sql);
$budget_row = mysqli_fetch_assoc($budget_result);
$total_budget = $budget_row["total"] ?? 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Reports & Analytics - NGO Fund Management</title>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background: #f4f7f5;
            color: #26352d;
        }

        .layout {
            display: flex;
            min-height: 100vh;
        }

        .sidebar {
            width: 250px;
            background: #123c2b;
            color: white;
            padding: 28px 18px;
            flex-shrink: 0;
        }

        .brand {
            font-size: 23px;
            font-weight: bold;
            margin: 0 0 8px 10px;
        }

        .brand-subtitle {
            color: #b9d7c5;
            font-size: 12px;
            margin: 0 0 35px 10px;
        }

        .menu-title {
            color: #9fc4ad;
            font-size: 11px;
            letter-spacing: 1.5px;
            margin: 0 10px 14px;
        }

        .sidebar a {
            display: block;
            color: #e4eee8;
            text-decoration: none;
            padding: 13px 12px;
            border-radius: 9px;
            margin-bottom: 7px;
            font-size: 14px;
            transition: 0.2s;
        }

        .sidebar a:hover,
        .sidebar a.active {
            background: #246346;
            color: white;
        }

        .main {
            flex: 1;
            min-width: 0;
            padding: 35px;
        }

        .topbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 15px;
            flex-wrap: wrap;
            margin-bottom: 30px;
        }

        .topbar h1 {
            font-size: 28px;
            color: #173e2d;
            margin-bottom: 8px;
        }

        .subtitle {
            color: #78877d;
            font-size: 14px;
            line-height: 1.5;
        }

        .date-label {
            background: white;
            border: 1px solid #e1e9e3;
            padding: 11px 16px;
            border-radius: 10px;
            color: #42634e;
            font-size: 13px;
        }

        .section-title {
            font-size: 18px;
            margin-bottom: 18px;
            color: #244a34;
        }

        .cards {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 20px;
            margin-bottom: 28px;
        }

        .card {
            background: white;
            padding: 25px;
            border-radius: 15px;
            border: 1px solid #e7eee8;
            box-shadow: 0 5px 18px rgba(25, 60, 40, 0.04);
            min-width: 0;
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .card:hover {
            transform: translateY(-3px);
            box-shadow: 0 9px 24px rgba(25, 60, 40, 0.08);
        }

        .card-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            margin-bottom: 22px;
        }

        .card-label {
            color: #718077;
            font-size: 13px;
            line-height: 1.5;
        }

        .icon {
            width: 43px;
            height: 43px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 21px;
            flex-shrink: 0;
        }

        .green-icon {
            background: #e4f4e9;
        }

        .orange-icon {
            background: #fff0df;
        }

        .blue-icon {
            background: #e6efff;
        }

        .purple-icon {
            background: #f0e8ff;
        }

        .red-icon {
            background: #ffe9e6;
        }

        .teal-icon {
            background: #e0f5f1;
        }

        .card-value {
            color: #173e2d;
            font-size: 27px;
            font-weight: bold;
            overflow-wrap: anywhere;
        }

        .card-description {
            margin-top: 10px;
            color: #8a968e;
            font-size: 12px;
        }

        .note {
            background: #fff9e9;
            border: 1px solid #f1e4bd;
            border-left: 4px solid #d9a83e;
            padding: 20px;
            border-radius: 10px;
            color: #705b2c;
            font-size: 13px;
            line-height: 1.8;
            margin-bottom: 28px;
        }

        .note strong {
            display: block;
            margin-bottom: 4px;
            font-size: 14px;
        }

        .quick-actions {
            background: white;
            padding: 24px;
            border: 1px solid #e7eee8;
            border-radius: 15px;
        }

        .quick-actions h2 {
            color: #244a34;
            font-size: 18px;
            margin-bottom: 17px;
        }

        .action-buttons {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
        }

        .button {
            display: inline-block;
            text-decoration: none;
            padding: 12px 17px;
            border-radius: 9px;
            background: #e9f4ec;
            color: #20583a;
            font-size: 13px;
            font-weight: bold;
            transition: 0.2s;
        }

        .button:hover {
            background: #d5eadb;
        }

        .button.primary {
            background: #246346;
            color: white;
        }

        .button.primary:hover {
            background: #174a32;
        }

        .footer {
            text-align: center;
            color: #8b978f;
            font-size: 12px;
            margin-top: 30px;
        }

        @media (max-width: 1100px) {
            .cards {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .main {
                padding: 25px;
            }
        }

        @media (max-width: 700px) {
            .layout {
                flex-direction: column;
            }

            .sidebar {
                width: 100%;
                padding: 20px;
            }

            .brand-subtitle {
                margin-bottom: 20px;
            }

            .sidebar nav {
                display: grid;
                grid-template-columns: repeat(2, minmax(0, 1fr));
                gap: 5px;
            }

            .sidebar a {
                margin-bottom: 0;
            }

            .main {
                padding: 20px;
            }

            .cards {
                grid-template-columns: 1fr;
            }

            .topbar h1 {
                font-size: 24px;
            }
        }
    </style>
</head>

<body>

<div class="layout">

    <aside class="sidebar">
        <div class="brand">NGO Manager</div>
        <div class="brand-subtitle">Fund Management System</div>

        <div class="menu-title">MAIN MENU</div>

        <nav>
            <a href="dashboard.php">▦ Dashboard</a>
            <a href="add_donor.php">＋ Add Donor</a>
            <a href="view_donors.php">♧ View Donors</a>
            <a href="add_expense.php">＋ Add Expense</a>
            <a href="view_expenses.php">▤ View Expenses</a>
            <a href="add_project.php">＋ Add Project</a>
            <a href="view_projects.php">▣ View Projects</a>
            <a href="reports.php" class="active">▥ Reports & Analytics</a>
            <a href="logout.php">↪ Logout</a>
        </nav>
    </aside>

    <main class="main">

        <div class="topbar">
            <div>
                <h1>Reports & Analytics</h1>
                <p class="subtitle">
                    Get a clear overview of your NGO's funds, donors and projects.
                </p>
            </div>

            <div class="date-label">📊 Financial Overview</div>
        </div>

        <h2 class="section-title">Financial Summary</h2>

        <div class="cards">

            <div class="card">
                <div class="card-top">
                    <span class="card-label">Total Donations Received</span>
                    <span class="icon green-icon">₹</span>
                </div>
                <div class="card-value">
                    ₹<?php echo number_format((float)$total_donations, 2); ?>
                </div>
                <div class="card-description">Total recorded donations</div>
            </div>

            <div class="card">
                <div class="card-top">
                    <span class="card-label">Total Expenses</span>
                    <span class="icon orange-icon">↗</span>
                </div>
                <div class="card-value">
                    ₹<?php echo number_format((float)$total_expenses, 2); ?>
                </div>
                <div class="card-description">Total recorded expenses</div>
            </div>

            <div class="card">
                <div class="card-top">
                    <span class="card-label">Remaining Funds</span>
                    <span class="icon teal-icon">✓</span>
                </div>
                <div class="card-value">
                    ₹<?php echo number_format((float)$remaining_funds, 2); ?>
                </div>
                <div class="card-description">Donations minus expenses</div>
            </div>

            <div class="card">
                <div class="card-top">
                    <span class="card-label">Registered Donors</span>
                    <span class="icon blue-icon">♧</span>
                </div>
                <div class="card-value">
                    <?php echo (int)$total_donors; ?>
                </div>
                <div class="card-description">Total donor records</div>
            </div>

            <div class="card">
                <div class="card-top">
                    <span class="card-label">Total Projects</span>
                    <span class="icon purple-icon">▣</span>
                </div>
                <div class="card-value">
                    <?php echo (int)$total_projects; ?>
                </div>
                <div class="card-description">Projects registered in the system</div>
            </div>

            <div class="card">
                <div class="card-top">
                    <span class="card-label">Total Project Budget</span>
                    <span class="icon red-icon">₹</span>
                </div>
                <div class="card-value">
                    ₹<?php echo number_format((float)$total_budget, 2); ?>
                </div>
                <div class="card-description">Combined budgets of all projects</div>
            </div>

        </div>

        <div class="note">
            <strong>ℹ Financial Information</strong>
            Remaining funds are calculated by subtracting recorded expenses
            from total donations. This is a basic estimate, not a complete
            accounting balance. Project budgets are shown separately and are
            not deducted again from the remaining funds.
        </div>

        <section class="quick-actions">
            <h2>Quick Access</h2>

            <div class="action-buttons">
                <a href="view_donors.php" class="button">View Donors</a>
                <a href="view_expenses.php" class="button">View Expenses</a>
                <a href="view_projects.php" class="button">View Projects</a>
                <a href="dashboard.php" class="button primary">← Back to Dashboard</a>
            </div>
        </section>

        <div class="footer">
            NGO Fund Management System · Reports & Analytics
        </div>

    </main>

</div>

</body>
</html>
