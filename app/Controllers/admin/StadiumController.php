<?php

namespace App\Controllers\admin;

use App\Controllers\BaseController;
use App\Models\StadiumModel;
use App\Models\CategoryModel;
use App\Models\VendorModel;
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

    public function index()
    {
        $stadiums = $this->stadiumModel
            ->select('stadiums.*, categories.name AS category_name, categories.emoji AS category_emoji, vendors.vendor_name AS vendor_name')
            ->join('categories', 'categories.id = stadiums.category_id', 'left')
            ->join('vendors', 'vendors.id = stadiums.vendor_id', 'left')
            ->orderBy('stadiums.id', 'DESC')
            ->findAll();

        $data = [
            'title'    => 'Stadiums',
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

    // =========================
    //  STORE (เพิ่มข้อมูลใหม่)
    // =========================
    public function store()
    {
        if (!$this->validate([
            'name'          => 'required|max_length[100]',
            'price'         => 'required|numeric',
            'category_id'   => 'required|integer',
            'vendor_id'     => 'required|integer',
            'contact_phone' => 'permit_empty|regex_match[/^[0-9]{10}$/]',
        ])) {
            return redirect()->back()
                ->withInput()
                ->with('validation', $this->validator);
        }

        $uploadPath = FCPATH . 'assets/uploads/stadiums/';
        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0777, true);
        }

        // 1. จัดการรูปปก (Outside Image)
        $outsideImagesJson = '[]'; 
        $outsideFile       = $this->request->getFile('outside_image');

        if ($outsideFile && $outsideFile->isValid() && !$outsideFile->hasMoved()) {
            $newName = 'outside_' . time() . '_' . $outsideFile->getRandomName();
            $outsideFile->move($uploadPath, $newName);
            $outsideImagesJson = json_encode([$newName]); 
        }

        // 2. จัดการรูปภายใน (Inside Images)
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

        // บันทึกข้อมูล
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

        return redirect()->to(base_url('admin/stadiums'))
                         ->with('success', 'เพิ่มสนามเรียบร้อยแล้ว');
    }

    public function edit($id = null)
    {
        $stadium = $this->stadiumModel->find($id);
        if (!$stadium) {
            return redirect()->to(base_url('admin/stadiums'))
                             ->with('error', 'ไม่พบข้อมูลสนามที่ต้องการแก้ไข');
        }

        $data = [
            'title'      => 'Edit Stadium',
            'stadium'    => $stadium,
            'categories' => $this->categoryModel->findAll(),
            'vendors'    => $this->vendorModel->findAll(),
        ];

        return view('admin/stadiums/edit', $data);
    }

    // =========================
    //  UPDATE (แก้ไขข้อมูล - อัปเดตใหม่ ลบรูปได้)
    // =========================
    public function update($id = null)
    {
        $stadium = $this->stadiumModel->find($id);
        if (!$stadium) {
            return redirect()->to(base_url('admin/stadiums'))
                             ->with('error', 'ไม่พบข้อมูลสนามที่ต้องการอัปเดต');
        }

        if (!$this->validate([
            'name'          => 'required|max_length[100]',
            'price'         => 'required|numeric',
            'category_id'   => 'required|integer',
            'vendor_id'     => 'required|integer',
            'contact_phone' => 'permit_empty|regex_match[/^[0-9]{10}$/]',
        ])) {
            return redirect()->back()
                ->withInput()
                ->with('validation', $this->validator);
        }

        $uploadPath = FCPATH . 'assets/uploads/stadiums/';
        if (!is_dir($uploadPath)) { mkdir($uploadPath, 0777, true); }

        // --- 1. จัดการรูปปก (Outside Image) ---
        $outsideOld = json_decode($stadium['outside_images'] ?? '[]', true) ?? [];
        $outsideResult = $outsideOld;

        // 1.1 กรณีติ๊ก "ลบรูปปก"
        if ($this->request->getPost('delete_outside') == '1') {
            if (!empty($outsideOld[0]) && file_exists($uploadPath . $outsideOld[0])) {
                unlink($uploadPath . $outsideOld[0]); // ลบไฟล์
            }
            $outsideResult = []; // เคลียร์ค่า
        }

        // 1.2 กรณีอัปโหลดรูปปกใหม่ (แทนที่)
        $outsideFile = $this->request->getFile('outside_image');
        if ($outsideFile && $outsideFile->isValid() && !$outsideFile->hasMoved()) {
            // ลบรูปเก่าทิ้งก่อน (ถ้ามี และยังไม่ได้ถูกลบใน step 1.1)
            if (!empty($outsideResult[0]) && file_exists($uploadPath . $outsideResult[0])) {
                 unlink($uploadPath . $outsideResult[0]);
            }
            
            $newName = 'outside_' . time() . '_' . $outsideFile->getRandomName();
            $outsideFile->move($uploadPath, $newName);
            $outsideResult = [$newName];
        }

        // --- 2. จัดการรูปภายใน (Inside Images) ---
        $insideOld = json_decode($stadium['inside_images'] ?? '[]', true) ?? [];
        $insideResult = [];

        // 2.1 รับรายชื่อรูปที่ต้องการลบ
        $filesToDelete = $this->request->getPost('delete_inside') ?? [];

        // 2.2 วนลูปรูปเก่า: เก็บเฉพาะรูปที่ไม่ได้ถูกสั่งลบ
        foreach ($insideOld as $oldImg) {
            if (in_array($oldImg, $filesToDelete)) {
                // ถ้าสั่งลบ -> ลบไฟล์ทิ้ง
                if (file_exists($uploadPath . $oldImg)) {
                    unlink($uploadPath . $oldImg);
                }
            } else {
                // ถ้าไม่ลบ -> เก็บไว้
                $insideResult[] = $oldImg;
            }
        }

        // 2.3 เพิ่มรูปใหม่ (ถ้ามี)
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

        // อัปเดตข้อมูล
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

        return redirect()->to(base_url('admin/stadiums'))
                         ->with('success', 'อัปเดตข้อมูลสนามเรียบร้อยแล้ว');
    }

    public function view($id = null)
    {
        // ดึงข้อมูลสนาม + Join ตาราง Category และ Vendor เพื่อเอาชื่อมาโชว์
        $stadium = $this->stadiumModel
            ->select('stadiums.*, categories.name AS category_name, vendors.vendor_name AS vendor_name, vendors.email AS vendor_email, vendors.phone_number AS vendor_phone')
            ->join('categories', 'categories.id = stadiums.category_id', 'left')
            ->join('vendors', 'vendors.id = stadiums.vendor_id', 'left')
            ->find($id);

        if (!$stadium) {
            return redirect()->to(base_url('admin/stadiums'))->with('error', 'ไม่พบข้อมูลสนาม');
        }

        $data = [
            'title'   => 'รายละเอียดสนาม: ' . $stadium['name'],
            'stadium' => $stadium
        ];

        return view('admin/stadiums/view', $data);
    }

    // =========================
    //  DELETE (ลบสนาม + ลบรูปภาพทิ้ง)
    // =========================
    public function delete($id = null)
    {
        // 1. ค้นหาข้อมูลสนามก่อน เพื่อจะเอารายชื่อรูปภาพ
        $stadium = $this->stadiumModel->find($id);

        if (!$stadium) {
            return redirect()->to(base_url('admin/stadiums'))->with('error', 'ไม่พบข้อมูลสนามที่ต้องการลบ');
        }

        try {
            // กำหนด Path ที่เก็บรูป (ใช้ FCPATH ตามที่คุณใช้ในฟังก์ชัน store/update)
            $uploadPath = FCPATH . 'assets/uploads/stadiums/';

            // --- ส่วนที่ 1: ลบรูปปก (Outside Images) ---
            $outsideImages = json_decode($stadium['outside_images'] ?? '[]', true);
            if (!empty($outsideImages)) {
                foreach ($outsideImages as $img) {
                    // เช็คว่ามีไฟล์อยู่จริงไหม แล้วสั่งลบ
                    if (file_exists($uploadPath . $img)) {
                        unlink($uploadPath . $img);
                    }
                }
            }

            // --- ส่วนที่ 2: ลบรูปภายใน (Inside Images) ---
            $insideImages = json_decode($stadium['inside_images'] ?? '[]', true);
            if (!empty($insideImages)) {
                foreach ($insideImages as $img) {
                    if (file_exists($uploadPath . $img)) {
                        unlink($uploadPath . $img);
                    }
                }
            }

            // --- ส่วนที่ 3: ลบข้อมูลใน Database ---
            $this->stadiumModel->delete($id);
            
            return redirect()->to(base_url('admin/stadiums'))
                             ->with('success', 'ลบข้อมูลสนามและไฟล์รูปภาพเรียบร้อยแล้ว');

        } catch (DatabaseException $e) {
            // ดักจับ Error กรณีลบไม่ได้ (เช่น มีการจองค้างอยู่ แล้วติด Foreign Key)
            if ($e->getCode() == 1451) {
                return redirect()->to(base_url('admin/stadiums'))
                    ->with('error', 'ไม่สามารถลบสนามนี้ได้ เนื่องจากมีประวัติการจองหรือข้อมูลอื่นอ้างอิงอยู่ (Foreign Key Constraint)');
            }
            return redirect()->to(base_url('admin/stadiums'))
                ->with('error', 'เกิดข้อผิดพลาดฐานข้อมูล: ' . $e->getMessage());
        }
    }

    // ... (โค้ดเดิม) ...

    // แสดงรายการสนามย่อย ของ Stadium ID นั้นๆ
    public function fields($stadium_id)
    {
        $stadiumModel = new \App\Models\StadiumModel();
        $fieldModel = new \App\Models\StadiumFieldModel(); // *ต้องสร้าง Model นี้นะครับ

        $data = [
            'title' => 'จัดการสนามย่อย',
            'stadium' => $stadiumModel->find($stadium_id),
            // ดึงสนามย่อยทั้งหมดของ Stadium นี้ออกมา
            'fields' => $fieldModel->where('stadium_id', $stadium_id)->findAll()
        ];

        return view('admin/stadiums/fields', $data);
    }

    // บันทึกสนามย่อยใหม่
    public function createField()
    {
        $fieldModel = new \App\Models\StadiumFieldModel();
        
        $stadium_id = $this->request->getPost('stadium_id');
        
        $fieldModel->save([
            'stadium_id' => $stadium_id,
            'name'       => $this->request->getPost('name'), // เช่น "สนาม 1 (หญ้าเทียม)"
            'status' => $this->request->getPost('status')
        ]);

        return redirect()->to('admin/stadiums/fields/' . $stadium_id)->with('success', 'เพิ่มสนามย่อยเรียบร้อย');
    }

    // ฟังก์ชันอัปเดตข้อมูลสนามย่อย
    public function updateField()
    {
        $fieldModel = new \App\Models\StadiumFieldModel();
        
        $id = $this->request->getPost('id'); // ID ของสนามย่อยที่จะแก้
        $stadium_id = $this->request->getPost('stadium_id'); // ID สนามหลัก (เอาไว้ Redirect กลับ)
        
        $fieldModel->update($id, [
            'name'       => $this->request->getPost('name'),
            'description'=> $this->request->getPost('description'),
            'status' => $this->request->getPost('status')
        ]);

        return redirect()->to('admin/stadiums/fields/' . $stadium_id)->with('success', 'แก้ไขข้อมูลเรียบร้อย');
    }
    
    // ... (เพิ่มฟังก์ชันลบ หรือ แก้ไขตามต้องการ) ...
}