<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\PaymentMethod;

class PaymentMethodApiTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_create_a_payment_method()
    {
        $payload = [
            'method_name' => 'Transfer Bank',
            'details'     => 'Rekening BCA 1234567890 a.n PT Lantana'
        ];

        $response = $this->postJson('/api/payment-methods', $payload);

        $response->assertStatus(201)
                 ->assertJson([
                     'success' => true,
                     'data' => [
                         'method_name' => 'Transfer Bank',
                         'details' => 'Rekening BCA 1234567890 a.n PT Lantana'
                     ],
                     'totalData' => 1,
                     'page' => 1,
                     'limit' => 1
                 ]);

        $this->assertDatabaseHas('payment_methods', $payload);
    }

    /** @test */
    public function it_fails_validation_when_method_name_is_missing()
    {
        $response = $this->postJson('/api/payment-methods', [
            'details' => 'Tanpa nama metode'
        ]);

        $response->assertStatus(422)
                 ->assertJson([
                     'success' => false,
                     'message' => 'Validation failed'
                 ])
                 ->assertJsonStructure([
                     'errors' => ['method_name']
                 ]);
    }
}
