<?php
try {
    $pdo = new PDO('mysql:host=127.0.0.1;port=3306;dbname=finance_accounting', 'root', '');
    $stmt = $pdo->query('select count(*) from supplier_bills');
    echo 'OK: ' . $stmt->fetchColumn() . ' bills';
} catch (Exception $e) {
    echo 'FAIL: ' . $e->getMessage();
}
