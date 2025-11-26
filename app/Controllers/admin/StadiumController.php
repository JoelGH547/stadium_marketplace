<?php

namespace App\Controllers\admin;

use App\Controllers\BaseController;
use App\Models\StadiumModel;
use App\Models\CategoryModel;
use App\Models\VendorModel;
use App\Models\FacilityModel;         // [ใหม่] เรียกใช้ Model สิ่งอำนวยความสะดวก
use App\Models\StadiumFacilityModel;  // [ใหม่] เรียกใช้ Model เชื่อมโยง
use App\Models\StadiumFieldModel;     // [จำเป็น] สำหรับจัดการสนามย่อย
use CodeIgniter\Database\Exceptions\DatabaseException;

class StadiumController extends BaseController
{
    protected $stadiumModel;
    protected $categoryModel;
    protected $vendorModel;
    protected $facilityModel;         // [ใหม่]
    protected $stadiumFacilityModel;  // [ใหม่]

    public function __construct()
    {
        // โหลด Model ทั้งหมดที่ต้องใช้
        $this->stadiumModel  = new StadiumModel();
        $this->categoryModel = new CategoryModel();
        $this->vendorModel   = new VendorModel();
        
        // [ใหม่] โหลด Model จัดการ Facilities
        $this->facilityModel = new FacilityModel();
        $this->stadiumFacilityModel = new StadiumFacilityModel();

        helper(['form']);
    }

    // --------------------------------------------------------------------------
    // 1. หน้าแสดงรายการสนามทั้งหมด
    // --------------------------------------------------------------------------
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

    // --------------------------------------------------------------------------
    // 2. หน้าฟอร์มสร้างสนามใหม่ (CREATE)
    // --------------------------------------------------------------------------
    public function create()
    {
        $data = [
            'title'      => 'Add New Stadium',
            'categories' => $this->categoryModel->findAll(),
            'vendors'    => $this->vendorModel->findAll(),
            // [ใหม่] ส่งรายการสิ่งอำนวยความสะดวกทั้งหมดไปให้เลือก (Checkbox)
            'facilities' => $this->facilityModel->findAll(),
        ];

        return view('admin/stadiums/create', $data);
    }

    // --------------------------------------------------------------------------
    // 3. บันทึกข้อมูลใหม่ลงฐานข้อมูล (STORE)
    // --------------------------------------------------------------------------
    public function store()
    {
        // Validation rules
        if (!$this->validate([
            'name'          => 'required|max_length[100]',
            'price'         => 'required|numeric',
            'category_id'   => 'required|integer',
            'vendor_id'     => 'required|integer',
            'contact_phone' => 'permit_empty|regex_match[/^[0-9]{10}$/]',
        ])) {
            return redirect()->back()->withInput()->with('validation', $this->validator);
        }

        // เตรียม Path รูป
        $uploadPath = FCPATH . 'assets/uploads/stadiums/';
        if (!is_dir($uploadPath)) { mkdir($uploadPath, 0777, true); }

        // --- จัดการรูปปก (Outside) ---
        $outsideImagesJson = '[]'; 
        $outsideFile = $this->request->getFile('outside_image');
        if ($outsideFile && $outsideFile->isValid() && !$outsideFile->hasMoved()) {
            $newName = 'outside_' . time() . '_' . $outsideFile->getRandomName();
            $outsideFile->move($uploadPath, $newName);
            $outsideImagesJson = json_encode([$newName]); 
        }

        // --- จัดการรูปภายใน (Inside) ---
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

        // --- บันทึกข้อมูลสนาม (Stadium) ---
        $this->stadiumModel->save([
            'name'           => $this->request->getPost('name'),
            'price'          => $this->request->getPost('price'),
            'description'    => $this->request->getPost('description'),
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

        // [ใหม่] --- บันทึก Facilities ---
        $newStadiumId = $this->stadiumModel->getInsertID(); // เอา ID ล่าสุดที่เพิ่งสร้าง
        $selectedFacilities = $this->request->getPost('facilities'); // รับค่า array จาก checkbox

        if (!empty($selectedFacilities)) {
            foreach ($selectedFacilities as $facilityId) {
                // Insert ลงตารางเชื่อม
                $this->stadiumFacilityModel->insert([
                    'stadium_id'  => $newStadiumId,
                    'facility_id' => $facilityId
                ]);
            }
        }

        return redirect()->to(base_url('admin/stadiums'))->with('success', 'เพิ่มสนามเรียบร้อยแล้ว');
    }

    // --------------------------------------------------------------------------
    // 4. หน้าแก้ไขสนาม (EDIT)
    // --------------------------------------------------------------------------
    public function edit($id = null)
    {
        $stadium = $this->stadiumModel->find($id);
        if (!$stadium) {
            return redirect()->to(base_url('admin/stadiums'))->with('error', 'ไม่พบข้อมูลสนาม');
        }

        // [จุดสำคัญ 1] ดึง ID หมวดหมู่กีฬาของสนามนี้
        $currentCategoryId = $stadium['category_id'];

        $data = [
            'title'      => 'Edit Stadium',
            'stadium'    => $stadium,
            'categories' => $this->categoryModel->findAll(),
            'vendors'    => $this->vendorModel->findAll(),
            
            // [จุดสำคัญ 2] สั่งให้ Model ดึงเฉพาะ Facility ที่เกี่ยวข้องกับกีฬานี้ + ของส่วนกลาง
            'facilities' => $this->facilityModel->getFacilitiesByCategory($currentCategoryId),
            
            // ดึงอันที่เคยเลือกไว้ (เหมือนเดิม)
            'selected_facilities' => $this->stadiumFacilityModel->getSelectedFacilities($id)
        ];

        return view('admin/stadiums/edit', $data);
    }

    // --------------------------------------------------------------------------
    // 5. อัปเดตข้อมูลสนาม (UPDATE)
    // --------------------------------------------------------------------------
    public function update($id = null)
    {
        $stadium = $this->stadiumModel->find($id);
        if (!$stadium) {
            return redirect()->to(base_url('admin/stadiums'))->with('error', 'ไม่พบข้อมูลสนาม');
        }

        // Validation
        if (!$this->validate([
            'name'          => 'required|max_length[100]',
            'price'         => 'required|numeric',
            'category_id'   => 'required|integer',
            'vendor_id'     => 'required|integer',
            'contact_phone' => 'permit_empty|regex_match[/^[0-9]{10}$/]',
        ])) {
            return redirect()->back()->withInput()->with('validation', $this->validator);
        }

        $uploadPath = FCPATH . 'assets/uploads/stadiums/';
        if (!is_dir($uploadPath)) { mkdir($uploadPath, 0777, true); }

        // --- Logic จัดการรูปภาพ (คงเดิม) ---
        // 1. Outside Image
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

        // 2. Inside Images
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
            'price'          => $this->request->getPost('price'),
            'description'    => $this->request->getPost('description'),
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

        // [ใหม่] --- อัปเดต Facilities (Sync Data) ---
        // 1. ลบข้อมูลเก่าทิ้งทั้งหมดของสนามนี้
        $this->stadiumFacilityModel->where('stadium_id', $id)->delete();
        
        // 2. เพิ่มข้อมูลใหม่เข้าไป (ถ้ามีการเลือก)
        $selectedFacilities = $this->request->getPost('facilities');
        if (!empty($selectedFacilities)) {
            foreach ($selectedFacilities as $facilityId) {
                $this->stadiumFacilityModel->insert([
                    'stadium_id'  => $id,
                    'facility_id' => $facilityId
                ]);
            }
        }

        return redirect()->to(base_url('admin/stadiums'))->with('success', 'อัปเดตข้อมูลสนามเรียบร้อยแล้ว');
    }

    // --------------------------------------------------------------------------
    // 6. ดูรายละเอียดสนาม (VIEW)
    // --------------------------------------------------------------------------
    public function view($id = null)
    {
        // 1. ดึงข้อมูลสนามหลัก
        $stadium = $this->stadiumModel
            ->select('stadiums.*, categories.name AS category_name, vendors.vendor_name AS vendor_name, vendors.email AS vendor_email, vendors.phone_number AS vendor_phone')
            ->join('categories', 'categories.id = stadiums.category_id', 'left')
            ->join('vendors', 'vendors.id = stadiums.vendor_id', 'left')
            ->find($id);

        if (!$stadium) {
            return redirect()->to(base_url('admin/stadiums'))->with('error', 'ไม่พบข้อมูลสนาม');
        }

        // 2. ดึงสิ่งอำนวยความสะดวก (Facilities)
        $db = \Config\Database::connect();
        $stadiumFacilities = $db->table('facilities')
            ->select('facilities.name, facilities.icon')
            ->join('stadium_facilities', 'stadium_facilities.facility_id = facilities.id')
            ->where('stadium_facilities.stadium_id', $id)
            ->get()
            ->getResultArray();

        // 3. [เพิ่มใหม่] ดึงข้อมูลสนามย่อย (Stadium Fields)
        // ต้องเรียกใช้ Model ของ Field ซึ่งคุณ use ไว้ข้างบนแล้ว (StadiumFieldModel)
        $fieldModel = new StadiumFieldModel();
        $stadiumFields = $fieldModel->where('stadium_id', $id)->findAll();

        $data = [
            'title'      => 'Detail: ' . $stadium['name'],
            'stadium'    => $stadium,
            'facilities' => $stadiumFacilities,
            'fields'     => $stadiumFields // ส่งตัวแปรนี้ไปที่หน้า View
        ];

        return view('admin/stadiums/view', $data);
    }

    // --------------------------------------------------------------------------
    // 7. ลบสนาม (DELETE)
    // --------------------------------------------------------------------------
    public function delete($id = null)
    {
        $stadium = $this->stadiumModel->find($id);

        if (!$stadium) {
            return redirect()->to(base_url('admin/stadiums'))->with('error', 'ไม่พบข้อมูลสนาม');
        }

        try {
            $uploadPath = FCPATH . 'assets/uploads/stadiums/';

            // ลบรูปภาพ
            $outsideImages = json_decode($stadium['outside_images'] ?? '[]', true);
            foreach ($outsideImages as $img) {
                if (file_exists($uploadPath . $img)) unlink($uploadPath . $img);
            }

            $insideImages = json_decode($stadium['inside_images'] ?? '[]', true);
            foreach ($insideImages as $img) {
                if (file_exists($uploadPath . $img)) unlink($uploadPath . $img);
            }

            // ลบข้อมูล (ตาราง facilities จะถูกลบ Auto ถ้าตั้ง Cascade ไว้ หรือถ้าไม่ตั้งก็ไม่มีผลเสีย)
            $this->stadiumModel->delete($id);
            
            return redirect()->to(base_url('admin/stadiums'))->with('success', 'ลบข้อมูลเรียบร้อยแล้ว');

        } catch (DatabaseException $e) {
            if ($e->getCode() == 1451) {
                return redirect()->to(base_url('admin/stadiums'))->with('error', 'ไม่สามารถลบได้ เนื่องจากข้อมูลถูกใช้งานอยู่');
            }
            return redirect()->to(base_url('admin/stadiums'))->with('error', 'เกิดข้อผิดพลาด: ' . $e->getMessage());
        }
    }

    // --------------------------------------------------------------------------
    // 8. จัดการสนามย่อย (Fields) - คงเดิม
    // --------------------------------------------------------------------------
    public function fields($stadium_id)
    {
        $stadiumModel = new StadiumModel();
        $fieldModel = new StadiumFieldModel();

        $data = [
            'title' => 'Manage Fields',
            'stadium' => $stadiumModel->find($stadium_id),
            'fields' => $fieldModel->where('stadium_id', $stadium_id)->findAll()
        ];

        return view('admin/stadiums/fields', $data);
    }

    public function createField()
    {
        $fieldModel = new StadiumFieldModel();
        $stadium_id = $this->request->getPost('stadium_id');
        
        $fieldModel->save([
            'stadium_id' => $stadium_id,
            'name'       => $this->request->getPost('name'),
            'description'=> $this->request->getPost('description'),
            'status'     => $this->request->getPost('status')
        ]);

        return redirect()->to('admin/stadiums/fields/' . $stadium_id)->with('success', 'เพิ่มสนามย่อยเรียบร้อย');
    }

    public function updateField()
    {
        $fieldModel = new StadiumFieldModel();
        $id = $this->request->getPost('id');
        $stadium_id = $this->request->getPost('stadium_id');
        
        $fieldModel->update($id, [
            'name'       => $this->request->getPost('name'),
            'description'=> $this->request->getPost('description'),
            'status'     => $this->request->getPost('status')
        ]);

        return redirect()->to('admin/stadiums/fields/' . $stadium_id)->with('success', 'แก้ไขข้อมูลเรียบร้อย');
    }

    public function deleteField($id)
    {
        $fieldModel = new StadiumFieldModel();
        $field = $fieldModel->find($id);
        
        if ($field) {
            $fieldModel->delete($id);
            return redirect()->to('admin/stadiums/fields/' . $field['stadium_id'])->with('success', 'ลบสนามย่อยเรียบร้อย');
        }
        return redirect()->back()->with('error', 'ไม่พบข้อมูล');
    }

    

}