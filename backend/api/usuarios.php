<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../config/config.php';

$sql = "SELECT * FROM usuarios";
$result = $conn->query($sql);
$usuarios = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        unset($row['password']);
        $usuarios[] = $row;
    }
}
echo json_encode(['success' => true, 'usuarios' => $usuarios]);
$conn->close();
?>