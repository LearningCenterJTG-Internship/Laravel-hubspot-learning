<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Session;
use HubSpot\Client\Auth\OAuth\ApiException;
use Illuminate\Support\Facades\Crypt;
use Carbon\Carbon;
use Illuminate\Support\Facades\Config;
use \App\Models\HubspotToken;

class HubSpotAuthController extends Controller
{
    # hubspot authorization
    public function redirectToHubSpot()
    {
        $scopes = [
            'crm.objects.contacts.read',
            'crm.objects.contacts.write',
            'crm.lists.read',
            'crm.lists.write',
            'oauth',
        ];

        $query = http_build_query([
            'client_id' => config('services.hubspot.client_id'),
            'redirect_uri' => config('services.hubspot.redirect'),
            'scope' => implode(' ', $scopes),
            'response_type' => 'code',
            'state' => bin2hex(random_bytes(16)),
        ]);

        return redirect('https://app.hubspot.com/oauth/authorize?' . $query);
    }


    # callback to get token
    # save token - to be finished
    public function handleHubSpotCallback(Request $request)
    {
        $http = new Client(['verify' => false]);

        $response = $http->post('https://api.hubapi.com/oauth/v1/token', [
            'form_params' => [
                'client_id' => config('services.hubspot.client_id'),
                'client_secret' => config('services.hubspot.client_secret'),
                'code' => $request->code,
                'redirect_uri' => config('services.hubspot.redirect'),
                'grant_type' => 'authorization_code',
            ],
        ]);

        

        $token = json_decode((string) $response->getBody(), true)['access_token'];
        $refresh_token = json_decode((string) $response->getBody(), true)['refresh_token'];
        $expires_in = json_decode((string) $response->getBody(), true)['expires_in'];
        $expires_at = Carbon::now()->addSeconds($expires_in);

        if ($token && $refresh_token) {
            HubspotToken::latest()->first()->delete();

            $encyptedToken = Crypt::encrypt($token);
            $encyptedRefresh = Crypt::encrypt($refresh_token);
            
            $hubspotToken = new HubspotToken();
            $hubspotToken->access_token = $encyptedToken;
            $hubspotToken->refresh_token = $encyptedRefresh;
            $hubspotToken->expires_at = $expires_at;
            $hubspotToken->save();

            echo 'Success: Access token obtained.';
        } else {
            echo 'Error: Failed to obtain access token.';
        }
        
    }

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

    # save token - to be finished
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
}
