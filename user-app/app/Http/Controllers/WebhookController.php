<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Product;
use App\Models\Contact;
use \App\Models\HubspotToken;
use App\Jobs\CreateContactJob;
use App\Jobs\DeleteContactJob;
use App\Jobs\UpdateContactJob;
use App\Jobs\CreateCompanyJob;
use App\Jobs\UpdateCompanyJob;
use App\Jobs\DeleteCompanyJob;
use App\Jobs\CreateTicketJob;
use App\Jobs\UpdateTicketJob;
use App\Jobs\DeleteTicketJob;

class WebhookController extends Controller
{

    # process the webhook payload
    # current available sync action:
    # - create new object
    # - delete object
    # - update object
    public function webhookProcess(Request $request) {
        $data = $request->all();

        try {
            $subscriptionHandlers = [
                'contact.creation' => 'handleNewContact',
                'contact.propertyChange' => 'handleContactProperty',
                'contact.deletion' => 'handleContactDelete',
                'company.creation' => 'handleNewCompany',
                'company.propertyChange' => 'handleCompanyProperty',
                'company.deletion' => 'handleCompanyDelete',
                'ticket.creation' => 'handleNewTicket',
                'ticket.propertyChange' => 'handleTicketProperty',
                'ticket.deletion' => 'handleTicketDelete',
            ];
            
            foreach ($data as $event) {
                $subscriptionType = $event['subscriptionType'];
            
                if (isset($subscriptionHandlers[$subscriptionType])) {
                    $handlerMethod = $subscriptionHandlers[$subscriptionType];
                    $this->$handlerMethod($event);
                } else {
                    \Log::error("Unknown subscription type: " . $subscriptionType);
                    return;
                }
            }
            
        } catch (\Exception $e) {
            \Log::error("Payload processing failed: " . $e->getMessage());
        }
    }

    private function dispatchJob($jobClass, $objectId, $propertyName = null, $newPropertyValue = null) {
        $dispatch = $jobClass::dispatch($objectId, $propertyName, $newPropertyValue);
        $dispatch->onQueue('default');
    }
    
    private function handleEvent($event, $jobClass) {
        $objectId = $event['objectId'];
        $propertyName = $event['propertyName'] ?? null;
        $newPropertyValue = $event['propertyValue'] ?? null;
    
        $this->dispatchJob($jobClass, $objectId, $propertyName, $newPropertyValue);
    }
    
    private function handleNewContact($event) {
        $this->handleEvent($event, CreateContactJob::class);
        return response()->json(['success' => true]);
    }
    
    private function handleContactProperty($event) {
        $this->handleEvent($event, UpdateContactJob::class);
    }
    
    private function handleContactDelete($event) {
        $this->handleEvent($event, DeleteContactJob::class);
    }

    private function handleNewCompany($event) {
        $this->handleEvent($event, CreateContactJob::class);
        return response()->json(['success' => true]);
    }

    private function handleCompanyProperty($event) {
        $this->handleEvent($event, updateCompanyJob::class);
    }

    private function handleCompanyDelete($event) {
        $this->handleEvent($event, DeleteCompanyJob::class);
    }

    private function handleNewTicket($event) {
        $this->handleEvent($event, CreateTicketJob::class);
        return response()->json(['success' => true]);
    }

    private function handleTicketProperty($event) {
        $this->handleEvent($event, updateTicketJob::class);
    }

    private function handleTicketDelete($event) {
        $this->handleEvent($event, DeleteTicketJob::class);
    }
    
}
    