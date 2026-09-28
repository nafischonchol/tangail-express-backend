<?php

namespace Tests\Feature;

use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class OrderApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_health_check(): void
    {
        $response = $this->getJson('/api/health');
        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Tangail Express API is healthy',
            ]);
    }

    public function test_validation_fails_if_required_fields_are_missing(): void
    {
        $response = $this->postJson('/api/orders', []);
        $response->assertStatus(422)
            ->assertJsonStructure([
                'success',
                'message',
                'errors' => ['customer_name', 'phone', 'address'],
            ]);
    }

    public function test_validation_fails_if_no_list_is_provided(): void
    {
        $response = $this->postJson('/api/orders', [
            'customer_name' => 'John Doe',
            'phone' => '01712345678',
            'address' => 'Akua, Mymensingh Road, Tangail',
        ]);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
                'message' => 'দয়া করে বাজারের লিস্ট লিখুন, ছবি দিন অথবা ভয়েস রেকর্ড করুন।',
            ]);
    }

    public function test_can_create_order_with_text_list(): void
    {
        $response = $this->postJson('/api/orders', [
            'customer_name' => 'Nafis Ahmed',
            'phone' => '01812345678',
            'address' => 'Victoria Road, Tangail Sadar',
            'raw_text_list' => "১ কেজি পেঁয়াজ\n১ ডজন ডিম\n৫০০ গ্রাম আদা",
        ]);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
            ])
            ->assertJsonPath('resources.customer_name', 'Nafis Ahmed')
            ->assertJsonPath('resources.status', 'pending');

        $this->assertDatabaseHas('orders', [
            'customer_name' => 'Nafis Ahmed',
            'phone' => '01812345678',
            'status' => 'pending',
        ]);
    }

    public function test_can_create_order_with_image_upload(): void
    {
        Storage::fake('public');

        $file = UploadedFile::fake()->image('bazar_list.jpg', 600, 800);

        $response = $this->postJson('/api/orders', [
            'customer_name' => 'Tanvir Hossain',
            'phone' => '01700000000',
            'address' => 'College Mor, Tangail',
            'image_list' => $file,
        ]);

        $response->assertStatus(201)
            ->assertJson(['success' => true]);

        $order = Order::first();
        $this->assertNotNull($order->image_list_path);
        Storage::disk('public')->assertExists($order->image_list_path);
    }

    public function test_can_create_order_with_voice_recording(): void
    {
        Storage::fake('public');

        $audioFile = UploadedFile::fake()->create('voice_note.webm', 500, 'audio/webm');

        $response = $this->postJson('/api/orders', [
            'customer_name' => 'Faruk Mia',
            'phone' => '01900000000',
            'address' => 'Baby Stand, Tangail',
            'voice_list' => $audioFile,
        ]);

        $response->assertStatus(201)
            ->assertJson(['success' => true]);

        $order = Order::first();
        $this->assertNotNull($order->voice_list_path);
        Storage::disk('public')->assertExists($order->voice_list_path);
    }
}
