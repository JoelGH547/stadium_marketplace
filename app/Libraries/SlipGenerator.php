<?php

namespace App\Libraries;

class SlipGenerator
{
    /**
     * สร้างรูปสลิป (PNG) จากข้อมูล และคืนค่าเป็น "ชื่อไฟล์" (ไม่รวม path)
     * บันทึกไว้ที่ public/assets/uploads/slips/
     */
    public function generate(array $payload): ?string
    {
        if (! function_exists('imagecreatetruecolor') || ! function_exists('imagettftext')) {
            // ไม่มี GD/FreeType
            throw new \Exception('The GD extension for PHP is not enabled. Please enable it to generate slip images.');
        }

        $publicDir = rtrim(FCPATH, DIRECTORY_SEPARATOR);
        $outDir    = $publicDir . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'slips';

        if (! is_dir($outDir)) {
            if (! @mkdir($outDir, 0775, true)) {
                 throw new \Exception('Failed to create slip directory: ' . $outDir);
            }
        }

        // ฟอนต์ไทย (พกไว้ในโปรเจกต์เพื่อให้เครื่องปลายทางใช้ได้แน่นอน)
        // ใช้ Tahoma เป็นหลัก (รองรับทั้งไทยและอังกฤษ/ตัวเลข)
        $font = $publicDir . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'fonts' . DIRECTORY_SEPARATOR . 'tahoma.ttf';
        
        if (! is_file($font)) {
            // Fallback: หากไม่มี Tahoma ให้ใช้ NotoSansThaiUI (เดิม)
            $font = $publicDir . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'fonts' . DIRECTORY_SEPARATOR . 'NotoSansThaiUI-Regular.ttf';
        }

        if (! is_file($font)) {
            // fallback: ให้ลองใช้ฟอนต์ในระบบ (เผื่อมี)
            $font = '/usr/share/fonts/truetype/noto/NotoSansThaiUI-Regular.ttf';
        }
        
        if (! is_file($font)) {
            // Last resort: check for local tahoma in windows if dev env
            $font = 'C:\Windows\Fonts\tahoma.ttf';
        }

        if (! is_file($font)) {
            throw new \Exception('Font file not found. Please make sure "tahoma.ttf" or "NotoSansThaiUI-Regular.ttf" exists in "public/assets/fonts/".');
        }

        $w = 920;
        $padX = 36;
        $padY = 30;

        $title = (string)($payload['title'] ?? 'สลิปการจอง');
        $lines = $payload['lines'] ?? [];
        if (! is_array($lines)) $lines = [];

        // คำนวณความสูงโดยคร่าว ๆ
        $lineH  = 34;
        $height = $padY * 2 + 120 + ($lineH * (count($lines) + 1));
        $height = max(520, $height);

        $im = imagecreatetruecolor($w, $height);

        // สี
        $white = imagecolorallocate($im, 255, 255, 255);
        $black = imagecolorallocate($im, 25, 25, 25);
        $gray  = imagecolorallocate($im, 120, 120, 120);
        $mint  = imagecolorallocate($im, 25, 180, 160);
        $line  = imagecolorallocate($im, 230, 230, 230);

        imagefilledrectangle($im, 0, 0, $w, $height, $white);

        // Header bar
        imagefilledrectangle($im, 0, 0, $w, 86, $mint);

        $y = 56;
        $this->ttf($im, 22, $padX, 52, $white, $font, $title);
        $meta = (string)($payload['meta'] ?? '');
        if ($meta !== '') {
            $this->ttf($im, 12, $padX, 74, $white, $font, $meta);
        }

        $y = 110;

        // วาดกล่องพื้นหลังเนื้อหา
        imagefilledrectangle($im, 18, 96, $w - 18, $height - 18, imagecolorallocate($im, 252, 252, 252));
        imagerectangle($im, 18, 96, $w - 18, $height - 18, $line);

        // วาดข้อความ
        foreach ($lines as $row) {
            if (! is_array($row)) continue;

            $type = $row['type'] ?? 'text';

            if ($type === 'hr') {
                imageline($im, $padX, $y, $w - $padX, $y, $line);
                $y += 22;
                continue;
            }

            if ($type === 'section') {
                $label = (string)($row['text'] ?? '');
                $this->ttf($im, 14, $padX, $y, $black, $font, $label);
                $y += 28;
                continue;
            }

            if ($type === 'kv') {
                $k = (string)($row['k'] ?? '');
                $v = (string)($row['v'] ?? '');
                $this->ttf($im, 12, $padX, $y, $gray, $font, $k);
                $this->ttf($im, 12, $padX + 220, $y, $black, $font, $v);
                $y += 26;
                continue;
            }

            if ($type === 'row') {
                // ซ้าย-ขวา (เช่น รายการ ... ราคา)
                $left  = (string)($row['left'] ?? '');
                $right = (string)($row['right'] ?? '');

                $fontSize = (int)($row['size'] ?? 12);
                $isBold   = (bool)($row['bold'] ?? false);

                $leftColor  = $isBold ? $black : $black;
                $rightColor = $isBold ? $black : $black;

                // วาดซ้าย
                $this->ttf($im, $fontSize, $padX, $y, $leftColor, $font, $left);

                // วัดความกว้างขวา เพื่อชิดขวา
                $bbox = imagettfbbox($fontSize, 0, $font, $right);
                $rw   = abs($bbox[2] - $bbox[0]);
                $rx   = $w - $padX - $rw;

                $this->ttf($im, $fontSize, $rx, $y, $rightColor, $font, $right);

                $y += 26;
                continue;
            }

            // text
            $text = (string)($row['text'] ?? '');
            $this->ttf($im, 12, $padX, $y, $black, $font, $text);
            $y += 26;
        }

        // crop / expand หาก y เกิน height
        $finalH = min(max($y + 40, 520), 2200);
        if ($finalH > $height) {
            // ขยาย canvas
            $new = imagecreatetruecolor($w, $finalH);
            imagefilledrectangle($new, 0, 0, $w, $finalH, $white);
            imagecopy($new, $im, 0, 0, 0, 0, $w, $height);
            imagedestroy($im);
            $im = $new;
            $height = $finalH;
        }

        $filename = 'slip_' . date('Ymd_His') . '_' . bin2hex(random_bytes(4)) . '.png';
        $fullpath = $outDir . DIRECTORY_SEPARATOR . $filename;

        imagepng($im, $fullpath, 7);
        imagedestroy($im);

        return $filename;
    }

    private function ttf($im, int $size, int $x, int $y, $color, string $font, string $text): void
    {
        // รองรับการขึ้นบรรทัดแบบง่าย (ถ้าข้อความยาวมาก)
        $maxChars = 58;
        $text = trim($text);

        if (mb_strlen($text) <= $maxChars) {
            imagettftext($im, $size, 0, $x, $y, $color, $font, $text);
            return;
        }

        $chunks = [];
        $buf = '';
        $len = mb_strlen($text);
        for ($i=0; $i<$len; $i++) {
            $buf .= mb_substr($text, $i, 1);
            if (mb_strlen($buf) >= $maxChars) {
                $chunks[] = $buf;
                $buf = '';
            }
        }
        if ($buf !== '') $chunks[] = $buf;

        $dy = 0;
        foreach ($chunks as $line) {
            imagettftext($im, $size, 0, $x, $y + $dy, $color, $font, $line);
            $dy += (int)($size + 10);
        }
    }
}
