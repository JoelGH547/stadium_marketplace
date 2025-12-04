<?php

namespace App\Controllers\Owner;

use App\Controllers\BaseController;
use App\Models\OwnerStadiumModel;
use App\Models\SubFieldModel;

class Subfield extends BaseController
{
    protected $stadiumModel;
    protected $subfieldModel;

    public function __construct()
    {
        $this->stadiumModel = new OwnerStadiumModel();
        $this->subfieldModel = new SubFieldModel();
    }

    public function index($stadium_id)
    {
        if (!session()->get('owner_login'))
            return redirect()->to(base_url('owner/login'));

        $stadium = $this->stadiumModel
            ->where('vendor_id', session()->get('owner_id'))
            ->where('id', $stadium_id)
            ->first();

        if (!$stadium)
            return redirect()->to(base_url('owner/dashboard'))->with('error', 'ไม่พบสนาม');

        $subfields = $this->subfieldModel->where('stadium_id', $stadium_id)->findAll();

        return view('owner/fields/subfields', [
            'stadium'   => $stadium,
            'subfields' => $subfields
        ]);
    }

    public function create($stadium_id)
{
    $name  = $this->request->getPost('name');
    $price = $this->request->getPost('price');

    $desc  = $this->request->getPost('description');
    $status = $this->request->getPost('status') ?? 'active';

    // จัดการรูป
    $images = [];
    $files = $this->request->getFiles();

    if (!empty($files['images'])) {
        foreach ($files['images'] as $img) {
            if ($img->isValid() && !$img->hasMoved()) {
                $newName = $img->getRandomName();
                $img->move('uploads/subfields/', $newName);
                $images[] = $newName;
            }
        }
    }

    $this->subfieldModel->insert([
        'stadium_id' => $stadium_id,
        'name'       => $name,
        'price'      => $price,

        'description'=> $desc,
        'outside_images' => json_encode($images),
        'status'     => $status
    ]);

    return redirect()->back()->with('success', 'เพิ่มสนามย่อยสำเร็จ');
}


    public function toggleStatus($stadium_id, $subfield_id)
    {
        $sub = $this->subfieldModel->find($subfield_id);

        if (!$sub) {
            return redirect()->back()->with('error', 'ไม่พบสนามย่อย');
        }

        // toggle
        $newStatus = ($sub['status'] === 'active') ? 'maintenance' : 'active';

        $this->subfieldModel->update($subfield_id, [
            'status' => $newStatus
        ]);

        return redirect()->back()->with('success', 'อัปเดตสถานะสำเร็จ');
    }

    public function delete($stadium_id, $subfield_id)
    {
        if (!session()->get('owner_login'))
            return redirect()->to(base_url('owner/login'));

        // Check ownership
        $stadium = $this->stadiumModel
            ->where('vendor_id', session()->get('owner_id'))
            ->where('id', $stadium_id)
            ->first();

        if (!$stadium)
            return redirect()->back()->with('error', 'ไม่ได้รับอนุญาต');

        // Find subfield
        $sub = $this->subfieldModel->find($subfield_id);
        if (!$sub)
            return redirect()->back()->with('error', 'ไม่พบสนามย่อย');

        // Delete images
        $images = json_decode($sub['images'], true) ?? [];
        foreach ($images as $img) {
            $path = FCPATH . 'uploads/subfields/' . $img;
            if (file_exists($path)) {
                unlink($path);
            }
        }

        // Delete record
        $this->subfieldModel->delete($subfield_id);

        return redirect()->back()->with('success', 'ลบสนามย่อยสำเร็จ');
    }

    // ==========================================
    // EDIT & UPDATE
    // ==========================================
    public function edit($subfield_id)
    {
        if (!session()->get('owner_login'))
            return redirect()->to(base_url('owner/login'));

        $sub = $this->subfieldModel->find($subfield_id);
        if (!$sub)
            return redirect()->back()->with('error', 'ไม่พบสนามย่อย');

        return view('owner/fields/subfield_edit', [
            'sub' => $sub
        ]);
    }

    public function update($subfield_id)
    {
        if (!session()->get('owner_login'))
            return redirect()->to(base_url('owner/login'));

        $sub = $this->subfieldModel->find($subfield_id);
        if (!$sub)
            return redirect()->back()->with('error', 'ไม่พบสนามย่อย');

        $name  = $this->request->getPost('name');
        $price = $this->request->getPost('price');

        $desc  = $this->request->getPost('description');
        $status = $this->request->getPost('status');

        // Handle Image Deletion
        $deleteImages = $this->request->getPost('delete_images') ?? [];
        $currentImages = json_decode($sub['outside_images'] ?? '[]', true);

        foreach ($deleteImages as $delImg) {
            $path = FCPATH . 'uploads/subfields/' . $delImg;
            if (file_exists($path)) {
                unlink($path);
            }
            $currentImages = array_filter($currentImages, fn($i) => $i !== $delImg);
        }

        // Handle New Images
        $files = $this->request->getFiles();
        if (!empty($files['images'])) {
            foreach ($files['images'] as $img) {
                if ($img->isValid() && !$img->hasMoved()) {
                    $newName = $img->getRandomName();
                    $img->move('uploads/subfields/', $newName);
                    $currentImages[] = $newName;
                }

            }
        }

        $this->subfieldModel->update($subfield_id, [
            'name'           => $name,
            'price'          => $price,
            'description'    => $desc,
            'status'         => $status,
            'outside_images' => json_encode(array_values($currentImages))
        ]);

        if ($this->request->isAJAX() || $this->request->getPost('is_ajax')) {
            return $this->response->setJSON(['success' => true]);
        }

        return redirect()->to(base_url('owner/fields/view/' . $sub['stadium_id']))->with('success', 'แก้ไขสนามย่อยสำเร็จ');
    }

    public function getDetail($subfield_id)
    {
        if (!session()->get('owner_login')) {
            return $this->response->setJSON(['error' => 'Unauthorized']);
        }

        $sub = $this->subfieldModel->find($subfield_id);
        if (!$sub) {
            return $this->response->setJSON(['error' => 'Not found']);
        }

        try {
            // Get Stadium to find Vendor
            $stadium = $this->stadiumModel->find($sub['stadium_id']);
            if (!$stadium) {
                return $this->response->setJSON(['error' => 'Stadium not found']);
            }
            $vendor_id = $stadium['vendor_id'];

            // 1. Fetch all available facilities (Catalog from VendorItems)
            $vendorItemModel = new \App\Models\VendorItemModel();
            $availableFacilities = $vendorItemModel
                ->where('vendor_id', $vendor_id)
                ->where('status', 'active')
                ->findAll();

            // 2. Fetch currently assigned facilities for this subfield
            $stadiumFacilityModel = new \App\Models\StadiumFacilityModel();
            $currentFacilities = $stadiumFacilityModel
                ->where('field_id', $subfield_id)
                ->findAll();
            
            // Map current facilities by name for easy checking
            $checkedNames = array_column($currentFacilities, 'name');

            // We need to pass "checked" IDs back to UI. 
            // But since we don't link by ID, we have to check by Name.
            // The UI expects "checked" array of IDs. 
            // We can iterate availableFacilities and see if their name is in $checkedNames.
            $checkedIds = [];
            foreach ($availableFacilities as $item) {
                if (in_array($item['name'], $checkedNames)) {
                    $checkedIds[] = $item['id'];
                }
            }

            return $this->response->setJSON([
                'subfield' => $sub,
                'facilities' => $availableFacilities,
                'checked' => $checkedIds
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON(['error' => $e->getMessage()]);
        }
    }

    public function updateFacilities($subfield_id)
    {
        if (!session()->get('owner_login')) {
            return $this->response->setJSON(['success' => false, 'message' => 'Unauthorized']);
        }

        $facilityIds = $this->request->getPost('facilities') ?? [];
        $stadiumFacilityModel = new \App\Models\StadiumFacilityModel();
        $vendorItemModel = new \App\Models\VendorItemModel();
        
        // Get subfield
        $sub = $this->subfieldModel->find($subfield_id);
        if(!$sub) return $this->response->setJSON(['success' => false, 'message' => 'Subfield not found']);

        // Delete existing facilities for this field
        $stadiumFacilityModel->where('field_id', $subfield_id)->delete();

        // Insert new facilities
        foreach ($facilityIds as $vid) {
            $vItem = $vendorItemModel->find($vid);
            if ($vItem) {
                $stadiumFacilityModel->insert([
                    'stadium_id' => $sub['stadium_id'],
                    'field_id'   => $subfield_id,
                    'type_id'    => $vItem['facility_type_id'],
                    'name'       => $vItem['name']
                ]);
            }
        }

        return $this->response->setJSON(['success' => true]);
    }

}
