<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HubspotToken extends Model
{
    protected $fillable = [
        'access_token',
        'refresh_token',
        'expires_at'
    ];

    # check if the token is expired
    public function isExpired() {
        return now()->gte($this->expires_at);
    }

    # refresh token
    public function tokenRefresh() {
        $http = new Client(['verify' => false]);
        $response = $http->post('https://api.hubapi.com/oauth/v1/token', [
            'form_params' => [
                'grant_type' => "refresh_token",
                'client_id' => config('services.hubspot.client_id'),
                'client_secret' => config('services.hubspot.client_secret'),
                'redirect_uri' => config('services.hubspot.redirect'),
                'refresh_token' => HubspotToken::latest()->first()->refresh_token
            ],
        ]);

        if ($response->successful()) {
            $return_body = $response->json();
            $this->update([
                'access_token' => $return_body['access_token'],
                'refresh_token' => $return_body['refresh_token'],
                'expires_at' => $return_body['expires_in']
            ]);
        } else {
            \Log::error("Refresh token failed.");
            return null;
        }
    }

    public function getAccessToken() {
        if ($this->isExpired()) {
            return $this->tokenRefresh();
        } else {
            return $this->access_token;
        }
    }
}
