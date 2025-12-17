<?php

namespace App\Controllers\customer;

use App\Controllers\BaseController;
use App\Models\BookingModel;
use App\Models\StadiumReviewModel;

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

        // 2.1 Review eligibility (confirmed + ended + not reviewed)
        $reviewModel = new StadiumReviewModel();
        $bookingIds  = array_map(static fn($x) => (int)($x['id'] ?? 0), $bookings);
        $existing    = $reviewModel->getExistingByBookingIds($bookingIds);
        $nowTs       = time();

        foreach ($bookings as &$b) {
            $bid      = (int) ($b['id'] ?? 0);
            $status   = strtolower((string) ($b['status'] ?? ''));
            $endTs    = strtotime((string) ($b['booking_end_time'] ?? ''));
            $reviewed = isset($existing[$bid]);
            $can      = (!$reviewed && $status === 'confirmed' && $endTs && $endTs < $nowTs);
            $b['reviewed']   = $reviewed;
            $b['can_review'] = $can;
        }
        unset($b);

        // 3. Separate bookings by status
        $pending   = [];
        $confirmed = [];
        $cancelled = [];

        foreach ($bookings as $b) {
            $status = strtolower((string) ($b['status'] ?? ''));
            if ($status === 'approved' || $status === 'paid' || $status === 'confirmed') {
                $confirmed[] = $b;
            } elseif ($status === 'cancelled' || $status === 'rejected') {
                $cancelled[] = $b;
            } else {
                // pending or others
                $pending[] = $b;
            }
        }

        // 4. Prepare data for View
        $data = [
            'bookings'  => $bookings, // Keep original for reference if needed, or remove if unused
            'pending'   => $pending,
            'confirmed' => $confirmed,
            'cancelled' => $cancelled,
            'title'     => 'รายการจองของฉัน'
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
