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
        $price_daily = $this->request->getPost('price_daily'); // New

        $desc  = $this->request->getPost('description');
        $status = $this->request->getPost('status') ?? 'active';

        if (!$name || !$price) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['success' => false, 'message' => 'กรุณากรอกชื่อและราคา']);
            }
            return redirect()->back()->with('error', 'กรุณากรอกชื่อและราคา');
        }

        // Handle Outside Images
        $outsideImages = [];
        $files = $this->request->getFiles();

        if (!empty($files['outside_images'])) {
            foreach ($files['outside_images'] as $img) {
                if ($img->isValid() && !$img->hasMoved()) {
                    $newName = 'field_out_' . $img->getRandomName();
                    $img->move('assets/uploads/fields/', $newName);
                    $outsideImages[] = $newName;
                }
            }
        }

        // Handle Inside Images
        $insideImages = [];
        if (!empty($files['inside_images'])) {
            foreach ($files['inside_images'] as $img) {
                if ($img->isValid() && !$img->hasMoved()) {
                    $newName = 'field_in_' . $img->getRandomName();
                    $img->move('assets/uploads/fields/', $newName);
                    $insideImages[] = $newName;
                }
            }
        }

        $this->subfieldModel->insert([
            'stadium_id' => $stadium_id,
            'name'       => $name,
            'price'      => $price,
            'price_daily'=> $price_daily,
            'description'=> $desc,
            'outside_images' => json_encode($outsideImages),
            'inside_images'  => json_encode($insideImages),
            'status'     => $status
        ]);

        $subfield_id = $this->subfieldModel->getInsertID();

        // Save selected facilities (Claim items from System Catalog)
        $selectedItems = $this->request->getPost('items');
        if (!empty($selectedItems) && is_array($selectedItems)) {
            $stadiumFacilityModel = new \App\Models\StadiumFacilityModel();
            $vendorItemModel = new \App\Models\VendorItemModel();

            foreach ($selectedItems as $itemId) {
                // Find item to get its facility pivot
                $item = $vendorItemModel->find($itemId);
                if ($item && !empty($item['stadium_facility_id'])) {
                    // Update the facility to belong to this new subfield
                    // This "moves" it from _SYSTEM_CATALOG_ to the real subfield
                    $stadiumFacilityModel->update($item['stadium_facility_id'], [
                        'field_id' => $subfield_id
                    ]);
                }
            }
        }

        if ($this->request->isAJAX()) {
            return $this->response->setJSON(['success' => true]);
        }

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
        $images = json_decode($sub['outside_images'], true) ?? [];
        foreach ($images as $img) {
            $path = FCPATH . 'assets/uploads/fields/' . $img;
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
        $price_daily = $this->request->getPost('price_daily');

        $desc  = $this->request->getPost('description');
        $status = $this->request->getPost('status');

        // --- Handle Outside Images ---
        $currentOutside = json_decode($sub['outside_images'] ?? '[]', true);
        $deleteOutside = $this->request->getPost('delete_outside_images') ?? [];
        
        foreach ($deleteOutside as $delImg) {
            $path = FCPATH . 'assets/uploads/fields/' . $delImg;
            if (file_exists($path)) @unlink($path);
            $currentOutside = array_filter($currentOutside, fn($i) => $i !== $delImg);
        }

        $files = $this->request->getFiles();
        if (!empty($files['outside_images'])) {
            foreach ($files['outside_images'] as $img) {
                if ($img->isValid() && !$img->hasMoved()) {
                    $newName = 'field_out_' . $img->getRandomName();
                    $img->move('assets/uploads/fields/', $newName);
                    $currentOutside[] = $newName;
                }
            }
        }

        // --- Handle Inside Images ---
        $currentInside = json_decode($sub['inside_images'] ?? '[]', true);
        $deleteInside = $this->request->getPost('delete_inside_images') ?? [];

        foreach ($deleteInside as $delImg) {
            $path = FCPATH . 'assets/uploads/fields/' . $delImg;
            if (file_exists($path)) @unlink($path);
            $currentInside = array_filter($currentInside, fn($i) => $i !== $delImg);
        }

        if (!empty($files['inside_images'])) {
            foreach ($files['inside_images'] as $img) {
                if ($img->isValid() && !$img->hasMoved()) {
                    $newName = 'field_in_' . $img->getRandomName();
                    $img->move('assets/uploads/fields/', $newName);
                    $currentInside[] = $newName;
                }
            }
        }

        $this->subfieldModel->update($subfield_id, [
            'name'           => $name,
            'price'          => $price,
            'price_daily'    => $price_daily,
            'description'    => $desc,
            'status'         => $status,
            'outside_images' => json_encode(array_values($currentOutside)),
            'inside_images'  => json_encode(array_values($currentInside))
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
            // 2. Fetch currently assigned facilities/items for this subfield
            $stadiumFacilityModel = new \App\Models\StadiumFacilityModel();
            
            // Join vendor_items (Standard: Item -> Facility)
            $facilities = $stadiumFacilityModel
                ->select('stadium_facilities.*, vendor_items.name, vendor_items.price, vendor_items.unit, vendor_items.status, vendor_items.image, vendor_items.description, vendor_items.id as item_id, facility_types.name as type_name')
                ->join('vendor_items', 'vendor_items.stadium_facility_id = stadium_facilities.id')
                ->join('facility_types', 'facility_types.id = stadium_facilities.facility_type_id', 'left')
                ->where('field_id', $subfield_id)
                ->findAll();

            // For "checked" status, we don't really have a link back to the catalog item ID easily unless we stored it.
            // But since this is a read-only list of "Assigned Items", we don't need to show them as "Checked Catalog Items".
            // We just show them as items belonging to this field.
            
            return $this->response->setJSON([
                'success' => true,
                'subfield' => $sub,
                'facilities' => $facilities
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON(['error' => $e->getMessage()]);
        }
    }
}

