<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \App\Models\HubspotToken;

class communicationController extends Controller
{
    protected $hubspot_token;

    public function __construct()
    {
        $this->hubspot_token = HubspotToken::latest()->first()->getAccessToken();
    }

    public function createMsg(Request $request)
    {
        $token = $this->hubspot_token;
        $msgProperty = $request->input("properties");
        $msgAssociation = $request->input("associations");
        $allowedTypes = ["WHATS_APP", "LINKEDIN_MESSAGE", "SMS"];

        if (isset($msgProperty['hs_communication_channel_type']) && in_array($msgProperty['hs_communication_channel_type'], $allowedChannelTypes)) {
            $requestData = [
                "properties" => $msgProperty,
                "associations" => $msgAssociation
            ];
        } else {
            \Log::error("Invalid hs_communication_channel_type value. Allowed values are WHATS_APP, LINKEDIN_MESSAGE, or SMS.");
        }

        if ($msgProperty['hs_communication_logged_from'] == "CRM")
        {
            $requestData = [
                "properties" => $msgProperty,
                "associations" => $msgAssociation
            ];
        } else {
            \Log::error("hs_communication_logged_from can only be set to 'CRM'");
        }

        $url = "https://api.hubapi.com/crm/v3/objects/communications";
        $response = \Http::withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Content-Type' => 'application/json',
        ])->post($url, $requestData);

        if ($response->successful()) {
            \Log::info($response->body());
            $responseData = $response->json();
        } else {
            \Log::error('Error creating message:', ['response_body' => $response->body()]);
        }
    }

    public function retrieveMsg()
    {
        $token = $this->hubspot_token;
        $url = "https://api.hubapi.com/crm/v3/objects/communications";
        $response = \Http::withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Content-Type' => 'application/json',
        ])->get($url);

        if ($response->successful()) {
            \Log::info($response->body());
            $responseData = $response->json();
        } else {
            \Log::error('Error retrieving message:', ['response_body' => $response->body()]);
        }
    }

    public function updateMsg(Request $request)
    {
        $token = $this->hubspot_token;
        $communicationId = "";
        $url = "https://api.hubapi.com/crm/v3/objects/communications/{$communicationId}";

        $properties = $request->input("properties");
        $requestData = [
            "properties" => $properties,
        ];
        $response = \Http::withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Content-Type' => 'application/json',
        ])->patch($url, $requestData);

        if ($response->successful()) {
            \Log::info($response->body());
            $responseData = $response->json();
        } else {
            \Log::error('Error updating message:', ['response_body' => $response->body()]);
        }
    }

    public function msgAssociation(Request $request)
    {
        $communicationId = $request->input("callId");
        $toObjectType = $request->input("toObjectType");
        $toObjectId = $request->input("toObjectId");
        $associationTypeId = $request->input("associationTypeId");

        $token = $this->hubspot_token;
        $url = "https://api.hubapi.com/crm/v3/objects/calls/{$communicationId}/associations/{$toObjectType}/{$toObjectId}/{$associationTypeId}";

        $response = \Http::withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Content-Type' => 'application/json',
        ])->put($url);

        if ($response->successful()) {
            \Log::info($response->body());
            $responseData = $response->json();
        } else {
            \Log::error("Error associating message with object {$toObjectId}:", ['response_body' => $response->body()]);
        }
    }

    public function deleteMsg()
    {
        $token = $this->hubspot_token;
        $communicationId = "";
        $url = "https://api.hubapi.com/crm/v3/objects/calls/{$communicationId}";

        $response = \Http::withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Content-Type' => 'application/json',
        ])->delete($url);

        if ($response->successful()) {
            \Log::info($response->body());
            $responseData = $response->json();
        } else {
            \Log::error('Error deleting message:', ['response_body' => $response->body()]);
        }
    }
}
