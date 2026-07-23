<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CustomPrice;
use App\Models\User;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class CustomPriceController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'product_id' => 'required|exists:products,id',
            'price' => 'required|numeric|min:0',
        ]);

        $tenantId = session('active_tenant_id') ?? request()->route('tenant') ?? 1;

        // Check if custom price already exists for this customer and product
        CustomPrice::updateOrCreate(
            [
                'user_id' => $request->user_id,
                'product_id' => $request->product_id,
                'tenant_id' => $tenantId,
            ],
            [
                'price' => $request->price,
            ]
        );

        return redirect()->route('admin.discounts', ['tab' => 'custom-prices'])->with('success', 'Custom pricing created successfully.');
    }

    public function destroy($id)
    {
        $customPrice = CustomPrice::findOrFail($id);
        $customPrice->delete();

        return redirect()->route('admin.discounts', ['tab' => 'custom-prices'])->with('success', 'Custom pricing deleted successfully.');
    }

    public function sampleExcel()
    {
        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=custom_pricing_template.csv",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $columns = ['customer_email', 'product_id', 'price'];

        $callback = function() use($columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);
            fputcsv($file, ['example@gmail.com', '12', '99.99']);
            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }

    public function importExcel(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt|max:5120', // allow up to 5mb
        ]);

        $file = $request->file('file');
        $tenantId = session('active_tenant_id') ?? request()->route('tenant') ?? 1;

        if (($handle = fopen($file->getRealPath(), "r")) !== FALSE) {
            $header = fgetcsv($handle, 1000, ",");
            
            if (!$header || count($header) < 3) {
                return back()->with('error', 'Invalid CSV format. Please use the sample template.');
            }

            $successCount = 0;
            $errorCount = 0;

            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                if (count($data) < 3) continue;

                $email = trim($data[0]);
                $productId = trim($data[1]);
                $price = trim($data[2]);

                if (empty($email) || empty($productId) || !is_numeric($price)) continue;

                $user = User::where('email', $email)->first();
                $product = Product::find($productId);

                if ($user && $product) {
                    CustomPrice::updateOrCreate(
                        [
                            'user_id' => $user->id,
                            'product_id' => $product->id,
                            'tenant_id' => $tenantId,
                        ],
                        [
                            'price' => $price,
                        ]
                    );
                    $successCount++;
                } else {
                    $errorCount++;
                }
            }
            fclose($handle);

            $msg = "Imported $successCount custom prices successfully.";
            if ($errorCount > 0) {
                $msg .= " Failed to import $errorCount rows (Customer email or Product ID not found).";
            }

            return redirect()->route('admin.discounts', ['tab' => 'custom-prices'])->with('success', $msg);
        }

        return redirect()->route('admin.discounts', ['tab' => 'custom-prices'])->with('error', 'Could not open the uploaded file.');
    }
}
