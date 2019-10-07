<?php 

namespace Max;

function db_path() {
    return $_SERVER["DOCUMENT_ROOT"] . "/data/database.db";
}

function get_db_version() {
    $version = '0';
    try {
        $db = new \SQLite3(db_path());
        $result = $db->query("SELECT name FROM sqlite_master WHERE type='table' AND name='properties';");
        $table_exists = $result->fetchArray();
        $result->finalize();
        if($table_exists != false) {
            $result = $db->query("SELECT value FROM properties WHERE name='version';");
            $version = $result->fetchArray(SQLITE3_NUM);
            $version = $version[0]; //can't access arrays after function call in php 5.3
            $result->finalize();
        }
        $db->close();
    }
    catch(\Exception $e) {
        $version = '0';
    }
    return $version;
}

function upgrade_db() {
    $version = get_db_version();
    try {
        $db = new \SQLite3(db_path());
        if($version == '0') {
            $db->exec("CREATE TABLE IF NOT EXISTS mail(timestamp TEXT, name TEXT, email TEXT, message TEXT);");
            $db->exec("CREATE TABLE IF NOT EXISTS properties(name TEXT, value TEXT);");
            $db->exec("INSERT INTO properties VALUES ('version', '1');");
            $db->exec("INSERT INTO properties VALUES ('last-updated', '');");
            $version = '1';
        }
        if($version == '1') {
            $db->exec("CREATE TABLE IF NOT EXISTS blogs (url TEXT PRIMARY KEY, title TEXT, author TEXT, published_time TEXT, last_edited TEXT, tags TEXT, markdown TEXT, html TEXT, views INTEGER, public INTEGER, include_highlighter INTEGER, custom_scripts TEXT, inline_scripts TEXT);");
            $version = '2';
        }
        if($version == '2') {
            $db->exec("CREATE TABLE IF NOT EXISTS resources (file_name TEXT, description TEXT, timestamp TEXT, content_type TEXT, file_size INTEGER);");
            $version = '3';
        }
        if($version == '3') {
            $db->exec("CREATE TABLE IF NOT EXISTS visits (page_id INTEGER, timestamp TEXT, views INTEGER);");
            $version = '4';
        }
        $db->exec("UPDATE properties SET value = '$version' WHERE name = 'version';");
        $db->exec("UPDATE properties SET value = datetime('now') WHERE name = 'last-updated';");
        $db->close();
        return true;
    }
    catch(\Exception $e) {
        return false;
    }
}

function erase_db() {
    try {
        $db = new \SQLite3(db_path());
        $db->exec("DROP TABLE properties");
        $db->close();
        return true;
    }
    catch(\Exception $e) {
        return false;
    }
}

function delete_rows($table, $col, $test) {
    try {
        $db = new \SQLite3(db_path());
        $statement = $db->prepare("DELETE FROM " . $table . " WHERE " . $col . " = :test;");
        $statement->bindValue(":test", $test);
        $statement->execute();
        $statement->close();
        $db->close();
        return true;
    }
    catch(Exception $e) {
        return false;
    }
}

//returns false if blog not found, associative array otherwise
function load_blog($url) {
    return fetch_one("SELECT * FROM blogs WHERE url = :url;", array("url"=>$url), SQLITE3_ASSOC);
}

//increases view count on blog by 1, returns true on success and false on failure
function increase_blog_view_count($url) {
    increase_page_view_count($url);
    return fetch_none("UPDATE blogs SET views = views + 1 WHERE url = :url;", array("url"=>$url));
}

//increases daily view count for specified page. page_id should be an integer for blogs and string for other pages.
function increase_page_view_count($page_id) {
    if(fetch_one("SELECT * FROM visits WHERE page_id = :page_id AND timestamp = date();", array("page_id"=>$page_id)) == false) {
        fetch_none("INSERT INTO visits VALUES (:page_id, date(), 1);", array("page_id"=>$page_id));
    } else {
        fetch_none("UPDATE visits SET views = views + 1 WHERE page_id = :page_id AND timestamp = date();", array("page_id"=>$page_id));
    }
}

//@param keywords     a string to be matched against titles of blogs, can be empty
//@param tags         an array of tags to be matched using OR, can be empty
//@return             an array of blogs that filtered through, containing all their column data
function search_blogs($keywords, $tags) {
    $query = "SELECT * FROM blogs ";
    $conditions = array();
    $values = array("keywords"=>$keywords);
    if($keywords != "") {
        $conditions[] = "(title LIKE '%' || :keywords || '%')";
    }
    $num_tags = count($tags);
    if($num_tags > 0) {
        $s = "(";
        for($i = 0; $i < $num_tags; $i++) {
            if($i > 0) {$s .= " OR ";}
            $s .= "tags LIKE '%' || :tag" . $i . " || '%'";
            $values["tag" . $i] = $tags[$i];
        }
        $s .= ")";
        $conditions[] = $s;
    }
    if(count($conditions) > 0) {
        $query .= " WHERE " . implode(" AND ", $conditions);
    }
    $query .= " ORDER BY last_edited DESC;";
    return fetch_many($query, $values, SQLITE3_ASSOC);
}

//returns an array of results of the query; each result is an array indexed by key and numerical index; returns empty array on failure
function fetch_many($query, $values, $index = SQLITE3_BOTH) {
    $result_array = array();
    try {    
        $db = new \SQLite3(db_path());
        $statement = $db->prepare($query);
        foreach ($values as $key => $value) {
            $statement->bindValue(":" . $key, $value);
        }
        $result = $statement->execute();
        if($result) {
            while($row = $result->fetchArray($index)) {
                $result_array[] = $row;
            }
            $result->finalize();
        }
        $statement->close();
        $db->close();
    }
    catch(\Exception $e) {}
    return $result_array;
}

//Returns an array indexed by key and numerical index for the the first result of the query; returns false on failure
function fetch_one($query, $values, $index = SQLITE3_BOTH) {
    $result_array = false;
    try {    
        $db = new \SQLite3(db_path());
        $statement = $db->prepare($query);
        foreach ($values as $key => $value) {
            $statement->bindValue(":" . $key, $value);
        }
        $result = $statement->execute();
        if($result) {
            $result_array = $result->fetchArray($index);
            $result->finalize();
        }
        $statement->close();
        $db->close();
    }
    catch(\Exception $e) {}
    return $result_array;
}

//Executes a resultless SQL statement; returns true/false if it worked; values should be an associative array, can be empty
function fetch_none($query, $values) {
    $worked = false;
    try {    
        $db = new \SQLite3(db_path());
        $statement = $db->prepare($query);
        foreach ($values as $key => $value) {
            $statement->bindValue(":" . $key, $value);
        }
        $result = $statement->execute();
        if($result) {
            $worked = true;
            $result->finalize();
        }
        $statement->close();
        $db->close();
    }
    catch(\Exception $e) {}
    return $worked;
}

class MaxDB {
    public $path;
    public $db;

    public function __construct($filename) {
        $this->path = $_SERVER['DOCUMENT_ROOT'] . "/data/" . $filename;
        $this->db = new \SQLite3($this->path);
    }

    public function __destruct() {
        $db->close();
    }
}


?>