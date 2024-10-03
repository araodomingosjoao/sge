<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;

class FileUploadService
{
    /**
     * Handle a single file upload.
     *
     * @param UploadedFile $file
     * @param array $options
     * @return string|null
     */
    public function handleSingleFile(UploadedFile $file, array $options): ?string
    {
        $folder = $options['folder'] ?? 'uploads';
        $disk = $options['disk'] ?? 'public';
        $fileName = $options['fileName'] ?? null;

        try {
            if ($fileName) {
                if (is_callable($fileName)) {
                    $fileName = $fileName($file);
                }
                $path = $file->storeAs($folder, $fileName, $disk);
            } else {
                $path = $file->store($folder, $disk);
            }

            Log::info("File path: " . $path);
            return $path;
        } catch (\Exception $e) {
            Log::error("File upload error: {$e->getMessage()}");
            throw new \RuntimeException("Failed to upload file: {$e->getMessage()}");
        }
    }

    /**
     * Handle multiple file uploads.
     *
     * @param array $files
     * @param array $options
     * @return array
     */
    public function handleMultipleFiles(array $files, array $options): array
    {
        $uploadedFiles = [];

        foreach ($files as $file) {
            if ($file instanceof UploadedFile) {
                $uploadedFiles[] = $this->handleSingleFile($file, $options);
            }
        }

        return $uploadedFiles;
    }

    /**
     * Delete old file.
     *
     * @param string $oldFilePath
     * @param string $disk
     * @return void
     */
    public function deleteOldFile(string $oldFilePath, string $disk = 'public'): void
    {
        if ($oldFilePath && Storage::disk($disk)->exists($oldFilePath)) {
            try {
                Storage::disk($disk)->delete($oldFilePath);
                Log::info("Arquivo antigo removido: " . $oldFilePath);
            } catch (\Exception $e) {
                Log::error("Erro ao remover o arquivo antigo: {$e->getMessage()}");
            }
        }
    }
}
