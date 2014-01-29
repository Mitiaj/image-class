<?php

/**
 * Class FileHelper
 */
class FileHelper
{
    /**
     * @var string defaul upload dir
     */
    private $uploadDir = 'images/';

    /**
     * @var integer file size
     */

    private $newFileName = 'default';
    /**
     * @var int default file size
     */
    private $maxFileSize = 20000000;

    /**
     * @var array allowed file types
     */
    private $allowedMimeTypes = ['image/png', 'image/jpeg', 'image/gif'];

    /**
     * @var object current uploading file
     */
    private $file;
    /**
     * @param array $fileTypes
     */
    public function setAllowedMimeTypes($mimeTypes)
    {
        $this->allowedMimeTypes = $mimeTypes;
    }

    /**
     * @param string $newFileName
     */
    public function setNewFileName($newFileName)
    {
        $this->newFileName = $newFileName;
    }

    /**
     * @return string
     */
    public function getNewFileName()
    {
        return $this->newFileName;
    }

    /**
     * @return array
     */
    public function getAllowedMimeTypes()
    {
        return $this->allowedMimeTypes;
    }

    /**
     * @param int $maxFileSize
     */
    public function setMaxFileSize($maxFileSize)
    {
        $this->maxFileSize = $maxFileSize;
    }

    /**
     * @return int
     */
    public function getMaxFileSize()
    {
        return $this->maxFileSize;
    }

    /**
     * @param string $uploadDir
     */
    public function setUploadDir($uploadDir)
    {
        $this->uploadDir = $uploadDir;
    }

    /**
     * @return string
     */
    public function getUploadDir()
    {
        return $this->uploadDir;
    }

    /**
     * Create new file helper object
     * you can pass config array and override default values.
     * This is defaul values
     *      [
     *          'newFileName' => 'default',
     *          'uploadDir' => 'images/',
     *          'fileTypes' => [
     *              'image/png',
     *              'image/jpeg',
     *              'image/gif'
     *          ],
     *          'maxFileSize' => 20000
     *      ]
     * or you can just create new object new FileHelper($file) and pass all parameters through setters methods
     *
     * @param array $aFile file from $_POST array
     * @param array $aConfig config array
     *
     * @throws exception
     */
    public function __construct(array $aFile, $aConfig = array()) {
        if (is_array($aFile) && $aFile['error'] == 0) {
            $this->file = (object) $aFile;
        } else {
           throw new Exception('Error uploading file');
        }
        if (count($aConfig) > 0) {
            foreach ($aConfig as  $key => $value) {
                if (property_exists($this, $key)) {
                    $this->$key = $value;
                }
            }
        }
    }

    /**
     * upload the file
     */
    public function upload() {
        if (!$this->isMimeAllowed($this->file->type)) {
            throw new Exception('Not allowed file type');
        } else if (!$this->notExceedSizeLimit()) {
            throw new Exception('File size to big');
        } else if ($this->hasScriptExtension()) {
            throw new Exception('File corrupted');
        } else if (!$this->uploadDirExists()) {
            throw new Exception('Upload dir doesnot exists.');
        } else {
            while ($this->fileExist()) {
                $this->randomizeFileName();
            }
            if (!move_uploaded_file($this->file->tmp_name, $this->getUploadDir().$this->getNewFileName())) {
                throw new Exception('Error occurred while uploading file');
            }

        }
    }

    /**
     * @return string full path to uploaded image
     */
    public function getUploadedFilePath() {
        return $this->getUploadDir().$this->getNewFileName();
    }

    /**
     * check if mime type is allowed
     * @return bool
     */
    private function isMimeAllowed() {
        return in_array($this->file->type, $this->allowedMimeTypes);
    }

    /**
     * check if file exists
     * @return bool
     */
    private function fileExist() {
        return file_exists($this->getUploadDir().$this->newFileName);
    }

    /**
     * check for script extension
     * @return int
     */
    private function hasScriptExtension() {
        return strpos($this->file->name, '.php');
    }

    /**
     * generate random file name
     * @return string
     */
    private function randomizeFileName(){
        $arr = explode('.',$this->file->name);
        $extension = $arr[count($arr) - 1];
        $this->newFileName = "";
        for($i = 0; $i < count($arr)-1; $i++)
            $this->newFileName .=$arr[$i];
        $this->newFileName .= mt_rand(0, 999999).'.'.$extension;
    }

    /**
     * check if file not exceed size limit
     * @return bool
     */
    private function notExceedSizeLimit() {
        return ($this->file->size < $this->getMaxFileSize());
    }

    /**
     * check if dir exists
     * @return bool
     */
    private function uploadDirExists() {
        return is_dir($this->getUploadDir());
    }

}