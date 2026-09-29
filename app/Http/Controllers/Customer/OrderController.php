<?php

namespace App\Http\Controllers\Customer;

use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Customer\Order\StoreOrderRequest;
use App\Models\Order;
use App\Traits\UploadAble;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    use UploadAble;

    public function store(StoreOrderRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $imagePath = null;
        if ($request->hasFile('image_list')) {
            $imagePath = $this->uploadFile($request->file('image_list'), 'order_attachments/images');
        }

        $voicePath = null;
        if ($request->hasFile('voice_list')) {
            $voicePath = $this->uploadFile($request->file('voice_list'), 'order_attachments/voices');
        }

        $rawText = ! empty($validated['raw_text_list']) ? trim((string) $validated['raw_text_list']) : null;

        $order = Order::create([
            'customer_name' => $validated['customer_name'],
            'phone' => $validated['phone'],
            'address' => $validated['address'],
            'raw_text_list' => $rawText,
            'image_list_path' => $imagePath,
            'voice_list_path' => $voicePath,
            'status' => OrderStatus::PENDING,
        ]);

        return responseSuccess(
            $order,
            'আপনার অর্ডারটি সফলভাবে গ্রহণ করা হয়েছে! কিছুক্ষণের মধ্যে কল করে কনফার্ম করা হবে।',
            201
        );
    }

    public function show($id): JsonResponse
    {
        $order = Order::with('items')->find($id);

        if (! $order) {
            return responseError('অর্ডার পাওয়া যায়নি।', 404);
        }

        return responseSuccess($order, 'Order retrieved successfully');
    }

    public function index(Request $request): JsonResponse
    {
        $orders = Order::with('items')
            ->latest()
            ->paginate($request->input('per_page', 20));

        return responseSuccess([
            'items' => $orders->items(),
            'pagination' => pagination($orders),
        ], 'Orders retrieved successfully');
    }
}
