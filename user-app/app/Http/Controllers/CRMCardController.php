<?php

namespace App\Http\Controllers;

use HubSpot\Factory;
use HubSpot\Client\Crm\Extensions\CardsDev\ApiException;
use HubSpot\Client\Crm\Extensions\CardsDev\Model\CardCreateRequest;
use HubSpot\Client\Crm\Extensions\CardsDev;
use Illuminate\Support\Facades\Config;

class CRMCardController extends Controller
{
    public function fetchSample()
    {
        $apiKey = config('services.hubspot.apikey');
        $url = "https://api.hubspot.com/crm/v3/extensions/cards-dev/sample-response?apikey={$apiKey}";

        $response = \Http::get($url);
        if ($response->successful()) {
            $data = $response->json();
            \Log::info("Sample card successfully retrieved:", $data);
        } else {
            $error = $response->json();
            \Log::error("Error retrieving sample card:", $error);
        }
    }

    public function fetchCards()
    {
        $apiKey = config('services.hubspot.apikey');
        $appId = '2551361';
        $url = "https://api.hubspot.com/crm/v3/extensions/cards-dev/{$appId}?apikey={$apiKey}";
        
        $response = \Http::get($url);
        if ($response->successful()) {
            $data = $response->json();
            \Log::info("CRM card successfully retrieved:", $data);
        } else {
            $error = $response->json();
            \Log::error("Error retrieving CRM card:", $error);
        }
    }

    public function fetchCard()
    {
        $apiKey = config('services.hubspot.apikey');
        $appId = '2551361';
        $cardId = '';
        $url = "https://api.hubspot.com/crm/v3/extensions/cards-dev/{$appId}/{$cardId}?apikey={$apiKey}";

        $response = \Http::get($url);
        if ($response->successful()) {
            $data = $response->json();
            \Log::info("CRM card successfully retrieved:", $data);
        } else {
            $error = $response->json();
            \Log::error("Error retrieving CRM card:", $error);
        }
    }

    public function createCard()
    {
        // 404 error after sending request
        $apiKey = config('services.hubspot.apikey');
        $appId = '2551361';
        $url = "https://api.hubspot.com/crm/v3/extensions/cards-dev/{$appId}?apikey={$apiKey}";
        
        $fetch1 = [
            'targetUrl' => 'https =>//www.example.com/hubspot/target',
            'objectTypes' => [
                [
                    'name' => 'contacts',
                    'propertiesToSend' => [
                        'email',
                        'firstname'
                    ]
                ]
            ]
        ];
        $display1 = [
            'properties' => [
                [
                    'name' => 'pet_name',
                    'label' => 'Pets Name',
                    'dataType' => 'STRING'
                ]
            ]
        ];
        $actions1 = [
            'baseUrls' => [
                'https =>//www.example.com/hubspot'
            ]
        ];
        $cardCreateRequest = new CardCreateRequest([
            'fetch' => $fetch1,
            'display' => $display1,
            'title' => 'PetSpot',
            'actions' => $actions1,
        ]);
        
        $response = \Http::post($url, $cardCreateRequest);

        if ($response->successful()) {
            $data = $response->json();
            \Log::info("CRM card successfully created:", $data);
        } else {
            $error = $response->json();
            \Log::error("Error creating CRM card:", $error);
        }
    }

    public function updateCard()
    {
        $apiKey = config('services.hubspot.apikey');
        $appId = '2551361';
        $cardId = '';
        $url = "https://api.hubspot.com/crm/v3/extensions/cards-dev/{$appId}/{$cardId}?apikey={$apiKey}";

        $fetch1 = [
            'targetUrl' => 'https =>//www.example.com/hubspot/target',
            'objectTypes' => [
                [
                    'name' => 'contacts',
                    'propertiesToSend' => [
                        'email',
                        'firstname'
                    ]
                ]
            ]
        ];
        $display1 = [
            'properties' => [
                [
                    'name' => 'pet_name',
                    'label' => 'Pets Name',
                    'dataType' => 'STRING'
                ]
            ]
        ];
        $actions1 = [
            'baseUrls' => [
                'https =>//www.example.com/hubspot'
            ]
        ];
        $cardPatchRequest = new CardPatchRequest([
            'fetch' => $fetch1,
            'display' => $display1,
            'title' => 'PetSpot',
            'actions' => $actions1,
        ]);

        $response = \Http::patch($url, $cardPatchRequest);
        if ($response->successful()) {
            $data = $response->json();
            \Log::info("CRM card successfully updated:", $data);
        } else {
            $error = $response->json();
            \Log::error("Error updating CRM card:", $error);
        }
    }

    public function deleteCard()
    {
        $apiKey = config('services.hubspot.apikey');
        $appId = '2551361';
        $cardId = '';
        $url = "https://api.hubspot.com/crm/v3/extensions/cards-dev/{$appId}/{$cardId}?apikey={$apiKey}";

        $response = \Http::delete($url);
        if ($response->successful()) {
            $data = $response->json();
            \Log::info("CRM card successfully deleted:", $data);
        } else {
            $error = $response->json();
            \Log::error("Error deleting CRM card:", $error);
        }
    }
}
