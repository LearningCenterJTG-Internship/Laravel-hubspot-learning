<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use HubSpot\Client\Auth\OAuth\ApiException;
use Illuminate\Support\Facades\Crypt;
use Carbon\Carbon;
use Illuminate\Support\Facades\Config;
use \App\Models\HubspotToken;
use App\Models\Contact;
use App\Models\User;

class HubSpotAuthController extends Controller
{
    protected $hubspot_scopes = [
        'crm.objects.contacts.read',
        'crm.objects.contacts.write',
        'crm.objects.companies.read',
        'crm.objects.companies.write',
        'crm.lists.read',
        'crm.lists.write',
        'tickets',
        'oauth',
    ];

    # hubspot authorization
    public function redirectToHubSpot()
    {
        $scopes = $this->hubspot_scopes;
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

}
