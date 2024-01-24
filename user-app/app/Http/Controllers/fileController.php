<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \App\Models\HubspotToken;


class fileController extends Controller
{
    protected $hubspot_token;

    public function __construct()
    {
        $this->hubspot_token = HubspotToken::latest()->first()->getAccessToken();
    }

    public function uploadFile(Request $request)
    {
        $token = $this->hubspot_token;
        $url = "https://api.hubapi.com/crm/v3/imports";

        $requestData = [
            "name" => "November Marketing Event Leads",
            "importOperations" => [
                "0-1" => "CREATE"
            ],
            "dateFormat" => "DAY_MONTH_YEAR",
            "files" => [
                [
                    "fileName" => "example_contact.csv",
                    "fileFormat" => "CSV",
                    "fileImportPage" => [
                        "hasHeader" => true,
                        "columnMappings" => [
                            [
                                "columnObjectTypeId" => "0-1",
                                "columnName" => "First Name",
                                "propertyName" => "firstname"
                            ],
                            [
                                "columnObjectTypeId" => "0-1",
                                "columnName" => "Last Name",
                                "propertyName" => "lastname"
                            ],
                            [
                                "columnObjectTypeId" => "0-1",
                                "columnName" => "Email",
                                "propertyName" => "email",
                                "columnType" => "HUBSPOT_ALTERNATE_ID"
                            ]
                        ]
                    ]
                ]
            ]
        ];

        $file_contents = file_get_contents('D:\internship\Laravel-hubspot-learning\user-app\app\Http\Controllers\example_contact.csv');
        $trimmed_contents = substr($file_contents, 3);

        $response = \Http::withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Content-Type' => 'multipart/form-data'
        ])->post($url, [
            'file' => [
                'contents' => $trimmed_contents,
                'filename' => 'example_contact.csv',
            ],
        ] + $requestData);

        if ($response->successful()) {
            \Log::info($response->body());
            $responseData = $response->json();
        } else {
            \Log::error('Error creating file:', ['response_body' => $response->body()]);
        }
    }

    public function getImport()
    {
        $token = $this->hubspot_token;
        $url = "https://api.hubapi.com/crm/v3/imports";

        $response = \Http::withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Content-Type' => 'application/json',
        ])->get($url);

        if ($response->successful()) {
            \Log::info($response->body());
            $responseData = $response->json();
        } else {
            \Log::error('Error retrieving imported file:', ['response_body' => $response->body()]);
        }
    }

    public function cancelImport(Request $request)
    {
        $token = $this->hubspot_token;
        $importId = $request->input("importId");
        $url = "https://api.hubapi.com/crm/v3/imports/{$importId}/cancel";
        $requestData = [];

        $response = \Http::withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Content-Type' => 'application/json',
        ])->post($url, $requestData);

        if ($response->successful()) {
            \Log::info($response->body());
            $responseData = $response->json();
        } else {
            \Log::error('Error canceling import:', ['response_body' => $response->body()]);
        }
    }

    public function getError()
    {
        $token = $this->hubspot_token;
        $importId = "";
        $url = "https://api.hubapi.com/crm/v3/imports/{$importId}/errors";

        $response = \Http::withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Content-Type' => 'application/json',
        ])->get($url);

        if ($response->successful()) {
            \Log::info($response->body());
            $responseData = $response->json();
        } else {
            \Log::error('Failed to retrieve import errors:', ['response_body' => $response->body()]);
        }
    }
}
