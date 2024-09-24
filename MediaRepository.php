<?php

namespace App\Repositories;

use App\Helpers\FilesUploader;
use App\Models\Media;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;

class MediaRepository extends BaseRepository
{
    /**
     * @inheritDoc
     */
    public function model(): string
    {
        return Media::class;
    }

    /**
     * @inheritDoc
     */
    public function getFieldsSearchable(): array
    {
        return [
            'path',
        ];
    }

    /**
     * @param  string|UploadedFile|array  $file
     * @param  string  $path
     * @return Model
     */
    public function upload(string|UploadedFile|array $file, string $path): Model
    {
        $uploadedInfo = FilesUploader::upload($path, $file);

        return $this->create($uploadedInfo);
    }

    /**
     * @param $id
     * @return bool|mixed|null
     */
    public function delete($id)
    {
        $query = $this->model->newQuery();

        $model = $query->findOrFail($id);

        FilesUploader::delete($model->url);

        return $model->delete();
    }
}
