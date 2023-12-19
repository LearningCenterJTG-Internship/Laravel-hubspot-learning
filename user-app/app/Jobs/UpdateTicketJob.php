<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Ticket;

class UpdateTicketJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $objectId;
    protected $propertyName;
    protected $newPropertyValue;

    /**
     * Create a new job instance.
     */
    public function __construct($objectId, $propertyName, $newPropertyValue)
    {
        $this->objectId = $objectId;
        $this->propertyName = $propertyName;
        $this->newPropertyValue = $newPropertyValue;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $objectId = $this->objectId;
        $propertyName = $this->propertyName;
        $newPropertyValue = $this->newPropertyValue;

        $ticket = Ticket::where('ticket_id', $objectId)->first();
        if ($ticket) {
            $ticket->{$this->propertyName} = $this->newPropertyValue;
            $ticket->save();
        }
    }
}
