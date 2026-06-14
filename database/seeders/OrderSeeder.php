<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\OrderStatus;
use App\Models\Medicine;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use Illuminate\Database\Seeder;

final class OrderSeeder extends Seeder
{
    public function run(): void
    {
        $customer = User::query()->where('email', 'test@example.com')->first();

        if (! $customer instanceof User) {
            return;
        }

        $medicines = Medicine::all();

        if ($medicines->isEmpty()) {
            return;
        }

        $orders = [
            [
                'status' => OrderStatus::Pending,
                'notes' => 'Please deliver before noon',
                'items' => [
                    ['medicine_index' => 0, 'quantity' => 2],
                    ['medicine_index' => 3, 'quantity' => 1],
                ],
            ],
            [
                'status' => OrderStatus::Confirmed,
                'notes' => 'Ring the doorbell twice',
                'items' => [
                    ['medicine_index' => 6, 'quantity' => 1],
                    ['medicine_index' => 7, 'quantity' => 3],
                    ['medicine_index' => 9, 'quantity' => 1],
                ],
            ],
            [
                'status' => OrderStatus::Completed,
                'notes' => null,
                'admin_notes' => 'Delivered on time',
                'items' => [
                    ['medicine_index' => 1, 'quantity' => 1],
                    ['medicine_index' => 4, 'quantity' => 2],
                ],
            ],
            [
                'status' => OrderStatus::Completed,
                'notes' => 'Leave at reception',
                'admin_notes' => 'Left with reception staff',
                'items' => [
                    ['medicine_index' => 11, 'quantity' => 1],
                    ['medicine_index' => 14, 'quantity' => 3],
                ],
            ],
            [
                'status' => OrderStatus::Rejected,
                'notes' => 'Need urgently',
                'admin_notes' => 'Prescription required but not provided',
                'items' => [
                    ['medicine_index' => 2, 'quantity' => 1],
                ],
            ],
            [
                'status' => OrderStatus::Pending,
                'notes' => null,
                'items' => [
                    ['medicine_index' => 5, 'quantity' => 1],
                    ['medicine_index' => 8, 'quantity' => 2],
                ],
            ],
        ];

        foreach ($orders as $orderData) {
            $totalPrice = 0;

            foreach ($orderData['items'] as $item) {
                $medicineIndex = $item['medicine_index'] % $medicines->count();
                $medicine = $medicines->get($medicineIndex);

                if ($medicine instanceof Medicine) {
                    $totalPrice += (float) $medicine->price * $item['quantity'];
                }
            }

            $order = Order::query()->create([
                'user_id' => $customer->id,
                'status' => $orderData['status'],
                'total_price' => $totalPrice,
                'notes' => $orderData['notes'] ?? null,
                'admin_notes' => $orderData['admin_notes'] ?? null,
            ]);

            foreach ($orderData['items'] as $item) {
                $medicineIndex = $item['medicine_index'] % $medicines->count();
                $medicine = $medicines->get($medicineIndex);

                if ($medicine instanceof Medicine) {
                    OrderItem::query()->create([
                        'order_id' => $order->id,
                        'medicine_id' => $medicine->id,
                        'quantity' => $item['quantity'],
                        'unit_price' => $medicine->price,
                    ]);
                }
            }
        }
    }
}
