<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class MainSeeder extends Seeder
{
    public function run()
    {
        // ---------------------------------------------------------------------
        // 1. Seed Categories (if empty)
        // ---------------------------------------------------------------------
        $categoryModel = new \App\Models\CategoryModel();
        if ($categoryModel->countAll() === 0) {
            $categories = [
                ['name' => 'Football', 'emoji' => '⚽'],
                ['name' => 'Basketball', 'emoji' => '🏀'],
                ['name' => 'Badminton', 'emoji' => '🏸'],
                ['name' => 'Tennis', 'emoji' => '🎾'],
                ['name' => 'Swimming', 'emoji' => '🏊'],
            ];
            $categoryModel->insertBatch($categories);
            echo "Seeded Categories.\n";
        }

        // ---------------------------------------------------------------------
        // 2. Seed Vendors (if empty)
        // ---------------------------------------------------------------------
        $vendorModel = new \App\Models\VendorModel();
        if ($vendorModel->countAll() === 0) {
            $vendors = [];
            for ($i = 1; $i <= 5; $i++) {
                $vendors[] = [
                    'username'      => 'vendor' . $i,
                    'vendor_name'   => 'Vendor ' . $i . ' Company',
                    'lastname'      => 'Owner ' . $i,
                    'email'         => 'vendor' . $i . '@example.com',
                    'phone_number'  => '081234567' . $i,
                    'password_hash' => password_hash('password', PASSWORD_DEFAULT),
                    'status'        => 'active',
                    'created_at'    => date('Y-m-d H:i:s'),
                ];
            }
            $vendorModel->insertBatch($vendors);
            echo "Seeded Vendors.\n";
        }

        // ---------------------------------------------------------------------
        // 3. Seed Stadiums (5 records)
        // ---------------------------------------------------------------------
        $stadiumModel = new \App\Models\StadiumModel();
        
        // Use existing images found in the directory
        $outsideImages = [
            'outside_1763951366_1763951366_7d3ed6fe73913590e3b1.jpg',
            'outside_1763951519_1763951519_c7221e137042eeccc06d.jpg',
            'outside_1763951549_1763951549_34470334d94301fd1aa0.jpeg',
            'outside_1764578746_1764578746_082e0fd0d8a0ea9ecc1e.jpg',
            'outside_1764578952_1764578952_6caf0bcbab25120115d6.jpg'
        ];

        // Fallback inside images
        $insideImagesPool = [
            'inside_1763951366_1763951366_18c63699e6ea4ba27466.jpg',
            'inside_1763951415_1763951415_5071c6ae4c5608654b6b.jpg',
            'inside_1763951519_1763951519_605b9cbd6765721ab757.jpg'
        ];

        $faker = \Faker\Factory::create('th_TH');

        // Fetch valid IDs
        $catIds = $categoryModel->findColumn('id');
        $venIds = $vendorModel->findColumn('id');

        for ($i = 0; $i < 5; $i++) {
            $outsideImg = $outsideImages[$i % count($outsideImages)];
            $insideImg = $insideImagesPool[$i % count($insideImagesPool)];

            $data = [
                'name'           => 'สนาม ' . $faker->company,
                'description'    => $faker->realText(100),
                'category_id'    => $catIds[array_rand($catIds)],
                'vendor_id'      => $venIds[array_rand($venIds)],
                'open_time'      => '08:00',
                'close_time'     => '22:00',
                'contact_email'  => $faker->email,
                'contact_phone'  => $faker->phoneNumber,
                'province'       => 'Bankok', 
                'address'        => $faker->address,
                'lat'            => 13.7563 + ($faker->randomFloat(4, -0.05, 0.05)),
                'lng'            => 100.5018 + ($faker->randomFloat(4, -0.05, 0.05)),
                'outside_images' => json_encode([$outsideImg]),
                'inside_images'  => json_encode([$insideImg]), 
                'created_at'     => date('Y-m-d H:i:s'),
                'updated_at'     => date('Y-m-d H:i:s'),
            ];

            $stadiumModel->insert($data);
        }
        echo "Seeded 5 Stadiums.\n";
    }
}
