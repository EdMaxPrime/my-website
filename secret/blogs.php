<?php 

include "../utils/db.php";
include "../utils/common.php";
include "../utils/admin.php";

$title = "Blogs";
$debug = "";
$table = array();
$page_content = "";
$action = isset($_POST["action"])? $_POST["action"] : "";
$my_errors = "";

if(Admin\loggedin()) {
    $_SESSION["page"] = "Blogs";
    try {
        $db = new SQLite3("../data/database.db");
        if(isset($_POST["deleteID"])) {
            Max\delete_rows("blogs", "url", $_POST["deleteID"]);
        }
        if(isset($_POST["copyID"])) {
            $blog = Max\load_blog($_POST["copyID"]);
            if($blog != false) {
                $blog['url'] = $blog['url'] . '-copy';
                $blog['title'] = $blog['title'] . ' (COPY)';
                Max\fetch_none("INSERT INTO blogs (url, title, author, published_time, last_edited, tags, markdown, html, views, public, include_highlighter, custom_scripts, inline_scripts) VALUES (:url, :title, :author, datetime('now'), datetime('now'), :tags, :markdown, :html, 0, :public, :include_highlighter, '', '');", $blog);
            } else {
                $my_errors .= " Could not find blog to be duplicated. ";
            }
        }
        $result = $db->query("SELECT url, title, published_time, last_edited, tags, views, public FROM blogs;");
        $table[0] = array("Title", "Published", "Last Edited", "Views", "Public", "Tags", "Actions");
        while($row = $result->fetchArray(SQLITE3_ASSOC)) {
            $table[] = array(
                '<a class="link" href="/blog/view.php?id=' . $row["url"] . '">' . $row["title"] . '</a>',
                Max\nyTime($row["published_time"]),
                Max\nyTime($row["last_edited"]),
                $row["views"],
                ($row["public"] == 1)? "&#10004" : "",
                $row["tags"],
                '<a class="push-button" href="/secret/write-blog.php?id=' . $row["url"] . '">Edit</a>' .
                Admin\button_form("Delete", array("action" => "Blogs", "deleteID" => $row["url"])) .
                Admin\button_form("Copy", array("copyID" => $row["url"]))
            );
        }
        $result->finalize();
        $db->close();
    }
    catch(Exception $e) {
        $my_errors .= " Something went wrong loading the blogs. ";
    }
}

if($my_errors != "") $page_content = '<div class="error">' . $my_errors . '</div>';


include "template.php";

?>