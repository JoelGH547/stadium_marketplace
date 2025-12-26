<?php

// Load CodeIgniter bootstrapper
require_once 'app/Config/Paths.php';
$paths = new Config\Paths();
require_once $paths->systemDirectory . '/Boot.php';
$app = CodeIgniter\Boot::bootWeb($paths);

$db = \Config\Database::connect();
$result = $db->query("UPDATE bookings SET status = 'confirmed' WHERE status = 'approved'");

if ($result) {
    echo "Successfully updated " . $db->affectedRows() . " records from 'approved' to 'confirmed'.\n";
} else {
    echo "Failed to update records.\n";
}
