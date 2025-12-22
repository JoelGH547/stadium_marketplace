<?php

namespace App\Models;

use CodeIgniter\Model;

class ReviewModel extends Model
{
    protected $table            = 'reviews';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;

    protected $allowedFields    = [
        'booking_id',
        'customer_id',
        'stadium_id',
        'rating',
        'comment',
        'status'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Get all reviews with related data for Admin
     */
    public function getReviewsWithDetails()
    {
        return $this->select('reviews.*, stadiums.name as stadium_name, customers.full_name as customer_name')
                    ->join('stadiums', 'stadiums.id = reviews.stadium_id', 'left')
                    ->join('customers', 'customers.id = reviews.customer_id', 'left')
                    ->orderBy('reviews.created_at', 'DESC')
                    ->findAll();
    }

    /**
     * Get average rating for a stadium
     */
    public function getAverageRating($stadiumId)
    {
        $result = $this->selectAvg('rating')
                       ->where('stadium_id', $stadiumId)
                       ->where('status', 'approved')
                       ->first();
        return $result ? round($result['rating'], 1) : 0;
    }
}
