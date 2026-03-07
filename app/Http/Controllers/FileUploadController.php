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
        $request->validate([
            'pdf_base64'     => 'required|string',
            'receipt_number' => 'required|string',
        ]);

        try {
            // Decode base64 PDF
            $base64 = $request->pdf_base64;

            // Remove data URI prefix if present (data:application/pdf;base64,...)
            if (str_contains($base64, ',')) {
                $base64 = explode(',', $base64)[1];
            }

            $pdfContent = base64_decode($base64);

            if (!$pdfContent) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid PDF data.'
                ], 422);
            }

            // Build filename: receipts/R-00048-2026-03-07.pdf
            $filename = 'receipts/' . $request->receipt_number . '-' . now()->format('Y-m-d') . '.pdf';

            // Upload to S3
            Storage::disk('s3')->put($filename, $pdfContent, [
                'visibility'  => 'public',
                'ContentType' => 'application/pdf',
            ]);

            $url = Storage::disk('s3')->url($filename);

            \Log::info('Receipt uploaded to S3', ['filename' => $filename, 'url' => $url]);

            return response()->json([
                'success'  => true,
                'url'      => $url,
                'filename' => $filename,
            ]);

        } catch (\Exception $e) {
            \Log::error('Receipt S3 upload failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Upload failed: ' . $e->getMessage()
            ], 500);
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