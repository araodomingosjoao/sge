<?php
namespace App\Listeners;

use App\Events\BeforeUpdate;
use App\Services\FileUploadService;
use Illuminate\Events\Dispatcher;
use Illuminate\Http\UploadedFile;

class BeforeUpdateSubscriber
{
    protected $fileUploadService;

    public function __construct(FileUploadService $fileUploadService)
    {
        $this->fileUploadService = $fileUploadService;
    }

    public function subscribe(Dispatcher $events)
    {
        $events->listen(
            BeforeUpdate::class,
            [BeforeUpdateSubscriber::class, 'handleFileUploads']
        );

        $events->listen(
            BeforeUpdate::class,
            [BeforeUpdateSubscriber::class, 'hashPassword']
        );
    }

    public function handleFileUploads(BeforeUpdate $event)
    {
        $data = &$event->data;
        $model = $event->model;

        $fileFields = $model::getFileFields();

        foreach ($fileFields as $field => $options) {
            if (isset($data[$field])) {
                $this->fileUploadService->deleteOldFile($model->getOriginal($field), $options['disk'] ?? 'public');
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

    public function hashPassword(BeforeUpdate $event)
    {
        $data = &$event->data;

        if (isset($data['password'])) {
            $data['password'] = bcrypt($data['password']);
        }

        return $data;
    }

}
