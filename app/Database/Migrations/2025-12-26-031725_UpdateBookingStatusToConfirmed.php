<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UpdateBookingStatusToConfirmed extends Migration
{
    public function up()
    {
        $this->db->query("UPDATE bookings SET status = 'confirmed' WHERE status = 'approved'");
    }

    public function down()
    {
        $this->db->query("UPDATE bookings SET status = 'approved' WHERE status = 'confirmed'");
    }
}
