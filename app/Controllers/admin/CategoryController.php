<?php

namespace App\Controllers\admin;

use App\Controllers\BaseController;
use App\Models\CategoryModel;
use CodeIgniter\Database\Exceptions\DatabaseException;

class CategoryController extends BaseController
{
    protected $categoryModel;

    public function __construct()
    {
        $this->categoryModel = new CategoryModel();
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = [
            'title' => 'Category Management',
            'categories' => $this->categoryModel->orderBy('id', 'ASC')->findAll(),
        ];
        
        return view('admin/categories/index', $data); 
    }

    /**
     * Show the form for creating a new resource.
     */
    public function new()
    {
        $data = [
            'title' => 'Add New Category'
        ];
        
        return view('admin/categories/create', $data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function create()
    {
        // 1. Validation
        if (!$this->validate([
            'name' => 'required|min_length[3]|max_length[100]|is_unique[categories.name]',
        ])) {
            return redirect()->back()->withInput()->with('validation', $this->validator);
        }

        // 2. Save
        $this->categoryModel->save([
            'name' => $this->request->getPost('name'),
            // 💡 (ลบ description ที่ไม่ใช้ออก)
        ]);

        return redirect()->to(base_url('admin/categories'))->with('success', 'Category created successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id = null)
    {
        $category = $this->categoryModel->find($id);
        if (!$category) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Category not found.');
        }

        $data = [
            'title' => 'Edit Category: ' . esc($category['name']),
            'category' => $category,
        ];

        return view('admin/categories/edit', $data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update($id = null)
    {
        // 1. Validation
        if (!$this->validate([
            'name' => "required|min_length[3]|max_length[100]|is_unique[categories.name,id,{$id}]",
        ])) {
            return redirect()->back()->withInput()->with('validation', $this->validator);
        }

        // 2. Update
        $this->categoryModel->update($id, [
            'name' => $this->request->getPost('name'),
            // 💡 (ลบ description ที่ไม่ใช้ออก)
        ]);

        return redirect()->to(base_url('admin/categories'))->with('success', 'Category updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function delete($id = null)
    {
        try {
            // 1. พยายามลบ
            $this->categoryModel->delete($id);

            // 2. ถ้าลบสำเร็จ
            return redirect()->to(base_url('admin/categories'))->with('success', 'Category deleted successfully.');
            
        } catch (DatabaseException $e) {
            // 3. ถ้าล้มเหลว (เพราะ Error 1451)
            if ($e->getCode() == 1451) {
                return redirect()->to(base_url('admin/categories'))
                                ->with('error', 'ไม่สามารถลบหมวดหมู่นี้ได้! (ID: '.esc($id).') เนื่องจากมีสินค้าอ้างอิงอยู่');
            }

            // ถ้าเป็น Error อื่น
            return redirect()->to(base_url('admin/categories'))->with('error', 'Database Error: ' . $e->getMessage());
        }
    }
}