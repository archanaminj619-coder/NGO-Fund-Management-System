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

$sql = "SELECT * FROM projects ORDER BY id DESC";
$result = mysqli_query($conn, $sql);

$total_sql = "SELECT COUNT(*) AS total FROM projects";
$total_result = mysqli_query($conn, $total_sql);
$total_row = $total_result ? mysqli_fetch_assoc($total_result) : [];
$total_projects = $total_row["total"] ?? 0;

$budget_sql = "SELECT COALESCE(SUM(budget), 0) AS total_budget FROM projects";
$budget_result = mysqli_query($conn, $budget_sql);
$budget_row = $budget_result ? mysqli_fetch_assoc($budget_result) : [];
$total_budget = $budget_row["total_budget"] ?? 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>View Projects - NGO Fund Management</title>

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
            min-width: 0;
            padding: 35px;
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

        .summary-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 20px;
            margin-bottom: 28px;
        }

        .summary-card {
            background: white;
            padding: 24px;
            border-radius: 14px;
            border: 1px solid #e8eee9;
            box-shadow: 0 5px 20px rgba(25, 65, 43, 0.06);
        }

        .summary-card p {
            margin: 0 0 12px;
            color: #718077;
            font-size: 14px;
        }

        .summary-card h2 {
            margin: 0;
            color: #216b43;
            font-size: 27px;
            overflow-wrap: anywhere;
        }

        .table-card {
            background: white;
            padding: 25px;
            border-radius: 14px;
            border: 1px solid #e8eee9;
            box-shadow: 0 5px 20px rgba(25, 65, 43, 0.06);
        }

        .table-heading {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 15px;
            flex-wrap: wrap;
            margin-bottom: 20px;
        }

        .table-heading h2 {
            margin: 0;
            color: #204b34;
            font-size: 21px;
        }

        .search-box {
            padding: 10px 13px;
            width: 250px;
            max-width: 100%;
            border: 1px solid #d9e2db;
            border-radius: 8px;
            font-size: 14px;
            outline: none;
        }

        .search-box:focus {
            border-color: #39845a;
            box-shadow: 0 0 0 3px rgba(57, 132, 90, 0.12);
        }

        .table-container {
            overflow-x: auto;
            border: 1px solid #e8eee9;
            border-radius: 9px;
        }

        table {
            width: 100%;
            min-width: 1000px;
            border-collapse: collapse;
        }

        th, td {
            padding: 14px 15px;
            text-align: left;
            border-bottom: 1px solid #edf1ee;
            font-size: 13px;
            vertical-align: top;
        }

        th {
            background: #216b43;
            color: white;
            font-size: 13px;
            white-space: nowrap;
        }

        tbody tr:nth-child(even) {
            background: #f8faf8;
        }

        tbody tr:hover {
            background: #edf6ef;
        }

        tbody tr:last-child td {
            border-bottom: none;
        }

        .budget {
            color: #216b43;
            font-weight: bold;
            white-space: nowrap;
        }

        .status {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 12px;
            white-space: nowrap;
        }

        .status-planned {
            background: #fff2d8;
            color: #865d0b;
        }

        .status-ongoing {
            background: #e2efff;
            color: #245ca6;
        }

        .status-completed {
            background: #e1f5e7;
            color: #216b43;
        }

        .empty-message {
            text-align: center;
            color: #718077;
            padding: 30px;
        }

        .actions {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
            margin-top: 24px;
        }

        .btn {
            display: inline-block;
            padding: 12px 18px;
            border-radius: 8px;
            text-decoration: none;
            font-size: 14px;
            font-weight: bold;
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

            .summary-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 520px) {
            .table-card {
                padding: 15px;
            }

            .topbar h1 {
                font-size: 24px;
            }

            .search-box {
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
        <a href="add_expense.php">Add Expense</a>
        <a href="view_expenses.php">View Expenses</a>
        <a href="add_project.php">Add Project</a>
        <a href="view_projects.php" class="active">View Projects</a>
        <a href="reports.php">Reports &amp; Analytics</a>
        <a href="logout.php">Logout</a>
    </aside>

    <main class="main">

        <div class="topbar">
            <h1>Project Management</h1>
            <p>Track your NGO projects, causes, budgets, and progress.</p>
        </div>

        <div class="summary-grid">

            <div class="summary-card">
                <p>Total Projects</p>
                <h2><?php echo (int)$total_projects; ?></h2>
            </div>

            <div class="summary-card">
                <p>Total Project Budget</p>
                <h2>₹<?php echo number_format((float)$total_budget, 2); ?></h2>
            </div>

        </div>

        <section class="table-card">

            <div class="table-heading">
                <h2>All Projects</h2>

                <input
                    type="text"
                    id="projectSearch"
                    class="search-box"
                    placeholder="Search projects..."
                    aria-label="Search projects"
                >
            </div>

            <div class="table-container">

                <table id="projectTable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Project Name</th>
                            <th>Cause</th>
                            <th>Budget</th>
                            <th>Start Date</th>
                            <th>End Date</th>
                            <th>Description</th>
                            <th>Status</th>
                        </tr>
                    </thead>

                    <tbody>
                    <?php if ($result && mysqli_num_rows($result) > 0) { ?>

                        <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                            <tr>
                                <td><?php echo (int)$row["id"]; ?></td>

                                <td>
                                    <?php echo htmlspecialchars($row["project_name"] ?? ""); ?>
                                </td>

                                <td>
                                    <?php echo htmlspecialchars($row["cause"] ?? ""); ?>
                                </td>

                                <td class="budget">
                                    ₹<?php echo number_format((float)$row["budget"], 2); ?>
                                </td>

                                <td>
                                    <?php echo htmlspecialchars($row["start_date"] ?? ""); ?>
                                </td>

                                <td>
                                    <?php echo htmlspecialchars($row["end_date"] ?? "Not set"); ?>
                                </td>

                                <td>
                                    <?php echo htmlspecialchars($row["description"] ?? ""); ?>
                                </td>

                                <td>
                                    <?php
                                    $status_value = $row["status"] ?? "";
                                    $status_class = "status-planned";

                                    if ($status_value === "Ongoing") {
                                        $status_class = "status-ongoing";
                                    } elseif ($status_value === "Completed") {
                                        $status_class = "status-completed";
                                    }
                                    ?>

                                    <span class="status <?php echo $status_class; ?>">
                                        <?php echo htmlspecialchars($status_value); ?>
                                    </span>
                                </td>
                            </tr>
                        <?php } ?>

                    <?php } else { ?>

                        <tr>
                            <td colspan="8" class="empty-message">
                                No projects added yet.
                            </td>
                        </tr>

                    <?php } ?>
                    </tbody>
                </table>

            </div>

            <div class="actions">
                <a class="btn btn-primary" href="add_project.php">
                    + Add New Project
                </a>

                <a class="btn btn-secondary" href="dashboard.php">
                    Back to Dashboard
                </a>
            </div>

        </section>

    </main>

</div>

<script>
    const searchInput = document.getElementById("projectSearch");
    const tableRows = document.querySelectorAll("#projectTable tbody tr");

    searchInput.addEventListener("input", function () {
        const searchText = this.value.toLowerCase();

        tableRows.forEach(function (row) {
            const rowText = row.textContent.toLowerCase();
            row.style.display = rowText.includes(searchText) ? "" : "none";
        });
    });
</script>

</body>
</html>
