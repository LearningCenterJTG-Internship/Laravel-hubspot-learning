<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class WebhookTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_example(): void
    {
        $webhookData = [
  
            'properties' => [
                'firstname' => 'John',
                'lastname' => 'Does',
                'email' => 'john.does@example.com',
                'phone' => '12345678',
                'company' => 'hubSPOT',
                'website' => 'hubspot.com',
                'lifecyclestage' => 'lead'
            ],

        ];

        $response = $this->post('/hubspot/contact', $webhookData);

        $response->assertJson(['success' => true]);
    }
}
