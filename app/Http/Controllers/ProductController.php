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
        // ── DEBUG: log everything we receive ──────────────────────────
        \Log::info('updateImage called', [
            'product_id' => $product->product_id,
            'has_file'   => $request->hasFile('image'),
            'all_input'  => $request->all(),
        ]);

        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
        ]);

        try {
            $cloudName = env('CLOUDINARY_CLOUD_NAME');
            $apiKey    = env('CLOUDINARY_API_KEY');
            $apiSecret = env('CLOUDINARY_API_SECRET');

            // ── DEBUG: check env vars are loaded ──────────────────────
            \Log::info('Cloudinary env', [
                'cloud_name' => $cloudName,
                'api_key'    => $apiKey,
                'has_secret' => !empty($apiSecret),
            ]);

            if (empty($cloudName) || empty($apiKey) || empty($apiSecret)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cloudinary credentials missing from environment. cloud_name=' . $cloudName . ' api_key=' . $apiKey,
                ], 500);
            }

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

            $uploadUrl = "https://api.cloudinary.com/v1_1/{$cloudName}/image/upload";

            $postFields = [
                'file'      => new \CURLFile($filePath, $file->getMimeType(), $file->getClientOriginalName()),
                'api_key'   => $apiKey,
                'timestamp' => $timestamp,
                'public_id' => $publicId,
                'signature' => $signature,
            ];

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $uploadUrl);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            $response  = curl_exec($ch);
            $httpCode  = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curlError = curl_error($ch);
            curl_close($ch);

            // ── DEBUG: log curl result ─────────────────────────────────
            \Log::info('Cloudinary response', [
                'http_code'  => $httpCode,
                'curl_error' => $curlError,
                'response'   => $response,
            ]);

            if ($curlError) {
                return response()->json([
                    'success' => false,
                    'message' => 'cURL error: ' . $curlError,
                ], 500);
            }

            $result = json_decode($response, true);

            if ($httpCode !== 200 || !isset($result['secure_url'])) {
                $errMsg = $result['error']['message'] ?? ('Unknown error. HTTP ' . $httpCode . ' Response: ' . $response);
                return response()->json([
                    'success' => false,
                    'message' => 'Cloudinary error: ' . $errMsg,
                ], 500);
            }

            $product->image_path = $result['secure_url'];
            $product->save();

            return response()->json([
                'success'    => true,
                'product_id' => $product->product_id,
                'image_path' => $product->image_path,
            ]);

        } catch (\Exception $e) {
            \Log::error('updateImage exception: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
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