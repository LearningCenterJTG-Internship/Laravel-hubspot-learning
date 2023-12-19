<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use HubSpot\Client\Auth\OAuth\ApiException;
use Illuminate\Support\Facades\Config;
use \App\Models\HubspotToken;
use App\Models\Contact;

class ContactController extends Controller
{
    # show contact form
    public function showForm() {
        return view('uploadContact');
    }

    # upload contact to hubspot
    public function uploadContact(Request $request) {
        
        $contactData = $request->only([
            'email',
            'firstname',
            'lastname',
            'phone',
            'company',
            'website',
            'lifecyclestage',
        ]);

        $this->createContact($contactData);

        echo "Success";
    }


    public function createContact($contact) {
        $token = HubspotToken::latest()->first()->getAccessToken();
        
        $response = \Http::withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Content-Type' => 'application/json',
        ])->post('https://api.hubapi.com/crm/v3/objects/contacts', [
            'properties' => $contact,
        ]);

        if ($response->successful()) {
            $responseData = $response->json();
            dd('Contact uploaded successfully:', $responseData);
        } else {
            dd('Error uploading:', $response->body());
        }
    }


    # associate contact with company
    public function ccAssociation(Request $request) {
        $token = HubspotToken::latest()->first()->getAccessToken();
        $contactId = $request->input('contact_id');
        $companyId = $request->input('company_id');

        $url = "https://api.hubapi.com/crm/v4/associations/0-1/0-2/batch/associate/default";

        $requestData = [
            "objectIds" => ["{$contactId}", "{$companyId}"]
        ];

        $response = \Http::withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Content-Type' => 'application/json',
        ])->post($url, $requestData);

        if ($response->successful()) {
            \Log::info($response->body());
            $responseData = $response->json();
        } else {
            \Log::error('Error associating:', $response->body());
        }
    }
    
}

