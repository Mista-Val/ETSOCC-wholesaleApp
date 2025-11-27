<?php

namespace Database\Seeders;

use App\Models\GlobalSetting;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class GlobalSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if(empty(GlobalSetting::count())){
            $data = [
                [
                    'title' => 'Logo',
                    'value' => '',
                    'slug' => 'logo',
                    'type' => 'file',
                    'category_type' => 'G',
                ],
                [
                    'title' => 'Favicon',
                    'value' => '',
                    'slug' => 'favicon',
                    'type' => 'file',
                    'category_type' => 'G',
                ],
                [
                    'title' => 'Admin Email',
                    'value' => '',
                    'slug' => 'emailFrom',
                    'type' => 'email',
                    'category_type' => 'A',
                ],
                [
                    'title' => 'Admin Receive Email',
                    'value' => '',
                    'slug' => 'adminEmail',
                    'type' => 'email',
                    'category_type' => 'A',
                ],
                [
                    'title' => 'Title',
                    'value' => '',
                    'slug' => 'title',
                    'type' => 'textarea',
                    'category_type' => 'G',
                ],
                [
                    'title' => 'Site title',
                    'value' => '',
                    'slug' => 'siteTitle',
                    'type' => 'textarea',
                    'category_type' => 'G',
                ],
                [
                    'title' => 'Address',
                    'value' => '',
                    'slug' => 'address',
                    'type' => 'textarea',
                    'category_type' => 'G',
                ],
                [
                    'title' => 'Contact No',
                    'value' => '',
                    'slug' => 'contactNo',
                    'type' => 'text',
                    'category_type' => 'G',
                ],
                [
                    'title' => 'Instagram',
                    'value' => '',
                    'slug' => 'instagram',
                    'type' => 'text',
                    'category_type' => 'S',
                ],
                [
                    'title' => 'Twitter',
                    'value' => '',
                    'slug' => 'twitter',
                    'type' => 'text',
                    'category_type' => 'S',
                ],
                [
                    'title' => 'Facebook',
                    'value' => '',
                    'slug' => 'facebook',
                    'type' => 'text',
                    'category_type' => 'S',
                ],
                [
                    'title' => 'Copy Right Text',
                    'value' => '',
                    'slug' => 'copyRightText',
                    'type' => 'text',
                    'category_type' => 'G',
                ],
                [
                    'title' => 'Footer Description',
                    'value' => '',
                    'slug' => 'footerDescription',
                    'type' => 'textarea',
                    'category_type' => 'G',
                ]

            ];
            GlobalSetting::insert($data);
        }
    }
}
