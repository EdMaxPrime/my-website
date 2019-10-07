<?php 

include "db.php";

$question = isset($_GET["ask"]) ? $_GET["ask"] : "";
$argument = isset($_GET["arg"]) ? $_GET["arg"] : "";

if($question == "blog_exists") {
    $answer = Max\load_blog($argument);
    $answer = ($answer == false)? "false" : "true";
    header("Content-Type: application/json");
    echo '{"blog_exists": ' . $answer . '}';
    exit;
}

?>