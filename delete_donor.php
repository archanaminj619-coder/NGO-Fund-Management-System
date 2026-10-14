<?php
include "db.php";

$id = $_GET["id"];

$sql = "DELETE FROM donors WHERE id = ?";

$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $id);

if (mysqli_stmt_execute($stmt)) {
    echo "Donor deleted successfully!";
    echo "<br><br>";
    echo '<a href="view_donors.php">Back to Donors</a>';
} else {
    echo "Error: " . mysqli_error($conn);
}

mysqli_stmt_close($stmt);
?>