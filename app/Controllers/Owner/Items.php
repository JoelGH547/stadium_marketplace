<?php

namespace App\Controllers\Owner;

use App\Controllers\BaseController;
use App\Models\VendorItemModel;
use App\Models\FacilityTypeModel;
use App\Models\StadiumFacilityModel;
use App\Models\SubFieldModel;

class Items extends BaseController
{
    public function add($stadium_id)
    {
        $facilityModel = new FacilityTypeModel();
        $subfieldModel = new SubFieldModel();

        // Check ownership of stadium (optional but good practice)
        // ...

        $subfields = $subfieldModel->where('stadium_id', $stadium_id)->findAll();

        return view('owner/items/add', [
            'facility_types' => $facilityModel->findAll(),
            'stadium_id' => $stadium_id,
            'subfields' => $subfields
        ]);
    }

    public function store($stadium_id)
    {
        $itemModel = new VendorItemModel();
        $stadiumFacilityModel = new StadiumFacilityModel();
        $subfieldModel = new \App\Models\SubfieldModel();

        // Validation
        if (!$this->validate([
            'name' => 'required',
            'price' => 'required|numeric',
            'type_id' => 'required'
        ])) {
            if ($this->request->isAJAX()) {
                 return $this->response->setJSON(['success' => false, 'message' => 'กรุณากรอกข้อมูลให้ครบ']);
            }
            return redirect()->back()->withInput()->with('error', 'กรุณากรอกข้อมูลให้ครบ');
        }

        // Upload Image
        $file = $this->request->getFile('image');
        $imageName = null;

        if ($file && $file->isValid() && !$file->hasMoved()) {
            $imageName = 'item_' . $file->getRandomName();
            $file->move('assets/uploads/items/', $imageName);
        }

        // 1. Determine Target Field (Real Subfield or Stadium Global)
        $targetFieldId = $this->request->getPost('field_id') ?: null;

        // 2. Create Stadium Facility
        $facilityData = [
            'stadium_id'       => $stadium_id,
            'field_id'         => $targetFieldId,
            'facility_type_id' => $this->request->getPost('type_id')
        ];
        $facilityId = $stadiumFacilityModel->insert($facilityData);

        // 3. Create Vendor Item linked to Facility
        $itemData = [
            'stadium_facility_id' => $facilityId,
            'name'            => $this->request->getPost('name'),
            'description'     => $this->request->getPost('description'),
            'price'           => $this->request->getPost('price'),
            'unit'            => $this->request->getPost('unit'),
            'image'           => $imageName,
            'status'          => 'active'
        ];

        $itemId = $itemModel->insert($itemData);

        if ($this->request->isAJAX()) {
            if ($itemId) {
                return $this->response->setJSON([
                    'success' => true, 
                    'item' => [
                        'id' => $itemId,
                        'name' => $itemData['name'],
                        'price' => $itemData['price'],
                        'unit' => $itemData['unit']
                    ]
                ]);
            } else {
                return $this->response->setJSON(['success' => false, 'message' => 'บันทึกไม่สำเร็จ']);
            }
        }

        return redirect()->to('owner/fields/view/' . $stadium_id)->with('success', 'เพิ่มสินค้าสำเร็จ!');
    }

    // ==========================================
    // AJAX METHODS
    // ==========================================

    public function getDetail($item_id)
    {
        if (!session()->get('owner_login')) {
            return $this->response->setJSON(['error' => 'Unauthorized']);
        }

        $itemModel = new VendorItemModel();
        
        // Join to get type_id (via stadium_facilities)
        $item = $itemModel
            ->select('vendor_items.*, stadium_facilities.facility_type_id')
            ->join('stadium_facilities', 'stadium_facilities.id = vendor_items.stadium_facility_id')
            ->find($item_id);

        if (!$item) {
            return $this->response->setJSON(['error' => 'Not found']);
        }

        $typeModel = new FacilityTypeModel();
        $types = $typeModel->findAll();

        return $this->response->setJSON([
            'item' => $item,
            'types' => $types
        ]);
    }

    public function update($item_id)
    {
        if (!session()->get('owner_login')) {
            return $this->response->setJSON(['success' => false, 'message' => 'Unauthorized']);
        }

        $itemModel = new VendorItemModel();
        $stadiumFacilityModel = new StadiumFacilityModel();
        $item = $itemModel->find($item_id);

        if (!$item) {
            return $this->response->setJSON(['success' => false, 'message' => 'Not found']);
        }

        // Validation
        $rules = [
            'name' => 'required',
            'price' => 'required|numeric',
            'type_id' => 'required'
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON(['success' => false, 'message' => 'กรุณากรอกข้อมูลให้ครบถ้วน']);
        }

        $name = $this->request->getPost('name');
        $price = $this->request->getPost('price');
        $unit = $this->request->getPost('unit');
        $type_id = $this->request->getPost('type_id');
        $desc = $this->request->getPost('description');

        // Handle Image
        $file = $this->request->getFile('image');
        $imageName = $item['image']; 

        if ($file && $file->isValid() && !$file->hasMoved()) {
            if (!empty($item['image'])) {
                $oldPath = FCPATH . 'assets/uploads/items/' . $item['image'];
                if (file_exists($oldPath)) unlink($oldPath);
            }

            $imageName = 'item_' . $file->getRandomName();
            $file->move('assets/uploads/items/', $imageName);
        }

        // Update vendor_items
        $itemModel->update($item_id, [
            'name' => $name,
            'price' => $price,
            'unit' => $unit,
            'description' => $desc,
            'image' => $imageName
        ]);

        // Update stadium_facilities (type_id)
        if ($item['stadium_facility_id']) {
            $stadiumFacilityModel->update($item['stadium_facility_id'], [
                'facility_type_id' => $type_id
            ]);
        }

        return $this->response->setJSON(['success' => true]);
    }

    public function delete($item_id)
    {
        if (!session()->get('owner_login'))
            return redirect()->to(base_url('owner/login'));

        $itemModel = new VendorItemModel();
        $stadiumFacilityModel = new \App\Models\StadiumFacilityModel();
        
        $item = $itemModel->find($item_id);

        if (!$item)
            return redirect()->back()->with('error', 'ไม่พบสินค้า');

        // Check ownership via Stadium
        // Join: item -> facility -> field -> stadium
        $verify = $itemModel
            ->select('stadiums.vendor_id')
            ->join('stadium_facilities', 'stadium_facilities.id = vendor_items.stadium_facility_id')
            ->join('stadium_fields', 'stadium_fields.id = stadium_facilities.field_id')
            ->join('stadiums', 'stadiums.id = stadium_fields.stadium_id')
            ->where('vendor_items.id', $item_id)
            ->first();

        if (!$verify || $verify['vendor_id'] != session()->get('owner_id')) {
             // return redirect()->back()->with('error', 'ไม่ได้รับอนุญาต');
        }

        // Delete image
        if (!empty($item['image'])) {
            $path = FCPATH . 'uploads/items/' . $item['image'];
            if (file_exists($path)) {
                unlink($path);
            }
        }

        // Get facility id to delete parent
        $sfId = $item['stadium_facility_id'];
        
        // Delete item
        $itemModel->delete($item_id);

        // Delete facility parent
        if($sfId) {
            $stadiumFacilityModel->delete($sfId);
        }

        if ($this->request->isAJAX()) {
            return $this->response->setJSON(['success' => true]);
        }

        return redirect()->back()->with('success', 'ลบสินค้าสำเร็จ');
    }

    public function toggleStatus($item_id)
    {
        if (!session()->get('owner_login'))
             return $this->response->setJSON(['success' => false, 'message' => 'Unauthorized']);

        $itemModel = new VendorItemModel();
        $item = $itemModel->find($item_id);

        if (!$item)
            return $this->response->setJSON(['success' => false, 'message' => 'Item not found']);

        // Toggle
        $newStatus = ($item['status'] === 'active') ? 'inactive' : 'active';
        $itemModel->update($item_id, ['status' => $newStatus]);

        return $this->response->setJSON(['success' => true, 'status' => $newStatus]);
    }

}
