<?php

namespace App\Controllers\admin;

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
        $items = $this->productModel
            ->withRelations()
            ->orderBy('vendor_products.id', 'DESC')
            ->findAll();

        $data = [
            'title'    => 'จัดการสินค้า/และบริการเสริม',
            'items'    => $items,
        ];

        return view('admin/vendor_items/index', $data);
    }

    /**
     * (ถ้ามีหน้า create แยก ก็ปล่อย logic แบบเดิมไว้ได้)
     */
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

    /**
     * บันทึกสินค้าใหม่
     */
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

        // หา stadium_facilities.id จาก stadium_id + facility_type_id
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

        // จัดการอัปโหลดรูป
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
            'status'              => $this->request->getPost('status') ?? 'active',
        ];

        $this->productModel->insert($data);

        return redirect()->to('/admin/vendor-items')
            ->with('success', 'เพิ่มสินค้าสำเร็จ');
    }

    /**
     * หน้าแก้ไขสินค้า
     */
    public function edit($id)
    {
        $product = $this->productModel->find($id);

        if (! $product) {
            return redirect()->to('/admin/vendor-items')
                ->with('error', 'ไม่พบข้อมูลสินค้า');
        }

        $data = [
            'title'      => 'แก้ไขสินค้า',
            'product'    => $product,
            'stadiums'   => $this->stadiumModel->findAll(),
        ];

        return view('admin/vendor_items/edit', $data);
    }

    /**
     * บันทึกการแก้ไขสินค้า
     */
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

        // หา stadium_facilities.id เหมือนตอนสร้าง
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

        // ถ้ามีอัปโหลดรูปใหม่ → ย้ายไฟล์ + ลบไฟล์เก่า
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

    /**
     * ลบสินค้า
     */
    public function delete($id)
    {
        $product = $this->productModel->find($id);

        if (! $product) {
            return redirect()->to('/admin/vendor-items')
                ->with('error', 'ไม่พบข้อมูลสินค้า');
        }

        // ลบไฟล์รูปถ้ามี
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
