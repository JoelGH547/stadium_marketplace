<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\StadiumModel;
use App\Models\CategoryModel;
use App\Models\VendorModel;
use App\Models\StadiumFieldModel;    // Model สนามย่อย
use App\Models\StadiumFacilityModel; // Model ของในสนาม
use App\Models\FacilityTypeModel;    // Model หมวดหมู่
use CodeIgniter\Database\Exceptions\DatabaseException;

class StadiumController extends BaseController
{
    protected $stadiumModel;
    protected $categoryModel;
    protected $vendorModel;

    public function __construct()
    {
        $this->stadiumModel  = new StadiumModel();
        $this->categoryModel = new CategoryModel();
        $this->vendorModel   = new VendorModel();
        
        helper(['form']);
    }

    // =================================================================================
    // 🏟️ ส่วนจัดการสนามหลัก (Stadiums)
    // =================================================================================

    public function index()
    {
        $stadiums = $this->stadiumModel
            ->select('stadiums.*, categories.name AS category_name, categories.emoji AS category_emoji, vendors.vendor_name AS vendor_name')
            ->join('categories', 'categories.id = stadiums.category_id', 'left')
            ->join('vendors', 'vendors.id = stadiums.vendor_id', 'left')
            ->orderBy('stadiums.id', 'DESC')
            ->findAll();

        $data = [
            'title'    => 'Stadiums List',
            'stadiums' => $stadiums,
        ];

        return view('admin/stadiums/index', $data);
    }

    public function create()
    {
        $data = [
            'title'      => 'Add New Stadium',
            'categories' => $this->categoryModel->findAll(),
            'vendors'    => $this->vendorModel->findAll(),
        ];

        return view('admin/stadiums/create', $data);
    }

    public function store()
    {
        if (!$this->validate([
            'name'          => 'required|max_length[100]',
            'category_id'   => 'required|integer',
            'vendor_id'     => 'required|integer',
            'contact_phone' => 'permit_empty|regex_match[/^[0-9]{10}$/]',
        ])) {
            return redirect()->back()->withInput()->with('validation', $this->validator);
        }

        $uploadPath = FCPATH . 'assets/uploads/stadiums/';
        if (!is_dir($uploadPath)) { mkdir($uploadPath, 0777, true); }

        // --- รูปปก (Outside) ---
        $outsideImagesJson = '[]'; 
        $outsideFile = $this->request->getFile('outside_image');
        if ($outsideFile && $outsideFile->isValid() && !$outsideFile->hasMoved()) {
            $newName = 'outside_' . time() . '_' . $outsideFile->getRandomName();
            $outsideFile->move($uploadPath, $newName);
            $outsideImagesJson = json_encode([$newName]); 
        }

        // --- รูปภายใน (Inside) ---
        $insideFiles = $this->request->getFileMultiple('inside_images');
        $insideImagesArray = [];
        if (!empty($insideFiles)) {
            foreach ($insideFiles as $file) {
                if ($file->isValid() && !$file->hasMoved()) {
                    $newName = 'inside_' . time() . '_' . $file->getRandomName();
                    $file->move($uploadPath, $newName);
                    $insideImagesArray[] = $newName;
                }
            }
        }

        // --- บันทึกข้อมูลสนาม ---
        $this->stadiumModel->save([
            'name'           => $this->request->getPost('name'),
            'description'    => $this->request->getPost('description'),
            'booking_type'   => $this->request->getPost('booking_type'), // [สำคัญ] ประเภทสนาม
            'category_id'    => $this->request->getPost('category_id'),
            'vendor_id'      => $this->request->getPost('vendor_id'),
            'open_time'      => $this->request->getPost('open_time'),
            'close_time'     => $this->request->getPost('close_time'),
            'contact_email'  => $this->request->getPost('contact_email'),
            'contact_phone'  => $this->request->getPost('contact_phone'),
            'province'       => $this->request->getPost('province'),
            'address'        => $this->request->getPost('address'),
            'lat'            => $this->request->getPost('lat'),
            'lng'            => $this->request->getPost('lng'),
            'map_link'       => $this->request->getPost('map_link'),
            'outside_images' => $outsideImagesJson,
            'inside_images'  => json_encode($insideImagesArray),
        ]);

        return redirect()->to(base_url('admin/stadiums'))->with('success', 'เพิ่มสนามเรียบร้อยแล้ว');
    }

    public function edit($id = null)
    {
        $stadium = $this->stadiumModel->find($id);
        if (!$stadium) {
            return redirect()->to(base_url('admin/stadiums'))->with('error', 'ไม่พบข้อมูลสนาม');
        }

        $data = [
            'title'      => 'Edit Stadium',
            'stadium'    => $stadium,
            'categories' => $this->categoryModel->findAll(),
            'vendors'    => $this->vendorModel->findAll(),
        ];

        return view('admin/stadiums/edit', $data);
    }

    public function update($id = null)
    {
        $stadium = $this->stadiumModel->find($id);
        if (!$stadium) {
            return redirect()->to(base_url('admin/stadiums'))->with('error', 'ไม่พบข้อมูลสนาม');
        }

        if (!$this->validate([
            'name'          => 'required|max_length[100]',
            'category_id'   => 'required|integer',
            'vendor_id'     => 'required|integer',
            'contact_phone' => 'permit_empty|regex_match[/^[0-9]{10}$/]',
        ])) {
            return redirect()->back()->withInput()->with('validation', $this->validator);
        }

        $uploadPath = FCPATH . 'assets/uploads/stadiums/';
        if (!is_dir($uploadPath)) { mkdir($uploadPath, 0777, true); }

        // --- Logic รูปภาพ ---
        $outsideOld = json_decode($stadium['outside_images'] ?? '[]', true) ?? [];
        $outsideResult = $outsideOld;

        if ($this->request->getPost('delete_outside') == '1') {
            if (!empty($outsideOld[0]) && file_exists($uploadPath . $outsideOld[0])) unlink($uploadPath . $outsideOld[0]);
            $outsideResult = []; 
        }
        $outsideFile = $this->request->getFile('outside_image');
        if ($outsideFile && $outsideFile->isValid() && !$outsideFile->hasMoved()) {
            if (!empty($outsideResult[0]) && file_exists($uploadPath . $outsideResult[0])) unlink($uploadPath . $outsideResult[0]);
            $newName = 'outside_' . time() . '_' . $outsideFile->getRandomName();
            $outsideFile->move($uploadPath, $newName);
            $outsideResult = [$newName];
        }

        $insideOld = json_decode($stadium['inside_images'] ?? '[]', true) ?? [];
        $insideResult = [];
        $filesToDelete = $this->request->getPost('delete_inside') ?? [];

        foreach ($insideOld as $oldImg) {
            if (in_array($oldImg, $filesToDelete)) {
                if (file_exists($uploadPath . $oldImg)) unlink($uploadPath . $oldImg);
            } else {
                $insideResult[] = $oldImg;
            }
        }
        $insideFiles = $this->request->getFileMultiple('inside_images');
        if ($insideFiles) {
            foreach ($insideFiles as $file) {
                if ($file->isValid() && !$file->hasMoved()) {
                    $newName = 'inside_' . time() . '_' . $file->getRandomName();
                    $file->move($uploadPath, $newName);
                    $insideResult[] = $newName;
                }
            }
        }

        // --- อัปเดตตาราง Stadiums ---
        $this->stadiumModel->update($id, [
            'name'           => $this->request->getPost('name'),
            'description'    => $this->request->getPost('description'),
            'booking_type'   => $this->request->getPost('booking_type'), // อัปเดตประเภท
            'category_id'    => $this->request->getPost('category_id'),
            'vendor_id'      => $this->request->getPost('vendor_id'),
            'open_time'      => $this->request->getPost('open_time'),
            'close_time'     => $this->request->getPost('close_time'),
            'contact_email'  => $this->request->getPost('contact_email'),
            'contact_phone'  => $this->request->getPost('contact_phone'),
            'province'       => $this->request->getPost('province'),
            'address'        => $this->request->getPost('address'),
            'lat'            => $this->request->getPost('lat'),
            'lng'            => $this->request->getPost('lng'),
            'map_link'       => $this->request->getPost('map_link'),
            'outside_images' => json_encode(array_values($outsideResult)),
            'inside_images'  => json_encode(array_values($insideResult)),
        ]);

        return redirect()->to(base_url('admin/stadiums'))->with('success', 'อัปเดตข้อมูลสนามเรียบร้อยแล้ว');
    }

    public function view($id = null)
    {
        $stadium = $this->stadiumModel
            ->select('stadiums.*, categories.name AS category_name, vendors.vendor_name AS vendor_name, vendors.email AS vendor_email, vendors.phone_number AS vendor_phone')
            ->join('categories', 'categories.id = stadiums.category_id', 'left')
            ->join('vendors', 'vendors.id = stadiums.vendor_id', 'left')
            ->find($id);

        if (!$stadium) {
            return redirect()->to(base_url('admin/stadiums'))->with('error', 'ไม่พบข้อมูลสนาม');
        }

        // [NEW] ดึง Facility ทั้งหมด (รวมจากสนามย่อยด้วย เพื่อแสดงภาพรวม)
        $db = \Config\Database::connect();
        $rawFacilities = $db->table('stadium_facilities')
            ->select('stadium_facilities.name as item_name, facility_types.name as type_name')
            ->join('facility_types', 'facility_types.id = stadium_facilities.type_id', 'left')
            ->where('stadium_facilities.stadium_id', $id)
            ->orderBy('facility_types.id', 'ASC')
            ->get()
            ->getResultArray();

        // จัดกลุ่มข้อมูล
        $groupedFacilities = [];
        foreach ($rawFacilities as $row) {
            $type = $row['type_name'] ?? 'อื่นๆ';
            $groupedFacilities[$type][] = $row['item_name'];
        }

        // Fields (สนามย่อย)
        $fieldModel = new StadiumFieldModel();
        $stadiumFields = $fieldModel->where('stadium_id', $id)->findAll();

        $data = [
            'title'      => 'Detail: ' . $stadium['name'],
            'stadium'    => $stadium,
            'facilities' => $groupedFacilities,
            'fields'     => $stadiumFields 
        ];

        return view('admin/stadiums/view', $data);
    }

    public function delete($id = null)
    {
        $stadium = $this->stadiumModel->find($id);
        if (!$stadium) return redirect()->to(base_url('admin/stadiums'))->with('error', 'ไม่พบข้อมูลสนาม');

        try {
            $uploadPath = FCPATH . 'assets/uploads/stadiums/';
            
            $outsideImages = json_decode($stadium['outside_images'] ?? '[]', true);
            foreach ($outsideImages as $img) @unlink($uploadPath . $img);
            
            $insideImages = json_decode($stadium['inside_images'] ?? '[]', true);
            foreach ($insideImages as $img) @unlink($uploadPath . $img);

            $this->stadiumModel->delete($id);
            return redirect()->to(base_url('admin/stadiums'))->with('success', 'ลบข้อมูลเรียบร้อยแล้ว');

        } catch (DatabaseException $e) {
            return redirect()->to(base_url('admin/stadiums'))->with('error', 'ไม่สามารถลบได้ (ติดข้อมูลการจองหรืออื่นๆ)');
        }
    }

    // =================================================================================
    // 🥅 [PART 2] จัดการสนามย่อย (Fields) + Facility + Images 🥅
    // =================================================================================

    public function fields($stadium_id)
    {
        $stadiumModel = new StadiumModel();
        $fieldModel = new StadiumFieldModel();
        $facilityTypeModel = new FacilityTypeModel(); 

        $data = [
            'title'         => 'Manage Fields',
            'stadium'       => $stadiumModel->find($stadium_id),
            'fields'        => $fieldModel->where('stadium_id', $stadium_id)->findAll(),
            'facilityTypes' => $facilityTypeModel->findAll()
        ];

        return view('admin/stadiums/fields', $data);
    }

    // สร้างสนามย่อยใหม่
    public function createField()
    {
        $fieldModel = new StadiumFieldModel();
        $facModel = new StadiumFacilityModel();
        $stadium_id = $this->request->getPost('stadium_id');

        // 1. อัปโหลดรูปภาพสนามย่อย
        $images = [];
        $files = $this->request->getFileMultiple('field_images');
        $uploadPath = FCPATH . 'assets/uploads/fields/';
        if (!is_dir($uploadPath)) { mkdir($uploadPath, 0777, true); }

        if ($files) {
            foreach ($files as $file) {
                if ($file->isValid() && !$file->hasMoved()) {
                    $newName = 'field_' . time() . '_' . $file->getRandomName();
                    $file->move($uploadPath, $newName);
                    $images[] = $newName;
                }
            }
        }

        // 2. บันทึกข้อมูลสนามย่อย
        $fieldData = [
            'stadium_id'   => $stadium_id,
            'name'         => $this->request->getPost('name'),
            'description'  => $this->request->getPost('description'),
            'price'        => $this->request->getPost('price'),
            'price_daily'  => $this->request->getPost('price_daily') ?: null,
            'status'       => $this->request->getPost('status'),
            'field_images' => json_encode($images)
        ];
        
        $fieldModel->save($fieldData);
        $field_id = $fieldModel->getInsertID();

        // 3. บันทึก Facilities
        $facilities = $this->request->getPost('facilities');
        if (!empty($facilities) && is_array($facilities)) {
            $facData = [];
            foreach ($facilities as $type_id => $items) {
                if(is_array($items)) {
                    foreach ($items as $itemName) {
                        if (!empty(trim($itemName))) {
                            $facData[] = [
                                'stadium_id' => $stadium_id,
                                'field_id'   => $field_id, // ผูกกับสนามย่อยนี้
                                'type_id'    => $type_id,
                                'name'       => trim($itemName)
                            ];
                        }
                    }
                }
            }
            if (!empty($facData)) {
                $facModel->insertBatch($facData);
            }
        }

        return redirect()->to('admin/stadiums/fields/' . $stadium_id)->with('success', 'เพิ่มสนามย่อยเรียบร้อย');
    }

    // อัปเดตสนามย่อย
    public function updateField()
    {
        $fieldModel = new StadiumFieldModel();
        $facModel = new StadiumFacilityModel();
        $uploadPath = FCPATH . 'assets/uploads/fields/';
        
        $id = $this->request->getPost('id');
        $stadium_id = $this->request->getPost('stadium_id');
        
        // 1. ดึงข้อมูลเก่า
        $oldData = $fieldModel->find($id);
        $existingImages = json_decode($oldData['field_images'] ?? '[]', true);

        // 2. อัปรูปใหม่เพิ่ม (ถ้ามี)
        $files = $this->request->getFileMultiple('field_images');
        if ($files) {
            foreach ($files as $file) {
                if ($file->isValid() && !$file->hasMoved()) {
                    $newName = 'field_' . time() . '_' . $file->getRandomName();
                    $file->move($uploadPath, $newName);
                    $existingImages[] = $newName;
                }
            }
        }

        // 3. อัปเดตข้อมูล
        $fieldModel->update($id, [
            'name'         => $this->request->getPost('name'),
            'description'  => $this->request->getPost('description'),
            'price'        => $this->request->getPost('price'),
            'price_daily'  => $this->request->getPost('price_daily') ?: null,
            'status'       => $this->request->getPost('status'),
            'field_images' => json_encode($existingImages)
        ]);

        // 4. อัปเดต Facility (ล้างเก่า -> ลงใหม่)
        $facilities = $this->request->getPost('facilities');
        
        // ลบของเก่าเฉพาะ field นี้
        $facModel->where('field_id', $id)->delete(); 

        if (!empty($facilities) && is_array($facilities)) {
            $facData = [];
            foreach ($facilities as $type_id => $items) {
                if(is_array($items)) {
                    foreach ($items as $itemName) {
                        if (!empty(trim($itemName))) {
                            $facData[] = [
                                'stadium_id' => $stadium_id,
                                'field_id'   => $id,
                                'type_id'    => $type_id,
                                'name'       => trim($itemName)
                            ];
                        }
                    }
                }
            }
            if (!empty($facData)) {
                $facModel->insertBatch($facData);
            }
        }

        return redirect()->to('admin/stadiums/fields/' . $stadium_id)->with('success', 'แก้ไขข้อมูลเรียบร้อย');
    }

    public function deleteField($id)
    {
        $fieldModel = new StadiumFieldModel();
        $field = $fieldModel->find($id);
        
        if ($field) {
            // ลบรูปภาพ
            $images = json_decode($field['field_images'] ?? '[]', true);
            foreach($images as $img) {
                @unlink(FCPATH . 'assets/uploads/fields/' . $img);
            }

            // ลบ Facility
            $facModel = new StadiumFacilityModel();
            $facModel->where('field_id', $id)->delete();

            $fieldModel->delete($id);
            return redirect()->to('admin/stadiums/fields/' . $field['stadium_id'])->with('success', 'ลบสนามย่อยเรียบร้อย');
        }
        return redirect()->back()->with('error', 'ไม่พบข้อมูล');
    }
}