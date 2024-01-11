<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \App\Models\HubspotToken;

class CRMFeatureController extends Controller
{
    # send search request for specific object type
    # filter issue
    public function searchRequest(Request $request) {

        $searchType = $request->input('searchType');
        $filterProperty = $request->input('propertyName');
        $filterValue = $request->input('propertyValue');
        
        $token = HubspotToken::latest()->first()->getAccessToken();

        if (empty($searchType)) {
            \Log::error("search type is required.");
            return response()->json(['error' => 'searchType is required.'], 400);
        }

        $filters = array(
            'filters' => array(
                array(
                    'value' => $filterValue,
                    'propertyName' => $filterProperty,
                    'operator' => 'EQ',
                ),
            ),
        );
        
        $searchParameters = ['filter_groups' => $filters];        

        
        $response =  \Http::withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Content-Type' => 'application/json',
        ])->post("https://api.hubapi.com/crm/v3/objects/{$searchType}/search", $searchParameters);

        if ($response->successful()) {
            \Log::info("Search success.");
            return $response->json();
        } else {
            \Log::error("Failed to search {$searchType}: ",  ['response_body' => $response->body()]);
            return response()->json(['error' => "Failed to search {$searchType}."], $response->status());
        }
    }

    # need to update account
    public function customObject(Request $request) {
        
        $customSchemas = $request->json()->all();
        $token = HubspotToken::latest()->first()->getAccessToken();

        $response =  \Http::withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Content-Type' => 'application/json',
        ])->post("https://api.hubspot.com/crm/v3/schemas", $customSchemas);

        if ($response->successful()) {
            \Log::info("Custom object creation success.");
        } else {
            \Log::error("Failed to create: ",  ['response_body' => $response->body()]);
        }
    }

    # create CRM cards
    public function createCard() {

        $token = HubspotToken::latest()->first()->getAccessToken();
        
        $cardData = [
            'fetch' => [
                'targetUrl' => 'https://www.example.com/demo-fetch',
                'objectTypes' => [
                    [
                        'name' => 'contacts',
                        'propertiesToSend' => [
                            'email',
                            'firstname',
                        ],
                    ],
                ],
            ],
            'display' => [
                'properties' => [
                    [
                        'name' => 'pet_name',
                        'label' => 'Pets Name',
                        'dataType' => 'STRING',
                    ],
                ],
            ],
            'actions' => [
                'baseUrls' => [
                    'https://www.example.com/demo-fetch',
                ],
            ],
            'title' => 'PetSpot',
        ];

        try {
            $response = \Http::withHeaders([
                'Authorization' => 'Bearer ' . $token,
                'Content-Type' => 'application/json',
            ])->post('https://api.hubapi.com/crm/v3/extensions/cards-dev/{2551361}', $cardData);
        
            if ($response->successful()) {
                $responseData = $response->json();
                var_dump($responseData);
            } else {
                echo 'Error creating CRM card: ' . $response->body();
            }
        } catch (\Exception $e) {
            echo 'Exception: ' . $e->getMessage();
        }
    }
}
