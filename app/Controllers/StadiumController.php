<?php

namespace App\Controllers;

use App\Models\StadiumModel; // ⬅️ เปลี่ยน
use App\Models\CategoryModel;
use CodeIgniter\Database\Exceptions\DatabaseException;

class StadiumController extends BaseController // ⬅️ เปลี่ยน
{
    protected $stadiumModel; // ⬅️ เปลี่ยน
    protected $categoryModel;

    public function __construct()
    {
        $this->stadiumModel = new StadiumModel(); // ⬅️ เปลี่ยน
        $this->categoryModel = new CategoryModel();
    }

    // --- 1. INDEX (READ: แสดงรายการสนามกีฬา) ---
    public function index()
    {
        $data = [
            'stadiums' => $this->stadiumModel->getStadiumsWithCategory(), // ⬅️ เปลี่ยน
            'title' => 'Stadium List', // ⬅️ เปลี่ยน
        ];

        return view('stadiums/index', $data); // ⬅️ เปลี่ยน
    }

    // --- 2. CREATE (แสดงฟอร์มสร้างสนามใหม่) ---
    public function create()
    {
        $data = [
            'title' => 'Create New Stadium', // ⬅️ เปลี่ยน
            'categories' => $this->categoryModel->findAll(), 
        ];
        return view('stadiums/create', $data); // ⬅️ เปลี่ยน
    }

    // --- 3. STORE (บันทึกสนามใหม่) ---
    public function store()
    {
        // ** Validation Rules Updated (ตัด stock ออก) **
        if (!$this->validate([
            'name' => 'required|max_length[100]',
            'price' => 'required|numeric',
            // 'stock' => 'required|integer|greater_than_equal_to[0]', // ⬅️ ลบ stock
            'category_id' => 'required|integer',
        ])) {
            return redirect()->back()->withInput()->with('validation', $this->validator);
        }

        // บันทึกข้อมูล
        $this->stadiumModel->save([ // ⬅️ เปลี่ยน
            'name' => $this->request->getPost('name'),
            'price' => $this->request->getPost('price'),
            'description' => $this->request->getPost('description'),
            // 'stock' => $this->request->getPost('stock'), // ⬅️ ลบ stock
            'category_id' => $this->request->getPost('category_id'), 
        ]);

        return redirect()->to(base_url('admin/stadiums'))->with('success', 'Stadium added successfully.'); // ⬅️ เปลี่ยน
    }

    // --- 4. EDIT (แสดงฟอร์มแก้ไขสนาม) ---
    public function edit($id = null)
    {
        $stadium = $this->stadiumModel->getStadiumsWithCategory($id); // ⬅️ เปลี่ยน

        if (!$stadium) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Cannot find the stadium item: ' . $id); // ⬅️ เปลี่ยน
        }

        $data = [
            'stadium' => $stadium, // ⬅️ เปลี่ยน
            'title' => 'Edit Stadium: ' . $stadium['name'], // ⬅️ เปลี่ยน
            'categories' => $this->categoryModel->findAll(), 
        ];

        return view('stadiums/edit', $data); // ⬅️ เปลี่ยน
    }

    // --- 5. UPDATE (อัพเดทสนาม) ---
    public function update($id = null)
    {
        // ** Validation Rules Updated (ตัด stock ออก) **
        if (!$this->validate([
            'name' => 'required|max_length[100]',
            'price' => 'required|numeric',
            // 'stock' => 'required|integer|greater_than_equal_to[0]', // ⬅️ ลบ stock
            'category_id' => 'required|integer',
        ])) {
            return redirect()->back()->withInput()->with('validation', $this->validator);
        }

        // อัพเดทข้อมูล
        $this->stadiumModel->update($id, [ // ⬅️ เปลี่ยน
            'name' => $this->request->getPost('name'),
            'price' => $this->request->getPost('price'),
            'description' => $this->request->getPost('description'),
            // 'stock' => $this->request->getPost('stock'), // ⬅️ ลบ stock
            'category_id' => $this->request->getPost('category_id'),
        ]);

        return redirect()->to(base_url('admin/stadiums'))->with('success', 'Stadium updated successfully.'); // ⬅️ เปลี่ยน
    }

    
    // --- 6. DELETE (ลบสนาม) ---
    public function delete($id = null)
    {
        try {
            // 1. พยายามลบ
            $this->stadiumModel->delete($id); // ⬅️ เปลี่ยน

            // 2. ถ้าลบสำเร็จ
            return redirect()->to(base_url('admin/stadiums'))->with('success', 'Stadium deleted successfully.'); // ⬅️ เปลี่ยน

        } catch (DatabaseException $e) {
            // 3. ถ้าล้มเหลว (เพราะ Error 1451 - Foreign Key)
            if ($e->getCode() == 1451) {
                return redirect()->to(base_url('admin/stadiums')) // ⬅️ เปลี่ยน
                                    ->with('error', 'ไม่สามารถลบสนามนี้ได้! (ID: '.esc($id).') เนื่องจากมีข้อมูลอื่น (เช่น การจอง) อ้างอิงอยู่'); // ⬅️ เปลี่ยน
            }

            // ถ้าเป็น Error อื่น
            return redirect()->to(base_url('admin/stadiums'))->with('error', 'Database Error: ' . $e->getMessage()); // ⬅️ เปลี่ยน
        }
    }
}