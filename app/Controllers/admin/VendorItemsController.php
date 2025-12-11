<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\VendorProductModel;
use App\Models\StadiumFacilityModel;
use App\Models\FacilityTypeModel;
use App\Models\StadiumFieldModel;
use App\Models\StadiumModel;

class VendorItemsController extends BaseController
{
    protected $productModel;
    protected $facilityModel;
    protected $fieldModel;
    protected $facilityTypeModel;
    protected $stadiumModel;

    public function __construct()
    {
        $this->productModel      = new VendorProductModel();
        $this->facilityModel     = new StadiumFacilityModel();
        $this->fieldModel        = new StadiumFieldModel();
        $this->facilityTypeModel = new FacilityTypeModel();
        $this->stadiumModel      = new StadiumModel();
    }

    /**
     * หน้า list สินค้า
     */
    public function index()
    {
        $db = \Config\Database::connect();
        
        // [แก้ 1] ใช้ชื่อตารางให้ถูกต้อง
        $builder = $db->table('vendor_products');

        $builder->select('
            vendor_products.*, 
            stadiums.name as stadium_name, 
            facility_types.name as facility_type_name,
            categories.name as sport_name
        ');

        // [แก้ 2] JOIN แบบลูกโซ่ เพื่อไต่ไปหาชื่อสนามและกีฬา
        // 1. จากสินค้า ไปหา facility (สิ่งอำนวยความสะดวก)
        $builder->join('stadium_facilities', 'stadium_facilities.id = vendor_products.stadium_facility_id', 'left');

        // 2. จาก facility ไปหา types (เพื่อเอาชื่อหมวดหมู่ เช่น เครื่องดื่ม)
        $builder->join('facility_types', 'facility_types.id = stadium_facilities.facility_type_id', 'left');

        // 3. จาก facility ไปหา fields (สนามย่อย)
        $builder->join('stadium_fields', 'stadium_fields.id = stadium_facilities.field_id', 'left');

        // 4. จาก fields ไปหา stadiums (เพื่อเอาชื่อสนามหลัก)
        $builder->join('stadiums', 'stadiums.id = stadium_fields.stadium_id', 'left');

        // 5. จาก stadiums ไปหา categories (เพื่อเอาชื่อกีฬา)
        $builder->join('categories', 'categories.id = stadiums.category_id', 'left');

        $builder->orderBy('vendor_products.id', 'DESC');

        $items = $builder->get()->getResultArray();

        $data = [
            'title' => 'จัดการสินค้า/และบริการเสริม',
            'items' => $items,
        ];

        return view('admin/vendor_items/index', $data);
    }

    // --- ฟังก์ชันด้านล่างนี้ (create, store, edit, update, delete) คงเดิมตามโค้ดของคุณ ---
    
    public function create()
    {
        $data = [
            'title'      => 'เพิ่มสินค้าใหม่',
            'facilities' => $this->facilityModel
                ->select('stadium_facilities.*, stadium_fields.name AS field_name, facility_types.name AS facility_name')
                ->join('stadium_fields', 'stadium_fields.id = stadium_facilities.field_id')
                ->join('facility_types', 'facility_types.id = stadium_facilities.facility_type_id')
                ->orderBy('field_name')
                ->findAll(),
        ];

        return view('admin/vendor_items/create', $data);
    }

    public function store()
    {
        $rules = [
            'name'             => 'required',
            'stadium_id'       => 'required|integer',
            'facility_type_id' => 'required|integer',
            'price'            => 'required|numeric',
            'unit'             => 'required',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'กรุณากรอกข้อมูลให้ครบถ้วน');
        }

        $stadiumId = (int) $this->request->getPost('stadium_id');
        $typeId    = (int) $this->request->getPost('facility_type_id');

        $facilityRow = $this->facilityModel
            ->select('stadium_facilities.id')
            ->join('stadium_fields', 'stadium_fields.id = stadium_facilities.field_id')
            ->where('stadium_fields.stadium_id', $stadiumId)
            ->where('stadium_facilities.facility_type_id', $typeId)
            ->first();

        if (! $facilityRow) {
            return redirect()->back()
                ->withInput()
                ->with(
                    'error',
                    'ไม่พบการเชื่อมต่อระหว่างสนามและหมวดหมู่ (stadium_facilities) ' .
                        'กรุณาตรวจสอบการตั้งค่าหมวดหมู่ของพื้นที่สนามก่อน'
                );
        }

        $stadiumFacilityId = $facilityRow['id'];

        $imageName = null;
        $imageFile = $this->request->getFile('image');
        if ($imageFile && $imageFile->isValid() && ! $imageFile->hasMoved()) {
            $uploadPath = FCPATH . 'assets/uploads/items/';
            if (! is_dir($uploadPath)) {
                mkdir($uploadPath, 0775, true);
            }

            $imageName = $imageFile->getRandomName();
            $imageFile->move($uploadPath, $imageName);
        }

        $data = [
            'stadium_facility_id' => $stadiumFacilityId,
            'name'                => $this->request->getPost('name'),
            'description'         => $this->request->getPost('description'),
            'price'               => $this->request->getPost('price'),
            'unit'                => $this->request->getPost('unit'),
            'image'               => $imageName,
            'stadium_id'          => $stadiumId,       // [เพิ่ม] บันทึก stadium_id โดยตรงเพื่อให้ง่ายต่อการ join ทีหลัง
            'facility_type_id'    => $typeId,          // [เพิ่ม] บันทึก facility_type_id โดยตรง
            'status'              => $this->request->getPost('status') ?? 'active',
        ];

        $this->productModel->insert($data);

        return redirect()->to('/admin/vendor-items')
            ->with('success', 'เพิ่มสินค้าสำเร็จ');
    }

    public function edit($id)
    {
        $product = $this->productModel->find($id);

        if (! $product) {
            return redirect()->to('/admin/vendor-items')
                ->with('error', 'ไม่พบข้อมูลสินค้า');
        }

        $data = [
            'title'    => 'แก้ไขสินค้า',
            'product'  => $product,
            'stadiums' => $this->stadiumModel->findAll(),
        ];

        return view('admin/vendor_items/edit', $data);
    }

    public function update($id)
    {
        $product = $this->productModel->find($id);

        if (! $product) {
            return redirect()->to('/admin/vendor-items')
                ->with('error', 'ไม่พบข้อมูลสินค้า');
        }

        $rules = [
            'name'             => 'required',
            'stadium_id'       => 'required|integer',
            'facility_type_id' => 'required|integer',
            'price'            => 'required|numeric',
            'unit'             => 'required',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'ข้อมูลไม่ถูกต้อง');
        }

        $stadiumId = (int) $this->request->getPost('stadium_id');
        $typeId    = (int) $this->request->getPost('facility_type_id');

        $facilityRow = $this->facilityModel
            ->select('stadium_facilities.id')
            ->join('stadium_fields', 'stadium_fields.id = stadium_facilities.field_id')
            ->where('stadium_fields.stadium_id', $stadiumId)
            ->where('stadium_facilities.facility_type_id', $typeId)
            ->first();

        if (! $facilityRow) {
            return redirect()->back()
                ->withInput()
                ->with(
                    'error',
                    'ไม่พบการเชื่อมต่อระหว่างสนามและหมวดหมู่ (stadium_facilities) ' .
                        'กรุณาตรวจสอบการตั้งค่าหมวดหมู่ของพื้นที่สนามก่อน'
                );
        }

        $stadiumFacilityId = $facilityRow['id'];

        $data = [
            'stadium_facility_id' => $stadiumFacilityId,
            'name'                => $this->request->getPost('name'),
            'description'         => $this->request->getPost('description'),
            'price'               => $this->request->getPost('price'),
            'unit'                => $this->request->getPost('unit'),
            'stadium_id'          => $stadiumId,    // [เพิ่ม] update stadium_id
            'facility_type_id'    => $typeId,       // [เพิ่ม] update facility_type_id
            'status'              => $this->request->getPost('status') ?? 'active',
        ];

        $imageFile = $this->request->getFile('image');
        if ($imageFile && $imageFile->isValid() && ! $imageFile->hasMoved()) {
            $uploadPath = FCPATH . 'assets/uploads/items/';
            if (! is_dir($uploadPath)) {
                mkdir($uploadPath, 0775, true);
            }

            $newName = $imageFile->getRandomName();
            $imageFile->move($uploadPath, $newName);
            $data['image'] = $newName;

            if (! empty($product['image'])) {
                $oldPath = $uploadPath . $product['image'];
                if (is_file($oldPath)) {
                    @unlink($oldPath);
                }
            }
        }

        $this->productModel->update($id, $data);

        return redirect()->to('/admin/vendor-items')
            ->with('success', 'แก้ไขสินค้าเรียบร้อย');
    }

    public function delete($id)
    {
        $product = $this->productModel->find($id);

        if (! $product) {
            return redirect()->to('/admin/vendor-items')
                ->with('error', 'ไม่พบข้อมูลสินค้า');
        }

        if (! empty($product['image'])) {
            $uploadPath = FCPATH . 'assets/uploads/items/';
            $filePath   = $uploadPath . $product['image'];

            if (is_file($filePath)) {
                @unlink($filePath);
            }
        }

        $this->productModel->delete($id);

        return redirect()->to('/admin/vendor-items')
            ->with('success', 'ลบสินค้าเรียบร้อย');
    }
}