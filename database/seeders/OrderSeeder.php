<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OrderSeeder extends Seeder
{
    public function run()
    {
        $orders = [
            [
                'type' => 'Order',
                'user_id' => 3, // Assuming the user with ID 1 exists.
                'session_id' => null, // Registered user order.
                'order_number' => 'UKORD' . Str::random(6),
                'address_info' => json_encode([
                    'billing_address' => [
                        'first_name' => 'John',
                        'last_name' => 'Doe',
                        'address_line1' => '123 High Street',
                        'address_line2' => '',
                        'city' => 'London',
                        'postcode' => 'W1A 1AA',
                        'country' => 'UK',
                        'phone' => '+44 20 7946 0958',
                    ],
                    'shipping_address' => [
                        'first_name' => 'John',
                        'last_name' => 'Doe',
                        'address_line1' => '456 Baker Street',
                        'address_line2' => '',
                        'city' => 'London',
                        'postcode' => 'NW1 6XE',
                        'country' => 'UK',
                        'phone' => '+44 20 7946 0958',
                    ],
                ]),
                'metadata' => json_encode([
                    'notes' => 'Please deliver between 9 AM and 5 PM.',
                ]),
                'price_detail' => json_encode([
                    'subtotal' => 120.00,
                    'discount' => 10.00,
                    'tax' => 5.00,
                    'shipping_cost' => 7.50,
                    'total' => 122.50,
                ]),
                'total' => 122.50,
                'order_status' => 'Confirmed',
                'payment_status' => 'Paid',
                'shipping_carrier' => 'Royal Mail',
                'tracking_number' => 'RM123456789GB',
                'payment_method' => 'stripe',
                'transaction_id' => 'txn_123ABC456DEF',
                'payment_details' => json_encode([
                    'payment_gateway' => 'Stripe',
                    'amount' => 122.50,
                    'currency' => 'GBP',
                    'status' => 'Paid',
                ]),
                'status_logs' => json_encode([
                    ['status' => 'Confirmed', 'updated_at' => now()],
                ]),
                'payment_logs' => json_encode([
                    ['status' => 'Payment completed', 'logged_at' => now()],
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'type' => 'Order',
                'user_id' => 3, // Guest user.
                'session_id' => Str::random(32),
                'order_number' => 'UKORD' . Str::random(6),
                'address_info' => json_encode([
                    'billing_address' => [
                        'first_name' => 'Emily',
                        'last_name' => 'Blunt',
                        'address_line1' => '987 Elm Street',
                        'address_line2' => 'Apt 12',
                        'city' => 'Manchester',
                        'postcode' => 'M1 1AE',
                        'country' => 'UK',
                        'phone' => '+44 161 123 4567',
                    ],
                    'shipping_address' => [
                        'first_name' => 'Emily',
                        'last_name' => 'Blunt',
                        'address_line1' => '987 Elm Street',
                        'address_line2' => 'Apt 12',
                        'city' => 'Manchester',
                        'postcode' => 'M1 1AE',
                        'country' => 'UK',
                        'phone' => '+44 161 123 4567',
                    ],
                ]),
                'metadata' => json_encode([
                    'notes' => 'Leave the package at the front door.',
                ]),
                'price_detail' => json_encode([
                    'subtotal' => 85.00,
                    'discount' => 5.00,
                    'tax' => 4.25,
                    'shipping_cost' => 5.00,
                    'total' => 89.25,
                ]),
                'total' => 89.25,
                'order_status' => 'Processing',
                'payment_status' => 'Unpaid',
                'shipping_carrier' => 'DHL',
                'tracking_number' => 'DHL56789GB',
                'payment_method' => 'paypal',
                'transaction_id' => null,
                'payment_details' => json_encode([]),
                'status_logs' => json_encode([
                    ['status' => 'Processing', 'updated_at' => now()],
                ]),
                'payment_logs' => json_encode([]),
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ];

        DB::table('orders')->insert($orders);

        $orderItems = [
            [
                'order_id' => 1,
                'product_id' => 1,
                'product_snapshot' => json_encode([
                    'name' => 'Leather Notebook',
                    'description' => 'A premium leather-bound notebook.',
                    'price' => 50.00,
                ]),
                'selected_options' => json_encode([
                    'color' => 'Black',
                    'size' => 'A5',
                ]),
                'files' => json_encode([]),
                'quantity' => 2,
                'unit_price' => 50.00,
                'total_price' => 100.00,
                'status_logs' => json_encode([
                    ['status' => 'Confirmed', 'updated_at' => now()],
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'order_id' => 1,
                'product_id' => 2,
                'product_snapshot' => json_encode([
                    'name' => 'Pen Set',
                    'description' => 'Luxury pen set with gold finish.',
                    'price' => 20.00,
                ]),
                'selected_options' => json_encode([
                    'material' => 'Gold-plated',
                ]),
                'files' => json_encode([]),
                'quantity' => 1,
                'unit_price' => 20.00,
                'total_price' => 20.00,
                'status_logs' => json_encode([
                    ['status' => 'Confirmed', 'updated_at' => now()],
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'order_id' => 2,
                'product_id' => 3,
                'product_snapshot' => json_encode([
                    'name' => 'Custom T-shirt',
                    'description' => '100% cotton T-shirt with custom print.',
                    'price' => 25.00,
                ]),
                'selected_options' => json_encode([
                    'size' => 'M',
                    'color' => 'White',
                ]),
                'files' => json_encode([]),
                'quantity' => 3,
                'unit_price' => 25.00,
                'total_price' => 75.00,
                'status_logs' => json_encode([
                    ['status' => 'Processing', 'updated_at' => now()],
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('order_items')->insert($orderItems);
    }
}
