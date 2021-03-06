<?php

namespace App\Models\Traits;

use App\Models\Upload;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Exception\NotReadableException;
use InvalidArgumentException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

trait HasFilesTrait
{
    use CanResizeImagesTrait;

    /**
     * The morphMany images relationship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function files()
    {
        return $this->morphMany(Upload::class, 'uploadable');
    }

    /**
     * Creates a new file attached to the current step.
     *
     * @param string $name
     * @param string $type
     * @param int    $size
     * @param string $path
     *
     * @return Upload
     */
    public function addFile($name, $type, $size, $path)
    {
        $uuid = uuid();

        return $this->files()->create(compact('uuid', 'name', 'type', 'path', 'size'));
    }

    /**
     * Uploads and adds a file to the current record.
     *
     * @param UploadedFile $file
     * @param null|string  $path
     * @param false|bool   $resize
     * @param int          $width
     * @param int          $height
     *
     * @throws InvalidArgumentException
     *
     * @return Upload|false
     */
    public function uploadFile(UploadedFile $file, $path = null, $resize = false, $width = 680, $height = 480)
    {
        // Generate a unique file name.
        $name = $this->getUniqueFileName($file);

        if (is_null($path)) {
            $path = $this->getStoragePath($name);
        }

        if ($resize === true) {
            try {
                // Resize the image, then put it into storage.
                $image = $this->resizeImage($file, $width, $height);

                // Move the file into storage.
                Storage::put($path, $image->stream());
            } catch (NotReadableException $e) {
                return false;
            }
        } else {
            // Move the file into storage.
            Storage::put($path, file_get_contents($file->getRealPath()));
        }

        return $this->addFile($file->getClientOriginalName(), $file->getClientMimeType(), $file->getClientSize(), $path);
    }

    /**
     * Finds a step image by the specified UUID.
     *
     * @param string $uuid
     *
     * @return Upload
     */
    public function findFile($uuid)
    {
        return $this->files()->where(compact('uuid'))->firstOrFail();
    }

    /**
     * Deletes all files attached to the current step.
     *
     * @return int
     */
    public function deleteFiles()
    {
        $files = $this->files()->get();

        $count = 0;

        foreach ($files as $file) {
            if ($file->delete()) {
                $count++;
            }
        }

        return $count;
    }

    /**
     * Generates a unique file name.
     *
     * @param UploadedFile $file
     *
     * @return mixed
     */
    protected function getUniqueFileName(UploadedFile $file)
    {
        return sprintf('%s.%s', uuid(), $file->getClientOriginalExtension());
    }

    /**
     * Generates a storage path for the specified file name.
     *
     * @param string $fileName
     *
     * @throws InvalidArgumentException
     *
     * @return string
     */
    protected function getStoragePath($fileName)
    {
        if (empty($fileName)) {
            throw new InvalidArgumentException('File name cannot be empty.');
        }

        return sprintf('%s%s%s%s%s%s%s',
            'uploads',
            DIRECTORY_SEPARATOR,
            $this->getTable(),
            DIRECTORY_SEPARATOR,
            $this->id,
            DIRECTORY_SEPARATOR,
            $fileName
        );
    }
}
