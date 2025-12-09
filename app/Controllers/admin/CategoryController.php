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

    
     
     
    public function index()
    {
        $data = [
            'title' => 'Category Management',
            
            'categories' => $this->categoryModel->orderBy('id', 'ASC')->findAll(),
        ];
        
        
        return view('admin/categories/index', $data); 
        
    }

     
     
    public function new()
    {
        $data = [
            'title' => 'Add New Category'
        ];
        
        
        return view('admin/categories/create', $data);
        
    }

    
     
     
    public function create()
    {
        
        if (!$this->validate([
            'name' => 'required|min_length[3]|max_length[100]|is_unique[categories.name]',
        ])) {
            return redirect()->back()->withInput()->with('validation', $this->validator);
        }

        
        $this->categoryModel->save([
            'name' => $this->request->getPost('name'),
            'emoji' => $this->request->getPost('emoji'),
        ]);

        return redirect()->to(base_url('admin/categories'))->with('success', 'Category created successfully.');
    }

    
     
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

    
    
     
    public function update($id = null)
    {
        
        if (!$this->validate([
            'name' => "required|min_length[3]|max_length[100]|is_unique[categories.name,id,{$id}]",
        ])) {
            return redirect()->back()->withInput()->with('validation', $this->validator);
        }

        
        $this->categoryModel->update($id, [
            'name' => $this->request->getPost('name'),
            'emoji' => $this->request->getPost('emoji'),
        ]);

        return redirect()->to(base_url('admin/categories'))->with('success', 'Category updated successfully.');
    }

    
    
    public function delete($id = null)
    {
        try {
            
            $this->categoryModel->delete($id);

            
            return redirect()->to(base_url('admin/categories'))->with('success', 'Category deleted successfully.');
            
        } catch (DatabaseException $e) {
            
            if ($e->getCode() == 1451) {
                return redirect()->to(base_url('admin/categories'))
                                 ->with('error', 'ไม่สามารถลบหมวดหมู่นี้ได้! (ID: '.esc($id).') เนื่องจากมีสินค้าอ้างอิงอยู่');
            }

            
            return redirect()->to(base_url('admin/categories'))->with('error', 'Database Error: ' . $e->getMessage());
        }
    }
}