<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Company;

class DeleteCompanyJob implements ShouldQueue
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
        
        $response = \Http::delete("https://api.hubapi.com/crm/v3/objects/companies/{$this->objectId}");

        if (!$response->successful()) {
            \Log::error("Failed to delete company from HubSpot API: {$response->status()} - {$response->body()}");
        }

        $company = Company::where('company_id', $this->objectId)->first();
        if ($company) {
            $company->delete();
            \Log::info("Company deleted from HubSpot and database: {$this->objectId}");
        }
    }
}
