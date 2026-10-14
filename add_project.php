<?php
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

include "db.php";

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $project_name = trim($_POST["project_name"]);
    $cause = trim($_POST["cause"]);
    $budget = $_POST["budget"];
    $start_date = $_POST["start_date"];
    $end_date = !empty($_POST["end_date"]) ? $_POST["end_date"] : null;
    $description = trim($_POST["description"]);
    $status = $_POST["status"];

    $sql = "INSERT INTO projects
            (project_name, cause, budget, start_date, end_date, description, status)
            VALUES (?, ?, ?, ?, ?, ?, ?)";

    $stmt = mysqli_prepare($conn, $sql);

    if ($stmt) {
        mysqli_stmt_bind_param(
            $stmt,
            "ssdssss",
            $project_name,
            $cause,
            $budget,
            $start_date,
            $end_date,
            $description,
            $status
        );

        if (mysqli_stmt_execute($stmt)) {
            $message = "Project added successfully!";
        } else {
            $message = "Unable to save the project. Please check your entries.";
        }

        mysqli_stmt_close($stmt);
    } else {
        $message = "Unable to prepare the project record.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Add Project - NGO Fund Management</title>

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
            background: white;
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
        <a href="add_expense.php">Add Expense</a>
        <a href="view_expenses.php">View Expenses</a>
        <a href="add_project.php" class="active">Add Project</a>
        <a href="reports.php">Reports &amp; Analytics</a>
        <a href="logout.php">Logout</a>
    </aside>

    <main class="main">

        <div class="topbar">
            <h1>Project Management</h1>
            <p>Create and manage projects for your NGO's causes.</p>
        </div>

        <section class="form-card">

            <div class="card-heading">
                <h2>Add New Project</h2>
                <p>Enter the project details below to create a record.</p>
            </div>

            <?php if ($message !== "") { ?>
                <p class="message">
                    <?php echo htmlspecialchars($message); ?>
                </p>
            <?php } ?>

            <form method="POST" action="">

                <div class="form-grid">

                    <div class="form-group">
                        <label for="project_name">Project Name</label>
                        <input
                            type="text"
                            id="project_name"
                            name="project_name"
                            placeholder="Enter project name"
                            required
                        >
                    </div>

                    <div class="form-group">
                        <label for="cause">Cause</label>
                        <select id="cause" name="cause" required>
                            <option value="">Select Cause</option>
                            <option value="Education">Education</option>
                            <option value="Food Distribution">Food Distribution</option>
                            <option value="Medical Assistance">Medical Assistance</option>
                            <option value="Women Empowerment">Women Empowerment</option>
                            <option value="Environmental Protection">Environmental Protection</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="budget">Budget (₹)</label>
                        <input
                            type="number"
                            id="budget"
                            name="budget"
                            min="0.01"
                            step="0.01"
                            placeholder="Enter project budget"
                            required
                        >
                    </div>

                    <div class="form-group">
                        <label for="status">Project Status</label>
                        <select id="status" name="status" required>
                            <option value="Planned">Planned</option>
                            <option value="Ongoing">Ongoing</option>
                            <option value="Completed">Completed</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="start_date">Start Date</label>
                        <input
                            type="date"
                            id="start_date"
                            name="start_date"
                            required
                        >
                    </div>

                    <div class="form-group">
                        <label for="end_date">
                            End Date <span class="optional">(Optional)</span>
                        </label>
                        <input
                            type="date"
                            id="end_date"
                            name="end_date"
                        >
                    </div>

                    <div class="form-group full">
                        <label for="description">Project Description</label>
                        <textarea
                            id="description"
                            name="description"
                            rows="4"
                            placeholder="Describe the project's objectives and activities..."
                        ></textarea>
                    </div>

                </div>

                <div class="actions">
                    <button type="submit" class="btn btn-primary">
                        Save Project
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
