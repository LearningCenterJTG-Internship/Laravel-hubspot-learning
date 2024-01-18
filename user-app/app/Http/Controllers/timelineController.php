<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \App\Models\HubspotToken;

class timelineController extends Controller
{
    public function createTemplate(Request $request)
    {
        try {
            $token = 'b5e3eb13-c1cc-4409-a55f-63ee3cf49807';
            $appId = '2551361';
    
            $url = "https://api.hubapi.com/crm/v3/timeline/{$appId}/event-templates?hapikey={$token}";
    
            $requestData = [
                'name' => 'Example Webinar Registration',
                'objectType' => 'contacts'
            ];
    
            $response = \Http::post($url, $requestData);
    
            if ($response->successful()) {
                \Log::info($response->body());
                $responseData = $response->json();
                return response()->json($responseData, 200);
            } else {
                \Log::error('Error creating event template:', $response->body());
                return response()->json(['error' => 'Failed to create event template'], $response->status());
            }
        } catch (\Exception $e) {
            \Log::error('Exception:', $e->getMessage());
            return response()->json(['error' => 'Internal Server Error'], 500);
        }
    }

    public function retrieveTemplate() 
    {
        $token = 'b5e3eb13-c1cc-4409-a55f-63ee3cf49807';
        $appId = '2551361';

        $url = "https://api.hubapi.com/crm/v3/timeline/{$appId}/event-templates?hapikey={$token}";

        $response = \Http::withHeaders([
            'Content-Type' => 'application/json',
        ])->get($url);

        if ($response->successful()) {
            \Log::info($response->body());
            $responseData = $response->json();
            return response()->json($responseData, 200);
        } else {
            \Log::error('Error getting event templates:', ['response' => $response->body()]);
            return response()->json(['error' => 'Failed to get event templates'], $response->status());
        }
    }

    public function createToken(Request $request)
    {
        $token = 'b5e3eb13-c1cc-4409-a55f-63ee3cf49807';
        $appId = '2551361';

        $eventTemplateId = $request->input('eventTemplateId');
        $customTokens = $request->input('customTokens', []);
        $validTokenTypes = ['string', 'number', 'enumeration', 'date'];
        $invalidTokenNames = ['log', 'lookup'];


        $url = "https://api.hubapi.com/crm/v3/timeline/{$appId}/event-templates/{$eventTemplateId}/tokens?hapikey={$token}";

        foreach ($customTokens as $customToken) {
            // check token type
            if (!isset($customToken['type']) || !in_array($customToken['type'], $validTokenTypes)) {
                return response()->json(['error' => 'Invalid token type'], 400);
            }
            // check token name
            if (isset($customToken['name']) && in_array($customToken['name'], $invalidTokenNames)) {
                return response()->json(['error' => 'Invalid token name'], 400);
            }

            $response = \Http::post($url, $customToken, [
                'Content-Type' => 'application/json',
            ]);

            if ($response->successful()) {
                \Log::info($response->body());
            } else {
                \Log::error('Error creating custom token:', ['response_body' => $response->body()]);
                return response()->json(['error' => 'Failed to create custom token'], $response->status());
            }
        }
        return response()->json(['success' => true], 200);
    }

    public function retrieveTimelineToken()
    {
        $token = 'b5e3eb13-c1cc-4409-a55f-63ee3cf49807';
        $appId = '2551361';
        $eventTemplateId = "1288838";

        $url = "https://api.hubapi.com/crm/v3/timeline/{$appId}/event-templates/{$eventTemplateId}?hapikey={$token}";

        $response = \Http::get($url);

        if ($response->successful()) {
            \Log::info($response->body());
        } else {
            \Log::error('Error retrieving custom token:', ['response_body' => $response->body()]);
            return response()->json(['error' => 'Failed to retrieve custom token'], $response->status());
        }
    }

    public function createEvent(Request $request)
    {
        $url = 'https://api.hubapi.com/crm/v3/timeline/events';
        $token = HubspotToken::latest()->first()->getAccessToken();
        $eventTemplateId = $request->input('eventTemplateId');
        $timestamp = $request->input('timestamp');

        $requestData = [
            'eventTemplateId' => $eventTemplateId,
            'timestamp' => $timestamp,
            'tokens' => $this->buildTokens($request)
        ];

        if ($request->has('email')) {
            $requestData['email'] = $request->input('email');
        } elseif ($request->has('objectId')) {
            $requestData['objectId'] = $request->input('objectId');
        }

        $response = \Http::withHeaders([
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $token,
        ])->post($url, $requestData);
        
        if ($response->successful()) {
            \Log::info($response->body());
        } else {
            \Log::error('Error creating new event:', ['response_body' => $response->body()]);
            return response()->json(['error' => 'Failed to create new event'], $response->status());
        }
    }

    private function buildTokens(Request $request)
    {
        $tokens = $request->input('tokens', []);
        return $tokens;
    }
}
