<?php

namespace App\Controllers\admin;

use App\Controllers\BaseController;
use App\Models\FacilityModel;
use App\Models\CategoryModel;

class FacilityController extends BaseController
{
    protected $facilityModel;
    protected $categoryModel;

    public function __construct()
    {
        $this->facilityModel = new FacilityModel();
        $this->categoryModel = new CategoryModel();
        helper(['form', 'url']);
    }

    // แสดงรายการสิ่งอำนวยความสะดวกทั้งหมด
    public function index()
    {
        // 1. ดึงสิ่งอำนวยความสะดวกทั้งหมด
        $facilities = $this->facilityModel->orderBy('id', 'DESC')->findAll();

        // 2. ดึงหมวดหมู่ทั้งหมด (บรรทัดนี้แหละที่ขาดไป!)
        $categories = $this->categoryModel->findAll();

        $data = [
            'title'      => 'จัดการสิ่งอำนวยความสะดวก',
            'facilities' => $facilities,
            'categories' => $categories // ส่งตัวแปรนี้ไปให้หน้า View
        ];

        return view('admin/facilities/index', $data);
    }

    // หน้าฟอร์มเพิ่มข้อมูล
    public function create()
    {
        $data = [
            'title'      => 'เพิ่มสิ่งอำนวยความสะดวก',
            'categories' => $this->categoryModel->findAll() // ส่งหมวดหมู่ไปให้เลือก
        ];

        return view('admin/facilities/create', $data);
    }

    // บันทึกข้อมูล
    public function store()
    {
        if (!$this->validate([
            'name' => 'required|max_length[255]',
        ])) {
            return redirect()->back()->withInput()->with('error', 'กรุณาระบุชื่อสิ่งอำนวยความสะดวก');
        }

        // เช็คว่าถ้าเลือก category_id เป็นค่าว่าง (ส่วนกลาง) ให้บันทึกเป็น NULL
        $categoryId = $this->request->getPost('category_id');
        if (empty($categoryId)) {
            $categoryId = null;
        }

        $this->facilityModel->save([
            'name'        => $this->request->getPost('name'),
            'icon'        => $this->request->getPost('icon'), // ถ้ามี class icon (เช่น fa-wifi)
            'category_id' => $categoryId
        ]);

        return redirect()->to('admin/facilities')->with('success', 'เพิ่มข้อมูลเรียบร้อยแล้ว');
    }

    // หน้าแก้ไข
    public function edit($id)
    {
        $facility = $this->facilityModel->find($id);
        if (!$facility) {
            return redirect()->to('admin/facilities')->with('error', 'ไม่พบข้อมูล');
        }

        $data = [
            'title'      => 'แก้ไขสิ่งอำนวยความสะดวก',
            'facility'   => $facility,
            'categories' => $this->categoryModel->findAll()
        ];

        return view('admin/facilities/edit', $data);
    }

    // อัปเดตข้อมูล
    public function update($id)
    {
        $categoryId = $this->request->getPost('category_id');
        if (empty($categoryId)) {
            $categoryId = null;
        }

        $this->facilityModel->update($id, [
            'name'        => $this->request->getPost('name'),
            'icon'        => $this->request->getPost('icon'),
            'category_id' => $categoryId
        ]);

        return redirect()->to('admin/facilities')->with('success', 'อัปเดตข้อมูลเรียบร้อยแล้ว');
    }

    // ลบข้อมูล
    public function delete($id)
    {
        $this->facilityModel->delete($id);
        return redirect()->to('admin/facilities')->with('success', 'ลบข้อมูลเรียบร้อยแล้ว');
    }

    // ... (ต่อจาก code เดิม) ...

    // ฟังก์ชันรับค่า AJAX เพื่อเปลี่ยนหมวดหมู่ทันที
    public function ajaxUpdateCategory()
    {
        $request = service('request');
        $json = $request->getJSON();
        
        $facilityId = $json->facility_id;
        $categoryId = $json->category_id;

        // เรียกใช้ Model ตารางเชื่อม (FacilityCategoryModel)
        $pivotModel = new \App\Models\FacilityCategoryModel();

        // เช็คว่ามีอยู่แล้วหรือยัง?
        $exists = $pivotModel->where('facility_id', $facilityId)
                             ->where('category_id', $categoryId)
                             ->first();

        try {
            if ($exists) {
                // ถ้ามี -> ลบออก (Untick)
                $pivotModel->delete($exists['id']);
                $action = 'removed';
            } else {
                // ถ้าไม่มี -> เพิ่ม (Tick)
                $pivotModel->insert([
                    'facility_id' => $facilityId,
                    'category_id' => $categoryId
                ]);
                $action = 'added';
            }
            return $this->response->setJSON(['status' => 'success', 'action' => $action]);
        } catch (\Exception $e) {
            return $this->response->setJSON(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }
}



