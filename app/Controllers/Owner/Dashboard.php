<?php
namespace App\Controllers\Owner;

use App\Controllers\BaseController;
use App\Models\OwnerStadiumModel;
use App\Models\CategoryModel;

class Dashboard extends BaseController
{
    public function index()
    {
        // ป้องกันไม่ให้เข้าโดยไม่ login
        if (!session()->get('owner_login')) {
            return redirect()->to(base_url('owner/login'));
        }

        helper('thai_province'); // ดึงรายชื่อจังหวัดจาก helper

        $stadiumModel = new OwnerStadiumModel();
        $categoryModel = new CategoryModel();

        // รับค่า search จาก GET
        $categoryFilter = $this->request->getGet('category');
        $provinceFilter = $this->request->getGet('province');
        $priceMin      = $this->request->getGet('price_min');
        $priceMax      = $this->request->getGet('price_max');

        // query เริ่มต้น
        $builder = $stadiumModel->where('vendor_id', session()->get('owner_id'));

        // ======= เงื่อนไขกรอง =======
        if (!empty($categoryFilter)) {
            $builder->where('category_id', $categoryFilter);
        }

        if (!empty($provinceFilter)) {
            $builder->where('province', $provinceFilter);
        }

        if (!empty($priceMin)) {
            $builder->where('price >=', $priceMin);
        }

        if (!empty($priceMax)) {
            $builder->where('price <=', $priceMax);
        }

        // ผลลัพธ์ทั้งหมดหลังกรอง
        $stadiums = $builder->findAll();

        return view('owner/dashboard', [
            'stadiums'   => $stadiums,
            'categories' => $categoryModel->findAll(),
            'provinces'  => getThaiProvinces(),
        ]);
    }
}
