<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\HubspotToken;
use App\Models\Contact;

class CreateContactJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $objectId;

    /**
     * Create a new job instance.
     */
    public function __construct($objectId)
    {
        $this->objectId = $objectId;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {   
        $objectId = $this->objectId;

        $token = HubspotToken::latest()->first()->getAccessToken();

        \Log::info("this is webhook id11: " . objectId);

        $response = \Http::withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Content-Type' => 'application/json',
        ])->get("https://api.hubapi.com/crm/v3/objects/contacts/{objectId}");

        if ($response->successful()) {
            $data = $response->json()['properties'];
            
            $firstName = $data['firstname'];
            $lastName = $data['lastname'];
            $email = $data['email'];
            $phone = "";
            $company = "";
            $website = "";
            $lifecyclestage = "";
            
            # save to database
            Contact::create([
                'first_name' => $firstName,
                'last_name' => $lastName,
                'email' => $email,
                'phone' => $phone,
                'company' => $company,
                'website' => $website,
                'lifecyclestage' => $lifecyclestage,
                'contact_id' => $objectId
            ]);

            return response()->json(['success' => true]);
        } else {
            return response()->json(['error' => 'Failed to fetch contacts'], $response->status());
        }
    }
}
