<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \App\Models\HubspotToken;

class listController extends Controller
{
    protected $hubspot_token;

    public function __construct()
    {
        $this->hubspot_token = HubspotToken::latest()->first()->getAccessToken();
    }

    public function createList(Request $request)
    {
        $token = $this->hubspot_token;
        $object_type_id = $request->input('objectTypeId');
        $processing_type = $request->input('processingType');
        $name = $request->input('name');
        $validProcessing = ["MANUAL", "DYNAMIC", "SNAPSHOT"];

        if (!in_array($processing_type, $validProcessing)) {
            \Log::error("Invalid processing type, allowed types are 'MANUAL', 'DYNAMIC', or 'SNAPSHOT'.");
        } else {
            $requestData = [
                'objectTypeId' => $object_type_id,
                'processingType' => $processing_type,
                'name' => $name,
            ];

            if ($processing_type === "DYNAMIC") {
                $filter_branch = $request->input('filter_branch');
                $requestData['filter_branch'] = $filter_branch;
            }
        }

        $url = "https://api.hubapi.com/crm/v3/lists";

        $response = \Http::withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Content-Type' => 'application/json',
        ])->post($url, $requestData);

        if ($response->successful()) {
            \Log::info($response->body());
            $responseData = $response->json();
        } else {
            \Log::error('Error creating list:', ['response_body' => $response->body()]);
        }
    }

    public function fetchListById()
    {
        $list_id = "";
        $response = fetchList($list_id);
        \Log::info($response);
    }

    public function fetchListByName()
    {
        $objectTypeId = "";
        $listName = "";
        $identifier = "object-type-id/{$objectTypeId}/name/{$listName}";
        $response = fetchList($identifier);
        \Log::info($response);
    }

    private function fetchList($identifier)
    {
        $token = $this->hubspot_token;
        $url = "https://api.hubapi.com/crm/v3/lists/{$identifier}";
        $response = \Http::withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Content-Type' => 'application/json',
        ])->get($url);

        if ($response->successful()) {
            $responseData = $response->json();
            return $responseData;
        } else {
            \Log::error('Error retrieving list:', ['response_body' => $response->body()]);
        }
    }

    public function updateName(Request $request)
    {
        $token = $this->hubspot_token;
        $listId = $request->input("listId");
        $newListName = $request->input("newListName");

        $url = "https://api.hubapi.com/crm/v3/lists/{$listId}/update-list-name";
        $requestData = [
            'listName' => $newListName
        ];

        $response = \Http::withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Content-Type' => 'application/json',
        ])->put($url, $requestData);

        if ($response->successful()) {
            \Log::info($response->body());
            $responseData = $response->json();
        } else {
            \Log::error('Error updating list name:', ['response_body' => $response->body()]);
        }
    }

    public function searchList(Request $request)
    {
        $token = $this->hubspot_token;
        $url = "https://api.hubapi.com/crm/v3/lists/search";
        $requestData = [];

        $response = \Http::withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Content-Type' => 'application/json',
        ])->post($url, $requestData);

        if ($response->successful()) {
            \Log::info($response->body());
            $responseData = $response->json();
        } else {
            \Log::error('Error searching list:', ['response_body' => $response->body()]);
        }
    }

    public function deleteList()
    {
        $listId = "";
        $token = $this->hubspot_token;
        $url = "https://api.hubapi.com/crm/v3/lists/{$listId}";

        $response = \Http::withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Content-Type' => 'application/json',
        ])->delete($url);

        if ($response->successful()) {
            \Log::info($response->body());
            $responseData = $response->json();
        } else {
            \Log::error('Error deleting list:', ['response_body' => $response->body()]);
        }
    }

    public function restoreList(Request $request)
    {
        $token = $this->hubspot_token;
        $listId = "";
        $url = "https://api.hubapi.com/crm/v3/lists/{$listId}/restore";

        $response = \Http::withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Content-Type' => 'application/json',
        ])->put($url);

        if ($response->successful()) {
            \Log::info($response->body());
            $responseData = $response->json();
        } else {
            \Log::error('Error restoring list:', ['response_body' => $response->body()]);
        }
    }

}
