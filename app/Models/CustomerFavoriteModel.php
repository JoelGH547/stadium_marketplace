<?php

namespace App\Models;

use CodeIgniter\Model;

class CustomerFavoriteModel extends Model
{
    protected $table            = 'customer_favorites';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $allowedFields = [
        'customer_id',
        'stadium_id',
    ];

    /**
     * @return int[]
     */
    public function getFavoriteStadiumIds(int $customerId): array
    {
        $rows = $this->select('stadium_id')
            ->where('customer_id', $customerId)
            ->findAll();

        $ids = [];
        foreach ($rows as $r) {
            $ids[] = (int) ($r['stadium_id'] ?? 0);
        }
        $ids = array_values(array_filter($ids, static fn($v) => $v > 0));
        return array_values(array_unique($ids));
    }

    public function isFavorited(int $customerId, int $stadiumId): bool
    {
        return (bool) $this->where([
            'customer_id' => $customerId,
            'stadium_id'  => $stadiumId,
        ])->first();
    }

    /**
     * Toggle favorite state.
     * @return bool New state (true = favorited)
     */
    public function toggle(int $customerId, int $stadiumId): bool
    {
        $existing = $this->where([
            'customer_id' => $customerId,
            'stadium_id'  => $stadiumId,
        ])->first();

        if ($existing) {
            $this->delete((int) ($existing['id'] ?? 0));
            return false;
        }

        $this->insert([
            'customer_id' => $customerId,
            'stadium_id'  => $stadiumId,
        ]);

        return true;
    }

    /**
     * Get favorite stadium rows (joined).
     */
    public function getFavoritesWithStadiumInfo(int $customerId): array
    {
        return $this->select('customer_favorites.created_at AS favorited_at, stadiums.*, categories.name AS category_name, categories.emoji AS category_emoji')
            ->join('stadiums', 'stadiums.id = customer_favorites.stadium_id', 'inner')
            ->join('categories', 'categories.id = stadiums.category_id', 'left')
            ->where('customer_favorites.customer_id', $customerId)
            ->orderBy('customer_favorites.created_at', 'DESC')
            ->findAll();
    }
}
