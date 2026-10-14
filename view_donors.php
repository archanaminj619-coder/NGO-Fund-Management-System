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
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>View Donors | NGO Fund Management</title>

<style>
* {
    box-sizing: border-box;
    margin: 0;
    padding: 0;
}

body {
    font-family: Arial, sans-serif;
    background: #f4f7f5;
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
    background: #123d2a;
    color: white;
    padding: 28px 18px;
}

.brand {
    font-size: 21px;
    font-weight: bold;
    line-height: 1.5;
    padding: 0 12px 30px;
    border-bottom: 1px solid rgba(255,255,255,0.15);
}

.brand span {
    display: block;
    color: #a8d8b5;
    font-size: 12px;
    font-weight: normal;
    letter-spacing: 1px;
}

.menu-title {
    color: #a8c8b4;
    font-size: 11px;
    letter-spacing: 1.5px;
    margin: 30px 12px 12px;
}

.sidebar a {
    display: block;
    color: #e5eee8;
    text-decoration: none;
    padding: 13px 12px;
    margin-bottom: 7px;
    border-radius: 8px;
    font-size: 14px;
}

.sidebar a:hover,
.sidebar a.active {
    background: #286346;
    color: white;
}

.sidebar .logout {
    margin-top: 25px;
    color: #ffdada;
}

/* Main area */
.main {
    flex: 1;
    min-width: 0;
}

.topbar {
    background: white;
    padding: 23px 30px;
    border-bottom: 1px solid #e6ece7;
}

.topbar h1 {
    font-size: 24px;
    color: #183f2b;
}

.topbar p {
    color: #78857b;
    font-size: 13px;
    margin-top: 7px;
}

.content {
    padding: 30px;
}

.page-heading {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 15px;
    margin-bottom: 25px;
}

.page-heading h2 {
    color: #244a32;
    font-size: 22px;
    margin-bottom: 8px;
}

.page-heading p {
    color: #78857b;
    font-size: 13px;
    line-height: 1.6;
}

/* Buttons */
.button {
    display: inline-block;
    padding: 11px 16px;
    border-radius: 8px;
    text-decoration: none;
    font-size: 13px;
    font-weight: bold;
    border: none;
    cursor: pointer;
}

.primary-button {
    background: #267347;
    color: white;
}

.primary-button:hover {
    background: #174e30;
}

/* Summary cards */
.summary {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 20px;
    margin-bottom: 28px;
}

.summary-card {
    background: white;
    padding: 23px;
    border: 1px solid #e6ece7;
    border-radius: 13px;
    box-shadow: 0 4px 14px rgba(24,63,43,0.04);
}

.summary-label {
    color: #78857b;
    font-size: 13px;
    margin-bottom: 12px;
}

.summary-value {
    color: #205b37;
    font-size: 27px;
    font-weight: bold;
}

/* Table card */
.table-card {
    background: white;
    border: 1px solid #e6ece7;
    border-radius: 13px;
    overflow: hidden;
    box-shadow: 0 4px 14px rgba(24,63,43,0.04);
}

.table-heading {
    padding: 22px;
    border-bottom: 1px solid #e8eee9;
}

.table-heading h3 {
    color: #244a32;
    font-size: 17px;
    margin-bottom: 7px;
}

.table-heading p {
    color: #78857b;
    font-size: 13px;
}

.table-tools {
    padding: 16px 20px;
    border-bottom: 1px solid #edf1ed;
}

.search-box {
    width: 100%;
    max-width: 350px;
    padding: 11px 13px;
    border: 1px solid #d8e3da;
    border-radius: 8px;
    font-size: 14px;
    outline: none;
}

.search-box:focus {
    border-color: #328353;
    box-shadow: 0 0 0 3px rgba(50,131,83,0.1);
}

.table-wrapper {
    width: 100%;
    overflow-x: auto;
}

table {
    width: 100%;
    border-collapse: collapse;
    min-width: 1150px;
}

th {
    background: #edf5ef;
    color: #28553a;
    text-align: left;
    padding: 15px 13px;
    font-size: 12px;
    white-space: nowrap;
}

td {
    padding: 15px 13px;
    border-bottom: 1px solid #edf1ed;
    color: #526057;
    font-size: 13px;
    vertical-align: top;
    max-width: 220px;
    overflow-wrap: anywhere;
}

tbody tr:nth-child(even) {
    background: #fafcfb;
}

tbody tr:hover {
    background: #f0f7f1;
}

.donor-name {
    font-weight: bold;
    color: #245c38;
}

.amount {
    color: #267347;
    font-weight: bold;
    white-space: nowrap;
}

.badge {
    display: inline-block;
    padding: 5px 8px;
    background: #e8f5ec;
    color: #276541;
    border-radius: 5px;
    font-size: 11px;
    white-space: nowrap;
}

.no-donation {
    color: #8a938d;
    font-style: italic;
}

.actions {
    display: flex;
    gap: 7px;
}

.edit-button {
    background: #e8f1fb;
    color: #245c91;
}

.delete-button {
    background: #fdecec;
    color: #b52c35;
}

.edit-button:hover {
    background: #d7e8fa;
}

.delete-button:hover {
    background: #f9d8d8;
}

.no-data {
    padding: 35px;
    text-align: center;
    color: #78857b;
    font-size: 14px;
}

.footer {
    text-align: center;
    padding: 22px;
    color: #829087;
    font-size: 12px;
}

@media (max-width: 800px) {
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
        margin-top: 18px;
    }

    .sidebar a {
        display: inline-block;
        margin: 4px 2px;
    }

    .content {
        padding: 20px 14px;
    }

    .topbar {
        padding: 20px;
    }

    .summary {
        grid-template-columns: 1fr;
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

        <a href="dashboard.php">▦ &nbsp; Dashboard</a>
        <a href="add_donor.php">♧ &nbsp; Donor Management</a>
        <a href="view_donors.php" class="active">♡ &nbsp; Donation Records</a>
        <a href="add_expense.php">₹ &nbsp; Expense Management</a>
        <a href="add_project.php">▣ &nbsp; Project Management</a>
        <a href="reports.php">▤ &nbsp; Reports &amp; Analytics</a>
        <a href="logout.php" class="logout">↪ &nbsp; Logout</a>
    </aside>

    <main class="main">

        <header class="topbar">
            <h1>Donor Records</h1>
            <p>View and manage registered donors and their contributions.</p>
        </header>

        <div class="content">

            <div class="page-heading">
                <div>
                    <h2>Donors &amp; Donations</h2>
                    <p>Review donor information and recorded contributions.</p>
                </div>

                <a href="add_donor.php" class="button primary-button">
                    + Add New Donor
                </a>
            </div>

            <div class="summary">

                <div class="summary-card">
                    <div class="summary-label">Total Registered Donors</div>
                    <div class="summary-value">
                        <?php echo number_format((int)$total_donors); ?>
                    </div>
                </div>

                <div class="summary-card">
                    <div class="summary-label">Total Donations Received</div>
                    <div class="summary-value">
                        ₹<?php echo number_format((float)$total_amount, 2); ?>
                    </div>
                </div>

            </div>

            <div class="table-card">

                <div class="table-heading">
                    <h3>Registered Donor List</h3>
                    <p>Donor details and donation history</p>
                </div>

                <div class="table-tools">
                    <input
                        type="search"
                        id="donorSearch"
                        class="search-box"
                        placeholder="Search donors by name, email, phone..."
                        aria-label="Search donor records">
                </div>

                <div class="table-wrapper">
                    <table id="donorTable">

                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Donor Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Address</th>
                                <th>Donation Amount</th>
                                <th>Donation Date</th>
                                <th>Payment Method</th>
                                <th>Purpose</th>
                                <th>Registered On</th>
                                <th>Actions</th>
                            </tr>
                        </thead>

                        <tbody>
                        <?php if ($result->num_rows > 0): ?>

                            <?php while ($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td>
                                        <?php echo (int)$row["id"]; ?>
                                    </td>

                                    <td class="donor-name">
                                        <?php echo htmlspecialchars($row["name"] ?? "", ENT_QUOTES, "UTF-8"); ?>
                                    </td>

                                    <td>
                                        <?php echo htmlspecialchars($row["email"] ?? "", ENT_QUOTES, "UTF-8"); ?>
                                    </td>

                                    <td>
                                        <?php echo htmlspecialchars($row["phone"] ?? "", ENT_QUOTES, "UTF-8"); ?>
                                    </td>

                                    <td>
                                        <?php echo htmlspecialchars($row["address"] ?? "", ENT_QUOTES, "UTF-8"); ?>
                                    </td>

                                    <td class="amount">
                                        <?php if ($row["amount"] !== null): ?>
                                            ₹<?php echo number_format((float)$row["amount"], 2); ?>
                                        <?php else: ?>
                                            <span class="no-donation">No donation</span>
                                        <?php endif; ?>
                                    </td>

                                    <td>
                                        <?php echo htmlspecialchars($row["donation_date"] ?? "—", ENT_QUOTES, "UTF-8"); ?>
                                    </td>

                                    <td>
                                        <?php if (!empty($row["payment_method"])): ?>
                                            <span class="badge">
                                                <?php echo htmlspecialchars($row["payment_method"], ENT_QUOTES, "UTF-8"); ?>
                                            </span>
                                        <?php else: ?>
                                            —
                                        <?php endif; ?>
                                    </td>

                                    <td>
                                        <?php echo htmlspecialchars($row["donation_purpose"] ?? "—", ENT_QUOTES, "UTF-8"); ?>
                                    </td>

                                    <td>
                                        <?php echo htmlspecialchars($row["created_at"] ?? "—", ENT_QUOTES, "UTF-8"); ?>
                                    </td>

                                    <td>
                                        <div class="actions">
                                            <a
                                                class="button edit-button"
                                                href="edit_donor.php?id=<?php echo (int)$row['id']; ?>">
                                                Edit
                                            </a>

                                            <a
                                                class="button delete-button"
                                                href="delete_donor.php?id=<?php echo (int)$row['id']; ?>"
                                                onclick="return confirm('Are you sure you want to delete this donor?');">
                                                Delete
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endwhile; ?>

                        <?php else: ?>

                            <tr>
                                <td colspan="11" class="no-data">
                                    No donors registered yet. Click "Add New Donor" to get started.
                                </td>
                            </tr>

                        <?php endif; ?>
                        </tbody>

                    </table>
                </div>

            </div>

        </div>

        <footer class="footer">
            NGO Fund Management System · Supporting transparency and accountability 💚
        </footer>

    </main>

</div>

<script>
document.getElementById("donorSearch").addEventListener("input", function () {
    const searchText = this.value.toLowerCase();
    const rows = document.querySelectorAll("#donorTable tbody tr");

    rows.forEach(function (row) {
        const rowText = row.textContent.toLowerCase();
        row.style.display = rowText.includes(searchText) ? "" : "none";
    });
});
</script>

</body>
</html>

<?php
$conn->close();
?>