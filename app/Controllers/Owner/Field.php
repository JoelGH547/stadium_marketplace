<?php
namespace App\Controllers\Owner;

use App\Controllers\BaseController;
use App\Models\OwnerStadiumModel;
use App\Models\CategoryModel;

class Field extends BaseController
{
    // ======================
    // STEP 1
    // ======================
    public function step1()
    {
        if (!session()->get('owner_login'))
            return redirect()->to(base_url('owner/login'));

        $cat = new CategoryModel();

        return view('owner/fields/step1', [
            'categories' => $cat->findAll()
        ]);
    }

    public function step1_save()
    {
        $category_id = $this->request->getPost('category_id');

        if (!$category_id)
            return redirect()->back()->with('error', 'กรุณาเลือกประเภทสนาม');

        session()->set('category_id', $category_id);

        return redirect()->to(base_url('owner/fields/step2'));
    }


    // ======================
    // STEP 2
    // ======================
    public function step2()
    {
        if (!session()->get('owner_login'))
            return redirect()->to(base_url('owner/login'));

        return view('owner/fields/step2');
    }

    public function step2_save()
    {
        $name        = $this->request->getPost('name');
        $open_time   = $this->request->getPost('open_time');
        $close_time  = $this->request->getPost('close_time');
        $description = $this->request->getPost('description');

        $contact_email = $this->request->getPost('contact_email');
        $contact_phone = $this->request->getPost('contact_phone');

        if (!$name || !$open_time || !$close_time)
            return redirect()->back()->with('error','กรุณากรอกข้อมูลให้ครบ');

        session()->set([
            'name'          => $name,
            'open_time'     => $open_time,
            'close_time'    => $close_time,
            'description'   => $description,
            'contact_email' => $contact_email,
            'contact_phone' => $contact_phone
        ]);

        return redirect()->to(base_url('owner/fields/step3'));
    }


    // ======================
    // STEP 3 (UPLOAD)
    // ======================
    public function step3()
    {
        if (!session()->get('owner_login'))
            return redirect()->to(base_url('owner/login'));

        return view('owner/fields/step3');
    }

    public function step3_save()
    {
        $outside = $this->request->getFileMultiple('outside_images');
        $inside  = $this->request->getFileMultiple('inside_images');

        if (!$outside || count($outside) < 1)
            return redirect()->back()->with('error','ต้องมีรูปภายนอกอย่างน้อย 1 รูป');

        if (!$inside || count($inside) < 1)
            return redirect()->back()->with('error','ต้องมีรูปภายในอย่างน้อย 1 รูป');

        $path = 'assets/uploads/stadiums/';

        if (!is_dir($path)) mkdir($path, 0777, true);

        foreach ($outside as $file) {
            if ($file->isValid()) {
                $name = 'outside_' . $file->getRandomName();
                $file->move($path, $name);
                $outsideList[] = $name;
            }
        }

        $insideList = [];
        foreach ($inside as $file) {
            if ($file->isValid()) {
                $name = 'inside_' . $file->getRandomName();
                $file->move($path, $name);
                $insideList[] = $name;
            }
        }

        session()->set([
            'outside_images' => json_encode($outsideList),
            'inside_images'  => json_encode($insideList)
        ]);

        return redirect()->to(base_url('owner/fields/step4'));
    }


    // ======================
    // STEP 4 MAP
    // ======================
    public function step4()
    {
        if (!session()->get('owner_login'))
            return redirect()->to(base_url('owner/login'));

        return view('owner/fields/step4');
    }

    public function step4_save()
    {
        $province = $this->request->getPost('province');
        $address  = $this->request->getPost('address');
        $lat      = $this->request->getPost('lat');
        $lng      = $this->request->getPost('lng');
        $map_link = $this->request->getPost('map_link');

        if (!$province || !$address || !$lat || !$lng)
            return redirect()->back()->with('error','กรุณากรอกให้ครบ');

        session()->set([
            'province' => $province,
            'address'  => $address,
            'lat'      => $lat,
            'lng'      => $lng,
            'map_link' => $map_link
        ]);

        return redirect()->to(base_url('owner/fields/confirm'));
    }


    // ======================
    // STEP 5 CONFIRM
    // ======================
    public function confirm()
    {
        if (!session()->get('owner_login'))
            return redirect()->to(base_url('owner/login'));

        return view('owner/fields/confirm');
    }


    // ======================
    // STORE FIELD
    // ======================
    public function store()
    {
        $model = new OwnerStadiumModel();

        $model->insert([
            'vendor_id'      => session()->get('owner_id'),
            'category_id'    => session()->get('category_id'),
            'name'           => session()->get('name'),
            'open_time'      => session()->get('open_time'),
            'close_time'     => session()->get('close_time'),
            'description'    => session()->get('description'),
            'contact_email'  => session()->get('contact_email'),
            'contact_phone'  => session()->get('contact_phone'),
            'province'       => session()->get('province'),
            'address'        => session()->get('address'),
            'lat'            => session()->get('lat'),
            'lng'            => session()->get('lng'),
            'map_link'       => session()->get('map_link'),
            'outside_images' => session()->get('outside_images'),
            'inside_images'  => session()->get('inside_images'),
        ]);

        session()->remove([
        'category_id', 'name', 'open_time', 'close_time',
        'description', 'contact_email', 'contact_phone',
        'province', 'address', 'lat', 'lng', 'map_link',
        'outside_images', 'inside_images'
    ]);

        return redirect()->to(base_url('owner/dashboard'))
                         ->with('success','เพิ่มสนามสำเร็จแล้ว!');
    }


    // ======================
    // EDIT FIELD
    // ======================
    public function edit($id)
    {
        if (!session()->get('owner_login'))
            return redirect()->to(base_url('owner/login'));

        $model = new OwnerStadiumModel();

        $stadium = $model->where('vendor_id', session()->get('owner_id'))
                         ->where('id', $id)
                         ->first();

        if (!$stadium)
            return redirect()->to(base_url('owner/dashboard'))
                             ->with('error','ไม่พบสนามนี้');

        return view('owner/fields/edit', [
            'stadium'    => $stadium,
            'categories' => (new CategoryModel())->findAll(),
        ]);
    }


    // ======================
    // UPDATE FIELD
    // ======================
    public function update($id)
    {
        if (!session()->get('owner_login'))
            return redirect()->to(base_url('owner/login'));

        $model = new OwnerStadiumModel();
        $stadium = $model->find($id);

        if (!$stadium)
            return redirect()->to(base_url('owner/dashboard'));

        // ----------------------------
        // ข้อมูลปกติ
        // ----------------------------
        $data = [
            'category_id'    => $this->request->getPost('category_id'),
            'name'           => $this->request->getPost('name'),
            'open_time'      => $this->request->getPost('open_time'),
            'close_time'     => $this->request->getPost('close_time'),
            'description'    => $this->request->getPost('description'),
            'contact_email'  => $this->request->getPost('contact_email'),
            'contact_phone'  => $this->request->getPost('contact_phone'),
            'province'       => $this->request->getPost('province'),
            'address'        => $this->request->getPost('address'),
            'lat'            => $this->request->getPost('lat'),
            'lng'            => $this->request->getPost('lng'),
            'map_link'       => $this->request->getPost('map_link'),
        ];

        // ----------------------------
        // ลบรูปที่ติ๊ก
        // ----------------------------
        $deleteOutside = $this->request->getPost('delete_outside') ?? [];
        $deleteInside  = $this->request->getPost('delete_inside') ?? [];

        $outsideOld = json_decode($stadium['outside_images'], true) ?? [];
        $insideOld  = json_decode($stadium['inside_images'], true) ?? [];

        foreach ($deleteOutside as $name) {
            $file = FCPATH . 'assets/uploads/stadiums/' . $name;
            if (file_exists($file)) unlink($file);
            $outsideOld = array_filter($outsideOld, fn($i)=>$i!=$name);
        }

        foreach ($deleteInside as $name) {
            $file = FCPATH . 'assets/uploads/stadiums/' . $name;
            if (file_exists($file)) unlink($file);
            $insideOld = array_filter($insideOld, fn($i)=>$i!=$name);
        }

        // ----------------------------
        // อัปโหลดรูปใหม่
        // ----------------------------
        $outsideNew = $this->request->getFileMultiple('outside_images');
        $insideNew  = $this->request->getFileMultiple('inside_images');

        foreach ($outsideNew as $file) {
            if ($file->isValid()) {
                $new = 'outside_' . $file->getRandomName();
                $file->move('assets/uploads/stadiums/', $new);
                $outsideOld[] = $new;
            }
        }

        foreach ($insideNew as $file) {
            if ($file->isValid()) {
                $new = 'inside_' . $file->getRandomName();
                $file->move('assets/uploads/stadiums/', $new);
                $insideOld[] = $new;
            }
        }

        $data['outside_images'] = json_encode(array_values($outsideOld));
        $data['inside_images']  = json_encode(array_values($insideOld));

        $model->update($id, $data);

        return redirect()->to(base_url('owner/dashboard'))
                         ->with('success','แก้ไขสนามสำเร็จแล้ว!');
    }

    public function delete($id)
{
    if (!session()->get('owner_login'))
        return redirect()->to(base_url('owner/login'));

    $model = new OwnerStadiumModel();

    // เช็คว่าสนามเป็นของเจ้าของคนนี้จริงไหม
    $stadium = $model
        ->where('vendor_id', session()->get('owner_id'))
        ->where('id', $id)
        ->first();

    if (!$stadium)
        return redirect()->to(base_url('owner/dashboard'))->with('error', 'ไม่พบสนามนี้');

    // ลบรูปภาพเก่า
    $outside = json_decode($stadium['outside_images'], true) ?? [];
    $inside  = json_decode($stadium['inside_images'], true) ?? [];

    foreach ($outside as $img) {
        $file = FCPATH . 'assets/uploads/stadiums/' . $img;
        if (file_exists($file)) unlink($file);
    }

    foreach ($inside as $img) {
        $file = FCPATH . 'assets/uploads/stadiums/' . $img;
        if (file_exists($file)) unlink($file);
    }

    // ลบจากฐานข้อมูล
    $model->delete($id);

    return redirect()->to(base_url('owner/dashboard'))
                     ->with('success', 'ลบสนามสำเร็จแล้ว!');
}

public function view($id)
{
    if (!session()->get('owner_login')) {
        return redirect()->to(base_url('owner/login'));
    }

    $stadiumModel = new \App\Models\OwnerStadiumModel();
    $subfieldModel = new \App\Models\SubfieldModel();
    $itemModel     = new \App\Models\VendorItemModel();
    $stadiumFacilityModel = new \App\Models\StadiumFacilityModel();
    $facilityTypeModel = new \App\Models\FacilityTypeModel();

    // 1) ข้อมูลสนาม
    $stadium = $stadiumModel
        ->select('stadiums.*, categories.name as category_name')
        ->join('categories', 'categories.id = stadiums.category_id', 'left')
        ->where('stadiums.id', $id)
        ->where('stadiums.vendor_id', session()->get('owner_id'))
        ->first();

    if (!$stadium) {
        return redirect()->back()->with('error', 'ไม่พบข้อมูลสนาม');
    }

    // 2) สนามย่อย
    $subfields = $subfieldModel->where('stadium_id', $id)
                               ->where('name !=', '_SYSTEM_CATALOG_')
                               ->findAll();

    // 3) ดึงสินค้าเสริม (All Items in Stadium)
    $vendorItemModel = new \App\Models\VendorItemModel();
    $items = $vendorItemModel
        ->select('vendor_items.*, stadium_facilities.field_id, facility_types.name as type_name, stadium_fields.name as field_name')
        ->join('stadium_facilities', 'stadium_facilities.id = vendor_items.stadium_facility_id')
        ->join('facility_types', 'facility_types.id = stadium_facilities.facility_type_id')
        ->join('stadium_fields', 'stadium_fields.id = stadium_facilities.field_id', 'left')
        ->where('stadium_fields.stadium_id', (int)$id)
        // ->where('vendor_items.status', 'active') // Show all statuses? User said "add status", implies showing active/inactive.
        ->orderBy('stadium_fields.name', 'ASC')
        ->findAll();

    // 4) Types for Dropdown
    $facilityTypes = $facilityTypeModel->findAll();

    // 5) รายการจองของสนามนี้
    $bookingModel = new \App\Models\BookingModel();
    // Use the existing proven method and filter in PHP to avoid query errors
    $allBookings = $bookingModel->getAllBookings();
    $bookings = array_filter($allBookings, function($b) use ($id) {
        return $b['stadium_id'] == $id;
    });

    return view('owner/fields/view', [
        'stadium'   => $stadium,
        'subfields' => $subfields,
        'items'     => $items,
        'facility_types' => $facilityTypes,
        'bookings'  => $bookings
    ]);
}


    


}
