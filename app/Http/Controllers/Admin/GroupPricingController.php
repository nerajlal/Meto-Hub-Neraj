<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CustomerGroup;
use App\Models\GroupCustomPrice;
use Illuminate\Http\Request;

class GroupPricingController extends Controller
{
    public function storeGroup(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $tenantId = session('active_tenant_id') ?? request()->route('tenant') ?? 1;

        CustomerGroup::create([
            'name' => $request->name,
            'tenant_id' => $tenantId,
        ]);

        return redirect()->route('admin.discounts', ['tab' => 'group-prices'])->with('success', 'Customer group created successfully.');
    }

    public function addUsersToGroup(Request $request)
    {
        $request->validate([
            'customer_group_id' => ['required', \Illuminate\Validation\Rule::exists('customer_groups', 'id')->where('tenant_id', $request->attributes->get('resolved_tenant_id'))],
            'users' => 'required|array',
            'users.*' => \Illuminate\Validation\Rule::exists('users', 'id')->where('tenant_id', request()->attributes->get('resolved_tenant_id')),
        ]);

        $group = CustomerGroup::findOrFail($request->customer_group_id);
        $group->users()->syncWithoutDetaching($request->users);

        return redirect()->route('admin.discounts', ['tab' => 'group-prices'])->with('success', 'Customers added to group successfully.');
    }

    public function destroyGroup($id)
    {
        $group = CustomerGroup::findOrFail($id);
        $group->delete();

        return redirect()->route('admin.discounts', ['tab' => 'group-prices'])->with('success', 'Customer group deleted successfully.');
    }

    public function removeUserFromGroup($groupId, $userId)
    {
        $group = CustomerGroup::findOrFail($groupId);
        $group->users()->detach($userId);

        return redirect()->route('admin.discounts', ['tab' => 'group-prices'])->with('success', 'Customer removed from group successfully.');
    }

    public function storeGroupPrice(Request $request)
    {
        $request->validate([
            'customer_group_id' => ['required', \Illuminate\Validation\Rule::exists('customer_groups', 'id')->where('tenant_id', $request->attributes->get('resolved_tenant_id'))],
            'product_id' => ['required', \Illuminate\Validation\Rule::exists('products', 'id')->where('tenant_id', $request->attributes->get('resolved_tenant_id'))],
            'price' => 'required|numeric|min:0',
        ]);

        $tenantId = session('active_tenant_id') ?? request()->route('tenant') ?? 1;

        GroupCustomPrice::updateOrCreate(
            [
                'customer_group_id' => $request->customer_group_id,
                'product_id' => $request->product_id,
                'tenant_id' => $tenantId,
            ],
            [
                'price' => $request->price,
            ]
        );

        return redirect()->route('admin.discounts', ['tab' => 'group-prices'])->with('success', 'Group custom price saved successfully.');
    }

    public function destroyGroupPrice($id)
    {
        $groupPrice = GroupCustomPrice::findOrFail($id);
        $groupPrice->delete();

        return redirect()->route('admin.discounts', ['tab' => 'group-prices'])->with('success', 'Group custom price deleted successfully.');
    }
}
