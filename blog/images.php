<?php 

/*
To use this script, call it as /blog/images.php?id=####
where #### are some number identifying the resource in the rowid column of the database.
If the resource is not found, a 404 will be sent.
If this script doesn't work, it's probably because you inserted some blank lines at the top of this file or some included file.
*/

include "../utils/db.php";

$id = isset($_GET["id"])? $_GET["id"] : "-1";
$imageFromDB = Max\fetch_one("SELECT content_type, file_data FROM resources WHERE rowid = :id;", array("id"=>$id));

//if the image was not found in the database, send a 404 Not Found
if($imageFromDB == false) {
    header($_SERVER["SERVER_PROTOCOL"]." 404 Not Found", true, 404);
    //if using PHP >= 5.4, do: http_response_code(404);
}
//if the image was found in the database, send a content-type header for images and then the image data itself
else {
    header("Content-type: " . $imageFromDB[0]); //image/jped
    echo $imageFromDB[1];
}

?>