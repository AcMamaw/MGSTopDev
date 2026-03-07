<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\FileUploadService;
use Illuminate\Support\Facades\Storage;

class FileUploadController extends Controller
{
    protected $fileUploadService;

    public function __construct(FileUploadService $fileUploadService)
    {
        $this->fileUploadService = $fileUploadService;
    }

    /**
     * Upload delivery receipt to S3
     */
    public function uploadDeliveryReceipt(Request $request)
    {
        $request->validate([
            'receipt'     => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'delivery_id' => 'required|exists:deliveries,delivery_id'
        ]);

        $uploadResult = $this->fileUploadService->uploadDeliveryReceipt($request->file('receipt'));

        if (!$uploadResult['success']) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to upload receipt: ' . $uploadResult['error']
            ], 500);
        }

        return response()->json([
            'success' => true,
            'url'     => $uploadResult['url'],
            'path'    => $uploadResult['path']
        ]);
    }

    /**
     * Upload PDF report to S3
     */
    public function uploadReport(Request $request)
    {
        $request->validate([
            'report'      => 'required|file|mimes:pdf|max:10240',
            'report_type' => 'required|string'
        ]);

        $uploadResult = $this->fileUploadService->uploadReport($request->file('report'));

        if (!$uploadResult['success']) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to upload report: ' . $uploadResult['error']
            ], 500);
        }

        return response()->json([
            'success' => true,
            'url'     => $uploadResult['url'],
            'path'    => $uploadResult['path']
        ]);
    }

    /**
     * Upload general document to S3
     */
    public function uploadDocument(Request $request)
    {
        $request->validate([
            'document' => 'required|file|mimes:pdf,doc,docx,xls,xlsx|max:5120',
            'folder'   => 'required|string'
        ]);

        $uploadResult = $this->fileUploadService->uploadDocument($request->file('document'), $request->folder);

        if (!$uploadResult['success']) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to upload document: ' . $uploadResult['error']
            ], 500);
        }

        return response()->json([
            'success' => true,
            'url'     => $uploadResult['url'],
            'path'    => $uploadResult['path']
        ]);
    }

    /**
     * ✅ NEW: Upload receipt PDF (base64) to S3
     * Called from the print button in the receipt modal
     */
    public function uploadReceiptPdf(Request $request)
    {
        $validated = $request->validate([
            'receipt_html'   => 'required|string',
            'order_id'       => 'nullable|integer',
            'receipt_number' => 'nullable|integer',
        ]);

        try {
            $orderPad   = str_pad($validated['order_id']       ?? 0, 3, '0', STR_PAD_LEFT);
            $receiptPad = str_pad($validated['receipt_number'] ?? 0, 4, '0', STR_PAD_LEFT);
            $timestamp  = now()->format('Ymd_His');
            $filename   = "receipts/O{$orderPad}_R{$receiptPad}_{$timestamp}.html";

            \Storage::disk('s3')->put($filename, $validated['receipt_html'], [
                'visibility'  => 'public',
                'ContentType' => 'text/html',
            ]);

            $url = \Storage::disk('s3')->url($filename);

            return response()->json(['success' => true, 'url' => $url, 'filename' => $filename]);

        } catch (\Exception $e) {
            \Log::error('Receipt upload error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Delete file from storage
     */
    public function deleteFile(Request $request)
    {
        $request->validate([
            'path'    => 'required|string',
            'storage' => 'required|in:s3,cloudinary'
        ]);

        $success = false;

        if ($request->storage === 's3') {
            $success = $this->fileUploadService->deleteFromS3($request->path);
        } elseif ($request->storage === 'cloudinary') {
            $success = $this->fileUploadService->deleteFromCloudinary($request->path);
        }

        return response()->json(['success' => $success]);
    }
}