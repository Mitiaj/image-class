<?php
require_once('IDriver.php');

/**
 * PNG image driver
 * Class PngDriver
 */
class GifDriver implements IDriver
{
    /**
     * path to image
     * @var image
     */
    private $image;

    /**
     * all info about image
     * @var image info
     */
    private $imageInfo;

    /**
     * @var striung default uploading dir
     */
    private $thumbPath;

    private $cropPath;

    public function __construct(array $aParams) {
        foreach ($aParams as  $key => $value) {
            if (property_exists($this, $key)) {
                $this->$key = $value;
            }
        }
    }
    public function createThumb(stdClass $dimensions) {

        $img = imagecreatefromgif( "{$this->image}" );
        $tmp_img = imagecreatetruecolor( $dimensions->width, $dimensions->height );
        imagecopyresampled( $tmp_img, $img, 0, 0, 0, 0, $dimensions->width, $dimensions->height, $this->imageInfo->width, $this->imageInfo->height );
        $thumbName = $this->getThumbName();
        if (imagegif( $tmp_img, "{$this->thumbPath}.{$thumbName}" )) {
            return $this->thumbPath.$thumbName;
        } else {
            throw new Exception('Error creating thumbnail');
        }
    }

    /**
     * gererates random thumb name
     */
    private function getThumbName() {
        $thumbName = $this->randomize();
        while (file_exists($this->thumbPath.$thumbName)) {
            $thumbName = $this->randomize($thumbName);
        }
        return $thumbName;
    }


    /**
     * randomizes the string
     * @return string
     */
    private function randomize() {
        $arr = explode('.',$this->image);
        $extension = $arr[count($arr) - 1];
        return mt_rand(0, 999999).'.'.$extension;
    }

    public function cropColor($color) {
        throw new Exception('File format not supported');

    }
}