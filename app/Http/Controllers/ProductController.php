<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\Category;
use Illuminate\Support\Facades\Storage;
use App\Services\FileUploadService;

class ProductController extends Controller
{
    protected $fileUploadService;

    public function __construct(FileUploadService $fileUploadService)
    {
        $this->fileUploadService = $fileUploadService;
    }

    // List products + suppliers + categories for dropdown
    public function index()
    {
        $products   = Product::with(['supplier', 'category'])->get();
        $suppliers  = Supplier::all();
        $categories = Category::all();

        return view('managestore.product', compact('products', 'suppliers', 'categories'));
    }

    // Store new product (AJAX)
    public function store(Request $request)
    {
        $data = $request->validate([
            'supplier_id'  => 'required|exists:suppliers,supplier_id',
            'category_id'  => 'required|exists:categories,category_id',
            'product_name' => 'required|string|max:255',
            'description'  => 'nullable|string',
            'unit'         => 'required|string|max:50',
            'markup_rule'  => 'nullable|numeric',
        ]);

        if (!isset($data['markup_rule'])) {
            $data['markup_rule'] = 0;
        }

        $product = Product::create($data);
        $product->load(['supplier', 'category']);

        return response()->json([
            'product_id'    => $product->product_id,
            'supplier_id'   => $product->supplier_id,
            'supplier_name' => $product->supplier->supplier_name ?? '',
            'category_id'   => $product->category_id,
            'category_name' => $product->category->category_name ?? '',
            'product_name'  => $product->product_name,
            'description'   => $product->description,
            'unit'          => $product->unit,
            'markup_rule'   => $product->markup_rule,
        ]);
    }

    // Update existing product (AJAX)
    public function update(Request $request, Product $product)
    {
        $data = $request->validate([
            'supplier_id'  => 'sometimes|nullable|exists:suppliers,supplier_id',
            'category_id'  => 'sometimes|nullable|exists:categories,category_id',
            'product_name' => 'sometimes|nullable|string|max:255',
            'description'  => 'sometimes|nullable|string',
            'unit'         => 'sometimes|nullable|string|max:50',
            'markup_rule'  => 'sometimes|nullable|numeric',
        ]);

        if (array_key_exists('markup_rule', $data) && $data['markup_rule'] === null) {
            $data['markup_rule'] = 0;
        }

        $product->fill($data)->save();
        $product->load(['supplier', 'category']);

        return response()->json([
            'product_id'    => $product->product_id,
            'supplier_id'   => $product->supplier_id,
            'supplier_name' => $product->supplier->supplier_name ?? '',
            'category_id'   => $product->category_id,
            'category_name' => $product->category->category_name ?? '',
            'product_name'  => $product->product_name,
            'description'   => $product->description,
            'unit'          => $product->unit,
            'markup_rule'   => $product->markup_rule,
        ]);
    }

    // Optional: return all as JSON
    public function fetch()
    {
        $products = Product::with(['supplier', 'category'])->get();
        return response()->json($products);
    }

    // -------------------------------------------------------
    // Upload product image to Cloudinary (FIXED)
    // -------------------------------------------------------
    public function updateImage(Request $request, Product $product)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
        ]);

        try {
            // Build Cloudinary upload URL directly (no package needed)
            $cloudName = env('CLOUDINARY_CLOUD_NAME', 'dlhcczwfz');
            $apiKey    = env('CLOUDINARY_API_KEY',    '896181671383421');
            $apiSecret = env('CLOUDINARY_API_SECRET', 'H2xrDLLGgPTU8Tr6HjGQBrMu5yY');

            $file      = $request->file('image');
            $filePath  = $file->getRealPath();
            $publicId  = 'products/product_' . $product->product_id . '_' . time();
            $timestamp = time();

            // Build signature
            $paramsToSign = [
                'public_id' => $publicId,
                'timestamp' => $timestamp,
            ];
            ksort($paramsToSign);

            $signatureString = '';
            foreach ($paramsToSign as $key => $value) {
                $signatureString .= $key . '=' . $value . '&';
            }
            $signatureString = rtrim($signatureString, '&') . $apiSecret;
            $signature = sha1($signatureString);

            // Upload via multipart POST to Cloudinary REST API
            $uploadUrl = "https://api.cloudinary.com/v1_1/{$cloudName}/image/upload";

            $postFields = [
                'file'       => new \CURLFile($filePath, $file->getMimeType(), $file->getClientOriginalName()),
                'api_key'    => $apiKey,
                'timestamp'  => $timestamp,
                'public_id'  => $publicId,
                'signature'  => $signature,
            ];

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $uploadUrl);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // needed on some servers
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curlError = curl_error($ch);
            curl_close($ch);

            if ($curlError) {
                throw new \Exception('cURL error: ' . $curlError);
            }

            $result = json_decode($response, true);

            if ($httpCode !== 200 || !isset($result['secure_url'])) {
                $errMsg = $result['error']['message'] ?? 'Unknown Cloudinary error';
                throw new \Exception('Cloudinary error: ' . $errMsg);
            }

            // Save URL to DB
            $product->image_path = $result['secure_url'];
            $product->save();

            return response()->json([
                'success'    => true,
                'product_id' => $product->product_id,
                'image_path' => $product->image_path,
            ]);

        } catch (\Exception $e) {
            \Log::error('Cloudinary upload failed for product ' . $product->product_id . ': ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Image upload failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function archive(Product $product)
    {
        $product->archive = 'Archived';
        $product->save();

        return response()->json(['status' => 'ok']);
    }

    public function unarchive(Product $product)
    {
        $product->archive = null;
        $product->save();

        return response()->json(['status' => 'ok']);
    }

    public function destroy(Product $product)
    {
        $product->delete();

        return response()->json(['status' => 'ok']);
    }
}