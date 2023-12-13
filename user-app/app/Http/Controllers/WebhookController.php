<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Product;
use App\Models\Contact;

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
        
        $token = '';
        $response = \Http::withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Content-Type' => 'application/json',
        ])->get("https://api.hubapi.com/crm/v3/objects/contacts/{$objectId}");

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

    # handle property change info (update)
    private function handleContactProperty($event) {
        $objectId = $event['objectId'];
        $propertyName = $event['propertyName'];
        $newPropertyValue = $event['propertyValue'];

        $this->updateContact($objectId, $propertyName, $newPropertyValue);
    }

    private function updateContact($objectId, $propertyName, $newPropertyValue) {

        // locate contact in database
        $contact = Contact::where('contact_id', $objectId)->first();
        if ($contact) {
            $contact->email = $newPropertyValue;
            $contact->save();
        }

    }

    # handle deleting contact
    private function handleContactDelete($event) {
        $objectId = $event['objectId'];
        $this->deleteContact($objectId);
    }
    private function deleteContact($objectId) {
        $token = "";

        $response = \Http::delete("https://api.hubapi.com/crm/v3/objects/contacts/{$objectId}");

        if (!$response->successful()) {
            \Log::error("Failed to delete contact from HubSpot API: {$response->status()} - {$response->body()}");
        }

        $contact = Contact::where('contact_id', $objectId)->first();
        if ($contact) {
            $contact->delete();
            \Log::info("Contact deleted from HubSpot and database: {$objectId}");
        }
    }

}
