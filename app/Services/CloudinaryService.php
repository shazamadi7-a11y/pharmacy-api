<?php

declare(strict_types=1);

namespace App\Services;

use Cloudinary\Cloudinary;
use Illuminate\Http\UploadedFile;

final class CloudinaryService
{
    private ?Cloudinary $cloudinary = null;

    public function upload(UploadedFile $file, string $folder = ''): string
    {
        /** @var string $baseFolder */
        $baseFolder = config('cloudinary.folder');
        $uploadFolder = $folder !== '' ? $baseFolder.'/'.$folder : $baseFolder;

        $result = $this->getClient()->uploadApi()->upload($file->getRealPath(), [
            'folder' => $uploadFolder,
            'resource_type' => 'image',
        ]);

        /** @var string $secureUrl */
        $secureUrl = $result['secure_url'];

        return $secureUrl;
    }

    public function delete(string $url): void
    {
        $publicId = $this->extractPublicId($url);

        if ($publicId !== '') {
            $this->getClient()->uploadApi()->destroy($publicId);
        }
    }

    private function getClient(): Cloudinary
    {
        if (! $this->cloudinary instanceof Cloudinary) {
            $this->cloudinary = new Cloudinary([
                'cloud' => [
                    'cloud_name' => config('cloudinary.cloud_name'),
                    'api_key' => config('cloudinary.api_key'),
                    'api_secret' => config('cloudinary.api_secret'),
                ],
            ]);
        }

        return $this->cloudinary;
    }

    private function extractPublicId(string $url): string
    {
        // URL format: https://res.cloudinary.com/{cloud}/image/upload/v{version}/{folder}/{public_id}.{ext}
        $pattern = '/\/upload\/(?:v\d+\/)?(.+)\.\w+$/';

        if (preg_match($pattern, $url, $matches) === 1) {
            return $matches[1];
        }

        return '';
    }
}
