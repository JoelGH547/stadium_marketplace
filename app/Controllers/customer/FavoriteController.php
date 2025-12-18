<?php

namespace App\Controllers\customer;

use App\Controllers\BaseController;
use App\Models\CustomerFavoriteModel;
use App\Models\StadiumReviewModel;

class FavoriteController extends BaseController
{
    public function index()
    {
        if (! session('customer_logged_in')) {
            return redirect()->to(site_url('customer/login'));
        }

        $customerId = (int) session('customer_id');
        $favModel   = new CustomerFavoriteModel();
        $rows       = $favModel->getFavoritesWithStadiumInfo($customerId);

        // Rating summary
        $reviewModel = new StadiumReviewModel();
        $stadiumIds  = array_map(static fn($x) => (int) ($x['id'] ?? 0), $rows);
        $summaries   = $reviewModel->getSummariesForStadiumIds($stadiumIds);

        $favoriteMap = [];
        foreach ($stadiumIds as $sid) {
            if ($sid > 0) $favoriteMap[$sid] = true;
        }

        // Attach rating + cover_image
        foreach ($rows as &$v) {
            $sid = (int) ($v['id'] ?? 0);
            $avg = $summaries[$sid]['avg'] ?? 0.0;
            $cnt = $summaries[$sid]['count'] ?? 0;

            $v['rating_avg']   = $cnt > 0 ? round((float) $avg, 1) : 0.0;
            $v['rating_count'] = (int) $cnt;

            $cover = null;
            if (! empty($v['outside_images'])) {
                $decoded = json_decode($v['outside_images'], true);
                if (is_array($decoded) && ! empty($decoded)) {
                    $cover = reset($decoded);
                }
            }
            $v['cover_image'] = $cover;
        }
        unset($v);

        return view('public/favorites', [
            'title'       => 'รายการโปรดของฉัน',
            'favorites'   => $rows,
            'favoriteMap' => $favoriteMap,
        ]);
    }

    public function toggle()
    {
        $responsePayload = ['csrf_hash' => csrf_hash()];

        $stadiumId = (int) ($this->request->getPost('stadium_id') ?? 0);
        if ($stadiumId <= 0) {
            $responsePayload['success'] = false;
            $responsePayload['message'] = 'ข้อมูลไม่ถูกต้อง';

            return $this->response->setJSON($responsePayload)->setStatusCode(400);
        }

        if (! session('customer_logged_in')) {
            $responsePayload['success'] = false;
            $responsePayload['need_login'] = true;
            $responsePayload['message'] = 'กรุณาเข้าสู่ระบบก่อน';

            return $this->response->setJSON($responsePayload)->setStatusCode(401);
        }

        $customerId = (int) session('customer_id');
        $favModel   = new CustomerFavoriteModel();

        try {
            $newState = $favModel->toggle($customerId, $stadiumId);
        } catch (\Throwable $e) {
            $responsePayload['success'] = false;
            $responsePayload['message'] = 'ไม่สามารถบันทึกรายการโปรดได้';

            return $this->response->setJSON($responsePayload)->setStatusCode(500);
        }

        $responsePayload['success']   = true;
        $responsePayload['favorited'] = $newState;

        return $this->response->setJSON($responsePayload);
    }
}
