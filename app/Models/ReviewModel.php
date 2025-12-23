<?php

namespace App\Models;

use CodeIgniter\Model;

class ReviewModel extends Model
{
    protected $table            = 'stadium_reviews';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;

    protected $allowedFields    = [
        'booking_id',
        'customer_id',
        'stadium_id',
        'field_id',
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
        return $this->select('stadium_reviews.*, stadiums.name as stadium_name, customers.full_name as customer_name')
                    ->join('stadiums', 'stadiums.id = stadium_reviews.stadium_id', 'left')
                    ->join('customers', 'customers.id = stadium_reviews.customer_id', 'left')
                    ->orderBy('stadium_reviews.created_at', 'DESC')
                    ->findAll();
    }

    /**
     * Get average rating for a stadium
     */
    public function getAverageRating($stadiumId)
    {
        $result = $this->selectAvg('rating')
                       ->where('stadium_id', $stadiumId)
                       ->where('status', 'published')
                       ->first();
        return $result ? round($result['rating'], 1) : 0;
    }
}
