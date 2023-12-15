<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Contact;

class DeleteContactJob implements ShouldQueue
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
        $response = \Http::delete("https://api.hubapi.com/crm/v3/objects/contacts/{$this->objectId}");

        if (!$response->successful()) {
            \Log::error("Failed to delete contact from HubSpot API: {$response->status()} - {$response->body()}");
        }

        $contact = Contact::where('contact_id', $this->objectId)->first();
        if ($contact) {
            $contact->delete();
            \Log::info("Contact deleted from HubSpot and database: {$this->objectId}");
        }
    }
}
