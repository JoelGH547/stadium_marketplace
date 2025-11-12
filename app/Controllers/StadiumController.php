<?php

namespace App\Controllers;

use App\Models\StadiumModel;
use App\Models\CategoryModel;
use App\Models\VendorModel; // ⬅️ 1. (เพิ่ม) นำเข้า VendorModel
use CodeIgniter\Database\Exceptions\DatabaseException;

class StadiumController extends BaseController
{
    protected $stadiumModel;
    protected $categoryModel;
    protected $vendorModel; // ⬅️ 2. (เพิ่ม) ประกาศ VendorModel

    public function __construct()
    {
        $this->stadiumModel = new StadiumModel();
        $this->categoryModel = new CategoryModel();
        $this->vendorModel = new VendorModel(); // ⬅️ 3. (เพิ่ม) สร้าง Instance
    }

    // --- 1. INDEX (READ: แสดงรายการสนามกีฬา) ---
    public function index()
    {
        $data = [
            'stadiums' => $this->stadiumModel->getStadiumsWithCategory(),
            'title' => 'Stadium List (All Vendors)',
        ];
        return view('stadiums/index', $data);
    }

    // --- 2. CREATE (แสดงฟอร์มสร้างสนามใหม่) ---
    public function create()
    {
        $data = [
            'title' => 'Create New Stadium (Admin)',
            'categories' => $this->categoryModel->findAll(), 
            'vendors' => $this->vendorModel->findAll(), // ⬅️ 4. (เพิ่ม) ส่ง "รายชื่อ" Vendor ไปให้ View
        ];
        return view('stadiums/create', $data);
    }

    // --- 3. STORE (บันทึกสนามใหม่) ---
    public function store()
    {
        // ** (เพิ่ม vendor_id) **
        if (!$this->validate([
            'name' => 'required|max_length[100]',
            'price' => 'required|numeric',
            'category_id' => 'required|integer',
            'vendor_id' => 'required|integer', // ⬅️ 5. (เพิ่ม) "บังคับ" ให้ Admin "เลือก" Vendor
        ])) {
            return redirect()->back()->withInput()->with('validation', $this->validator);
        }

        $this->stadiumModel->save([
            'name' => $this->request->getPost('name'),
            'price' => $this->request->getPost('price'),
            'description' => $this->request->getPost('description'),
            'category_id' => $this->request->getPost('category_id'), 
            'vendor_id' => $this->request->getPost('vendor_id'), // ⬅️ 6. (เพิ่ม) "บันทึก" vendor_id
        ]);

        return redirect()->to(base_url('admin/stadiums'))->with('success', 'Stadium added successfully.');
    }

    // --- 4. EDIT (แสดงฟอร์มแก้ไขสนาม) ---
    public function edit($id = null)
    {
        $stadium = $this->stadiumModel->getStadiumsWithCategory($id);

        if (!$stadium) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Cannot find the stadium item: ' . $id);
        }

        $data = [
            'stadium' => $stadium,
            'title' => 'Edit Stadium: ' . $stadium['name'],
            'categories' => $this->categoryModel->findAll(), 
            'vendors' => $this->vendorModel->findAll(), // ⬅️ 7. (เพิ่ม) ส่ง "รายชื่อ" Vendor ไปให้ View (สำหรับ Edit)
        ];

        return view('stadiums/edit', $data);
    }

    // --- 5. UPDATE (อัพเดทสนาม) ---
    public function update($id = null)
    {
        // ** (เพิ่ม vendor_id) **
        if (!$this->validate([
            'name' => 'required|max_length[100]',
            'price' => 'required|numeric',
            'category_id' => 'required|integer',
            'vendor_id' => 'required|integer', // ⬅️ 8. (เพิ่ม) "บังคับ" ให้ Admin "เลือก" Vendor
        ])) {
            return redirect()->back()->withInput()->with('validation', $this->validator);
        }

        $this->stadiumModel->update($id, [
            'name' => $this->request->getPost('name'),
            'price' => $this->request->getPost('price'),
            'description' => $this->request->getPost('description'),
            'category_id' => $this->request->getPost('category_id'),
            'vendor_id' => $this->request->getPost('vendor_id'), // ⬅️ 9. (เพิ่ม) "อัปเดต" vendor_id
        ]);

        return redirect()->to(base_url('admin/stadiums'))->with('success', 'Stadium updated successfully.');
    }

    
    // --- 6. DELETE (ลบสนาม) ---
    public function delete($id = null)
    {
        try {
            $this->stadiumModel->delete($id);
            return redirect()->to(base_url('admin/stadiums'))->with('success', 'Stadium deleted successfully.');
        } catch (DatabaseException $e) {
            if ($e->getCode() == 1451) {
                return redirect()->to(base_url('admin/stadiums'))
                                     ->with('error', 'ไม่สามารถลบสนามนี้ได้! (ID: '.esc($id).') เนื่องจากมีข้อมูลอื่น (เช่น การจอง) อ้างอิงอยู่');
            }
            return redirect()->to(base_url('admin/stadiums'))->with('error', 'Database Error: ' . $e->getMessage());
        }
    }
}