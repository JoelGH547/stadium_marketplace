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
     * แสดงรายการสินค้า
     */
    public function index()
    {
        // ดึงข้อมูลด้วย schema ใหม่ แต่ป้อนให้ view เดิมผ่านตัวแปร $items
        $items = $this->productModel
            ->withRelations()
            ->orderBy('vendor_products.id', 'DESC')
            ->findAll();

        $data = [
            'title'    => 'จัดการสินค้า/และบริการเสริม',
            'items'    => $items,
            'stadiums' => $this->stadiumModel->findAll(),
        ];

        return view('admin/vendor_items/index', $data);
    }

    /**
     * หน้าเพิ่มสินค้าใหม่
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
            'name'                => 'required',
            'stadium_facility_id' => 'required|integer',
            'price'               => 'required|numeric',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', 'กรุณากรอกข้อมูลให้ครบถ้วน');
        }

        $data = [
            'stadium_facility_id' => $this->request->getPost('stadium_facility_id'),
            'name'                => $this->request->getPost('name'),
            'description'         => $this->request->getPost('description'),
            'price'               => $this->request->getPost('price'),
            'unit'                => $this->request->getPost('unit'),
            'status'              => $this->request->getPost('status') ?? 'active',
        ];

        // TODO: ระบบอัปโหลดรูปภาพค่อยเพิ่มในรอบหน้า
        $this->productModel->insert($data);

        return redirect()->to('/admin/vendor-items')->with('success', 'เพิ่มสินค้าสำเร็จ');
    }

    /**
     * หน้าแก้ไขสินค้า
     */
    public function edit($id)
    {
        $product = $this->productModel->find($id);

        if (!$product) {
            return redirect()->to('/admin/vendor-items')->with('error', 'ไม่พบข้อมูลสินค้า');
        }

        $data = [
            'title'      => 'แก้ไขสินค้า',
            'product'    => $product,
            'facilities' => $this->facilityModel
                ->select('stadium_facilities.*, stadium_fields.name AS field_name, facility_types.name AS facility_name')
                ->join('stadium_fields', 'stadium_fields.id = stadium_facilities.field_id')
                ->join('facility_types', 'facility_types.id = stadium_facilities.facility_type_id')
                ->orderBy('field_name')
                ->findAll(),
        ];

        return view('admin/vendor_items/edit', $data);
    }

    /**
     * บันทึกการแก้ไขสินค้า
     */
    public function update($id)
    {
        $product = $this->productModel->find($id);

        if (!$product) {
            return redirect()->to('/admin/vendor-items')->with('error', 'ไม่พบข้อมูลสินค้า');
        }

        $rules = [
            'name'                => 'required',
            'stadium_facility_id' => 'required|integer',
            'price'               => 'required|numeric',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', 'ข้อมูลไม่ถูกต้อง');
        }

        $data = [
            'stadium_facility_id' => $this->request->getPost('stadium_facility_id'),
            'name'                => $this->request->getPost('name'),
            'description'         => $this->request->getPost('description'),
            'price'               => $this->request->getPost('price'),
            'unit'                => $this->request->getPost('unit'),
            'status'              => $this->request->getPost('status') ?? 'active',
        ];

        $this->productModel->update($id, $data);

        return redirect()->to('/admin/vendor-items')->with('success', 'แก้ไขสินค้าเรียบร้อย');
    }

    /**
     * ลบสินค้า
     */
    public function delete($id)
    {
        $product = $this->productModel->find($id);

        if (!$product) {
            return redirect()->to('/admin/vendor-items')->with('error', 'ไม่พบข้อมูลสินค้า');
        }

        $this->productModel->delete($id);

        return redirect()->to('/admin/vendor-items')->with('success', 'ลบสินค้าเรียบร้อย');
    }
}
