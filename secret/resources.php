<?php 

include "../utils/db.php";
include "../utils/common.php";
include "../utils/admin.php";

$title = "Media and Resource Management";
$debug = "";
$table = array();
$scriptname = $_SERVER['PHP_SELF'];
$location = "../data/uploads/";
$page_content = <<<HTML
<form method="POST" action="$scriptname" class="form-fancy" enctype="multipart/form-data">
    <div>
        <label for="file">Select file to upload</label>
        <input type="file" accept="image/*,.gif" id="file" name="file_data">

        <label for="description">Caption</label>
        <input type="text" name="description" id="description">

        <label for="content_type">Choose the type of the file</label>
        <select name="content_type" id="content_type">
            <option value="image/png" selected>PNG</option>
            <option value="image/jpeg">JPG</option>
            <option value="image/gif">GIF</option>
            <option value="">Unknown</option>
        </select>
    </div>
    <input type="submit" name="action" value="Upload">
</form>
HTML;

$action = isset($_POST["action"])? $_POST["action"] : "";

/*
Images must be uploaded to the database or deleted from the database before they are loaded into the page.
*/
if(Admin\loggedin()) {
    $_SESSION["page"] = "Resources";
    try {
        //upload any new images
        if($action == "Upload") {
            if (!isset($_FILES['file_data']['error']) || is_array($_FILES['file_data']['error'])) {
                throw new RuntimeException('Invalid POST parameters. Request may have been corrupted or multiple files were sent.');
            }
            switch ($_FILES['file_data']['error']) {
                case UPLOAD_ERR_OK:
                    break;
                case UPLOAD_ERR_NO_FILE:
                    throw new RuntimeException('No file sent.');
                case UPLOAD_ERR_INI_SIZE:
                case UPLOAD_ERR_FORM_SIZE:
                    throw new RuntimeException('Exceeded filesize limit.');
                default:
                    throw new RuntimeException('Unknown errors.');
            }
            // You should also check filesize here (max 3 MB)
            if ($_FILES['file_data']['size'] > 3000000) {
                throw new RuntimeException('Exceeded filesize limit.');
            }
            //Now that everything is settled, being getting info about the file and upload
            $file_path = $_FILES["file_data"]["tmp_name"];
            $file_name = $_FILES["file_data"]["name"];
            $file_size = $_FILES["file_data"]["size"];
            $file_desc = isset($_POST["description"])? $_POST["description"] : "";
            $file_type = isset($_POST["content_type"])? $_POST["content_type"] : "";
            //clean up filename by replacing illegal characters with a dash
            $file_name = preg_replace("/[^a-zA-Z0-9_\\-.()\\[\\]]/", "-", $file_name);
            //put record into database
            Max\fetch_none("INSERT INTO resources (file_name, description, timestamp, content_type, file_size) VALUES (:file_name, :description, datetime('now'), :file_type, :file_size);", array("file_name"=>$file_name, "description"=>$file_desc, "file_type"=>$file_type, "file_size"=>$file_size));
            $file_id_in_database = Max\fetch_one("SELECT rowid FROM resources WHERE file_name = :file_name ORDER BY timestamp DESC;", array("file_name"=>$file_name), SQLITE3_NUM);
            $file_id_in_database = $file_id_in_database[0];
            //try to move uploaded file into uploads folder from its temporary location
            if(move_uploaded_file($file_path, $location.$file_id_in_database.'-'.$file_name)) {
                chmod($location.$file_id_in_database.'-'.$file_name, 0644); //grant read/write for owner and read for everyone else
                $page_content = '<div class="success">File uploaded</div>' . $page_content;
            } else {
                Max\fetch_none("DELETE FROM resources WHERE rowid = :id;", array("id"=>$file_id_in_database));
                throw new RuntimeException("Failed to move file into uploads directory. All records have been deleted.");
            }
        }
        //delete any existing ones
        else if($action == "delete") {
            if(isset($_POST["id"]) && !empty($_POST["id"])) {
                $file_id_in_database = $_POST["id"];
                $file_name = Max\fetch_one("SELECT file_name FROM resources WHERE rowid = :id;", array("id"=>$file_id_in_database));
                if($file_name != false) {
                    $file_name = $file_name[0];
                    //delete record from database
                    Max\fetch_none("DELETE FROM resources WHERE rowid = :id;", array("id"=>$file_id_in_database));
                    //delete file now if it exists
                    if(file_exists($location . $file_id_in_database . '-' . $file_name)) {
                        unlink($location . $file_id_in_database . '-' . $file_name);
                        $page_content = '<div class="success">Deleted the file "' . $file_name . '"</div>' . $page_content;
                    } else {
                        throw new RuntimeException("File named \"$file_name\" with id $file_id_in_database was not found on the server");
                    }
                } else {
                    throw new RuntimeException("File named \"$file_name\" with id $file_id_in_database was not found in database");
                }
            }
        }
        else if($action == "Edit") {
            //
        }
        //load images
        $db = new SQLite3("../data/database.db");
        $result = $db->query("SELECT rowid, file_name, description, timestamp, content_type, file_size FROM resources ORDER BY timestamp DESC;");
        $table[0] = array("ID", "Name", "Description", "Uploaded", "Type", "Size", "Image", "Actions");
        while($row = $result->fetchArray(SQLITE3_NUM)) {
            $table[] = array($row[0], $row[1], $row[2], preg_replace("/ /", "<br>", $row[3]), $row[4], Max\formatFileSize($row[5]), '<img class="preview" src="/data/uploads/' . $row[0] . '-' . $row[1] . '" />', Admin\button_form("Delete", array("id"=>$row[0], "action"=>"delete")));
        }
        $result->finalize();
        $db->close();
    }
    catch(Exception $e) {
        $page_content = '<div class="error">' . $e->getMessage() . '</div>' . $page_content;
    }
}


include "template.php";

?>