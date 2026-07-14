<?php
try {
    $conn = new PDO("mysql:host=localhost;dbname=university_system;charset=utf8mb4", "root", "", [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    $stmt = $conn->query("SELECT id, title, file_type, file_extension FROM files LIMIT 5");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo $row['id'] . " | " . $row['title'] . " | file_type=" . $row['file_type'] . " | ext=" . $row['file_extension'] . "\n";
    }
    echo "---\n";
    $result = $conn->query("SELECT COLUMN_TYPE FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='files' AND COLUMN_NAME='file_type'");
    $r = $result->fetch(PDO::FETCH_ASSOC);
    echo "file_type ENUM: " . ($r['COLUMN_TYPE'] ?? 'N/A') . "\n";
    $result2 = $conn->query("SELECT COLUMN_TYPE FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='files' AND COLUMN_NAME='file_extension'");
    $r2 = $result2->fetch(PDO::FETCH_ASSOC);
    echo "file_extension: " . ($r2['COLUMN_TYPE'] ?? 'N/A') . "\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
