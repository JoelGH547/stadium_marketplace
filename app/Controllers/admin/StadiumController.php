<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\StadiumModel;
use App\Models\CategoryModel;
use App\Models\VendorModel;
use App\Models\StadiumFieldModel;
use App\Models\StadiumFacilityModel;
use App\Models\FacilityTypeModel;
use App\Models\VendorProductModel; // ✅ Model คลังสินค้า
use App\Models\FieldItemModel;     // ✅ Model สินค้าในสนามย่อย
use CodeIgniter\Database\Exceptions\DatabaseException;

class StadiumController extends BaseController
{
    protected $stadiumModel;
    protected $categoryModel;
    protected $vendorModel;

    public function __construct()
    {
        $this->stadiumModel  = new StadiumModel();
        $this->categoryModel = new CategoryModel();
        $this->vendorModel   = new VendorModel();
        
        helper(['form']);
    }

    // =================================================================================
    // 🏟️ ส่วนจัดการสนามหลัก (Stadiums)
    // =================================================================================

    public function index()
    {
        $stadiums = $this->stadiumModel
            ->select('stadiums.*, categories.name AS category_name, categories.emoji AS category_emoji, vendors.vendor_name AS vendor_name')
            ->join('categories', 'categories.id = stadiums.category_id', 'left')
            ->join('vendors', 'vendors.id = stadiums.vendor_id', 'left')
            ->orderBy('stadiums.id', 'DESC')
            ->findAll();

        $data = [
            'title'    => 'Stadiums List',
            'stadiums' => $stadiums,
        ];

        return view('admin/stadiums/index', $data);
    }

    public function create()
    {
        $data = [
            'title'      => 'Add New Stadium',
            'categories' => $this->categoryModel->findAll(),
            'vendors'    => $this->vendorModel->findAll(),
        ];

        return view('admin/stadiums/create', $data);
    }

    public function store()
    {
        if (!$this->validate([
            'name'          => 'required|max_length[100]',
            'category_id'   => 'required|integer',
            'vendor_id'     => 'required|integer',
            'contact_phone' => 'permit_empty|regex_match[/^[0-9]{10}$/]',
        ])) {
            return redirect()->back()->withInput()->with('validation', $this->validator);
        }

        $uploadPath = FCPATH . 'assets/uploads/stadiums/';
        if (!is_dir($uploadPath)) { mkdir($uploadPath, 0777, true); }

        $outsideImagesJson = '[]'; 
        $outsideFile = $this->request->getFile('outside_image');
        if ($outsideFile && $outsideFile->isValid() && !$outsideFile->hasMoved()) {
            $newName = 'outside_' . time() . '_' . $outsideFile->getRandomName();
            $outsideFile->move($uploadPath, $newName);
            $outsideImagesJson = json_encode([$newName]); 
        }

        $insideFiles = $this->request->getFileMultiple('inside_images');
        $insideImagesArray = [];
        if (!empty($insideFiles)) {
            foreach ($insideFiles as $file) {
                if ($file->isValid() && !$file->hasMoved()) {
                    $newName = 'inside_' . time() . '_' . $file->getRandomName();
                    $file->move($uploadPath, $newName);
                    $insideImagesArray[] = $newName;
                }
            }
        }

        $this->stadiumModel->save([
            'name'           => $this->request->getPost('name'),
            'description'    => $this->request->getPost('description'),
            'booking_type'   => $this->request->getPost('booking_type'),
            'category_id'    => $this->request->getPost('category_id'),
            'vendor_id'      => $this->request->getPost('vendor_id'),
            'open_time'      => $this->request->getPost('open_time'),
            'close_time'     => $this->request->getPost('close_time'),
            'contact_email'  => $this->request->getPost('contact_email'),
            'contact_phone'  => $this->request->getPost('contact_phone'),
            'province'       => $this->request->getPost('province'),
            'address'        => $this->request->getPost('address'),
            'lat'            => $this->request->getPost('lat'),
            'lng'            => $this->request->getPost('lng'),
            'map_link'       => $this->request->getPost('map_link'),
            'outside_images' => $outsideImagesJson,
            'inside_images'  => json_encode($insideImagesArray),
        ]);

        return redirect()->to(base_url('admin/stadiums'))->with('success', 'เพิ่มสนามเรียบร้อยแล้ว');
    }

    public function edit($id = null)
    {
        $stadium = $this->stadiumModel->find($id);
        if (!$stadium) {
            return redirect()->to(base_url('admin/stadiums'))->with('error', 'ไม่พบข้อมูลสนาม');
        }

        $data = [
            'title'      => 'Edit Stadium',
            'stadium'    => $stadium,
            'categories' => $this->categoryModel->findAll(),
            'vendors'    => $this->vendorModel->findAll(),
        ];

        return view('admin/stadiums/edit', $data);
    }

    public function update($id = null)
    {
        $stadium = $this->stadiumModel->find($id);
        if (!$stadium) {
            return redirect()->to(base_url('admin/stadiums'))->with('error', 'ไม่พบข้อมูลสนาม');
        }

        if (!$this->validate([
            'name'          => 'required|max_length[100]',
            'category_id'   => 'required|integer',
            'vendor_id'     => 'required|integer',
            'contact_phone' => 'permit_empty|regex_match[/^[0-9]{10}$/]',
        ])) {
            return redirect()->back()->withInput()->with('validation', $this->validator);
        }

        $uploadPath = FCPATH . 'assets/uploads/stadiums/';
        if (!is_dir($uploadPath)) { mkdir($uploadPath, 0777, true); }

        $outsideOld = json_decode($stadium['outside_images'] ?? '[]', true) ?? [];
        $outsideResult = $outsideOld;

        if ($this->request->getPost('delete_outside') == '1') {
            if (!empty($outsideOld[0]) && file_exists($uploadPath . $outsideOld[0])) unlink($uploadPath . $outsideOld[0]);
            $outsideResult = []; 
        }
        $outsideFile = $this->request->getFile('outside_image');
        if ($outsideFile && $outsideFile->isValid() && !$outsideFile->hasMoved()) {
            if (!empty($outsideResult[0]) && file_exists($uploadPath . $outsideResult[0])) unlink($uploadPath . $outsideResult[0]);
            $newName = 'outside_' . time() . '_' . $outsideFile->getRandomName();
            $outsideFile->move($uploadPath, $newName);
            $outsideResult = [$newName];
        }

        $insideOld = json_decode($stadium['inside_images'] ?? '[]', true) ?? [];
        $insideResult = [];
        $filesToDelete = $this->request->getPost('delete_inside') ?? [];

        foreach ($insideOld as $oldImg) {
            if (in_array($oldImg, $filesToDelete)) {
                if (file_exists($uploadPath . $oldImg)) unlink($uploadPath . $oldImg);
            } else {
                $insideResult[] = $oldImg;
            }
        }
        $insideFiles = $this->request->getFileMultiple('inside_images');
        if ($insideFiles) {
            foreach ($insideFiles as $file) {
                if ($file->isValid() && !$file->hasMoved()) {
                    $newName = 'inside_' . time() . '_' . $file->getRandomName();
                    $file->move($uploadPath, $newName);
                    $insideResult[] = $newName;
                }
            }
        }

        $this->stadiumModel->update($id, [
            'name'           => $this->request->getPost('name'),
            'description'    => $this->request->getPost('description'),
            'booking_type'   => $this->request->getPost('booking_type'),
            'category_id'    => $this->request->getPost('category_id'),
            'vendor_id'      => $this->request->getPost('vendor_id'),
            'open_time'      => $this->request->getPost('open_time'),
            'close_time'     => $this->request->getPost('close_time'),
            'contact_email'  => $this->request->getPost('contact_email'),
            'contact_phone'  => $this->request->getPost('contact_phone'),
            'province'       => $this->request->getPost('province'),
            'address'        => $this->request->getPost('address'),
            'lat'            => $this->request->getPost('lat'),
            'lng'            => $this->request->getPost('lng'),
            'map_link'       => $this->request->getPost('map_link'),
            'outside_images' => json_encode(array_values($outsideResult)),
            'inside_images'  => json_encode(array_values($insideResult)),
        ]);

        return redirect()->to(base_url('admin/stadiums'))->with('success', 'อัปเดตข้อมูลสนามเรียบร้อยแล้ว');
    }

    public function view($id = null)
    {
        // 1. ดึงข้อมูลสนาม
        $stadium = $this->stadiumModel
            ->select('stadiums.*, categories.name AS category_name, vendors.vendor_name AS vendor_name, vendors.email AS vendor_email, vendors.phone_number AS vendor_phone')
            ->join('categories', 'categories.id = stadiums.category_id', 'left')
            ->join('vendors', 'vendors.id = stadiums.vendor_id', 'left')
            ->find($id);

        if (!$stadium) {
            return redirect()->to(base_url('admin/stadiums'))->with('error', 'ไม่พบข้อมูลสนาม');
        }

        // 2. ดึงสิ่งอำนวยความสะดวก
        $db = \Config\Database::connect();
        $rawFacilities = $db->table('stadium_facilities')
            ->select('stadium_facilities.name as item_name, facility_types.name as type_name')
            ->join('facility_types', 'facility_types.id = stadium_facilities.type_id', 'left')
            ->where('stadium_facilities.stadium_id', $id)
            ->orderBy('facility_types.id', 'ASC')
            ->get()
            ->getResultArray();

        $groupedFacilities = [];
        foreach ($rawFacilities as $row) {
            $type = $row['type_name'] ?? 'อื่นๆ';
            $groupedFacilities[$type][] = $row['item_name'];
        }

        // 3. ดึงสนามย่อย
        $fieldModel = new StadiumFieldModel();
        $stadiumFields = $fieldModel->where('stadium_id', $id)->findAll();

        // 4. ✅ [UPDATED] ดึงสินค้าจากคลังแม่ (แก้ไขตรงนี้!)
        // ดึงจาก stadium_id แทน vendor_id เพราะเราเปลี่ยนโครงสร้างแล้ว
        $vendorProductModel = new VendorProductModel();
        $vendorItems = $vendorProductModel
            ->select('vendor_products.*, facility_types.name as type_name')
            ->join('facility_types', 'facility_types.id = vendor_products.facility_type_id', 'left')
            ->where('vendor_products.stadium_id', $id) // ✅ ใช้ ID สนาม
            ->findAll();

        foreach ($vendorItems as &$item) {
            $item['price'] = $item['price'];
        }

        $data = [
            'title'        => 'Detail: ' . $stadium['name'],
            'stadium'      => $stadium,
            'facilities'   => $groupedFacilities,
            'fields'       => $stadiumFields,
            'vendor_items' => $vendorItems
        ];

        return view('admin/stadiums/view', $data);
    }

    public function delete($id = null)
    {
        $stadium = $this->stadiumModel->find($id);
        if (!$stadium) return redirect()->to(base_url('admin/stadiums'))->with('error', 'ไม่พบข้อมูลสนาม');

        try {
            $uploadPath = FCPATH . 'assets/uploads/stadiums/';
            
            $outsideImages = json_decode($stadium['outside_images'] ?? '[]', true);
            foreach ($outsideImages as $img) @unlink($uploadPath . $img);
            
            $insideImages = json_decode($stadium['inside_images'] ?? '[]', true);
            foreach ($insideImages as $img) @unlink($uploadPath . $img);

            $this->stadiumModel->delete($id);
            return redirect()->to(base_url('admin/stadiums'))->with('success', 'ลบข้อมูลเรียบร้อยแล้ว');

        } catch (DatabaseException $e) {
            return redirect()->to(base_url('admin/stadiums'))->with('error', 'ไม่สามารถลบได้ (ติดข้อมูลการจองหรืออื่นๆ)');
        }
    }

    // =================================================================================
    // 🥅 [PART 2] จัดการสนามย่อย (Fields) + สินค้า (Items)
    // =================================================================================

    public function fields($stadium_id)
    {
        $stadiumModel = new StadiumModel();
        $fieldModel = new StadiumFieldModel();
        // $facilityTypeModel = new FacilityTypeModel(); // ❌ ไม่ต้องใช้ตัวนี้แล้ว (เพราะมันดึงทั้งหมด)
        $productModel = new VendorProductModel();

        // 1. ดึงข้อมูลสนามหลัก
        $stadium = $stadiumModel->find($stadium_id);

        // 2. ✅ [แก้ตรงนี้] ดึงเฉพาะ Type ที่สนามนี้มีอยู่จริง (ใช้ Query Builder Join เอา)
        // เพื่อให้ Dropdown ใน Modal "สร้างสินค้าใหม่" แสดงเฉพาะหมวดหมู่ของสนามนี้
        $db = \Config\Database::connect();
        $filteredTypes = $db->table('stadium_facilities')
            ->select('facility_types.*')
            ->join('facility_types', 'facility_types.id = stadium_facilities.type_id')
            ->where('stadium_facilities.stadium_id', $stadium_id)
            ->groupBy('facility_types.id') // กันซ้ำ
            ->orderBy('facility_types.id', 'ASC')
            ->get()
            ->getResultArray();

        $data = [
            'title'         => 'Manage Fields',
            'stadium'       => $stadium,
            'fields'        => $fieldModel->where('stadium_id', $stadium_id)->findAll(),
            
            // ✅ ส่งตัวแปร $filteredTypes ไปแทน (ชื่อตัวแปร facilityTypes เหมือนเดิม View จะได้ไม่ต้องแก้)
            'facilityTypes' => $filteredTypes, 
            
            'products'      => $productModel->where('stadium_id', $stadium['id'])
                                            ->where('status', 'active')
                                            ->findAll()
        ];

        return view('admin/stadiums/fields', $data);
    }

    // สร้างสนามย่อย + สิ่งอำนวยความสะดวก + สินค้า
    public function createField()
    {
        $fieldModel = new StadiumFieldModel();
        $facModel = new StadiumFacilityModel();
        $itemModel = new FieldItemModel(); // ✅ Model สำหรับบันทึกสินค้าลงสนาม

        $stadium_id = $this->request->getPost('stadium_id');
        $uploadPath = FCPATH . 'assets/uploads/fields/';

        if (!is_dir($uploadPath)) { mkdir($uploadPath, 0777, true); }

        // 1. จัดการรูปภาพ (เหมือนเดิม)
        $outsideImagesJson = '[]';
        $outsideFile = $this->request->getFile('outside_image');
        if ($outsideFile && $outsideFile->isValid() && !$outsideFile->hasMoved()) {
            $newName = 'field_out_' . time() . '_' . $outsideFile->getRandomName();
            $outsideFile->move($uploadPath, $newName);
            $outsideImagesJson = json_encode([$newName]);
        }

        $insideImagesArray = [];
        $insideFiles = $this->request->getFileMultiple('inside_images');
        if ($insideFiles) {
            foreach ($insideFiles as $file) {
                if ($file->isValid() && !$file->hasMoved()) {
                    $newName = 'field_in_' . time() . '_' . $file->getRandomName();
                    $file->move($uploadPath, $newName);
                    $insideImagesArray[] = $newName;
                }
            }
        }

        // 2. บันทึกข้อมูลสนาม (เหมือนเดิม)
        $fieldData = [
            'stadium_id'     => $stadium_id,
            'name'           => $this->request->getPost('name'),
            'description'    => $this->request->getPost('description'),
            'price'          => $this->request->getPost('price'),
            'price_daily'    => $this->request->getPost('price_daily') ?: null,
            'status'         => $this->request->getPost('status'),
            'outside_images' => $outsideImagesJson,
            'inside_images'  => json_encode($insideImagesArray) 
        ];

        $fieldModel->save($fieldData);
        $field_id = $fieldModel->getInsertID();

        // 3. บันทึก Facilities (เหมือนเดิม)
        $facilities = $this->request->getPost('facilities');
        if (!empty($facilities) && is_array($facilities)) {
            $facData = [];
            foreach ($facilities as $type_id => $items) {
                if(is_array($items)) {
                    foreach ($items as $itemName) {
                        $saveName = trim($itemName);
                        if ($saveName === '') $saveName = 'มีให้บริการ';
                        $facData[] = [
                            'stadium_id' => $stadium_id,
                            'field_id'   => $field_id,
                            'type_id'    => $type_id,
                            'name'       => $saveName
                        ];
                    }
                }
            }
            if (!empty($facData)) $facModel->insertBatch($facData);
        }

        // 4. ✅ บันทึกสินค้า (Items for Sale)
        $items = $this->request->getPost('items'); // รับค่า array items
        if (!empty($items) && is_array($items)) {
            $itemData = [];
            foreach ($items as $prodId => $data) {
                // เช็คว่า User ติ๊กเลือกสินค้านี้ไหม
                if (isset($data['selected']) && $data['selected'] == 1) {
                    $itemData[] = [
                        'stadium_id'   => $stadium_id,
                        'field_id'     => $field_id,
                        'product_id'   => $prodId,
                        'custom_price' => !empty($data['price']) ? $data['price'] : null // ถ้าไม่ใส่ราคา ใช้ราคาคลัง
                    ];
                }
            }
            if (!empty($itemData)) $itemModel->insertBatch($itemData);
        }

        return redirect()->to('admin/stadiums/fields/' . $stadium_id)->with('success', 'เพิ่มข้อมูลเรียบร้อย');
    }

    // อัปเดตสนามย่อย
    public function updateField()
    {
        $fieldModel = new StadiumFieldModel();
        $facModel = new StadiumFacilityModel();
        $itemModel = new FieldItemModel(); // ✅
        
        $uploadPath = FCPATH . 'assets/uploads/fields/';
        $id = $this->request->getPost('id');
        $stadium_id = $this->request->getPost('stadium_id');
        
        // 1. จัดการรูปภาพ (เหมือนเดิม)
        $oldData = $fieldModel->find($id);
        $outsideResult = json_decode($oldData['outside_images'] ?? '[]', true);
        $outsideFile = $this->request->getFile('outside_image');
        if ($outsideFile && $outsideFile->isValid() && !$outsideFile->hasMoved()) {
            if (!empty($outsideResult[0]) && file_exists($uploadPath . $outsideResult[0])) @unlink($uploadPath . $outsideResult[0]);
            $newName = 'field_out_' . time() . '_' . $outsideFile->getRandomName();
            $outsideFile->move($uploadPath, $newName);
            $outsideResult = [$newName];
        }
        $insideResult = json_decode($oldData['inside_images'] ?? '[]', true);
        $insideFiles = $this->request->getFileMultiple('inside_images');
        if ($insideFiles) {
            foreach ($insideFiles as $file) {
                if ($file->isValid() && !$file->hasMoved()) {
                    $newName = 'field_in_' . time() . '_' . $file->getRandomName();
                    $file->move($uploadPath, $newName);
                    $insideResult[] = $newName;
                }
            }
        }

        // 2. อัปเดตข้อมูลหลัก
        $fieldModel->update($id, [
            'name'           => $this->request->getPost('name'),
            'description'    => $this->request->getPost('description'),
            'price'          => $this->request->getPost('price'),
            'price_daily'    => $this->request->getPost('price_daily') ?: null,
            'status'         => $this->request->getPost('status'),
            'outside_images' => json_encode($outsideResult),
            'inside_images'  => json_encode($insideResult)
        ]);

        // 3. อัปเดต Facilities (ลบเก่า -> ลงใหม่)
        $facilities = $this->request->getPost('facilities');
        $facModel->where('field_id', $id)->delete(); 
        if (!empty($facilities) && is_array($facilities)) {
            $facData = [];
            foreach ($facilities as $type_id => $items) {
                if(is_array($items)) {
                    foreach ($items as $itemName) {
                        $saveName = trim($itemName);
                        if ($saveName === '') $saveName = 'มีให้บริการ';
                        $facData[] = [
                            'stadium_id' => $stadium_id,
                            'field_id'   => $id,
                            'type_id'    => $type_id,
                            'name'       => $saveName
                        ];
                    }
                }
            }
            if (!empty($facData)) $facModel->insertBatch($facData);
        }

        // 4. ✅ อัปเดตสินค้า (ลบเก่า -> ลงใหม่)
        $items = $this->request->getPost('items');
        $itemModel->where('field_id', $id)->delete(); // ลบรายการสินค้าเดิมของสนามนี้ออกก่อน
        
        if (!empty($items) && is_array($items)) {
            $itemData = [];
            foreach ($items as $prodId => $data) {
                if (isset($data['selected']) && $data['selected'] == 1) {
                    $itemData[] = [
                        'stadium_id'   => $stadium_id,
                        'field_id'     => $id,
                        'product_id'   => $prodId,
                        'custom_price' => !empty($data['price']) ? $data['price'] : null
                    ];
                }
            }
            if (!empty($itemData)) $itemModel->insertBatch($itemData);
        }

        return redirect()->to('admin/stadiums/fields/' . $stadium_id)->with('success', 'แก้ไขข้อมูลเรียบร้อย');
    }

    // ลบ (เหมือนเดิม)
    public function deleteField($id)
    {
        $fieldModel = new StadiumFieldModel();
        $field = $fieldModel->find($id);
        
        if ($field) {
            $uploadPath = FCPATH . 'assets/uploads/fields/';
            $outsideImages = json_decode($field['outside_images'] ?? '[]', true);
            foreach($outsideImages as $img) if(file_exists($uploadPath . $img)) @unlink($uploadPath . $img);
            $insideImages = json_decode($field['inside_images'] ?? '[]', true);
            foreach($insideImages as $img) if(file_exists($uploadPath . $img)) @unlink($uploadPath . $img);

            // ลบ Facility และ Item จะหายไปเองถ้าตั้ง FK Cascade (แต่ลบ Manual เผื่อไว้)
            $facModel = new StadiumFacilityModel();
            $facModel->where('field_id', $id)->delete();
            
            $itemModel = new FieldItemModel(); // ✅ ลบสินค้าด้วย
            $itemModel->where('field_id', $id)->delete();

            $fieldModel->delete($id);
            return redirect()->to('admin/stadiums/fields/' . $field['stadium_id'])->with('success', 'ลบข้อมูลเรียบร้อย');
        }
        return redirect()->back()->with('error', 'ไม่พบข้อมูล');
    }
}