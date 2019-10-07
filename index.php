<!DOCTYPE html>
<html>
<head>
    <title>Max Zlotskiy</title>
    <meta charset="UTF-8">
    <meta name="description" content="Max Zlotskiy's personal website">
    <meta name="keywords" content="Max,Zlotskiy">
    <meta name="author" content="Max Zlotskiy">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css?family=Lato|Raleway" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="home.css">
</head>
<body>
    <div id="header">
        <div>
            <h1 id="title">Max Zlotskiy</h1>
            <div class="navbar">
                <a href="#body" class="navigation">About</a><!--
                --><a href="/blog/" class="navigation">Blog</a><!--
                --><a href="/projects/" class="navigation">Projects</a><!--
                --><a href="/resume/" class="navigation">Resume</a><!--
                --><a href="/contact/" class="navigation">Contact</a>
            </div>
        </div>
    </div>
    <div><img src="max.jpg" class="picture"></img></div>
    <div id="body">
        <h2>Welcome</h2>
        <p>You've reached my personal website. Feel free to peruse these pages to learn more about me!</p>
        <?php
            include "utils/db.php";
            $blog = Max\load_blog("about");
            Max\increase_page_view_count("home");
            if($blog != false) {
                echo $blog["html"];
            }
        ?>
    </div>

</body>
</html>