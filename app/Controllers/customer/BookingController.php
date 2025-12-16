<?php

namespace App\Controllers\customer;

use App\Controllers\BaseController;
use App\Models\BookingModel;

class BookingController extends BaseController
{
    public function index()
    {
        // 1. Check Authentication
        if (! session()->get('customer_logged_in')) {
            return redirect()->to(site_url('customer/login'));
        }

        $customerId = session()->get('customer_id');

        // 2. Fetch Bookings
        $bookingModel = new BookingModel();
        $bookings = $bookingModel->getBookingsByCustomerId($customerId);

        // 3. Prepare data for View
        $data = [
            'bookings' => $bookings,
            'title'    => 'รายการจองของฉัน'
        ];

        return view('public/booking_status', $data);
    }
/**
 * JSON สำหรับปฏิทินการจอง (FullCalendar)
 * GET sport/schedule/field/{fieldId}?start=...&end=...
 * - แสดงเฉพาะ pending/confirmed (cancelled ไม่นับว่าไม่ว่าง)
 */
public function fieldSchedule($fieldId = null)
{
    $fieldId = (int) ($fieldId ?? 0);
    if ($fieldId <= 0) {
        return $this->response->setStatusCode(400)->setJSON([
            'error' => 'Invalid field_id',
        ]);
    }

    // FullCalendar จะส่ง start/end เป็น ISO string
    $startRaw = (string) $this->request->getGet('start');
    $endRaw   = (string) $this->request->getGet('end');

    $startTs = $startRaw !== '' ? strtotime($startRaw) : strtotime('today -7 days');
    $endTs   = $endRaw   !== '' ? strtotime($endRaw)   : strtotime('today +30 days');

    if (!$startTs) $startTs = strtotime('today -7 days');
    if (!$endTs)   $endTs   = strtotime('today +30 days');

    $startSql = date('Y-m-d H:i:s', $startTs);
    $endSql   = date('Y-m-d H:i:s', $endTs);

    $bookingModel = new BookingModel();
    $rows = $bookingModel->getScheduleForField($fieldId, $startSql, $endSql);

    $events = [];
    foreach ($rows as $r) {
        $status = strtolower((string) ($r['status'] ?? 'pending'));
        $title  = ($status === 'confirmed') ? 'จองแล้ว' : 'รออนุมัติ';
        $class  = ($status === 'confirmed') ? 'is-confirmed' : 'is-pending';

        $events[] = [
            'id'        => (int) ($r['id'] ?? 0),
            'title'     => $title,
            'start'     => $r['booking_start_time'],
            'end'       => $r['booking_end_time'],
            'className' => [$class],
            'extendedProps' => [
                'status' => $status,
            ],
        ];
    }

    return $this->response->setJSON($events);
}
}
