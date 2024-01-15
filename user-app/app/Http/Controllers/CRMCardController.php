<?php

namespace App\Http\Controllers;

use HubSpot\Factory;
use HubSpot\Client\Crm\Extensions\CardsDev\ApiException;
use HubSpot\Client\Crm\Extensions\CardsDev\Model\CardCreateRequest;

class CRMCardController extends Controller
{
    public function fetchSample()
    {
        $apiKey = config('HUBSPOT_API_KEY');
        $client = Factory::createWithApiKey($apiKey);

        try {
            $apiResponse = $client->crm()->extensions()->cardsDev()->sampleresponseApi()->getCardsSampleResponse();
            var_dump($apiResponse);
        } catch (ApiException $e) {
            echo "Exception when calling sampleresponse_api->get_cards_sample_response: ", $e->getMessage();
        }
    }

    public function fetchCard()
    {
        $apiKey = config('HUBSPOT_API_KEY');
        $client = Factory::createWithDeveloperApiKey($apiKey);

        try {
            $apiResponse = $client->crm()->extensions()->cardsDev()->cardsApi()->getAll(100);
            var_dump($apiResponse);
        } catch (ApiException $e) {
            echo "Exception when calling cards_api->get_all: ", $e->getMessage();
        }
    }

    public function createCard()
    {
        $apiKey = config('HUBSPOT_API_KEY');
        $client = Factory::createWithDeveloperApiKey($apiKey);
        
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
        try {
            $apiResponse = $client->crm()->extensions()->cardsDev()->cardsApi()->create(100, $cardCreateRequest);
            var_dump($apiResponse);
        } catch (ApiException $e) {
            echo "Exception when calling cards_api->create: ", $e->getMessage();
        }
    }
}
