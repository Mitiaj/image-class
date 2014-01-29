<?php
require_once('IDriver.php');

/**
 * PNG image driver
 * Class PngDriver
 */
class JpgDriver implements IDriver
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

        $img = imagecreatefromjpeg( "{$this->image}" );
        $tmp_img = imagecreatetruecolor( $dimensions->width, $dimensions->height );
        imagecopyresized( $tmp_img, $img, 0, 0, 0, 0, $dimensions->width, $dimensions->height, $this->imageInfo->width, $this->imageInfo->height );
        $thumbName = $this->getThumbName();
        if (imagejpeg( $tmp_img, "{$this->thumbPath}.{$thumbName}" )) {
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
        $img = imagecreatefromjpeg($this->image);
        $b_top = 0;
        $b_btm = 0;
        $b_lft = 0;
        $b_rt = 0;

        for(; $b_top < imagesy($img); ++$b_top) {
            for($x = 0; $x < imagesx($img); ++$x) {
                if(imagecolorat($img, $x, $b_top) != $color) {
                    break 2; //out of the 'top' loop
                }
            }
        }

        //bottom
        for(; $b_btm < imagesy($img); ++$b_btm) {
            for($x = 0; $x < imagesx($img); ++$x) {
                if(imagecolorat($img, $x, imagesy($img) - $b_btm-1) != $color) {
                    break 2; //out of the 'bottom' loop
                }
            }
        }

        //left
        for(; $b_lft < imagesx($img); ++$b_lft) {
            for($y = 0; $y < imagesy($img); ++$y) {
                if(imagecolorat($img, $b_lft, $y) != $color) {
                    break 2; //out of the 'left' loop
                }
            }
        }

        //right
        for(; $b_rt < imagesx($img); ++$b_rt) {
            for($y = 0; $y < imagesy($img); ++$y) {
                if(imagecolorat($img, imagesx($img) - $b_rt-1, $y) != $color) {
                    break 2; //out of the 'right' loop
                }
            }
        }

        $newimg = imagecreatetruecolor(
            imagesx($img)-($b_lft+$b_rt), imagesy($img)-($b_top+$b_btm));

        imagecopy($newimg, $img, 0, 0, $b_lft, $b_top, imagesx($newimg), imagesy($newimg));

        $border = 0;
        while(imagecolorat($img, $border, $border) == $color) {
            $border++;
        }
        $cropName = $this->cropPath.$this->getThumbName();
        imagejpeg( $newimg, "{$cropName}" );

        return $cropName;

    }
}