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
            var_dump($data);
        } else {
            $error = $response->json();
            var_dump($error);
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
            var_dump($data);
        } else {
            $error = $response->json();
            var_dump($error);
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
            var_dump($data);
        } else {
            $error = $response->json();
            var_dump($error);
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
            var_dump($data);
        } else {
            $error = $response->json();
            var_dump($error);
        }
    }
}
