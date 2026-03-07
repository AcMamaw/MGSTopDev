<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;

class FileUploadService
{
    /**
     * Upload product image to Cloudinary
     */
    public function uploadProductImage(UploadedFile $file, string $folder = 'products'): array
    {
        try {
            $uploadedFile = Cloudinary::upload($file->getRealPath(), [
                'folder' => $folder,
                'public_id' => uniqid(),
                'transformation' => [
                    ['width' => 800, 'height' => 800, 'crop' => 'limit'],
                    ['quality' => 'auto']
                ]
            ]);

            return [
                'success'   => true,
                'url'       => $uploadedFile->getSecurePath(),
                'public_id' => $uploadedFile->getPublicId(),
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error'   => $e->getMessage()
            ];
        }
    }

    /**
     * Upload employee profile picture to Cloudinary
     */
    public function uploadEmployeePicture(UploadedFile $file): array
    {
        return $this->uploadProductImage($file, 'employees');
    }

    /**
     * Upload document to S3 (PDF reports, receipts, etc.)
     */
    public function uploadDocument(UploadedFile $file, string $folder = 'documents'): array
    {
        try {
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs($folder, $filename, 's3');

            return [
                'success'  => true,
                'url'      => Storage::disk('s3')->url($path),
                'path'     => $path,
                'filename' => $filename
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error'   => $e->getMessage()
            ];
        }
    }

    /**
     * Upload delivery receipt to S3
     */
    public function uploadDeliveryReceipt(UploadedFile $file): array
    {
        return $this->uploadDocument($file, 'delivery-receipts');
    }

    /**
     * Upload PDF report to S3
     */
    public function uploadReport(UploadedFile $file): array
    {
        return $this->uploadDocument($file, 'reports');
    }

    /**
     * Delete file from Cloudinary
     */
    public function deleteFromCloudinary(string $publicId): bool
    {
        try {
            Cloudinary::destroy($publicId);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Delete file from S3
     */
    public function deleteFromS3(string $path): bool
    {
        try {
            Storage::disk('s3')->delete($path);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}