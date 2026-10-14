<?php
require 'db.php';

$sql = "SELECT id, name, email, role FROM users";
$result = mysqli_query($conn, $sql);

if (!$result) {
    die("Query failed: " . mysqli_error($conn));
}

echo "<h2>Users found in the database</h2>";

while ($row = mysqli_fetch_assoc($result)) {
    echo "ID: " . htmlspecialchars($row['id']) . "<br>";
    echo "Name: " . htmlspecialchars($row['name']) . "<br>";
    echo "Email: " . htmlspecialchars($row['email']) . "<br>";
    echo "Role: " . htmlspecialchars($row['role']) . "<hr>";
}
?>
