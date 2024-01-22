<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \App\Models\HubspotToken;

class callController extends Controller
{
    protected $hubspot_token;

    public function __construct()
    {
        $this->hubspot_token = HubspotToken::latest()->first()->getAccessToken();
    }

    public function createCall(Request $request)
    {
        $token = $this->hubspot_token;
        $callProperty = $request->input("properties");
        $callAssociation = $request->input("associations");
        $requestData = [
            "properties" => $callProperty,
            "associations" => $callAssociation
        ];

        $url = "https://api.hubapi.com/crm/v3/objects/calls";
        $response = \Http::withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Content-Type' => 'application/json',
        ])->post($url, $requestData);

        if ($response->successful()) {
            \Log::info($response->body());
            $responseData = $response->json();
        } else {
            \Log::error('Error creating call:', ['response_body' => $response->body()]);
        }
    }

    public function retrieveCall()
    {
        $token = $this->hubspot_token;
        $url = "https://api.hubapi.com/crm/v3/objects/calls";
        $response = \Http::withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Content-Type' => 'application/json',
        ])->get($url);

        if ($response->successful()) {
            \Log::info($response->body());
            $responseData = $response->json();
        } else {
            \Log::error('Error retrieving call:', ['response_body' => $response->body()]);
        }
    }

    # voicemail - hs_call_status->missed & hs_call_has_voicemail ->true
    public function updateCall(Request $request)
    {
        $token = $this->hubspot_token;
        $callId = "46041760274";
        $url = "https://api.hubapi.com/crm/v3/objects/calls/{$callId}";

        $properties = $request->input("properties");
        $requestData = [
            "properties" => $properties,
        ];
        $response = \Http::withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Content-Type' => 'application/json',
        ])->put($url, $requestData);

        if ($response->successful()) {
            \Log::info($response->body());
            $responseData = $response->json();
        } else {
            \Log::error('Error updating call:', ['response_body' => $response->body()]);
        }
    }

    public function deleteCall()
    {
        $token = $this->hubspot_token;
        $callId = "46041760274";
        $url = "https://api.hubapi.com/crm/v3/objects/calls/{$callId}";

        $response = \Http::withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Content-Type' => 'application/json',
        ])->delete($url);

        if ($response->successful()) {
            \Log::info($response->body());
            $responseData = $response->json();
        } else {
            \Log::error('Error deleting call:', ['response_body' => $response->body()]);
        }
    }

    public function callAssociation(Request $request)
    {
        $callId = $request->input("callId");
        $toObjectType = $request->input("toObjectType");
        $toObjectId = $request->input("toObjectId");
        $associationTypeId = $request->input("associationTypeId");

        $token = $this->hubspot_token;
        $url = "https://api.hubapi.com/crm/v3/objects/calls/{$callId}/associations/{$toObjectType}/{$toObjectId}/{$associationTypeId}";

        $response = \Http::withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Content-Type' => 'application/json',
        ])->put($url);

        if ($response->successful()) {
            \Log::info($response->body());
            $responseData = $response->json();
        } else {
            \Log::error("Error associating call with object {$toObjectId}:", ['response_body' => $response->body()]);
        }
    }

}
