<?php
include "db.php";

$id = $_GET["id"];
$message = "";

// Get donor information
$sql = "SELECT * FROM donors WHERE id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);
$donor = mysqli_fetch_assoc($result);

mysqli_stmt_close($stmt);

// Update donor information
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $name = $_POST["name"];
    $email = $_POST["email"];
    $phone = $_POST["phone"];
    $address = $_POST["address"];
    $amount = $_POST["amount"];

    $sql = "UPDATE donors 
            SET name = ?, email = ?, phone = ?, address = ?, amount = ?
            WHERE id = ?";

    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param(
        $stmt,
        "ssssdi",
        $name,
        $email,
        $phone,
        $address,
        $amount,
        $id
    );

    if (mysqli_stmt_execute($stmt)) {
        $message = "Donor updated successfully!";
    } else {
        $message = "Error: " . mysqli_error($conn);
    }

    mysqli_stmt_close($stmt);

    // Get updated donor information
    $sql = "SELECT * FROM donors WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);
    $donor = mysqli_fetch_assoc($result);

    mysqli_stmt_close($stmt);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Donor</title>
</head>

<body>

<h2>NGO Fund Management - Edit Donor</h2>

<form method="POST">

    <label>Donor Name:</label><br>
    <input type="text" name="name"
           value="<?php echo $donor['name']; ?>" required>
    <br><br>

    <label>Email:</label><br>
    <input type="email" name="email"
           value="<?php echo $donor['email']; ?>">
    <br><br>

    <label>Phone:</label><br>
    <input type="text" name="phone"
           value="<?php echo $donor['phone']; ?>">
    <br><br>

    <label>Address:</label><br>
    <textarea name="address"><?php echo $donor['address']; ?></textarea>
    <br><br>

    <label>Donation Amount (₹):</label><br>
    <input type="number" name="amount"
           value="<?php echo $donor['amount']; ?>"
           step="0.01" min="0" required>
    <br><br>

    <button type="submit">Update Donor</button>

</form>

<p><?php echo $message; ?></p>

<br>

<a href="view_donors.php">← Back to Donors</a>

</body>
</html>