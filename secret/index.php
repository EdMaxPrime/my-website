<?php 

include "../utils/db.php";
include "../utils/common.php";
include "../utils/admin.php";

$title = "Home";
$debug = "";
$table = array();
$page_content = "";
$action = isset($_POST["action"])? $_POST["action"] : "";

Admin\handle_logout();

if(Admin\loggedin()) {
    $_SESSION["page"] = "Blogs";
    try {
        $db = new SQLite3("../data/database.db");
        $_SESSION["page"] = "Home";
        if(isset($_POST["action2"])) {
            if($_POST["action2"] == "upgrade_db") {Max\upgrade_db();}
            else if($_POST["action2"] == "erase_db") {Max\erase_db();}
        }
        if(Max\get_db_version() == '0') {
            $page_content = "<p>Database version: 0</p>";
        }
        else {
            $table[0] = array("Name", "Value");
            $result = $db->query("SELECT name, value FROM properties;");
            while($row = $result->fetchArray(SQLITE3_NUM)) {
                $table[] = $row;
            }
            $result->finalize();
        }

        $page_content = $page_content . Admin\button_form("Upgrade", array("action2"=>"upgrade_db")). Admin\button_form("Erase", array("action2"=>"erase_db"));
        $db->close();
    }
    catch(Exception $e) {
        $page_content = "Something went wrong";
    }
}


include "template.php";

?>