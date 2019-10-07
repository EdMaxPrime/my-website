<?php 

session_unset();
session_destroy();
setcookie(session_name(), "", time()-1000, "/"); //get rid of session cookie
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Administrator</title>
    <style type="text/css">
    body {background-color: #dfffdf;}
    table {background-color: white; border-collapse: collapse; margin: 20px 10px;}
    th, td {border: 1px solid black; border-collapse: collapse; padding: 0.5em 1em;}
    h1 {text-align: center;}
    a.nav {display: inline-block; color: #078CB8; padding: 10px;}
    div#navbar {text-align: center; background-color: #ffffdf;}
    </style>
</head>
<body>
    <h1>Logged Out</h1>
    <div id="navbar">
        <a href="/" class="nav">Home</a>
        <a href="/about/" class="nav">About</a>
        <a href="/projects/" class="nav">Projects</a>
        <a href="/resume/" class="nav">Resume</a>
        <a href="/contact/" class="nav">Contact</a>
        <a href="/secret/" class="nav">Admin</a>
    </div>
    
</body>
</html>