<?php
require_once('PngDriver.php');
require_once('JpgDriver.php');
require_once('GifDriver.php');

class Image {
    /**
     * @var string default image path
     */

    private $image;

    private $oDriver;

    private $imageInfo;

    private $thumbWidth = 50;

    private $thumbHeight = 50;

    private $thumbPath = 'images/thumbs/';

    private $cropPath = 'images/crops/';

    private $proportional = false;

    private $thumb;

    private $croppedImage;

    private $cropColor = 0xFFFFFF;

    public function getCroppedImage() {
        return $this->croppedImage;
    }

    /**
     * @param string $cropPath
     */
    public function setCropPath($cropPath)
    {
        $this->cropPath = $cropPath;
    }

    /**
     * @return string
     */
    public function getCropPath()
    {
        return $this->cropPath;
    }

    /**
     * @param mixed $newImageHeight
     */
    public function setNewImageHeight($newImageHeight)
    {
        $this->newImageHeight = $newImageHeight;
    }

    /**
     * @return mixed
     */
    public function getNewImageHeight()
    {
        return $this->newImageHeight;
    }

    /**
     * @param mixed $newImageWidth
     */
    public function setNewImageWidth($newImageWidth)
    {
        $this->newImageWidth = $newImageWidth;
    }

    /**
     * @return mixed
     */
    public function getNewImageWidth()
    {
        return $this->newImageWidth;
    }

    /**
     * @param boolean $proportional
     */
    public function setProportional($proportional)
    {
        $this->proportional = $proportional;
    }

    /**
     * @return boolean
     */
    public function getProportional()
    {
        return $this->proportional;
    }

    /**
     * @param int $thumbHeight
     */
    public function setThumbHeight($thumbHeight)
    {
        $this->thumbHeight = $thumbHeight;
    }

    /**
     * @return int
     */
    public function getThumbHeight()
    {
        return $this->thumbHeight;
    }

    /**
     * @param string $thumbPath
     */
    public function setThumbPath($thumbPath)
    {
        $this->thumbPath = $thumbPath;
    }

    /**
     * @return string
     */
    public function getThumbPath()
    {
        return $this->thumbPath;
    }

    /**
     * @param int $thumbWidth
     */
    public function setThumbWidth($thumbWidth)
    {
        $this->thumbWidth = $thumbWidth;
    }

    /**
     * @return int
     */
    public function getThumbWidht()
    {
        return $this->thumbWidht;
    }



    /**
     * Create image instance and pass array of params
     * [
     * 'image' => 'url to image',   //this one is nessesary
     * 'proportional' => false //ttrue/false for proportional thumb
     * 'thumbWidth' => 50,
     * 'thumbHeight' => 50,
     * 'thumbPath' => 'images/thumbs/',
     * 'cropsPath' => 'images/crops'
     * ]
     *
     * or you can set all properties by setters.
     *
     * @param array $aParams
     * @throws Exception
     */
    public function __construct(array $aParams) {
        if (count($aParams) && $aParams['image'] != '' && file_exists($aParams['image'])) {
            foreach ($aParams as  $key => $value) {
                if (property_exists($this, $key)) {
                    $this->$key = $value;
                }
            }
        } else {
            throw new Exception('Wrong params');
        }
        $tmp =  getimagesize($this->image);
        $this->imageInfo = (object) [
            'width' => $tmp[0],
            'height' => $tmp[1],
            'typeInt' => $tmp[2],
        ];
    }

    public function createThumb() {
        $dimensions = $this->getDimensionsByProportion(
            $this->imageInfo->width,
            $this->imageInfo->height,
            $this->thumbWidth,
            $this->thumbHeight
        );
        $this->loadDriver();
        $this->thumb = $this->oDriver->createThumb($dimensions);
    }

    public function resize () {

    }

    public function cropAround() {
        $this->croppedImage = $this->oDriver->cropColor($this->cropColor);
    }

    public function getCreatedThumb() {
        return $this->thumb;
    }

    private function loadDriver() {
        $driver = '';
        switch ( $this->imageInfo->typeInt ) {
            case IMAGETYPE_GIF:
                $driver = 'GifDriver';
                break;
            case IMAGETYPE_JPEG:
                $driver = 'JpgDriver';
                break;
            case IMAGETYPE_PNG:
                $driver = 'PngDriver';
                break;
            default: throw new Exception('Failed loading driver');
        }
        $this->initDriver(new $driver([
            'image' => $this->image,
            'imageInfo' => $this->imageInfo,
            'thumbPath' => $this->thumbPath,
            'cropPath' => $this->cropPath,
            'cropColor' => $this->cropColor
        ]));
    }

    private function initDriver(IDriver $driver) {
        $this->oDriver = $driver;
    }

    private function  getDimensionsByProportion($width, $height, $newWidth, $newHeight){

        if ($this->proportional) {
            $factor = 0;
            if ($newWidth  == 0) {
                $factor = $newHeight/$height;
            }  elseif ($newHeight == 0) {
                $factor = $newWidth/$width;
            } else {
                $factor = min( $newWidth / $width, $newHeight / $height);
            }
            return (object) [
                'width' =>  round( $width * $factor ),
                'height' => round( $height * $factor )
            ];
        }
        else {
            return (object) [
                'width' => ( $newWidth <= 0 ) ? $width : $newWidth,
                'height' => ( $height <= 0 ) ? $height : $newHeight
            ];
        }
    }

}
