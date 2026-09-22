<?php

require_once "database.php";

$faculty_id = isset($_GET['faculty_id']) ? $_GET['faculty_id']
    : null;

if (!$faculty_id) {
    echo json_encode(array());
    exit;
}

$sql = "
    SELECT department_id, name
    FROM departments
    WHERE faculty_id = ?";

$stmt = $pdo->prepare($sql);
$stmt->execute(array($faculty_id));

$departments = $stmt->fetchAll(PDO::FETCH_ASSOC);

header('Content-Type: application/json');

echo json_encode($departments);
?>