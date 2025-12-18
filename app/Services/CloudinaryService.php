<?php

namespace App\Services;

use Cloudinary\Api\Upload\UploadApi;
use Illuminate\Http\UploadedFile;

class CloudinaryService
{
    public static function upload(UploadedFile $file, string $folder): array
    {
        $result = (new UploadApi())->upload(
            $file->getRealPath(),
            ['folder' => $folder]
        );

        return [
            'url' => $result['secure_url'],
            'public_id' => $result['public_id'],
        ];
    }

    public static function delete(?string $publicId): void
    {
        if ($publicId) {
            (new UploadApi())->destroy($publicId);
        }
    }
}
