<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use HubSpot\Factory;
use HubSpot\Client\Crm\Extensions\CardsDev\ApiException;

class CRMCardController extends Controller
{
    public function fetchCard()
    {
        $client = Factory::createWithApiKey("b5e3eb13-c1cc-4409-a55f-63ee3cf49807");

        try {
            $apiResponse = $client->crm()->extensions()->cardsDev()->sampleresponseApi()->getCardsSampleResponse();
            var_dump($apiResponse);
        } catch (ApiException $e) {
            echo "Exception when calling sampleresponse_api->get_cards_sample_response: ", $e->getMessage();
        }
    }
}
