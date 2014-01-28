<html>
    <head>
        Suzie upload
    </head>
    <body>
    <form method="post" action="/" enctype="multipart/form-data">
        <input type="file" name="file" />
        <input type="submit" value="Upload" />
    </form>
    </body>
</html>

<?php
    if (isset($_FILES['file'])){
        $file = $_FILES['file'];
    }
?>