<?php
namespace App\Controllers\Owner;

use App\Controllers\BaseController;
use App\Models\OwnerStadiumModel;
use App\Models\CategoryModel;

class Field extends BaseController
{
    // STEP 1 — ประเภทสนาม
    public function step1()
    {
        // แก้ตรงนี้!!!!
        if (!session()->get('owner_login')) {
            return redirect()->to(base_url('owner/login'));
        }

        $categoryModel = new CategoryModel();
        $data['categories'] = $categoryModel->findAll();

        return view('owner/fields/step1', $data);
    }

    public function step1_save()
    {
        $category_id = $this->request->getPost('category_id'); 

        if (!$category_id) {
            return redirect()->back()->with('error', 'กรุณาเลือกประเภทสนาม');
        }

        session()->set('category_id', $category_id);

        return redirect()->to(base_url('owner/fields/step2'));
    }


    // STEP 2 — ข้อมูลพื้นฐาน
    public function step2()
    {
        // แก้ตรงนี้!!!!
        if (!session()->get('owner_login')) {
            return redirect()->to(base_url('owner/login'));
        }
        return view('owner/fields/step2');
    }

    public function step2_save()
    {
        $name        = $this->request->getPost('name');
        $price       = $this->request->getPost('price');
        $open_time   = $this->request->getPost('open_time');
        $close_time  = $this->request->getPost('close_time');
        $description = $this->request->getPost('description');

        $contact_email = $this->request->getPost('contact_email');
        $contact_phone = $this->request->getPost('contact_phone');

        if (!$name || !$price || !$open_time || !$close_time || !$contact_phone) {
            return redirect()->back()->with('error', 'กรุณากรอกข้อมูลให้ครบถ้วน');
        }

        session()->set([
            'name'           => $name,
            'price'          => $price,
            'open_time'      => $open_time,
            'close_time'     => $close_time,
            'description'    => $description,
            'contact_email'  => $contact_email,
            'contact_phone'  => $contact_phone
        ]);

        return redirect()->to(base_url('owner/fields/step3'));
    }


    // STEP 3 — อัปโหลดรูป
    public function step3()
    {
        // แก้ตรงนี้!!!!
        if (!session()->get('owner_login')) {
            return redirect()->to(base_url('owner/login'));
        }

        return view('owner/fields/step3');
    }

    public function step3_save()
    {
        $outside = $this->request->getFileMultiple('outside_images');
        $inside  = $this->request->getFileMultiple('inside_images');

        if (!$outside || count($outside) < 1)
            return redirect()->back()->with('error', 'ต้องมีรูปภายนอกอย่างน้อย 1 รูป');

        if (!$inside || count($inside) < 1)
            return redirect()->back()->with('error', 'ต้องมีรูปภายในอย่างน้อย 1 รูป');

        $path_out = 'uploads/stadiums/outside/';
        $path_in  = 'uploads/stadiums/inside/';

        if (!is_dir($path_out)) mkdir($path_out, 0777, true);
        if (!is_dir($path_in)) mkdir($path_in, 0777, true);

        $outsideList = [];
        foreach ($outside as $file) {
            if ($file->isValid()) {
                $newName = $file->getRandomName();
                $file->move($path_out, $newName);
                $outsideList[] = $newName;
            }
        }

        $insideList = [];
        foreach ($inside as $file) {
            if ($file->isValid()) {
                $newName = $file->getRandomName();
                $file->move($path_in, $newName);
                $insideList[] = $newName;
            }
        }

        session()->set([
            'outside_images' => json_encode($outsideList),
            'inside_images'  => json_encode($insideList)
        ]);

        return redirect()->to(base_url('owner/fields/step4'));
    }


    // STEP 4 — ที่อยู่
    public function step4()
    {
        // แก้ตรงนี้!!!!
        if (!session()->get('owner_login')) {
            return redirect()->to(base_url('owner/login'));
        }

        return view('owner/fields/step4');
    }

    public function step4_save()
    {
        $province = $this->request->getPost('province');
        $address  = $this->request->getPost('address');
        $lat      = $this->request->getPost('lat');
        $lng      = $this->request->getPost('lng');
        $map_link = $this->request->getPost('map_link');

        if (!$province || !$address || !$lat || !$lng) {
            return redirect()->back()->with('error', 'กรุณากรอกข้อมูลที่อยู่ให้ครบ');
        }

        session()->set([
            'province' => $province,
            'address'  => $address,
            'lat'      => $lat,
            'lng'      => $lng,
            'map_link' => $map_link
        ]);

        return redirect()->to(base_url('owner/fields/confirm'));
    }


    // STEP 5 — หน้ายืนยัน
    public function confirm()
    {
        // แก้ตรงนี้!!!!
        if (!session()->get('owner_login')) {
            return redirect()->to(base_url('owner/login'));
        }

        return view('owner/fields/confirm');
    }


    // บันทึกจริงลง DB
    public function store()
    {
        $model = new OwnerStadiumModel();

        $data = [
            'vendor_id'      => session()->get('owner_id'),

            'category_id'    => session()->get('category_id'),
            'name'           => session()->get('name'),

            'price'          => session()->get('price'),
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
        ];

        $model->insert($data);

        session()->remove([
            'category_id', 'name', 'price', 'open_time', 'close_time',
            'description', 'contact_email', 'contact_phone',
            'province', 'address', 'lat', 'lng', 'map_link',
            'outside_images', 'inside_images'
        ]);

        return redirect()->to(base_url('owner/dashboard'))
                         ->with('success', 'เพิ่มสนามสำเร็จแล้ว!');
    }
}
