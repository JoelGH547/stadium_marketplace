<?php namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\FacilityTypeModel;

class FacilityTypeController extends BaseController
{
    protected $typeModel;

    public function __construct()
    {
        $this->typeModel = new FacilityTypeModel();
    }

    
    public function index()
    {
        $data = [
            'title' => 'จัดการหมวดหมู่สิ่งอำนวยความสะดวก',
            'types' => $this->typeModel->findAll()
        ];
        return view('admin/facility_types/index', $data);
    }

    
    public function create()
    {
        $name = $this->request->getPost('name');
        if($name) {
            $this->typeModel->save(['name' => $name]);
            return redirect()->to('admin/facility-types')->with('success', 'เพิ่มหมวดหมู่เรียบร้อย');
        }
        return redirect()->back()->with('error', 'กรุณากรอกชื่อหมวดหมู่');
    }

    
    public function delete($id)
    {
        $this->typeModel->delete($id);
        return redirect()->to('admin/facility-types')->with('success', 'ลบข้อมูลเรียบร้อย');
    }
}