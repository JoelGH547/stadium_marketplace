<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\VendorProductModel;
use App\Models\VendorModel;
use App\Models\StadiumModel; 
use App\Models\FacilityTypeModel;

class VendorItemController extends BaseController
{
    protected $productModel;
    protected $vendorModel;
    protected $stadiumModel;
    protected $typeModel;

    public function __construct()
    {
        $this->productModel = new VendorProductModel(); 
        $this->vendorModel  = new VendorModel();
        $this->stadiumModel = new StadiumModel(); 
        $this->typeModel    = new FacilityTypeModel();
        helper(['form']);
    }

    
    public function index()
    {
        
        $items = $this->productModel->select('vendor_products.*, stadiums.name as stadium_name, vendors.vendor_name, facility_types.name as type_name')
            ->join('stadiums', 'stadiums.id = vendor_products.stadium_id', 'left') 
            ->join('vendors', 'vendors.id = stadiums.vendor_id', 'left')
            ->join('facility_types', 'facility_types.id = vendor_products.facility_type_id', 'left')
            ->orderBy('vendor_products.id', 'DESC')
            ->findAll();

        $data = [
            'title'    => 'จัดการคลังสินค้ากลาง (Master Catalog)',
            'items'    => $items,
            'stadiums' => $this->stadiumModel->orderBy('name', 'ASC')->findAll(), 
            'vendors'  => $this->vendorModel->orderBy('vendor_name', 'ASC')->findAll(), 
            'types'    => $this->typeModel->orderBy('id', 'ASC')->findAll()
        ];

        return view('admin/vendor_items/index', $data);
    }

    
    public function store()
    {
        $imgName = null;
        $file = $this->request->getFile('image');
        
        if ($file && $file->isValid() && !$file->hasMoved()) {
            $imgName = $file->getRandomName();
            $uploadPath = FCPATH . 'assets/uploads/items/';
            if (!is_dir($uploadPath)) { mkdir($uploadPath, 0777, true); }
            $file->move($uploadPath, $imgName);
        }

        
        $this->productModel->save([
            'stadium_id'       => $this->request->getPost('stadium_id'), 
            'facility_type_id' => $this->request->getPost('facility_type_id'),
            'name'             => $this->request->getPost('name'),
            'description'      => $this->request->getPost('description'),
            'price'            => $this->request->getPost('price'),
            'unit'             => $this->request->getPost('unit'),
            'status'           => $this->request->getPost('status'),
            'image'            => $imgName
        ]);

        return redirect()->to('admin/vendor-items')->with('success', 'เพิ่มสินค้าลงคลังเรียบร้อย');
    }

    
    public function update()
    {
        $id = $this->request->getPost('id');
        $oldItem = $this->productModel->find($id);

        if (!$oldItem) {
            return redirect()->back()->with('error', 'ไม่พบข้อมูลสินค้า');
        }

        $imgName = $oldItem['image'];
        $file = $this->request->getFile('image');
        
        if ($file && $file->isValid() && !$file->hasMoved()) {
            if ($oldItem['image'] && file_exists(FCPATH . 'assets/uploads/items/' . $oldItem['image'])) {
                @unlink(FCPATH . 'assets/uploads/items/' . $oldItem['image']);
            }
            $imgName = $file->getRandomName();
            $file->move(FCPATH . 'assets/uploads/items/', $imgName);
        }

        
        $this->productModel->update($id, [
            'stadium_id'       => $this->request->getPost('stadium_id'),
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

    
    public function delete($id)
    {
        $item = $this->productModel->find($id);
        
        if ($item) {
            if ($item['image'] && file_exists(FCPATH . 'assets/uploads/items/' . $item['image'])) {
                @unlink(FCPATH . 'assets/uploads/items/' . $item['image']);
            }
            $this->productModel->delete($id);
            return redirect()->to('admin/vendor-items')->with('success', 'ลบสินค้าเรียบร้อย');
        }

        return redirect()->to('admin/vendor-items')->with('error', 'ไม่พบสินค้าที่ต้องการลบ');
    }

    
    public function quickCreate()
    {
        
        $stadium_id = $this->request->getPost('stadium_id'); 
        $type_id    = $this->request->getPost('type_id');
        $name       = $this->request->getPost('name');
        $price      = $this->request->getPost('price');
        $unit       = $this->request->getPost('unit');
        $desc       = $this->request->getPost('description');

        if (!$name || !$price) {
            return $this->response->setJSON(['success' => false, 'message' => 'กรุณากรอกข้อมูลให้ครบ']);
        }

        $imgName = '';
        $file = $this->request->getFile('image');
        if ($file && $file->isValid() && !$file->hasMoved()) {
            $imgName = $file->getRandomName();
            $uploadPath = FCPATH . 'assets/uploads/items/';
            if (!is_dir($uploadPath)) { mkdir($uploadPath, 0777, true); }
            $file->move($uploadPath, $imgName);
        }

        $data = [
            'stadium_id'       => $stadium_id, 
            'facility_type_id' => $type_id,
            'name'             => $name,
            'price'            => $price,
            'unit'             => $unit ? $unit : 'รายการ',
            'description'      => $desc,
            'image'            => $imgName,
            'status'           => 'active'
        ];
        
        try {
            $newId = $this->productModel->insert($data);
            
            if ($newId) {
                return $this->response->setJSON([
                    'success' => true,
                    'id'      => $newId,
                    'name'    => $name,
                    'price'   => number_format((float)$price, 2),
                    'image'   => $imgName
                ]);
            } else {
                return $this->response->setJSON(['success' => false, 'message' => 'บันทึกไม่สำเร็จ']);
            }
        } catch (\Exception $e) {
            return $this->response->setJSON(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function getStadiumFacilityTypes($stadium_id)
    {
        $db = \Config\Database::connect();
        
        
        $types = $db->table('stadium_facilities')
            ->select('facility_types.id, facility_types.name')
            ->join('facility_types', 'facility_types.id = stadium_facilities.type_id')
            ->where('stadium_facilities.stadium_id', $stadium_id)
            ->groupBy('facility_types.id')
            ->orderBy('facility_types.id', 'ASC')
            ->get()
            ->getResultArray();

        return $this->response->setJSON($types);
    }

}