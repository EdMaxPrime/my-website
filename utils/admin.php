<?php

namespace Admin;

session_start();
date_default_timezone_set("America/New_York");

//emits a button. SumbitText is a string written on the button, and hiddenFields is an associative array of hidden input element name/value pairs
function button_form($submitText, $hiddenFields) {
    $form = '<form class="inline" method="POST" action="' . $_SERVER["PHP_SELF"] . '">';
    foreach ($hiddenFields as $key => $value) {
        $form = $form . '<input type="hidden" name="' . $key . '" value="' . $value . '">';
    }
    $form = $form . '<input type="submit" name="submit" class="push-button" value="' . $submitText . '">' . '</form>';
    return $form;
}

//NOTE: session_start() must have been called already
function loggedin() {
    $d = getdate();
    $d = $d[mday];
    if($GLOBALS["action"] == "Logout") {
        return false; //logging out
    }
    else if(isset($_SESSION["day"]) && $_SESSION["day"] == $d) {
        return true; //already logged in today
    }
    //trying to login
    else if(isset($_POST["uname"]) && isset($_POST["pword"])) {
        $username = $_POST["uname"];
        $password = $_POST["pword"];
        $success = false;
        try {
            $db = new SQLite3($_SERVER['DOCUMENT_ROOT'] . "/data/accounts.db");
            $result = $db->query("SELECT username, password FROM accounts WHERE admin = 1;");
            $credentials = $result->fetchArray(SQLITE3_NUM);
            $success = ($credentials[0] == $username && $credentials[1] == $password);
            $result->finalize();
            $db->close();
        }
        catch(Exception $e) {
            //if something went wrong with the database, nobody should log in
        }
        if($success == true) {
            $_SESSION["day"] = $d;
        }
        return $success;
    }
    return false; //not already logged in and no attempt to login being made
}

//should be called once per page load
function handle_logout() {
    if($GLOBALS["action"] == "Logout") {
        session_unset();
        session_destroy();
        setcookie(session_name(), "", time()-1000, "/"); //get rid of session cookie
    }
}

function update_accounts_database() {
    try {
        $db = new SQLite3($_SERVER['DOCUMENT_ROOT'] . "/data/accounts.db");
        $result = $db->query("PRAGMA user_version;");
        $version = $result->fetchArray(SQLITE3_NUM);
        $version = $version[0]; //only one column in result set, so the array may as well become an integer
        $result->finalize();
        if($version == 0) {
            $db->exec("CREATE TABLE IF NOT EXISTS accounts (username TEXT, password TEXT, admin INTEGER, email TEXT, last_login TEXT, last_page TEXT, last_failed_login TEXT);");
            $db->exec("CREATE TABLE IF NOT EXISTS special_links (recipient_email TEXT, sent_timestamp TEXT, expires_timestamp TEXT, link_id TEXT, check_value TEXT, action TEXT, action_target TEXT, expired INTEGER);");
            $db->exec("PRAGMA user_version = 1;");
            $version = 1;
        }
        $db->close();
        return true;
    }
    catch(Exception $e) {
        return false;
    }
}

/* Used to validate a special link and get its action on success
@param link_id
@param check_value
@return  false if link expired or neither of the checked values matched; otherwise, returns true
 */
function validate_special_link() {}

/* Returns information about the special link's target
@param link_id
@param check_value
@return  an associative array with the keys "action" and "action_target"
*/
function get_special_link_info() {}

/*
@param recipient_email  (string) email address of who you're sending it to; used for record keeping
@param expires          (string) when this link will expire as YYYY-MM-DD HH:MM:SS
@param check_value      (string) used to validate this link
@param action           (string) stores information about the intent of this link once it is validated
@param action_target    (string) stores information about the link's target
@return                 (string) the link's unique id.
*/
function create_special_link() {}

?>