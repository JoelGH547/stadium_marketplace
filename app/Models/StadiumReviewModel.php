<?php

namespace App\Models;

use CodeIgniter\Model;

class StadiumReviewModel extends Model
{
    protected $table            = 'stadium_reviews';
    protected $primaryKey       = 'id';
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $useTimestamps    = true;

    protected $allowedFields = [
        'booking_id',
        'customer_id',
        'stadium_id',
        'field_id',
        'rating',
        'comment',
        'status',
    ];

    public function existsForBooking(int $bookingId): bool
    {
        return (bool) $this->where('booking_id', $bookingId)->first();
    }

    public function getExistingByBookingIds(array $bookingIds): array
    {
        $bookingIds = array_values(array_filter(array_map('intval', $bookingIds)));
        if (empty($bookingIds)) {
            return [];
        }

        $rows = $this->select('booking_id')
            ->whereIn('booking_id', $bookingIds)
            ->findAll();

        $map = [];
        foreach ($rows as $r) {
            $map[(int) $r['booking_id']] = true;
        }
        return $map;
    }

    public function getSummaryForStadium(int $stadiumId): array
    {
        $row = $this->select('AVG(rating) as avg_rating, COUNT(*) as review_count')
            ->where('stadium_id', $stadiumId)
            ->where('status', 'published')
            ->first();

        $avg = isset($row['avg_rating']) ? (float) $row['avg_rating'] : 0.0;
        $cnt = isset($row['review_count']) ? (int) $row['review_count'] : 0;

        return [
            'avg'   => $cnt > 0 ? $avg : 0.0,
            'count' => $cnt,
        ];
    }

    public function getSummariesForStadiumIds(array $stadiumIds): array
    {
        $stadiumIds = array_values(array_filter(array_map('intval', $stadiumIds)));
        if (empty($stadiumIds)) {
            return [];
        }

        $rows = $this->select('stadium_id, AVG(rating) as avg_rating, COUNT(*) as review_count')
            ->whereIn('stadium_id', $stadiumIds)
            ->where('status', 'published')
            ->groupBy('stadium_id')
            ->findAll();

        $map = [];
        foreach ($rows as $r) {
            $sid = (int) $r['stadium_id'];
            $cnt = (int) ($r['review_count'] ?? 0);
            $avg = (float) ($r['avg_rating'] ?? 0);
            $map[$sid] = [
                'avg'   => $cnt > 0 ? $avg : 0.0,
                'count' => $cnt,
            ];
        }
        return $map;
    }

    public function getLatestForStadium(int $stadiumId, int $limit = 8): array
    {
        return $this->select('stadium_reviews.*, customers.full_name as customer_name')
            ->join('customers', 'customers.id = stadium_reviews.customer_id', 'left')
            ->where('stadium_reviews.stadium_id', $stadiumId)
            ->where('stadium_reviews.status', 'published')
            ->orderBy('stadium_reviews.created_at', 'DESC')
            ->limit($limit)
            ->findAll();
    }
}
