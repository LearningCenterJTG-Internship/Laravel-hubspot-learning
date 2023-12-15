<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Product;
use App\Models\Contact;
use \App\Models\HubspotToken;
use App\Jobs\CreateContactJob;
use App\Jobs\DeleteContactJob;
use App\Jobs\UpdateContactJob;

class WebhookController extends Controller
{

    # process the webhook payload
    # current available sync action:
    # - create new contact
    # - delete contact
    # - update email
    public function webhookProcess(Request $request) {
        $data = $request->all();

        try {
            foreach($data as $event) {
                
                if ($event['subscriptionType'] === 'contact.creation') {
                    $this->handleNewContact($event);
                } else if($event['subscriptionType'] === 'contact.propertyChange') {
                    $this->handleContactProperty($event);
                } else if ($event['subscriptionType'] === 'contact.deletion') {
                    $this->handleContactDelete($event);
                }
            }

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            \Log::error("Payload processing failed: " . $e->getMessage());
        }
    }

    # handle new contact info (create)
    private function handleNewContact($event) {
        $objectId = $event['objectId'];
        \Log::info("this is webhook id: " . $objectId);
        CreateContactJob::dispatch($objectId)->onQueue('hubspot-contact-fetch');
        return response()->json(['success' => true]);
    }

    # handle property change info (update)
    private function handleContactProperty($event) {
        $objectId = $event['objectId'];
        $propertyName = $event['propertyName'];
        $newPropertyValue = $event['propertyValue'];

        UpdateContactJob::dispatch($objectId, $propertyName, $newPropertyValue)->onQueue('update-contact-property');
    }


    # handle deleting contact
    private function handleContactDelete($event) {
        $objectId = $event['objectId'];
        DeleteContactJob::dispatch($objectId)->onQueue('hubspot-contact-delete');
    }
}
