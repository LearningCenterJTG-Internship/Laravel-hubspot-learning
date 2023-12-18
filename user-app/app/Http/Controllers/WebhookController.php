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

class WebhookController extends Controller
{

    # process the webhook payload
    # current available sync action:
    # - create new contact
    # - delete contact
    # - update email
    public function webhookProcess(Request $request) {
        $data = $request->all();

        \Log::info($data);

        try {
            foreach($data as $event) {
                
                if ($event['subscriptionType'] === 'contact.creation') {
                    $this->handleNewContact($event);
                } else if($event['subscriptionType'] === 'contact.propertyChange') {
                    $this->handleContactProperty($event);
                } else if ($event['subscriptionType'] === 'contact.deletion') {
                    $this->handleContactDelete($event);
                } else if ($event['subscriptionType'] === 'company.creation') {
                    $this->handleNewCompany($event);
                } else if($event['subscriptionType'] === 'company.propertyChange') {
                    $this->handleCompanyProperty($event);
                } else if ($event['subscriptionType'] === 'company.deletion') {
                    $this->handleCompanyDelete($event);
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
        CreateContactJob::dispatch($objectId)->onQueue('default');
        return response()->json(['success' => true]);
    }

    # handle property change info (update)
    private function handleContactProperty($event) {
        $objectId = $event['objectId'];
        $propertyName = $event['propertyName'];
        $newPropertyValue = $event['propertyValue'];

        UpdateContactJob::dispatch($objectId, $propertyName, $newPropertyValue)->onQueue('default');
    }

    # handle deleting contact
    private function handleContactDelete($event) {
        $objectId = $event['objectId'];
        DeleteContactJob::dispatch($objectId)->onQueue('default');
    }

    private function handleNewCompany($event) {
        $objectId = $event['objectId'];
        CreateCompanyJob::dispatch($objectId)->onQueue('default');
        return response()->json(['success' => true]);
    }

    private function handleCompanyProperty($event) {
        $objectId = $event['objectId'];
        $propertyName = $event['propertyName'];
        $newPropertyValue = $event['propertyValue'];

        UpdateCompanyJob::dispatch($objectId, $propertyName, $newPropertyValue)->onQueue('default');
    }

    private function handleCompanyDelete($event) {
        $objectId = $event['objectId'];
        DeleteCompanyJob::dispatch($objectId)->onQueue('default');
    }
}
