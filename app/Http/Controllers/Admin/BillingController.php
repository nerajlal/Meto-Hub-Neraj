<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Razorpay\Api\Api;
use App\Models\Tenant;

class BillingController extends Controller
{
    public function index()
    {
        $tenant = auth()->user()->tenant;
        return view('admin.billing.index', compact('tenant'));
    }

    public function subscribe(Request $request)
    {
        $request->validate([
            'cycle' => 'required|in:monthly,yearly',
            'plan_tier' => 'nullable|string'
        ]);

        $tenant = auth()->user()->tenant;
        $tier = $request->input('plan_tier', $tenant->plan ?? 'sprout');
        $cycle = $request->input('cycle');
        $isYearly = $cycle === 'yearly';

        try {
            $api = new Api(env('RAZORPAY_KEY'), env('RAZORPAY_SECRET'));
            if ($tier === 'blossom') {
                $basePlanName = 'Grocery Blossom';
                $baseAmount = 4999;
            } elseif ($tier === 'tree') {
                $basePlanName = 'Grocery Tree';
                $baseAmount = 7999;
            } else {
                $basePlanName = 'Grocery Sprout';
                $baseAmount = 2999;
            }
            
            $planName = $isYearly ? $basePlanName . ' Yearly' : $basePlanName;
            
            // Yearly gives 2 months free (10 months cost)
            $planAmount = $isYearly ? ($baseAmount * 10) : $baseAmount;
            $planPaise = $planAmount * 100;
            
            // Fetch existing plans
            $plans = $api->plan->all();
            $planId = null;
            
            foreach($plans['items'] as $plan) {
                if($plan['item']['name'] == $planName) {
                    $planId = $plan['id'];
                    break;
                }
            }
            
            // Create plan if it doesn't exist
            if(!$planId) {
                $newPlan = $api->plan->create([
                    'period' => $isYearly ? 'yearly' : 'monthly',
                    'interval' => 1,
                    'item' => [
                        'name' => $planName,
                        'description' => $planName . ' Plan',
                        'amount' => $planPaise,
                        'currency' => 'INR'
                    ]
                ]);
                $planId = $newPlan->id;
            }

            $subscription  = $api->subscription->create(array(
                'plan_id'         => $planId,
                'customer_notify' => 1,
                'total_count'     => $isYearly ? 10 : 120, // For yearly or continuous
                'notes'           => array(
                    'tenant_id' => $tenant->id
                )
            ));

            $tenant->razorpay_subscription_id = $subscription->id;
            $tenant->subscription_status = 'pending';
            if ($tenant->plan !== $tier) {
                $tenant->plan = $tier;
            }
            $tenant->save();

            return response()->json([
                'success' => true,
                'subscription_id' => $subscription->id,
                'key' => env('RAZORPAY_KEY')
            ]);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function verify(Request $request)
    {
        $tenant = auth()->user()->tenant;
        
        try {
            $api = new Api(env('RAZORPAY_KEY'), env('RAZORPAY_SECRET'));

            $attributes = array(
                'razorpay_payment_id' => $request->razorpay_payment_id,
                'razorpay_subscription_id' => $request->razorpay_subscription_id,
                'razorpay_signature' => $request->razorpay_signature
            );

            $api->utility->verifyPaymentSignature($attributes);

            // Verification successful
            $tenant->subscription_status = 'active';
            // Extract plan if possible or assume from previous context
            $tenant->save();

            return response()->json(['success' => true, 'message' => 'Subscription activated successfully!']);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
