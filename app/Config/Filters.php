<?php

namespace Config;

use CodeIgniter\Config\Filters as BaseFilters;
use CodeIgniter\Filters\CSRF;
use CodeIgniter\Filters\DebugToolbar;
use CodeIgniter\Filters\Honeypot;
use CodeIgniter\Filters\InvalidChars;
use CodeIgniter\Filters\SecureHeaders;
// 1. ⬇️ (เพิ่ม) Import Filter มาตรฐาน (ที่ขาดไป) ⬇️
use CodeIgniter\Filters\Cors;
use CodeIgniter\Filters\ForceHTTPS;
use CodeIgniter\Filters\PageCache;
use CodeIgniter\Filters\PerformanceMetrics;

// 2. ⬇️ (เพิ่ม) Import "ยาม" 3 คนของเรา (เหมือนเดิม) ⬇️
use App\Filters\AuthFilter;
use App\Filters\AdminFilter;
use App\Filters\CustomerFilter;
// (ในอนาคต: use App\Filters\VendorFilter;)

class Filters extends BaseFilters
{
    /**
     * Configures aliases for Filter classes...
     */
    public array $aliases = [
        'csrf'     => CSRF::class,
        'toolbar'  => DebugToolbar::class,
        'honeypot' => Honeypot::class,
        'invalidchars' => InvalidChars::class,
        'secureheaders' => SecureHeaders::class,
        
        // 3. ⬇️ (เพิ่ม) Alias มาตรฐาน (ที่ขาดไป) ⬇️
        'cors'        => Cors::class,
        'forcehttps'  => ForceHTTPS::class, // ⬅️ (นี่คือตัวที่ Error)
        'pagecache'   => PageCache::class,
        'performance' => PerformanceMetrics::class,

        // 4. ⬇️ (เพิ่ม) Alias ของเรา (เหมือนเดิม) ⬇️
        'admin'      => AdminFilter::class,
        'customer'   => CustomerFilter::class,
    ];

    /**
     * List of special required filters.
     * (ส่วนนี้แหละที่เรียก 'forcehttps')
     */
    public array $required = [
        'before' => [
            'forcehttps', // ⬅️ (ตัวปัญหามันถูกเรียกตรงนี้)
            'pagecache',
        ],
        'after' => [
            'pagecache',
            'performance',
            'toolbar',
        ],
    ];

    /**
     * List of filter aliases that are always
     * applied before and after every request.
     */
    public array $globals = [
        'before' => [
            // 'honeypot',
            // 'csrf',
            // 'invalidchars',
        ],
        'after' => [
            // 'honeypot',
            // 'secureheaders',
        ],
    ];

    // ... (ส่วน $methods และ $filters อยู่เหมือนเดิม) ...
    public array $methods = [];
    public array $filters = [];
}