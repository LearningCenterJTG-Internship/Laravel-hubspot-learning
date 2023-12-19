<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\HubspotToken;
use App\Models\Ticket;

class CreateTicketJob implements ShouldQueue
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
        ])->get("https://api.hubapi.com/crm/v3/properties/tickets/{$objectId}");

        if ($response->successful()) {

            $data = $response->json()['properties']; 
            
            $hs_pipeline = $data['hs_pipeline'];
            $hs_pipeline_stage = $data['hs_pipeline_stage'];
            $hs_ticket_priority = $data['hs_ticket_priority'];
            $subject = $data['subject'];

            Ticket::create([
                "hs_pipeline" => $hs_pipeline,
                "hs_pipeline_stage" => $hs_pipeline_stage,
                "hs_ticket_priority"=> $hs_ticket_priority,
                "subject" => $subject,
                "ticket_id" => $objectId
            ]);

            return response()->json(['success' => true]);
        } else {
 
            return response()->json(['error' => 'Failed to fetch tickets'], $response->status());
        }
    }
}
