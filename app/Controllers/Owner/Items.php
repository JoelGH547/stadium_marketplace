<?php

namespace App\Controllers\Owner;

use App\Controllers\BaseController;
use App\Models\VendorItemModel;
use App\Models\FacilityTypeModel;
use App\Models\StadiumFacilityModel;

class Items extends BaseController
{
    public function add($stadium_id)
    {
        $facilityModel = new FacilityTypeModel(); 

        return view('owner/items/add', [
            'facility_types' => $facilityModel->findAll(),
            'stadium_id' => $stadium_id
        ]);
    }

    public function store($stadium_id)
    {
        $itemModel = new VendorItemModel();
        $stadiumFacilityModel = new StadiumFacilityModel();

        // Validation
        if (!$this->validate([
            'name' => 'required',
            'price' => 'required|numeric',
            'type_id' => 'required'
        ])) {
            return redirect()->back()->withInput()->with('error', 'กรุณากรอกข้อมูลให้ครบ');
        }

        // Upload Image (Single)
        $file = $this->request->getFile('image');
        $imageName = null;

        if ($file && $file->isValid() && !$file->hasMoved()) {
            $imageName = $file->getRandomName();
            $file->move('uploads/items/', $imageName);
        }

        // Insert into vendor_items
        // Insert into vendor_items
        $itemData = [
            'vendor_id'       => session()->get('owner_id'),
            'facility_type_id'=> $this->request->getPost('type_id'),
            'name'            => $this->request->getPost('name'),
            'description'     => $this->request->getPost('description'),
            'price'           => $this->request->getPost('price'),
            'unit'            => $this->request->getPost('unit'),
            'image'           => $imageName,
            'status'          => 'active'
        ];

        $itemId = $itemModel->insert($itemData);

        if ($itemId) {
            // Save relationship to stadium_facilities
            // stadium_facilities table: id, stadium_id, field_id, type_id, name
            $stadiumFacilityModel->insert([
                'stadium_id' => $stadium_id,
                'field_id'   => null, // Stadium level
                'type_id'    => $this->request->getPost('type_id'),
                'name'       => $this->request->getPost('name')
            ]);

            return redirect()->to('owner/fields/view/' . $stadium_id)->with('success', 'เพิ่มสินค้าสำเร็จ!');
        }

        return redirect()->back()->with('error', 'เกิดข้อผิดพลาดในการบันทึกข้อมูล');
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
        $item = $itemModel->find($item_id);

        if (!$item) {
            return $this->response->setJSON(['error' => 'Not found']);
        }

        // Get all facility types for dropdown
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
        $imageName = $item['image']; // Default to old image

        if ($file && $file->isValid() && !$file->hasMoved()) {
            // Delete old image if exists
            if (!empty($item['image'])) {
                $oldPath = FCPATH . 'uploads/items/' . $item['image'];
                if (file_exists($oldPath)) {
                    unlink($oldPath);
                }
            }

            $imageName = $file->getRandomName();
            $file->move('uploads/items/', $imageName);
        }

        $itemModel->update($item_id, [
            'name' => $name,
            'price' => $price,
            'unit' => $unit,
            'facility_type_id' => $type_id,
            'description' => $desc,
            'image' => $imageName
        ]);

        return $this->response->setJSON(['success' => true]);
    }

    public function delete($item_id)
    {
        if (!session()->get('owner_login'))
            return redirect()->to(base_url('owner/login'));

        $itemModel = new VendorItemModel();
        $item = $itemModel->find($item_id);

        if (!$item)
            return redirect()->back()->with('error', 'ไม่พบสินค้า');

        // Check ownership
        if ($item['vendor_id'] != session()->get('owner_id'))
            return redirect()->back()->with('error', 'ไม่ได้รับอนุญาต');

        // Delete image
        if (!empty($item['image'])) {
            $path = FCPATH . 'uploads/items/' . $item['image'];
            if (file_exists($path)) {
                unlink($path);
            }
        }

        $itemModel->delete($item_id);

        return redirect()->back()->with('success', 'ลบสินค้าสำเร็จ');
    }
}
