<?php 

include "../utils/db.php";
include "../utils/common.php";
include "../utils/admin.php";

$title = "Fanmail";
$debug = "";
$table = array();
$page_content = "";
$action = isset($_POST["action"])? $_POST["action"] : "";

if(Admin\loggedin()) {
    $_SESSION["page"] = "Fanmail";
    try {
        $db = new SQLite3("../data/database.db");
        $result = $db->query("SELECT timestamp, name, email, message FROM mail ORDER BY timestamp DESC;");
        $table[0] = array("When", "Name", "Email", "Message");
        while($row = $result->fetchArray(SQLITE3_NUM)) {
            $table[] = $row;
        }
        $result->finalize();
        $db->close();
    }
    catch(Exception $e) {
        $page_content = '<div class="error">Something went wrong</div>';
    }
}


include "template.php";

?>