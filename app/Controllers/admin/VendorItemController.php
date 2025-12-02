<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\VendorItemModel;
use App\Models\VendorModel;
use App\Models\FacilityTypeModel;

class VendorItemController extends BaseController
{
    protected $itemModel;
    protected $vendorModel;
    protected $typeModel;

    public function __construct()
    {
        $this->itemModel   = new VendorItemModel();
        $this->vendorModel = new VendorModel();
        $this->typeModel   = new FacilityTypeModel();
        helper(['form']);
    }

    // แสดงรายการสินค้าทั้งหมด
    public function index()
    {
        // ใช้ฟังก์ชัน getItemsWithDetails() ที่เราเขียนไว้ใน Model
        // หรือจะเขียน Join สดตรงนี้ก็ได้ แต่ใช้ Model สะอาดกว่า
        $items = $this->itemModel->getItemsWithDetails();

        $data = [
            'title'   => 'จัดการสินค้า/บริการเสริม',
            'items'   => $items,
            // ส่งข้อมูลไปทำ Dropdown ใน Modal
            'vendors' => $this->vendorModel->orderBy('vendor_name', 'ASC')->findAll(),
            'types'   => $this->typeModel->orderBy('id', 'ASC')->findAll()
        ];

        return view('admin/vendor_items/index', $data);
    }

    // บันทึกสินค้าใหม่
    public function store()
    {
        // 1. จัดการรูปภาพ
        $imgName = null;
        $file = $this->request->getFile('image');
        
        if ($file && $file->isValid() && !$file->hasMoved()) {
            $imgName = $file->getRandomName();
            // ตรวจสอบว่าโฟลเดอร์มีอยู่จริงไหม ถ้าไม่มีให้สร้าง
            $uploadPath = FCPATH . 'assets/uploads/items/';
            if (!is_dir($uploadPath)) { mkdir($uploadPath, 0777, true); }
            
            $file->move($uploadPath, $imgName);
        }

        // 2. บันทึกข้อมูล
        $this->itemModel->save([
            'vendor_id'        => $this->request->getPost('vendor_id'),
            'facility_type_id' => $this->request->getPost('facility_type_id'),
            'name'             => $this->request->getPost('name'),
            'description'      => $this->request->getPost('description'),
            'price'            => $this->request->getPost('price'),
            'unit'             => $this->request->getPost('unit'),
            'status'           => $this->request->getPost('status'),
            'image'            => $imgName
        ]);

        return redirect()->to('admin/vendor-items')->with('success', 'เพิ่มสินค้าเรียบร้อย');
    }

    // อัปเดตสินค้า
    public function update()
    {
        $id = $this->request->getPost('id');
        $oldItem = $this->itemModel->find($id);

        if (!$oldItem) {
            return redirect()->back()->with('error', 'ไม่พบข้อมูลสินค้า');
        }

        // 1. จัดการรูปภาพ (ถ้ามีการอัปใหม่)
        $imgName = $oldItem['image'];
        $file = $this->request->getFile('image');
        
        if ($file && $file->isValid() && !$file->hasMoved()) {
            // ลบรูปเก่าทิ้ง (ถ้ามี)
            if ($oldItem['image'] && file_exists(FCPATH . 'assets/uploads/items/' . $oldItem['image'])) {
                @unlink(FCPATH . 'assets/uploads/items/' . $oldItem['image']);
            }
            
            // อัปใหม่
            $imgName = $file->getRandomName();
            $uploadPath = FCPATH . 'assets/uploads/items/';
            if (!is_dir($uploadPath)) { mkdir($uploadPath, 0777, true); }
            
            $file->move($uploadPath, $imgName);
        }

        // 2. อัปเดตข้อมูล
        $this->itemModel->update($id, [
            'vendor_id'        => $this->request->getPost('vendor_id'),
            'facility_type_id' => $this->request->getPost('facility_type_id'),
            'name'             => $this->request->getPost('name'),
            'description'      => $this->request->getPost('description'),
            'price'            => $this->request->getPost('price'),
            'unit'             => $this->request->getPost('unit'),
            'status'           => $this->request->getPost('status'),
            'image'            => $imgName
        ]);

        return redirect()->to('admin/vendor-items')->with('success', 'แก้ไขสินค้าเรียบร้อย');
    }

    // ลบสินค้า
    public function delete($id)
    {
        $item = $this->itemModel->find($id);
        
        if ($item) {
            // ลบรูปภาพออกจาก Server
            if ($item['image'] && file_exists(FCPATH . 'assets/uploads/items/' . $item['image'])) {
                @unlink(FCPATH . 'assets/uploads/items/' . $item['image']);
            }
            
            $this->itemModel->delete($id);
            return redirect()->to('admin/vendor-items')->with('success', 'ลบสินค้าเรียบร้อย');
        }

        return redirect()->to('admin/vendor-items')->with('error', 'ไม่พบสินค้าที่ต้องการลบ');
    }
}