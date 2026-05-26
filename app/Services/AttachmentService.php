<?php

namespace App\Services;

use App\Models\Attachment;
use Illuminate\Http\UploadedFile;
use Illuminate\Database\Eloquent\Model;

class AttachmentService {
    public function store(Model $model , UploadedFile $file): Attachment
    {
        $path = $file->store('attachments', 'public');
        $attachment = Attachment::create([
            'file_path' => $path,
            'file_type' => $file->getMimeType(),
            'attachable_id' => $model->id,
            'attachable_type' => get_class($model),
        ]);
        return $attachment;
    }
}
