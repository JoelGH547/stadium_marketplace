<?php namespace App\Models;

use CodeIgniter\Model;

class StadiumFacilityModel extends Model
{
    protected $table = 'stadium_facilities';
    protected $primaryKey = 'id';
    protected $allowedFields = ['stadium_id', 'facility_id'];
    
    // ดึง ID สิ่งอำนวยความสะดวกที่เลือกไว้
    public function getSelectedFacilities($stadiumId)
    {
        return $this->where('stadium_id', $stadiumId)->findColumn('facility_id') ?? [];
    }

    // ฟังก์ชันลบของเก่าแล้วบันทึกใหม่
    public function updateFacilities($stadiumId, $selectedFacilityIds)
    {
        $this->where('stadium_id', $stadiumId)->delete();

        if (!empty($selectedFacilityIds) && is_array($selectedFacilityIds)) {
            $data = [];
            foreach ($selectedFacilityIds as $facId) {
                $data[] = [
                    'stadium_id'  => $stadiumId,
                    'facility_id' => $facId
                ];
            }
            return $this->insertBatch($data);
        }
        return true;
    }
}