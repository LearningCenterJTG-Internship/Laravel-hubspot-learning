<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \App\Models\HubspotToken;

class noteController extends Controller
{
    public function createNote(Request $request)
    {
        $token = HubspotToken::latest()->first()->getAccessToken();
        $url = 'https://api.hubspot.com/crm/v3/objects/notes';

        /*$requestData = [
            "properties" => [
                "hs_timestamp" => "2021-11-12T15:48:22Z",
                "hs_note_body" => "Spoke with decision maker Carla. Attached the proposal and draft of contract.",
                "hubspot_owner_id" => "14240720",
                "hs_attachment_ids" => "24332474034;24332474044",
            ],
            "associations" => [
                [
                    "to" => [
                        "id" => 18458610693,
                    ],
                    "types" => [
                        [
                            "associationCategory" => "HUBSPOT_DEFINED",
                            "associationTypeId" => 190,
                        ],
                    ],
                ],
            ],
        ];*/

        $requestData = [
            "properties" => $request->input('properties'),
            "associations" => $request->input('associations')
        ];

        $response = \Http::withHeaders([
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $token,
        ])->post($url, $requestData);

        if ($response->successful()) {
            $data = $response->json();
            \Log::info("Note successfully created:", $data);
        } else {
            $error = $response->json();
            \Log::error("Error creating note:", $error);
        }
    }

    public function retrieveNote()
    {
        $noteId = "";
        $url = "https://api.hubspot.com/crm/v3/objects/notes/{$noteId}";
        $token = HubspotToken::latest()->first()->getAccessToken();

        $response = \Http::withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->get($url);

        if ($response->successful()) {
            $data = $response->json();
            \Log::info("Note successfully retrieved:", $data);
        } else {
            $error = $response->json();
            \Log::error("Error retrieving note:", $error);
        }
    }

    public function updateNote(Request $request)
    {
        $noteId = "";
        $url = "https://api.hubspot.com/crm/v3/objects/notes/{$noteId}";
        $token = HubspotToken::latest()->first()->getAccessToken();

        $requestData = [
            'properties' => [
                'hs_note_body' => 'Updated note content',
            ],
        ];

        $response = \Http::withHeaders([
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $token,
        ])->patch($url, $requestData);

        if ($response->successful()) {
            $data = $response->json();
            \Log::info("Note successfully updated:", $data);
        } else {
            $error = $response->json();
            \Log::error("Error updating note:", $error);
        }
    } 

    public function deleteNote()
    {
        $noteId = "";
        $url = "https://api.hubspot.com/crm/v3/objects/notes/{$noteId}";
        $token = HubspotToken::latest()->first()->getAccessToken();

        $response = \Http::withHeaders([
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $token,
        ])->delete($url);

        if ($response->successful()) {
            $data = $response->json();
            \Log::info("Note successfully deleted:", $data);
        } else {
            $error = $response->json();
            \Log::error("Error deleting note:", $error);
        }
    }

    public function mergeNote()
    {
        $mergedId = "";
        $primaryId = "";

        $url = "https://api.hubspot.com/crm/v3/objects/notes/merge";
        $token = HubspotToken::latest()->first()->getAccessToken();

        $publicMergeInput = [
            'object_id_to_merge' => $mergedId,
            'primary_object_id' => $primaryId,
        ];
        

        $response = \Http::withHeaders([
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $token,
        ])->post($url, $publicMergeInput);

        if ($response->successful()) {
            $data = $response->json();
            \Log::info("Note successfully merged:", $data);
        } else {
            $error = $response->json();
            \Log::error("Error merging note:", $error);
        }
    }

    public function GDPRdelete()
    {
        $idProperty = "";
        $objectId = "";

        $url = "https://api.hubspot.com/crm/v3/objects/notes/gdpr/purge";
        $token = HubspotToken::latest()->first()->getAccessToken();

        $publicGdprDeleteInput = [
            'id_property' => $idProperty,
            'object_id' => $objectId,
        ];
        
        $response = \Http::withHeaders([
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $token,
        ])->post($url, $publicGdprDeleteInput);
        
        if ($response->successful()) {
            $data = $response->json();
            \Log::info("GDPR deletion success:", $data);
        } else {
            $error = $response->json();
            \Log::error("GDPR deletion error:", $error);
        }
    }

    public function searchNote()
    {
        $query = "";
        $limit = 0;
        $after = "";
        $sorts = ['string'];
        $properties = ['string'];
        $propertyName = "";
        $highValue = "";
        $operator = "EQ";

        $url = "https://api.hubspot.com/crm/v3/objects/notes/search";
        $token = HubspotToken::latest()->first()->getAccessToken();

        $filter1 = [
            'high_value' => $highValue,
            'property_name' => $propertyName,
            'values' => ['string'],
            'value' => 'string',
            'operator' => $operator,
        ];

        $filterGroup1 = [
            'filters' => [$filter1],
        ];

        $publicObjectSearchRequest = [
            'query' => $query,
            'limit' => $limit,
            'after' => $after,
            'sorts' => $sorts,
            'properties' => $properties,
            'filter_groups' => [$filterGroup1],
        ];

        $response = \Http::withHeaders([
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $token,
        ])->post($url, $publicObjectSearchRequest);
        
        if ($response->successful()) {
            $data = $response->json();
            \Log::info("Note successfully searched:", $data);
        } else {
            $error = $response->json();
            \Log::error("Error searching note:", $error);
        }
    }
}
