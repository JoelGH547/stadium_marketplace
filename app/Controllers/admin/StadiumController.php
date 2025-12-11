<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\StadiumModel;
use App\Models\CategoryModel;
use App\Models\VendorModel;
use App\Models\StadiumFieldModel;
use App\Models\StadiumFacilityModel;
use App\Models\FacilityTypeModel;
use App\Models\VendorProductModel;
use CodeIgniter\Database\Exceptions\DatabaseException;

class StadiumController extends BaseController
{
    protected $stadiumModel;
    protected $categoryModel;
    protected $vendorModel;
    protected $stadiumFacilityModel;
    protected $facilityTypeModel;

    public function __construct()
    {
        $this->stadiumModel = new StadiumModel();
        $this->categoryModel = new CategoryModel();
        $this->vendorModel   = new VendorModel();


        $this->stadiumFacilityModel = new StadiumFacilityModel();
        $this->facilityTypeModel = new FacilityTypeModel();

        helper(['form']);
    }

    // =================================================================================
    // 🏟️ ส่วนจัดการสนามหลัก (Stadiums)
    // =================================================================================

    public function index()
    {
        // 1. รับค่าจากช่องค้นหา และ ตัวกรอง
        $search = $this->request->getGet('search');
        $filter = $this->request->getGet('booking_type'); // เผื่อตัวกรอง dropdown เดิม

        // 2. เริ่มสร้าง Query
        $builder = $this->stadiumModel
            ->select('stadiums.*, categories.name AS category_name, categories.emoji AS category_emoji, vendors.vendor_name AS vendor_name')
            ->join('categories', 'categories.id = stadiums.category_id', 'left')
            ->join('vendors', 'vendors.id = stadiums.vendor_id', 'left');

        // 3. ถ้ามีการค้นหา (Search)
        if (!empty($search)) {
            $builder->groupStart() // ใช้วงเล็บครอบเงื่อนไข OR
                ->like('stadiums.name', $search)
                ->orLike('vendors.vendor_name', $search) // แถม: ค้นหาชื่อเจ้าของได้ด้วย
                ->groupEnd();
        }

        // 4. ถ้ามีการกรองประเภทการจอง (Dropdown เดิม)
        if (!empty($filter) && $filter != 'all') {
            $builder->where('stadiums.booking_type', $filter);
        }

        // 5. ดึงข้อมูล
        $stadiums = $builder->orderBy('stadiums.id', 'DESC')->findAll();

        $data = [
            'title'    => 'Stadiums List',
            'stadiums' => $stadiums,
            'search'   => $search, // ส่งค่ากลับไปแปะในช่อง input
            'filter'   => $filter  // ส่งค่ากลับไป select dropdown
        ];

        return view('admin/stadiums/index', $data);
    }

    public function create()
    {
        $data = [
            'title'      => 'Add New Stadium',
            'categories' => $this->categoryModel->findAll(),
            'vendors'    => $this->vendorModel->findAll(),
            'facilityTypes' => $this->facilityTypeModel->findAll(),
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
        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0777, true);
        }

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


        // ดึง facility type ที่ใช้อยู่ในสนามนี้ (จากสนามย่อยทั้งหมด)
        $db = \Config\Database::connect();

        $currentFacilities = $db->table('stadium_facilities')
            ->select('stadium_facilities.facility_type_id')
            ->join('stadium_fields', 'stadium_fields.id = stadium_facilities.field_id', 'left')
            ->where('stadium_fields.stadium_id', $id)
            ->groupBy('stadium_facilities.facility_type_id')
            ->get()
            ->getResultArray();

        $selectedTypeIds = array_column($currentFacilities, 'facility_type_id');

        $data = [
            'title'           => 'Edit Stadium',
            'stadium'         => $stadium,
            'categories'      => $this->categoryModel->findAll(),
            'vendors'         => $this->vendorModel->findAll(),
            'facilityTypes'   => $this->facilityTypeModel->findAll(),
            'selectedTypeIds' => $selectedTypeIds
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
        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0777, true);
        }

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

        $stadium = $this->stadiumModel
            ->select('stadiums.*, categories.name AS category_name, vendors.vendor_name AS vendor_name, vendors.email AS vendor_email, vendors.phone_number AS vendor_phone')
            ->join('categories', 'categories.id = stadiums.category_id', 'left')
            ->join('vendors', 'vendors.id = stadiums.vendor_id', 'left')
            ->find($id);

        if (!$stadium) {
            return redirect()->to(base_url('admin/stadiums'))->with('error', 'ไม่พบข้อมูลสนาม');
        }


        $db = \Config\Database::connect();

        $rawFacilities = $db->table('stadium_facilities')
            ->select('facility_types.name AS type_name')
            ->join('stadium_fields', 'stadium_fields.id = stadium_facilities.field_id', 'left')
            ->join('facility_types', 'facility_types.id = stadium_facilities.facility_type_id', 'left')
            ->where('stadium_fields.stadium_id', $id)
            ->groupBy('facility_types.id')
            ->orderBy('facility_types.id', 'ASC')
            ->get()
            ->getResultArray();

        $groupedFacilities = [];
        foreach ($rawFacilities as $row) {
            $typeName = $row['type_name'] ?? null;
            if (!$typeName) {
                continue;
            }
            if (!isset($groupedFacilities[$typeName])) {
                $groupedFacilities[$typeName] = ['available'];
            }
        }


        $fieldModel = new StadiumFieldModel();
        $stadiumFields = $fieldModel->where('stadium_id', $id)->findAll();


        $vendorProductModel = new VendorProductModel();
        $vendorItems = $vendorProductModel
            ->withRelations()
            ->where('stadiums.id', $id)
            ->findAll();

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
        $fieldModel   = new StadiumFieldModel();

        // หา stadium ถ้าไม่เจอให้เด้งกลับ
        $stadium = $stadiumModel->find($stadium_id);
        if (!$stadium) {
            return redirect()->to('admin/stadiums')
                ->with('error', 'ไม่พบข้อมูลสนาม');
        }

        // สนามย่อยทั้งหมดของสนามนี้
        $fields = $fieldModel->where('stadium_id', $stadium_id)->findAll();

        // ประเภทสิ่งอำนวยความสะดวกทั้งหมด
        $facilityTypes = $this->facilityTypeModel->orderBy('id', 'ASC')->findAll();

        $fieldFacilities = [];
        $fieldProducts   = [];

        if (!empty($fields)) {
            $fieldIds = array_column($fields, 'id');

            // ดึง stadium_facilities ของ field เหล่านี้
            $sfModel = $this->stadiumFacilityModel;
            $sfRows  = $sfModel->whereIn('field_id', $fieldIds)->findAll();

            $facilityIdMap = [];
            foreach ($sfRows as $row) {
                $fieldFacilities[$row['field_id']][] = $row;
                $facilityIdMap[$row['id']] = $row;
            }

            // ดึงสินค้าใน vendor_products ที่ผูกกับ stadium_facilities เหล่านี้
            if (!empty($facilityIdMap)) {
                $productModel = new VendorProductModel();
                $products     = $productModel->withRelations()
                    ->whereIn('stadium_facility_id', array_keys($facilityIdMap))
                    ->findAll();

                foreach ($products as $prod) {
                    $sfId = $prod['stadium_facility_id'];
                    if (!isset($facilityIdMap[$sfId])) {
                        continue;
                    }
                    $fieldId = $facilityIdMap[$sfId]['field_id'];
                    $typeId  = $facilityIdMap[$sfId]['facility_type_id'];

                    if (!isset($fieldProducts[$fieldId][$typeId])) {
                        $fieldProducts[$fieldId][$typeId] = [];
                    }
                    $fieldProducts[$fieldId][$typeId][] = $prod;
                }
            }
        }

        $data = [
            'title'           => 'Manage Fields',
            'stadium'         => $stadium,
            'fields'          => $fields,
            'facilityTypes'   => $facilityTypes,
            'fieldFacilities' => $fieldFacilities,
            'fieldProducts'   => $fieldProducts,
        ];

        return view('admin/stadiums/fields', $data);
    }

    public function toggleFieldFacility()
    {
        if (! $this->request->isAJAX()) {
            return $this->response->setStatusCode(400)->setJSON([
                'success' => false,
                'message' => 'Invalid request type',
            ]);
        }

        $fieldId = (int) $this->request->getPost('field_id');
        $typeId  = (int) $this->request->getPost('facility_type_id');
        $checked = $this->request->getPost('checked') === '1';

        if ($fieldId <= 0 || $typeId <= 0) {
            return $this->response->setStatusCode(400)->setJSON([
                'success' => false,
                'message' => 'Missing field_id or facility_type_id',
            ]);
        }

        $sfModel      = $this->stadiumFacilityModel;
        $productModel = new \App\Models\VendorProductModel();

        // เช็คว่ามี row นี้อยู่แล้วหรือยัง
        $existing = $sfModel
            ->where('field_id', $fieldId)
            ->where('facility_type_id', $typeId)
            ->first();

        if ($checked) {
            // ติ๊กเปิดหมวดหมู่
            if ($existing) {
                return $this->response->setJSON([
                    'success'             => true,
                    'stadium_facility_id' => $existing['id'] ?? null,
                ]);
            }

            $id = $sfModel->insert([
                'field_id'         => $fieldId,
                'facility_type_id' => $typeId,
            ], true);

            return $this->response->setJSON([
                'success'             => true,
                'stadium_facility_id' => $id,
            ]);
        }

        // unchecked -> ลบ row + ลบสินค้าใน vendor_products ที่ผูกกับ stadium_facility นี้
        $deletedProducts = 0;

        if ($existing) {
            $sfId = $existing['id'];

            // ดึงสินค้าเพื่อจะลบรูปออกจากดิสก์ด้วย
            $products = $productModel
                ->where('stadium_facility_id', $sfId)
                ->findAll();

            $uploadPath = FCPATH . 'assets/uploads/items/';

            foreach ($products as $prod) {
                if (! empty($prod['image'])) {
                    $filePath = $uploadPath . $prod['image'];
                    if (is_file($filePath)) {
                        @unlink($filePath);
                    }
                }
            }

            $deletedProducts = count($products);

            // ลบสินค้าใน DB
            $productModel->where('stadium_facility_id', $sfId)->delete();

            // แล้วค่อยลบ row ใน stadium_facilities
            $sfModel->delete($sfId);
        }

        return $this->response->setJSON([
            'success'          => true,
            'deleted_products' => $deletedProducts,
        ]);
    }


    public function createField()
    {
        $fieldModel = new StadiumFieldModel();
        $facModel = new StadiumFacilityModel();

        $stadium_id = $this->request->getPost('stadium_id');
        $uploadPath = FCPATH . 'assets/uploads/fields/';

        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0777, true);
        }

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


        $facilities = $this->request->getPost('facilities');
        if (!empty($facilities) && is_array($facilities)) {
            $facData = [];
            foreach ($facilities as $type_id => $items) {
                // ตอนนี้เราไม่เก็บชื่อแล้ว เหลือแค่ field_id + facility_type_id
                if (!empty($items) && is_array($items)) {
                    $facData[] = [
                        'field_id'         => $field_id,
                        'facility_type_id' => $type_id,
                    ];
                }
            }
            if (!empty($facData)) {
                $facModel->insertBatch($facData);
            }
        }

        return redirect()->to('admin/stadiums/fields/' . $stadium_id)->with('success', 'เพิ่มข้อมูลเรียบร้อย');
    }

    public function updateField()
    {
        $fieldModel = new StadiumFieldModel();
        $facModel = new StadiumFacilityModel();

        $uploadPath = FCPATH . 'assets/uploads/fields/';
        $id = $this->request->getPost('id');
        $stadium_id = $this->request->getPost('stadium_id');

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

        $fieldModel->update($id, [
            'name'           => $this->request->getPost('name'),
            'description'    => $this->request->getPost('description'),
            'price'          => $this->request->getPost('price'),
            'price_daily'    => $this->request->getPost('price_daily') ?: null,
            'status'         => $this->request->getPost('status'),
            'outside_images' => json_encode($outsideResult),
            'inside_images'  => json_encode($insideResult)
        ]);


        $facilities = $this->request->getPost('facilities');

        // ลบของเก่าออกก่อนตาม field_id
        $facModel->where('field_id', $id)->delete();

        if (!empty($facilities) && is_array($facilities)) {
            $facData = [];
            foreach ($facilities as $type_id => $items) {
                // ไม่เก็บ name แล้ว เหลือแค่ field_id + facility_type_id
                if (!empty($items) && is_array($items)) {
                    $facData[] = [
                        'field_id'         => $id,
                        'facility_type_id' => $type_id,
                    ];
                }
            }
            if (!empty($facData)) {
                $facModel->insertBatch($facData);
            }
        }

        return redirect()->to('admin/stadiums/fields/' . $stadium_id)->with('success', 'แก้ไขข้อมูลเรียบร้อย');
    }

    public function deleteField($id)
    {
        $fieldModel = new StadiumFieldModel();
        $field = $fieldModel->find($id);

        if ($field) {
            $uploadPath = FCPATH . 'assets/uploads/fields/';
            $outsideImages = json_decode($field['outside_images'] ?? '[]', true);
            foreach ($outsideImages as $img) if (file_exists($uploadPath . $img)) @unlink($uploadPath . $img);
            $insideImages = json_decode($field['inside_images'] ?? '[]', true);
            foreach ($insideImages as $img) if (file_exists($uploadPath . $img)) @unlink($uploadPath . $img);

            $facModel = new StadiumFacilityModel();
            $facModel->where('field_id', $id)->delete();

            $fieldModel->delete($id);
            return redirect()->to('admin/stadiums/fields/' . $field['stadium_id'])->with('success', 'ลบข้อมูลเรียบร้อย');
        }
        return redirect()->back()->with('error', 'ไม่พบข้อมูล');
    }

    public function saveProduct()
    {
        if (!$this->request->isAJAX()) {
             return $this->response->setStatusCode(400)->setJSON(['success' => false, 'message' => 'Invalid Request']);
        }

        $productModel = new VendorProductModel();
        
        $id = $this->request->getPost('id');
        $stadiumFacilityId = $this->request->getPost('stadium_facility_id');
        
        // Basic Validation
        if (empty($stadiumFacilityId)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Missing Stadium Facility ID']);
        }

        $data = [
            'stadium_facility_id' => $stadiumFacilityId,
            'name'        => $this->request->getPost('name'),
            'description' => $this->request->getPost('description'),
            'price'       => $this->request->getPost('price'),
            'unit'        => $this->request->getPost('unit'),
            'status'      => $this->request->getPost('status') ?? 'active',
        ];

        // Handle Image Upload
        $file = $this->request->getFile('image');
        if ($file && $file->isValid() && !$file->hasMoved()) {
            $uploadPath = FCPATH . 'assets/uploads/items/';
            if (!is_dir($uploadPath)) mkdir($uploadPath, 0777, true);

            $newName = 'item_' . time() . '_' . $file->getRandomName();
            $file->move($uploadPath, $newName);
            
            $data['image'] = $newName;

            // Delete old image if updating
            if ($id) {
                $oldItem = $productModel->find($id);
                if ($oldItem && !empty($oldItem['image'])) {
                     if (file_exists($uploadPath . $oldItem['image'])) @unlink($uploadPath . $oldItem['image']);
                }
            }
        }

        if ($id) {
            $productModel->update($id, $data);
            $newId = $id;
        } else {
            $newId = $productModel->insert($data);
        }

        return $this->response->setJSON([
            'success' => true, 
            'id' => $newId,
            'image_url' => isset($data['image']) ? base_url('assets/uploads/items/' . $data['image']) : null
        ]);
    }

    public function deleteProduct($id)
    {
        $productModel = new VendorProductModel();
        $product = $productModel->find($id);

        if ($product) {
            if (!empty($product['image'])) {
                $path = FCPATH . 'assets/uploads/items/' . $product['image'];
                if (file_exists($path)) @unlink($path);
            }
            $productModel->delete($id);
            return $this->response->setJSON(['success' => true]);
        }
        
        return $this->response->setJSON(['success' => false, 'message' => 'Not found']);
    }
}
