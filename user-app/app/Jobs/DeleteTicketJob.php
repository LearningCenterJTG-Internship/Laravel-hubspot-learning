<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Ticket;

class DeleteTicketJob implements ShouldQueue
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
    public function handle(): void
    {
        $response = \Http::delete("https://api.hubapi.com/crm/v3/objects/tickets/{$this->objectId}");

        if (!$response->successful()) {
            \Log::error("Failed to delete ticket from HubSpot API: {$response->status()} - {$response->body()}");
        }

        $ticket = Ticket::where('ticket_id', $this->objectId)->first();
        if ($ticket) {
            $ticket->delete();
            \Log::info("Ticket deleted from HubSpot and database: {$this->objectId}");
        }
    }
}
