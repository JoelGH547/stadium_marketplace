<?php

namespace App\Controllers; // ‼️ ใช้ Backslash (\)

use App\Models\ProductModel;
use App\Models\StockMovementModel;
use App\Models\UserModel;

class StockController extends BaseController
{
    protected $productModel;
    protected $stockMovementModel;
    protected $userModel;
    protected $db; // สำหรับ Database Transaction

    public function __construct()
    {
        $this->productModel = new ProductModel();
        $this->stockMovementModel = new StockMovementModel();
        $this->userModel = new UserModel();
        $this->db = \Config\Database::connect();
    }

    /**
     * แสดงหน้ารวม (Redirect ไป Stock In)
     */
    public function index()
    {
        return redirect()->to(base_url('admin/stock/in'));
    }

    // =================================================================
    // STOCK IN (รับเข้า) - (ส่วนนี้เสร็จสมบูรณ์แล้ว)
    // =================================================================

    public function stockIn()
    {
        $data = [
            'title' => 'Stock In (รับสินค้าเข้า)',
            'products' => $this->productModel->findAll()
        ];
        return view('stock/stock_in', $data);
    }

    public function processStockIn()
    {
        // ... (โค้ด Stock In ที่ทำงานได้ดีอยู่แล้ว) ...
        // (Validation, Get Data, User Check, Transaction)

        // --- 1. Validation Rules ---
        $rules = [
            'product_id' => 'required|integer|is_not_unique[products.id]',
            'quantity' => 'required|integer|greater_than[0]',
            'reference' => 'permit_empty|string|max_length[100]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('validation', $this->validator);
        }

        // --- 2. Get Data ---
        $productId = $this->request->getPost('product_id');
        $quantity = (int)$this->request->getPost('quantity');
        $reference = $this->request->getPost('reference');
        $userId = session()->get('user_id'); 

        // --- 3. Validate User ID ---
        if (empty($userId)) {
            return redirect()->back()->withInput()->with('error', 'SESSION ERROR: Cannot find User ID in session. (Key: user_id)');
        }
        $user = $this->userModel->find($userId);
        if (empty($user)) {
             return redirect()->back()->withInput()->with('error', 'SESSION ERROR: User ID ' . esc($userId) . ' not found in users table.');
        }

        // --- 4. Database Transaction ---
        $this->db->transStart();
        try {
            // (Action 1) บันทึกรายการ
            $movementData = [
                'product_id' => $productId,
                'type' => 'IN',
                'quantity' => $quantity,
                'reference' => $reference,
                'user_id' => $userId
            ];
            if (! $this->stockMovementModel->save($movementData)) {
                 throw new \Exception('StockMovementModel save() failed: ' . implode(', ', $this->stockMovementModel->errors() ?? ['Unknown model error']));
            }
            // (Action 2) อัปเดตสต็อก (บวก)
            $this->productModel
                ->where('id', $productId)
                ->set('stock', 'stock + ' . $this->db->escape($quantity), false)
                ->update();
            $this->db->transComplete();
        } catch (\Exception $e) { 
            $this->db->transRollback();
            return redirect()->back()->withInput()->with('error', 'Transaction Error: ' . $e->getMessage());
        }

        if ($this->db->transStatus() === false) {
             return redirect()->back()->withInput()->with('error', 'Transaction Failed. (DB rejected the save)');
        }
        return redirect()->to(base_url('admin/stock/in'))->with('success', 'Stock added successfully!');
    }


    // =================================================================
    // ‼️ STOCK OUT (เบิกออก) - (ส่วนที่เรากำลังทำ) ‼️
    // =================================================================

    /**
     * แสดงฟอร์มสำหรับ "เบิกสินค้าออก"
     */
    public function stockOut()
    {
        $data = [
            'title' => 'Stock Out (เบิกสินค้าออก)',
            // ดึงสินค้าทั้งหมด (เราสามารถกรองเฉพาะสินค้าที่มี stock > 0 ได้ถ้าต้องการ)
            'products' => $this->productModel->findAll() 
        ];
        // ‼️ โหลด View ใหม่ที่เราเพิ่งสร้าง
        return view('stock/stock_out', $data); 
    }

    /**
     * ประมวลผลการ "เบิกสินค้าออก"
     */
    public function processStockOut()
    {
        // --- 1. Validation Rules ---
        $rules = [
            'product_id' => 'required|integer|is_not_unique[products.id]',
            'quantity' => 'required|integer|greater_than[0]',
            'reference' => 'permit_empty|string|max_length[100]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('validation', $this->validator);
        }

        // --- 2. Get Data ---
        $productId = $this->request->getPost('product_id');
        $quantity = (int)$this->request->getPost('quantity');
        $reference = $this->request->getPost('reference');
        $userId = session()->get('user_id'); // (ดึงจาก session)

        // --- 3. ‼️ VALIDATION (ตรวจสอบสต็อกคงเหลือ) ‼️ ---
        $product = $this->productModel->find($productId);

        if (!$product) {
            return redirect()->back()->withInput()->with('error', 'Error: Product not found.');
        }

        // ‼️ นี่คือส่วนที่สำคัญที่สุด ‼️
        if ($quantity > $product['stock']) {
            return redirect()->back()->withInput()->with('error', 'Stock Error: Cannot withdraw ' . $quantity . ' items. Only ' . $product['stock'] . ' available.');
        }

        // (ตรวจสอบ User ID - เหมือน Stock In)
        if (empty($userId)) {
            return redirect()->back()->withInput()->with('error', 'SESSION ERROR: Cannot find User ID in session.');
        }
        $user = $this->userModel->find($userId);
        if (empty($user)) {
             return redirect()->back()->withInput()->with('error', 'SESSION ERROR: User ID ' . esc($userId) . ' not found in users table.');
        }

        // --- 4. Database Transaction ---
        $this->db->transStart();
        try {
            // (Action 1) บันทึกรายการ
            $movementData = [
                'product_id' => $productId,
                'type' => 'OUT', // ‼️ เปลี่ยน Type เป็น 'OUT'
                'quantity' => $quantity, // (เราจะเก็บเป็นค่าบวกเสมอ)
                'reference' => $reference,
                'user_id' => $userId
            ];
            if (! $this->stockMovementModel->save($movementData)) {
                 throw new \Exception('StockMovementModel save() failed: ' . implode(', ', $this->stockMovementModel->errors() ?? ['Unknown model error']));
            }

            // (Action 2) อัปเดตสต็อกสินค้า (‼️ ลบออก ‼️)
            $this->productModel
                ->where('id', $productId)
                ->set('stock', 'stock - ' . $this->db->escape($quantity), false) // ‼️ เปลี่ยนเป็น 'stock -'
                ->update();

            $this->db->transComplete();

        } catch (\Exception $e) { 
            $this->db->transRollback();
            return redirect()->back()->withInput()->with('error', 'Transaction Error: ' . $e->getMessage());
        }

        if ($this->db->transStatus() === false) {
             return redirect()->back()->withInput()->with('error', 'Transaction Failed. (DB rejected the save)');
        }

        // --- 5. Redirect with Success ---
        // ‼️ Redirect กลับไปหน้า 'stock/out'
        return redirect()->to(base_url('admin/stock/out'))->with('success', 'Stock withdrawn successfully!');
    }
}