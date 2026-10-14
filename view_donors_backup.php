<?php
include 'db.php';

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get donor details
$sql = "SELECT * FROM donors ORDER BY id DESC";
$result = $conn->query($sql);

// Count registered donors
$total_sql = "SELECT COUNT(*) AS total FROM donors";
$total_result = $conn->query($total_sql);
$total_row = $total_result->fetch_assoc();
$total_donors = $total_row["total"] ?? 0;

// Calculate total donations
$donation_sql = "SELECT COALESCE(SUM(amount), 0) AS total_amount FROM donations";
$donation_result = $conn->query($donation_sql);
$donation_row = $donation_result->fetch_assoc();
$total_amount = $donation_row["total_amount"] ?? 0;
?>


<!DOCTYPE html>
<html>
<head>
    <title>View Donors - NGO Management System</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f6f8;
            margin: 0;
            padding: 30px;
        }

        h1 {
            text-align: center;
            color: #333;
        }

        table {
            width: 95%;
            margin: 30px auto;
            border-collapse: collapse;
            background-color: white;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: center;
        }

        th {
            background-color: #4CAF50;
            color: white;
        }

        tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        .no-data {
            text-align: center;
            margin-top: 30px;
            color: #777;
        }
    </style>
</head>

<body>

    <h1>Registered Donors</h1>

    <div style="text-align: center; margin: 20px;">
    <h2>Total Registered Donors: <?php echo $total_donors; ?></h2>

<h2>Total Donations: ₹<?php echo number_format((float)$total_amount, 2); ?></h2>
</div>

    <div style="text-align: center; margin: 20px;">
    <a href="add_donor.php"
       style="background-color: #4CAF50; color: white; padding: 12px 20px; text-decoration: none; border-radius: 5px;">
        + Add New Donor
    </a>
</div>

    <?php
   if ($result && $result->num_rows > 0) {
?> 
<table>
    <tr>
        <th>ID</th>
        <th>Name</th>
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

<?php
$sql = "SELECT
            donors.id,
            donors.name,
            donors.email,
            donors.phone,
            donors.address,
            donors.created_at,
            donations.amount,
            donations.donation_date,
            donations.payment_method,
            donations.donation_purpose
        FROM donors
        LEFT JOIN donations ON donors.id = donations.donor_id
        ORDER BY donors.id DESC, donations.donation_date DESC";

$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
?>
    <tr>
        <td><?php echo htmlspecialchars($row["id"] ?? ""); ?></td>
        <td><?php echo htmlspecialchars($row["name"] ?? ""); ?></td>
        <td><?php echo htmlspecialchars($row["email"] ?? ""); ?></td>
        <td><?php echo htmlspecialchars($row["phone"] ?? ""); ?></td>
        <td><?php echo htmlspecialchars($row["address"] ?? ""); ?></td>

        <td>
            <?php
            echo $row["amount"] !== null
                ? "₹" . number_format((float)$row["amount"], 2)
                : "No donation";
            ?>
        </td>

        <td><?php echo htmlspecialchars($row["donation_date"] ?? ""); ?></td>
        <td><?php echo htmlspecialchars($row["payment_method"] ?? ""); ?></td>
        <td><?php echo htmlspecialchars($row["donation_purpose"] ?? ""); ?></td>
        <td><?php echo htmlspecialchars($row["created_at"] ?? ""); ?></td>

        <td>
            <a href="edit_donor.php?id=<?php echo (int)$row['id']; ?>"
               style="background-color: #2196F3; color: white; padding: 6px 10px; text-decoration: none; border-radius: 4px;">
                Edit
            </a>

            <a href="delete_donor.php?id=<?php echo (int)$row['id']; ?>"
               onclick="return confirm('Are you sure you want to delete this donor?');"
               style="background-color: #f44336; color: white; padding: 6px 10px; text-decoration: none; border-radius: 4px; margin-left: 5px;">
                Delete
            </a>
        </td>
    </tr>
<?php
    }
} else {
    echo "<tr><td colspan='11'>No donors registered yet.</td></tr>";
}
?>

</table>

<?php
} else {
    echo "<p class='no-data'>No donors registered yet.</p>";
}

$conn->close();
?>

</body>
</html>
