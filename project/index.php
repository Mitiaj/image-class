<?php
require_once('lib/FileHelper.php');

    if (isset($_FILES['file'])){
        $file = $_FILES['file'];
        try {
            $oFile = new FileHelper($file, ['uploadDir' => 'images/']);
            $oFile->upload();
            $str = $oFile->getUploadedFilePath();
            echo "<img src='{$str}'>";
            unset($oFile);
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