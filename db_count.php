<?php
$mysqli = new mysqli('localhost', 'root', '', 'stadium');
$res = $mysqli->query("SELECT status, COUNT(*) as count FROM bookings GROUP BY status");
while ($row = $res->fetch_assoc()) {
    print_r($row);
}
