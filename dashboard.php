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

<title>NGO Fund Management | Dashboard</title>
<style>
* {
    box-sizing: border-box;
    margin: 0;
    padding: 0;
}

body {
    font-family: "Segoe UI", Arial, sans-serif;
    background: #f3f7f4;
    color: #26352b;
}

.layout {
    display: flex;
    min-height: 100vh;
}

/* Sidebar */
.sidebar {
    width: 250px;
    flex-shrink: 0;
    background: linear-gradient(180deg, #103d2b, #092e20);
    color: white;
    padding: 28px 18px;
    display: flex;
    flex-direction: column;
}

.brand {
    font-size: 22px;
    font-weight: bold;
    line-height: 1.5;
    padding: 0 12px 25px;
    border-bottom: 1px solid rgba(255,255,255,0.15);
}

.brand span {
    display: block;
    color: #a8d8b5;
    font-size: 11px;
    font-weight: normal;
    letter-spacing: 2px;
}

.menu-title {
    color: #a8c8b4;
    font-size: 11px;
    letter-spacing: 1.5px;
    margin: 28px 12px 12px;
}

.sidebar a {
    display: block;
    color: #e5eee8;
    text-decoration: none;
    padding: 13px 12px;
    margin-bottom: 7px;
    border-radius: 9px;
    font-size: 14px;
    transition: all 0.25s ease;
}

.sidebar a:hover,
.sidebar a.active {
    background: #286346;
    color: white;
    transform: translateX(3px);
}

.sidebar .logout {
    margin-top: auto;
    color: #ffdada;
    background: rgba(220,53,69,0.12);
}

/* Main content */
.main {
    flex: 1;
    min-width: 0;
}

.topbar {
    background: white;
    padding: 24px 35px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 15px;
    border-bottom: 1px solid #e6ece7;
    box-shadow: 0 3px 12px rgba(24,63,43,0.03);
}

.topbar h1 {
    font-size: 26px;
    color: #183f2b;
}

.profile {
    text-align: right;
    font-size: 13px;
    line-height: 1.8;
    color: #68776d;
}

.profile strong {
    color: #23633d;
    font-size: 15px;
}

.content {
    padding: 32px 35px;
}

/* Welcome banner */
.welcome {
    background: linear-gradient(120deg, #155b38, #32945b);
    color: white;
    padding: 35px;
    border-radius: 18px;
    margin-bottom: 35px;
    box-shadow: 0 10px 25px rgba(27,91,53,0.15);
    position: relative;
    overflow: hidden;
}

.welcome h2 {
    font-size: 28px;
    margin-bottom: 12px;
}

.welcome p {
    color: #e3f3e7;
    line-height: 1.9;
    font-size: 14px;
}

/* Section heading */
.section-heading {
    margin-bottom: 22px;
}

.section-heading h2 {
    font-size: 22px;
    color: #244a32;
    margin-bottom: 8px;
}

.section-heading p {
    font-size: 14px;
    color: #78857b;
}

/* Module cards */
.cards {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 24px;
}

.card {
    background: white;
    padding: 27px;
    border-radius: 16px;
    border: 1px solid #e3ebe5;
    box-shadow: 0 5px 18px rgba(24,63,43,0.045);
    transition: transform 0.25s ease, box-shadow 0.25s ease;
}

.card:hover {
    transform: translateY(-5px);
    box-shadow: 0 12px 28px rgba(24,63,43,0.10);
}

.card-icon {
    width: 52px;
    height: 52px;
    background: #e8f5ec;
    color: #23633d;
    border-radius: 14px;
    display: flex;
    justify-content: center;
    align-items: center;
    font-size: 25px;
    margin-bottom: 20px;
}

.card h3 {
    color: #204d31;
    font-size: 18px;
    margin-bottom: 11px;
}

.card p {
    color: #718075;
    font-size: 13px;
    line-height: 1.8;
    min-height: 46px;
    margin-bottom: 20px;
}

/* Buttons */
.actions {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
}

.button {
    display: inline-block;
    text-decoration: none;
    background: #267347;
    color: white;
    padding: 11px 15px;
    border-radius: 8px;
    font-size: 12px;
    font-weight: 600;
    border: 1px solid #267347;
    transition: all 0.2s ease;
}

.button:hover {
    background: #174e30;
    border-color: #174e30;
    transform: translateY(-2px);
}

.button.secondary {
    background: #edf5ef;
    color: #276541;
    border-color: #dcece0;
}

.button.secondary:hover {
    background: #dcece0;
}

/* Footer */
.footer {
    padding: 24px 20px;
    text-align: center;
    font-size: 12px;
    color: #829087;
    border-top: 1px solid #e5ece6;
    margin-top: 30px;
}

/* Responsive layout */
@media (max-width: 900px) {
    .sidebar {
        width: 220px;
    }

    .content {
        padding: 25px 20px;
    }

    .topbar {
        padding: 20px;
    }

    .cards {
        gap: 18px;
    }

    .card {
        padding: 22px;
    }
}

@media (max-width: 650px) {
    .layout {
        flex-direction: column;
    }

    .sidebar {
        width: 100%;
        padding: 18px;
    }

    .brand {
        padding-bottom: 15px;
    }

    .menu-title {
        margin-top: 15px;
    }

    .sidebar a {
        display: inline-block;
        margin: 4px 2px;
    }

    .sidebar .logout {
        margin-top: 10px;
    }

    .topbar {
        align-items: flex-start;
        flex-direction: column;
    }

    .profile {
        text-align: left;
    }

    .cards {
        grid-template-columns: 1fr;
    }

    .welcome {
        padding: 25px;
    }

    .welcome h2 {
        font-size: 23px;
    }
}
</style>
</head>

<body>

<div class="layout">

    <aside class="sidebar">
        <div class="brand">
            🌿 NGO Manager
            <span>FUND MANAGEMENT</span>
        </div>

        <div class="menu-title">MAIN MENU</div>

        <a href="dashboard.php" class="active">▦ &nbsp; Dashboard</a>
        <a href="add_donor.php">♧ &nbsp; Donor Management</a>
        <a href="view_donors.php">♡ &nbsp; Donation Records</a>
        <a href="add_expense.php">₹ &nbsp; Expense Management</a>
        <a href="add_project.php">▣ &nbsp; Project Management</a>
        <a href="reports.php">▤ &nbsp; Reports &amp; Analytics</a>
    <?php if (strtolower($role) === "admin") { ?>
    <a href="manage_users.php">Manage Users</a>
<?php } ?>

        <a href="logout.php" class="logout">↪ &nbsp; Logout</a>
    </aside>

    <main class="main">

        <div class="topbar">
            <h1>Dashboard</h1>

            <div class="profile">
                Welcome back,<br>
                <strong>
                    <?php echo htmlspecialchars($name, ENT_QUOTES, 'UTF-8'); ?>
                </strong>
                · <?php echo htmlspecialchars($role, ENT_QUOTES, 'UTF-8'); ?>
            </div>
        </div>

        <div class="content">

            <section class="welcome">
                <h2>Hello, <?php echo htmlspecialchars($name, ENT_QUOTES, 'UTF-8'); ?>! 👋</h2>
                <p>
                    Welcome to your NGO Fund Management System.<br>
                    Manage donors, track expenses, organize projects,
                    and review your NGO records from one place.
                </p>
            </section>

            <div class="section-heading">
                <h2>Management Modules</h2>
                <p>Choose a module to manage your NGO activities.</p>
            </div>

            <div class="cards">

    
<div class="card">
    <div class="card-icon">♧</div>
    <h3>Donor Management</h3>
    <p>Add new donors and view registered donor information.</p>
    <div class="actions">
        <a class="button" href="add_donor.php">+ Add Donor</a>
        <?php if (strtolower($role) === "admin") { ?>
            <a class="button secondary" href="view_donors.php">View Donors</a>
        <?php } ?>
    </div>
</div>

<div class="card">
    <div class="card-icon">♡</div>
    <h3>Donation Management</h3>
    <p>Record and review donation information.</p>
    <div class="actions">
        <a class="button" href="add_donor.php">Record Donation</a>
        <?php if (strtolower($role) === "admin") { ?>
            <a class="button secondary" href="view_donors.php">View Records</a>
        <?php } ?>
    </div>
</div>

<div class="card">
    <div class="card-icon">₹</div>
    <h3>Expense &amp; Vendor Tracker</h3>
    <p>Manage expense entries and review recorded expenses.</p>
    <div class="actions">
        <a class="button" href="add_expense.php">+ Add Expense</a>
        <?php if (strtolower($role) === "admin") { ?>
            <a class="button secondary" href="view_expenses.php">View Expenses</a>
        <?php } ?>
    </div>
</div>

<div class="card">
    <div class="card-icon">▣</div>
    <h3>Project &amp; Cause Budgeting</h3>
    <p>Organize NGO projects and budgets.</p>
    <div class="actions">
        <a class="button" href="add_project.php">+ Add Project</a>
        <?php if (strtolower($role) === "admin") { ?>
            <a class="button secondary" href="view_projects.php">View Projects</a>
        <?php } ?>
    </div>
</div>

<?php if (strtolower($role) === "admin") { ?>
<div class="card">
    <div class="card-icon">▤</div>
    <h3>Reports &amp; Analytics</h3>
    <p>Review NGO records and financial summaries.</p>
    <div class="actions">
        <a class="button" href="reports.php">View Reports →</a>
    </div>
</div>
<?php } ?>


        <footer class="footer">
            NGO Fund Management System · Supporting transparency and accountability 💚
        </footer>

    </main>

</div>

</body>
</html>
