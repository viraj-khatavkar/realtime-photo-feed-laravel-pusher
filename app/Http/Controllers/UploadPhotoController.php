<?php

namespace App\Http\Controllers;

use App\Events\NewPhoto;
use App\Photo;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;

class UploadPhotoController extends Controller
{
	public function __construct(ImageManager $images)
	{
		$this->images = $images;
	}

    public function __invoke()
    {
    	$file = request('photo');

        $path = $file->hashName('profiles');

        // We will store the profile photos on the "public" disk, which is a convention
        // for where to place assets we want to be publicly accessible. Then, we can
        // grab the URL for the image to store with this user in the database row.
        $disk = Storage::disk('public');

        $disk->put(
            $path, $this->formatImage($file)
        );

        $photo = request()->user()->photos()->create([
            'photo_url' => $disk->url($path),
        ]);

        broadcast(new NewPhoto($photo))->toOthers();

        return $photo->load('user');
    }

    /**
     * Resize an image instance for the given file.
     *
     * @param  \SplFileInfo  $file
     * @return \Intervention\Image\Image
     */
    protected function formatImage($file)
    {
        return (string) $this->images->make($file->path())
                            ->fit(300)->encode();
    }
}
