<?php

namespace App\Services;

use App\Models\Media;
use App\Models\StorageLocation;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MediaService
{
    /**
     * Upload a single media file.
     *
     * @param UploadedFile $file
     * @param string $mediaType
     * @param ?string $storageLocationId
     * @param ?string $userId
     * @return Media
     */
    public function uploadMedia(
        UploadedFile $file,
        string $mediaType,
        ?string $storageLocationId = null,
        ?string $userId = null
    ): Media {
        $storageLocation = $storageLocationId
            ? StorageLocation::findOrFail($storageLocationId)
            : StorageLocation::getDefault();

        if (!$storageLocation) {
            throw new \Exception('No default storage location configured');
        }

        $path = $this->storeFile($file, $mediaType, $storageLocation);

        return Media::create([
            'filename' => Str::random(32) . '.' . $file->getClientOriginalExtension(),
            'original_name' => $file->getClientOriginalName(),
            'media_type' => $mediaType,
            'size' => $file->getSize(),
            'path' => $path,
            'storage_location_id' => $storageLocation->id,
            'user_id' => $userId,
        ]);
    }

    /**
     * Upload multiple media files.
     *
     * @param array<UploadedFile> $files
     * @param string $mediaType
     * @param ?string $storageLocationId
     * @param ?string $userId
     * @return array<Media>
     */
    public function bulkUploadMedia(
        array $files,
        string $mediaType,
        ?string $storageLocationId = null,
        ?string $userId = null
    ): array {
        $media = [];
        foreach ($files as $file) {
            $media[] = $this->uploadMedia($file, $mediaType, $storageLocationId, $userId);
        }
        return $media;
    }

    /**
     * Delete a media file.
     *
     * @param Media $media
     * @return bool
     */
    public function deleteMedia(Media $media): bool
    {
        $this->deleteFile($media->path, $media->storageLocation);
        return $media->delete();
    }

    /**
     * Store file based on storage location type.
     *
     * @param UploadedFile $file
     * @param string $mediaType
     * @param StorageLocation $storageLocation
     * @return string
     */
    private function storeFile(
        UploadedFile $file,
        string $mediaType,
        StorageLocation $storageLocation
    ): string {
        $filename = Str::random(32) . '.' . $file->getClientOriginalExtension();
        $path = "media/{$mediaType}/" . date('Y/m/d') . '/' . $filename;

        return match ($storageLocation->type) {
            'local' => $this->storeLocal($file, $path),
            's3' => $this->storeS3($file, $path, $storageLocation),
            'gcs' => $this->storeGCS($file, $path, $storageLocation),
            default => throw new \Exception('Unsupported storage type'),
        };
    }

    /**
     * Store file on local filesystem.
     */
    private function storeLocal(UploadedFile $file, string $path): string
    {
        $disk = Storage::disk('local');
        $disk->putFileAs(dirname($path), $file, basename($path));
        return $path;
    }

    /**
     * Store file on S3.
     */
    private function storeS3(UploadedFile $file, string $path, StorageLocation $storageLocation): string
    {
        $disk = Storage::disk('s3');
        $disk->putFileAs(dirname($path), $file, basename($path), 'public');
        return $path;
    }

    /**
     * Store file on Google Cloud Storage.
     */
    private function storeGCS(UploadedFile $file, string $path, StorageLocation $storageLocation): string
    {
        $disk = Storage::disk('gcs');
        $disk->putFileAs(dirname($path), $file, basename($path), 'public');
        return $path;
    }

    /**
     * Delete file from storage.
     */
    private function deleteFile(string $path, ?StorageLocation $storageLocation): void
    {
        if (!$storageLocation) {
            return;
        }

        $disk = match ($storageLocation->type) {
            'local' => Storage::disk('local'),
            's3' => Storage::disk('s3'),
            'gcs' => Storage::disk('gcs'),
            default => null,
        };

        if ($disk && $disk->exists($path)) {
            $disk->delete($path);
        }
    }
}
