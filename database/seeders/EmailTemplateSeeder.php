<?php

namespace Database\Seeders;

use App\Models\EmailTemplate;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EmailTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if(empty(EmailTemplate::count())){
            $data = [
                [
                    'title' => 'Reset Your Password',
                    'subject' => 'Reset you password',
                    'content' => '<p>Hello <strong>{username}</strong>,<br />
                    <br />
                    You have requested reset password. Please use below one time password to reset your password.&nbsp;<br />
                    <br />
                    <strong>{otp}</strong></p>
                    
                    <p>Best regards,<br />
                    <br />
                    <strong>Le Toots</strong></p>
                    
                    <p><br />
                    &nbsp;</p>
                    
                    <p>&nbsp;</p>
                    
                    <p>&nbsp;</p>
                    
                    <p>&nbsp;</p>',
                    'slug' => 'forgot-password',
                    'status' => 1,
                ],
                [
                    'title' => 'User Registration',
                    'subject' => 'User Registration',
                    'content' => '<p>Hi {username},</p><p>Welcome to {username}</p><p>Your registration was successful.</p><p>Thanks for joining!</p><p>Best regards,</p><p>Le Toots Support Team</p>',
                    'slug' => 'user-registration',
                    'status' => 1,
                ],
                [
                    'title' => 'Send OTP',
                    'subject' => 'Your OTP Code for Secure Access',
                    'content' => '<p>Hello {username},</p><p>We have received a request to access your account. For your security, please use the following One-Time Password (OTP) to complete your action:</p><p><strong>Your OTP Code: {otp}</strong></p><p>Best regards,<br>Le Toots Support Team</p>',
                    'slug' => 'send-otp',
                    'status' => 1,
                ],

            ];
            EmailTemplate::insert($data);
        }
    }
}
