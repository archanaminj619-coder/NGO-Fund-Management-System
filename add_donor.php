<?php
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

include "db.php";

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $name = $_POST["name"];
    $email = $_POST["email"];
    $phone = $_POST["phone"];
    $address = $_POST["address"];

    $amount = $_POST["amount"];
    $donation_date = $_POST["donation_date"];
    $payment_method = $_POST["payment_method"];
    $donation_purpose = $_POST["donation_purpose"];

    // Insert donor details
    mysqli_begin_transaction($conn);

    try {
        $sql = "INSERT INTO donors (name, email, phone, address)
                VALUES (?, ?, ?, ?)";

        $stmt = mysqli_prepare($conn, $sql);

        mysqli_stmt_bind_param(
            $stmt,
            "ssss",
            $name,
            $email,
            $phone,
            $address
        );

        mysqli_stmt_execute($stmt);

        // Get the ID of the newly added donor
        $donor_id = mysqli_insert_id($conn);

        mysqli_stmt_close($stmt);

        // Insert donation details
        $sql = "INSERT INTO donations
                (donor_id, amount, donation_date, payment_method, donation_purpose)
                VALUES (?, ?, ?, ?, ?)";

        $stmt = mysqli_prepare($conn, $sql);

        mysqli_stmt_bind_param(
            $stmt,
            "idsss",
            $donor_id,
            $amount,
            $donation_date,
            $payment_method,
            $donation_purpose
        );

        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        mysqli_commit($conn);

        $message = "Donor and donation added successfully!";

    } catch (mysqli_sql_exception $e) {
        mysqli_rollback($conn);
        $message = "Unable to save donor and donation. Please check your entries and database setup.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Donor | NGO Fund Management</title>

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
            padding: 23px 35px;
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
            max-width: 1000px;
            margin: 0 auto;
            padding: 32px;
        }

        .page-intro {
            margin-bottom: 25px;
        }

        .page-intro h2 {
            color: #244a32;
            font-size: 22px;
            margin-bottom: 9px;
        }

        .page-intro p {
            color: #78857b;
            font-size: 14px;
            line-height: 1.6;
        }

        /* Form card */
        .form-card {
            background: white;
            padding: 32px;
            border-radius: 15px;
            border: 1px solid #e6ece7;
            box-shadow: 0 5px 20px rgba(24, 63, 43, 0.05);
        }

        .form-section-title {
            color: #245c38;
            font-size: 17px;
            padding-bottom: 14px;
            margin-bottom: 22px;
            border-bottom: 1px solid #e8eee9;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 21px;
        }

        .form-group {
            min-width: 0;
        }

        .form-group.full-width {
            grid-column: 1 / -1;
        }

        label {
            display: block;
            font-size: 13px;
            font-weight: bold;
            color: #344b3a;
            margin-bottom: 9px;
        }

        .required {
            color: #c0392b;
        }

        input,
        select,
        textarea {
            width: 100%;
            padding: 12px 13px;
            border: 1px solid #d8e3da;
            border-radius: 8px;
            font-size: 14px;
            font-family: inherit;
            color: #26352b;
            background: #fcfdfc;
            outline: none;
            transition: border-color 0.2s, box-shadow 0.2s;
        }

        input:focus,
        select:focus,
        textarea:focus {
            border-color: #328353;
            box-shadow: 0 0 0 3px rgba(50, 131, 83, 0.12);
        }

        textarea {
            min-height: 90px;
            resize: vertical;
        }

        .form-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            margin-top: 28px;
            padding-top: 22px;
            border-top: 1px solid #e8eee9;
        }

        .button {
            display: inline-block;
            border: none;
            border-radius: 8px;
            padding: 13px 20px;
            font-size: 14px;
            font-weight: bold;
            text-decoration: none;
            cursor: pointer;
            text-align: center;
        }

        .primary-button {
            background: #267347;
            color: white;
        }

        .primary-button:hover {
            background: #174e30;
        }

        .secondary-button {
            background: #edf5ef;
            color: #276541;
        }

        .secondary-button:hover {
            background: #dcece0;
        }

        .message {
            margin-bottom: 20px;
            padding: 14px 16px;
            border-radius: 8px;
            background: #e8f5ec;
            color: #205c36;
            font-size: 14px;
            line-height: 1.6;
        }

        .footer {
            text-align: center;
            padding: 22px;
            color: #829087;
            font-size: 12px;
        }

        @media (max-width: 750px) {
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
                padding: 20px 15px;
            }

            .topbar {
                padding: 20px;
            }

            .form-card {
                padding: 22px 18px;
            }

            .form-grid {
                grid-template-columns: 1fr;
            }

            .form-group.full-width {
                grid-column: auto;
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
        <a href="add_donor.php" class="active">♧ &nbsp; Donor Management</a>
        <a href="view_donors.php">♡ &nbsp; Donation Records</a>
        <a href="add_expense.php">₹ &nbsp; Expense Management</a>
        <a href="add_project.php">▣ &nbsp; Project Management</a>
        <a href="reports.php">▤ &nbsp; Reports &amp; Analytics</a>
        <a href="logout.php" class="logout">↪ &nbsp; Logout</a>
    </aside>

    <main class="main">

        <header class="topbar">
            <h1>Add Donor</h1>
            <p>Register donor details and record their contribution.</p>
        </header>

        <div class="content">

            <div class="page-intro">
                <h2>Donor &amp; Donation Registration</h2>
                <p>Enter the donor's information and donation details below.</p>
            </div>

            <?php if ($message !== ""): ?>
                <div class="message" role="status">
                    <?php echo htmlspecialchars($message, ENT_QUOTES, 'UTF-8'); ?>
                </div>
            <?php endif; ?>

            <div class="form-card">

                <form method="POST" action="add_donor.php">

                    <h3 class="form-section-title">Donor Information</h3>

                    <div class="form-grid">

                        <div class="form-group">
                            <label for="name">Donor Name <span class="required">*</span></label>
                            <input type="text" id="name" name="name"
                                   placeholder="Enter full name" required>
                        </div>

                        <div class="form-group">
                            <label for="email">Email Address</label>
                            <input type="email" id="email" name="email"
                                   placeholder="example@email.com">
                        </div>

                        <div class="form-group">
                            <label for="phone">Phone Number</label>
                            <input type="tel" id="phone" name="phone"
                                   placeholder="Enter phone number">
                        </div>

                        <div class="form-group">
                            <label for="address">Address</label>
                            <textarea id="address" name="address"
                                      placeholder="Enter donor address"></textarea>
                        </div>

                    </div>

                    <h3 class="form-section-title" style="margin-top:32px;">
                        Donation Information
                    </h3>

                    <div class="form-grid">

                        <div class="form-group">
                            <label for="amount">Donation Amount (₹) <span class="required">*</span></label>
                            <input type="number" id="amount" name="amount"
                                   min="0.01" step="0.01"
                                   placeholder="Enter amount" required>
                        </div>

                        <div class="form-group">
                            <label for="donation_date">Donation Date <span class="required">*</span></label>
                            <input type="date" id="donation_date" name="donation_date" required>
                        </div>

                        <div class="form-group">
                            <label for="payment_method">Payment Method <span class="required">*</span></label>
                            <select id="payment_method" name="payment_method" required>
                                <option value="">Select payment method</option>
                                <option value="Cash">Cash</option>
                                <option value="UPI">UPI</option>
                                <option value="Bank Transfer">Bank Transfer</option>
                                <option value="Cheque">Cheque</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="donation_purpose">Donation Purpose</label>
                            <textarea id="donation_purpose" name="donation_purpose"
                                      placeholder="What will the donation support?"></textarea>
                        </div>

                    </div>

                    <div class="form-actions">
                        <button type="submit" class="button primary-button">
                            + Save Donor &amp; Donation
                        </button>

                        <a href="dashboard.php" class="button secondary-button">
                            Back to Dashboard
                        </a>
                    </div>

                </form>
            </div>

        </div>

        <footer class="footer">
            NGO Fund Management System · Supporting transparency and accountability 💚
        </footer>

    </main>
</div>
</body>
</html>
