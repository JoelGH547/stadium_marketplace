<?php
$mysqli = new mysqli('localhost', 'root', '', 'stadium');
if ($mysqli->connect_error) {
    die('Connect Error (' . $mysqli->connect_errno . ') ' . $mysqli->connect_error);
}
$mysqli->query("UPDATE bookings SET status = 'confirmed' WHERE status = 'approved'");
echo 'Affected: ' . $mysqli->affected_rows . "\n";
$mysqli->close();
