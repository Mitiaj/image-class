<?php
require_once('lib/FileHelper.php');
require_once('lib/Image.php');

    if (isset($_FILES['file'])){
        $file = $_FILES['file'];
        try {
            /**
             * For FileHelper you need to pass $_FILES['file'] array
             * and you can pass array of params
             * [
             * 'newFileName' => 'default',
             * 'uploadDir' => 'images/',
             * 'fileTypes' => [
             *      'image/png',
             *      'image/jpeg',
             *      'image/gif'
             *  ],
             * 'maxFileSize' => 20000
             * ]
             *
             * or you can user setters for each param
             */
            $oFile = new FileHelper($file, [
                'newFileName' => 'test.jpg'
            ]);

            // Call to upload image
            $oFile->upload();

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
             * */
            $image = new Image(
                [
                    'image' => $oFile->getUploadedFilePath(),
                    'proportional' => false
                ]
            );
            unset($oFile);
            $image->createThumb();

            //get thumb path
            echo $image->getCreatedThumb();

            $image->cropAround();
            //get cropped image
           echo $image->getCroppedImage();

            unset($image);

        } catch (Exception $e) {
            echo $e->getMessage();
        }

    }
?>
<html>
    <head>
        <title>Suzie upload</title>
    </head>
    <body>
    <form method="post" action="<?=$_SERVER['PHP_SELF'] ?>" enctype="multipart/form-data">
        <input type="file" name="file" />
        <input type="submit" value="Upload" />
    </form>
    </body>
</html>