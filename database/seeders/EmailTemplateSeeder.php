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
        $templates = [
            [
                'name' => 'email_verification_otp',
                'subject'=>'Email Verification',
                'content' => '<h2>Hi {{USER_NAME}},</h2>
                    <p>Thank you for signing up with {{APP_NAME}}! Please verify your email address to complete your registration.</p>
                    <p>OTP: <strong>{{OTP}}</strong></p>

                    <p>If you did not create this request, please ignore this email.</p>
                    <p>Thank you for choosing {{APP_NAME}}</p>
                    <p>Best regards, <br> {{APP_NAME}} Team</p>',
                'default_content' => '',
            ],
            [
                'name' => 'forget_password_otp',
                'subject'=>'Forget password',
                'content' => '<h2>Hi {{USER_NAME}},</h2>
                    <p>You have requested a password reset. Please verify your email address to reset your password:</p>
                    <p>OTP: <strong>{{OTP}}</strong></p>

                    <p>If you did not create this request, please ignore this email.</p>
                    <p>Thank you for choosing {{APP_NAME}}</p>
                    <p>Best regards, <br> {{APP_NAME}} Team</p>',
                'default_content' => '',
            ],
            [
                'name' => 'order_status_update-Confirmed',
                'subject' => 'Order Confirmed - Thank you for your order #{{ORDER_ID}}',
                'content' => '<h2>Hi {{USER_NAME}},</h2>
                    <p>Great news! Your order <strong>#{{ORDER_ID}}</strong> has been confirmed and is now being processed.</p>
                    <p>We\'ll start working on your order right away and keep you updated on its progress.</p>
                    <p>Thank you for choosing {{APP_NAME}}!</p>
                    <p>Best regards,<br>{{APP_NAME}} Team</p>',
                'default_content' => '',
            ],
            [
                'name' => 'order_status_update-Processing',
                'subject' => 'Your order #{{ORDER_ID}} is in production',
                'content' => '<h2>Hi {{USER_NAME}},</h2>
                    <p>Your order <strong>#{{ORDER_ID}}</strong> is now in production!</p>
                    <p>Our team has started working on your prints, and we\'ll notify you once they\'re ready for shipping.</p>
                    <p>Thank you for choosing {{APP_NAME}}!</p>
                    <p>Best regards,<br>{{APP_NAME}} Team</p>',
                'default_content' => '',
            ],
            [
                'name' => 'order_status_update-Shipped',
                'subject' => 'Your order #{{ORDER_ID}} is on its way!',
                'content' => '<h2>Hi {{USER_NAME}},</h2>
                    <p>Your order <strong>#{{ORDER_ID}}</strong> is on its way!</p>
                    <p>Your prints have been carefully packaged and handed over to our delivery partner.</p>
                    <p>Thank you for choosing {{APP_NAME}}!</p>
                    <p>Best regards,<br>{{APP_NAME}} Team</p>',
                'default_content' => '',
            ],
            [
                'name' => 'order_status_update-Delivered',
                'subject' => 'Your order #{{ORDER_ID}} has been delivered',
                'content' => '<h2>Hi {{USER_NAME}},</h2>
                    <p>Your order <strong>#{{ORDER_ID}}</strong> has been delivered!</p>
                    <p>We hope you\'re happy with your prints. If you have any concerns, please don\'t hesitate to contact us.</p>
                    <p>Thank you for choosing {{APP_NAME}}!</p>
                    <p>Best regards,<br>{{APP_NAME}} Team</p>',
                'default_content' => '',
            ],
            [
                'name' => 'order_status_update-Completed',
                'subject' => 'Order Complete - Thank you for choosing us!',
                'content' => '<h2>Hi {{USER_NAME}},</h2>
                    <p>Your order <strong>#{{ORDER_ID}}</strong> is now complete!</p>
                    <p>Thank you for choosing us for your printing needs. We look forward to serving you again!</p>
                    <p>Best regards,<br>{{APP_NAME}} Team</p>',
                'default_content' => '',
            ],
            [
                'name' => 'order_status_update-Cancelled',
                'subject' => 'Order #{{ORDER_ID}} has been cancelled',
                'content' => '<h2>Hi {{USER_NAME}},</h2>
                    <p>Your order <strong>#{{ORDER_ID}}</strong> has been cancelled as requested.</p>
                    <p>If you didn\'t request this cancellation or have any questions, please contact our support team.</p>
                    <p>Thank you for choosing {{APP_NAME}}!</p>
                    <p>Best regards,<br>{{APP_NAME}} Team</p>',
                'default_content' => '',
            ],
            [
                'name' => 'order_status_update-Expired',
                'subject' => 'Order #{{ORDER_ID}} has expired',
                'content' => '<h2>Hi {{USER_NAME}},</h2>
                    <p>Your order <strong>#{{ORDER_ID}}</strong> has expired due to inactivity.</p>
                    <p>Please place a new order if you would still like to proceed with your printing.</p>
                    <p>Thank you for choosing {{APP_NAME}}!</p>
                    <p>Best regards,<br>{{APP_NAME}} Team</p>',
                'default_content' => '',
            ],
        ];

        foreach ($templates as $template) {
            $template['default_content'] = $template['content'];
            EmailTemplate::updateOrCreate(['name' => $template['name']], $template);
        }
    }
}
