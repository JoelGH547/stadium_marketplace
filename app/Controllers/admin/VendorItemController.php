<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\VendorProductModel; // ✅ เรียกใช้ Model คลังสินค้าตัวใหม่
use App\Models\VendorModel;
use App\Models\FacilityTypeModel;

class VendorItemController extends BaseController
{
    protected $productModel;
    protected $vendorModel;
    protected $typeModel;

    public function __construct()
    {
        // ✅ เปลี่ยนมาใช้ VendorProductModel
        $this->productModel = new VendorProductModel(); 
        $this->vendorModel  = new VendorModel();
        $this->typeModel    = new FacilityTypeModel();
        helper(['form']);
    }

    // แสดงรายการสินค้าทั้งหมด (คลังสินค้า)
    public function index()
    {
        // ดึงข้อมูลสินค้า + ชื่อร้าน + ชื่อหมวดหมู่
        $items = $this->productModel->select('vendor_products.*, vendors.vendor_name, facility_types.name as type_name')
                                    ->join('vendors', 'vendors.id = vendor_products.vendor_id')
                                    ->join('facility_types', 'facility_types.id = vendor_products.facility_type_id', 'left')
                                    ->orderBy('vendors.vendor_name', 'ASC')
                                    ->findAll();

        $data = [
            'title'   => 'จัดการคลังสินค้ากลาง (Master Catalog)',
            'items'   => $items,
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
            $uploadPath = FCPATH . 'assets/uploads/items/';
            if (!is_dir($uploadPath)) { mkdir($uploadPath, 0777, true); }
            $file->move($uploadPath, $imgName);
        }

        // 2. บันทึกข้อมูล
        $this->productModel->save([
            'vendor_id'        => $this->request->getPost('vendor_id'),
            'facility_type_id' => $this->request->getPost('facility_type_id'),
            'name'             => $this->request->getPost('name'),
            'description'      => $this->request->getPost('description'),
            'base_price'       => $this->request->getPost('price'), // ✅ แมพ input 'price' เข้า 'base_price'
            'unit'             => $this->request->getPost('unit'),
            'status'           => $this->request->getPost('status'),
            'image'            => $imgName
        ]);

        return redirect()->to('admin/vendor-items')->with('success', 'เพิ่มสินค้าลงคลังเรียบร้อย');
    }

    // อัปเดตสินค้า
    public function update()
    {
        $id = $this->request->getPost('id');
        $oldItem = $this->productModel->find($id);

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
            $file->move(FCPATH . 'assets/uploads/items/', $imgName);
        }

        // 2. อัปเดตข้อมูล
        $this->productModel->update($id, [
            'vendor_id'        => $this->request->getPost('vendor_id'),
            'facility_type_id' => $this->request->getPost('facility_type_id'),
            'name'             => $this->request->getPost('name'),
            'description'      => $this->request->getPost('description'),
            'base_price'       => $this->request->getPost('price'), // ✅ แมพ 'price' -> 'base_price'
            'unit'             => $this->request->getPost('unit'),
            'status'           => $this->request->getPost('status'),
            'image'            => $imgName
        ]);

        return redirect()->to('admin/vendor-items')->with('success', 'แก้ไขสินค้าเรียบร้อย');
    }

    // ลบสินค้า
    public function delete($id)
    {
        $item = $this->productModel->find($id);
        
        if ($item) {
            // ลบรูปภาพ
            if ($item['image'] && file_exists(FCPATH . 'assets/uploads/items/' . $item['image'])) {
                @unlink(FCPATH . 'assets/uploads/items/' . $item['image']);
            }
            
            $this->productModel->delete($id);
            // หมายเหตุ: field_items ที่ผูกอยู่จะหายไปด้วยเพราะเราตั้ง CASCADE ใน Database แล้ว
            
            return redirect()->to('admin/vendor-items')->with('success', 'ลบสินค้าเรียบร้อย');
        }

        return redirect()->to('admin/vendor-items')->with('error', 'ไม่พบสินค้าที่ต้องการลบ');
    }
}