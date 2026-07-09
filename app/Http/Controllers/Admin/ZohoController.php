<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\TenantZohoToken;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Support\Str;

class ZohoController extends Controller
{
    /**
     * Redirects the user to Zoho's OAuth consent screen.
     */
    public function connect(Request $request)
    {
        $tenantId = session('active_tenant_id') ?? 1;

        $clientId = env('ZOHO_CLIENT_ID');
        $redirectUri = env('ZOHO_REDIRECT_URI');
        
        // Scope for Zoho Inventory items
        $scope = 'ZohoInventory.FullAccess.all'; 

        $url = "https://accounts.zoho.com/oauth/v2/auth?" . http_build_query([
            'scope' => $scope,
            'client_id' => $clientId,
            'response_type' => 'code',
            'access_type' => 'offline',
            'redirect_uri' => $redirectUri,
            'prompt' => 'consent',
            'state' => $tenantId, // Passing tenant_id as state
        ]);

        return redirect()->away($url);
    }

    /**
     * Callback from Zoho after OAuth consent.
     */
    public function callback(Request $request)
    {
        if ($request->has('error')) {
            return redirect('/')->withErrors('Zoho connection failed: ' . $request->error);
        }

        $code = $request->code;
        $tenantId = $request->state;

        $clientId = env('ZOHO_CLIENT_ID');
        $clientSecret = env('ZOHO_CLIENT_SECRET');
        $redirectUri = env('ZOHO_REDIRECT_URI');

        // Exchange code for tokens
        $response = Http::asForm()->post('https://accounts.zoho.com/oauth/v2/token', [
            'code' => $code,
            'client_id' => $clientId,
            'client_secret' => $clientSecret,
            'redirect_uri' => $redirectUri,
            'grant_type' => 'authorization_code',
        ]);

        if ($response->successful()) {
            $data = $response->json();
            
            // Store token for this tenant
            TenantZohoToken::updateOrCreate(
                ['tenant_id' => $tenantId],
                [
                    'access_token' => $data['access_token'],
                    'refresh_token' => $data['refresh_token'] ?? null, // Refresh token is only sent on first auth
                    'expires_at' => now()->addSeconds($data['expires_in']),
                ]
            );

            return redirect()->route('admin.products', ['tenant' => $tenantId])
                             ->with('success', 'Zoho Account successfully connected!');
        }

        return redirect()->route('admin.products', ['tenant' => $tenantId])
                         ->withErrors('Failed to connect to Zoho: ' . $response->body());
    }

    /**
     * Syncs products from Zoho to the database.
     */
    public function syncProducts(Request $request)
    {
        $tenantId = session('active_tenant_id') ?? 1;
        $token = TenantZohoToken::where('tenant_id', $tenantId)->first();

        if (!$token) {
            return back()->withErrors('Zoho is not connected.');
        }

        // Check if token is expired, if so, refresh it
        if ($token->expires_at && $token->expires_at->isPast()) {
            $this->refreshToken($token);
        }

        // Fetch Items from Zoho Inventory API
        $apiDomain = env('ZOHO_API_DOMAIN', 'https://inventory.zoho.com');
        
        // Note: For Zoho Inventory, organization_id is usually required as a header or param.
        // We will fetch organizations first to get the first one if we don't have it stored.
        if (empty($token->organization_id)) {
            $orgResponse = Http::withToken($token->access_token)
                               ->get("{$apiDomain}/api/v1/organizations");
            
            if ($orgResponse->successful() && !empty($orgResponse->json()['organizations'])) {
                $orgId = $orgResponse->json()['organizations'][0]['organization_id'];
                $token->update(['organization_id' => $orgId]);
            } else {
                return back()->withErrors('Could not fetch Zoho organizations.');
            }
        }

        $response = Http::withToken($token->access_token)
                        ->get("{$apiDomain}/api/v1/items", [
                            'organization_id' => $token->organization_id
                        ]);

        if ($response->successful()) {
            $items = $response->json()['items'];
            $importedCount = 0;

            \Illuminate\Support\Facades\DB::beginTransaction();
            try {
                foreach ($items as $item) {
                    if ($item['status'] !== 'active') continue;

                    // Create or update Product based on Name
                    $product = Product::firstOrCreate([
                        'title' => $item['name'],
                        'tenant_id' => $tenantId,
                    ], [
                        'status' => 'active',
                        'type' => 'product',
                        'description' => $item['description'] ?? null,
                    ]);

                    // Map variant fields
                    $sku = $item['sku'] ?? (Str::slug($item['name']) . '-' . rand(100, 999));
                    $price = $item['rate'] ?? 0;
                    $stock = $item['available_stock'] ?? 0;

                    // Update or create Variant
                    $product->variants()->updateOrCreate([
                        'sku' => $sku
                    ], [
                        'size' => 'Standard',
                        'price' => $price,
                        'stock' => $stock
                    ]);

                    $importedCount++;
                }
                \Illuminate\Support\Facades\DB::commit();
                
                return back()->with('success', "Successfully synced {$importedCount} products from Zoho!");

            } catch (\Exception $e) {
                \Illuminate\Support\Facades\DB::rollBack();
                return back()->withErrors('Error syncing from Zoho: ' . $e->getMessage());
            }
        }

        return back()->withErrors('Failed to fetch items from Zoho: ' . $response->body());
    }

    /**
     * Refreshes the Zoho access token.
     */
    private function refreshToken(TenantZohoToken $token)
    {
        $clientId = env('ZOHO_CLIENT_ID');
        $clientSecret = env('ZOHO_CLIENT_SECRET');

        $response = Http::asForm()->post('https://accounts.zoho.com/oauth/v2/token', [
            'refresh_token' => $token->refresh_token,
            'client_id' => $clientId,
            'client_secret' => $clientSecret,
            'grant_type' => 'refresh_token',
        ]);

        if ($response->successful()) {
            $data = $response->json();
            $token->update([
                'access_token' => $data['access_token'],
                'expires_at' => now()->addSeconds($data['expires_in']),
            ]);
            return true;
        }
        return false;
    }
}
