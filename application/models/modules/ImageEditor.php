<?php

use Intervention\Image\ImageManager;

defined('BASEPATH') OR exit('No direct script access allowed');

class ImageEditor extends CI_Model
{
    /**
     * Set watermark to image.
     *
     * @param $imagePath
     * @param string $watermarkPath
     * @param string $position
     * @return bool
     */
    public function watermark($imagePath, $watermarkPath = 'assets/app/img/layout/watermark1.png', $position = 'top-right')
    {
        $manager = new ImageManager();
        $img = $manager->make($imagePath);

        // and insert a watermark 1 by 2 ration of width
        $watermark = $manager->make($watermarkPath);
        $watermark->resize(ceil($img->getWidth() / 2), null, function ($constraint) {
            $constraint->aspectRatio();
        });
        $img->insert($watermark, $position, 1, 1);

        if ($img->save($imagePath, 80)) {
            return true;
        } else {
            return false;
        }
    }
}