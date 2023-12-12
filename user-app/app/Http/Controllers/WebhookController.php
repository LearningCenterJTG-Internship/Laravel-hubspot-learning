<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Product;

class WebhookController extends Controller
{
    public function handleHubSpotWebhook(Request $request)
    {
        // Handle the incoming HubSpot webhook payload
        $payload = $request->all();

        // Extract relevant data from the payload
        $productName = $payload['product_name'];
        $productDescription = $payload['description'];
        $company = $payload['company'];
        $productPrice = $payload['price'];

        // Create a new product in the database
        $product = Product::create([
            'product_name' => $productName,
            'description' => $productDescription,
            'company' => $company,
            'price' => $productPrice,
        ]);

        // Respond to HubSpot
        return response()->json(['message' => 'Product created successfully']);
    }
}
