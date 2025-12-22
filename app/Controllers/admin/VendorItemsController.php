<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\VendorItemModel; // เรียกใช้ Model ใหม่
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
        $this->productModel      = new VendorItemModel(); // ใช้ Model ใหม่ (vendor_items)
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
        
        // [แก้ไข 1] เปลี่ยนชื่อตารางเป็น vendor_items
        $builder = $db->table('vendor_items');

        $builder->select('
            vendor_items.*, 
            stadiums.name as stadium_name, 
            facility_types.name as facility_type_name,
            categories.name as sport_name
        ');

        // [แก้ไข 2] JOIN ตารางให้สัมพันธ์กับ vendor_items
        // 1. จาก vendor_items ไปหา stadium_facilities
        $builder->join('stadium_facilities', 'stadium_facilities.id = vendor_items.stadium_facility_id', 'left');

        // 2. จาก stadium_facilities ไปหา facility_types (หมวดหมู่)
        $builder->join('facility_types', 'facility_types.id = stadium_facilities.facility_type_id', 'left');

        // 3. จาก stadium_facilities ไปหา stadium_fields (สนามย่อย)
        $builder->join('stadium_fields', 'stadium_fields.id = stadium_facilities.field_id', 'left');

        // 4. จาก stadium_fields ไปหา stadiums (สนามหลัก)
        $builder->join('stadiums', 'stadiums.id = stadium_fields.stadium_id', 'left');

        // 5. จาก stadiums ไปหา categories (ประเภทกีฬา)
        $builder->join('categories', 'categories.id = stadiums.category_id', 'left');

        $builder->orderBy('vendor_items.id', 'DESC');

        $items = $builder->get()->getResultArray();

        $data = [
            'title' => 'จัดการสินค้า/และบริการเสริม',
            'items' => $items,
        ];

        return view('admin/vendor_items/index', $data);
    }

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
            // 'stadium_id' และ 'facility_type_id' ไม่ต้องใส่ก็ได้ เพราะอยู่ในตาราง stadium_facilities แล้ว
            // แต่ถ้าตาราง vendor_items มีฟิลด์นี้จริงๆ ก็ใส่ได้ครับ (แต่ปกติ Normalize แล้วไม่ควรมี)
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