<?php
namespace App\Listeners;

use App\Events\BeforeCreate;
use Illuminate\Events\Dispatcher;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class BeforeCreateSubscriber
{
    public function subscribe(Dispatcher $events)
    {
        $events->listen(
            BeforeCreate::class,
            [BeforeCreateSubscriber::class, 'handleFileUploads']
        );

        $events->listen(
            BeforeCreate::class,
            [BeforeCreateSubscriber::class, 'hashPassword']
        );
    }

    public function handleFileUploads(BeforeCreate $event)
    {
        $data = &$event->data;
        $model = $event->model;

        $fileFields = $model::getFileFields();

        foreach ($fileFields as $field => $options) {
            if (isset($data[$field])) {
                $event->data[$field] = $this->processField($data[$field], $options);
            }
        }

        return $data;
    }

    /**
     * Process a single field.
     *
     * @param mixed $field
     * @param array $options
     * @return string|array|null
     */
    private function processField($field, array $options): ?string
    {
        if ($field instanceof UploadedFile) {
            return $this->handleSingleFile($field, $options);
        } elseif (is_array($field)) {
            return $this->handleMultipleFiles($field, $options);
        }

        return $field;
    }

    /**
     * Handle a single file upload.
     *
     * @param UploadedFile $file
     * @param array $options
     * @return string|null
     */
    private function handleSingleFile(UploadedFile $file, array $options): ?string
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

            Log::info("File path:" . $path);
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
    private function handleMultipleFiles(array $files, array $options): array
    {
        $uploadedFiles = [];

        foreach ($files as $file) {
            if ($file instanceof UploadedFile) {
                $uploadedFiles[] = $this->handleSingleFile($file, $options);
            }
        }

        return $uploadedFiles;
    }

    public function hashPassword(BeforeCreate $event)
    {
        $data = &$event->data;

        if (isset($data['password'])) {
            $data['password'] = bcrypt($data['password']);
        }

        return $data;
    }

}
