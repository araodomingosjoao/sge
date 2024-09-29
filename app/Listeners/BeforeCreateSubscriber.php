<?php
namespace App\Listeners;

use App\Events\BeforeCreate;
use App\Services\FileUploadService;
use Illuminate\Events\Dispatcher;
use Illuminate\Http\UploadedFile;

class BeforeCreateSubscriber
{
    protected $fileUploadService;

    public function __construct(FileUploadService $fileUploadService)
    {
        $this->fileUploadService = $fileUploadService;
    }

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

    private function processField($field, array $options): ?string
    {
        if ($field instanceof UploadedFile) {
            return $this->fileUploadService->handleSingleFile($field, $options);
        } elseif (is_array($field)) {
            return $this->fileUploadService->handleMultipleFiles($field, $options);
        }

        return $field;
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
