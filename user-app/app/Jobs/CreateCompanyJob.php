<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\HubspotToken;
use App\Models\Company;

class CreateCompanyJob implements ShouldQueue
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

        $response = \Http::withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Content-Type' => 'application/json',
        ])->get("https://api.hubapi.com/crm/v3/objects/companies/{$objectId}");

        if ($response->successful()) {

            $data = $response->json()['properties'];    
            
            $companyName = $data['name'];
            $companyDomain = $data['domain'];

            # save to database
            Company::create([
                "name" => $companyName,
                "domain" => $companyDomain,
                "city" => "",
                "industry" => "",
                "address" => "",
                "phone" => "",
                "state" => "",
                "lifecyclestage" => "",
                "company_id" => $objectId
            ]);

            return response()->json(['success' => true]);
        } else {
            return response()->json(['error' => 'Failed to fetch contacts'], $response->status());
        }
    }
}
