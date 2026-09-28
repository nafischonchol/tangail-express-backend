<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{
    /**
     * Store a new zero-friction grocery order from customer.
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'customer_name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:30'],
            'address' => ['required', 'string', 'max:1000'],
            'raw_text_list' => ['nullable', 'string', 'max:10000'],
            'image_list' => ['nullable', 'file', 'max:15360'], // 15MB
            'voice_list' => ['nullable', 'file', 'max:30720'], // 30MB
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'প্রয়োজনীয় তথ্য সঠিকভাবে পূরণ করুন।',
                'errors' => $validator->errors(),
            ], 422);
        }

        $rawText = trim((string) $request->input('raw_text_list'));
        $hasImage = $request->hasFile('image_list');
        $hasVoice = $request->hasFile('voice_list');

        if (empty($rawText) && !$hasImage && !$hasVoice) {
            return response()->json([
                'success' => false,
                'message' => 'দয়া করে বাজারের লিস্ট লিখুন, ছবি দিন অথবা ভয়েস রেকর্ড করুন।',
                'errors' => [
                    'list' => ['যেকোনো একটি উপায়ে বাজারের লিস্ট প্রদান করা আবশ্যক।']
                ],
            ], 422);
        }

        $imagePath = null;
        if ($hasImage) {
            $imagePath = $request->file('image_list')->store('order_attachments/images', 'public');
        }

        $voicePath = null;
        if ($hasVoice) {
            $voicePath = $request->file('voice_list')->store('order_attachments/voices', 'public');
        }

        $order = Order::create([
            'customer_name' => $request->input('customer_name'),
            'phone' => $request->input('phone'),
            'address' => $request->input('address'),
            'raw_text_list' => !empty($rawText) ? $rawText : null,
            'image_list_path' => $imagePath,
            'voice_list_path' => $voicePath,
            'status' => 'pending',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'আপনার অর্ডারটি সফলভাবে গ্রহণ করা হয়েছে! কিছুক্ষণের মধ্যে কল করে কনফার্ম করা হবে।',
            'resources' => $order,
        ], 201);
    }

    /**
     * Get details of a single order.
     */
    public function show($id): JsonResponse
    {
        $order = Order::with('items')->find($id);

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'অর্ডার পাওয়া যায়নি।',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Order retrieved successfully',
            'resources' => $order,
        ]);
    }

    /**
     * List all orders (useful for API/Admin).
     */
    public function index(Request $request): JsonResponse
    {
        $orders = Order::with('items')
            ->latest()
            ->paginate($request->input('per_page', 20));

        return response()->json([
            'success' => true,
            'message' => 'Orders retrieved successfully',
            'resources' => $orders->items(),
            'pagination' => [
                'current_page' => $orders->currentPage(),
                'last_page' => $orders->lastPage(),
                'per_page' => $orders->perPage(),
                'total' => $orders->total(),
            ],
        ]);
    }
}
