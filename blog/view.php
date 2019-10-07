<?php 

session_start();
date_default_timezone_set("America/New_York");

include "../utils/db.php";
include "../utils/admin.php";
include "../utils/common.php";

$blog_url = isset($_GET["id"]) ? $_GET["id"] : "";
$blog = Max\load_blog($blog_url);

//if this blog was found on the server
if($blog != false) {
    //you must be logged in to view private blogs; hide it otherwise
    if($blog["public"] == 0 && !Admin\loggedin()) {
        $blog = false;
    }
    //this is a valid blog that can be views
    else {
        Max\increase_blog_view_count($blog_url);
    }
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Max Zlotskiy</title>
    <link href="https://fonts.googleapis.com/css?family=Lato|Raleway" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="/assets/base.css">
    <link rel="stylesheet" type="text/css" href="/assets/blog.css">
    <?php if($blog && $blog["include_highlighter"]): ?>
    <link rel="stylesheet" type="text/css" href="/assets/kimbie-light.css">
    <?php endif; ?>
    <!-- Regular meta tags -->
    <meta charset="UTF-8">
    <meta name="description" content="Max Zlotskiy's personal website">
    <meta name="keywords" content="Max,Zlotskiy">
    <meta name="author" content="Max Zlotskiy">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Open Graph Tags for social media -->
    <?php if($blog): ?>
    <meta property="og:title" content="<?= $blog['title'] ?>">
    <meta property="og:description" content="<?= Max\substr_word_wrap(strip_tags($blog["html"]), 300) ?>">
    <meta property="og:image" content="">
    <meta property="og:url" content="http://max.zlotskiy.com/blog/view.php?id=<?= $blog_url ?>">
    <meta property="og:type" content="article">
    <meta property="og:type:article" content="article">
    <meta property="article:published_time" content="<?= $blog['published_time'] ?>">
    <meta property="article:modified_time" content="<?= $blog['last_edited'] ?>">
    <meta property="article:tag" content="<?= $blog['tags'] ?>">
    <meta property="article:author" content="Max Zlotskiy">
    <meta property="twitter:title" content="<?= $blog['title'] ?>">
    <meta property="twitter:description" content="<?= Max\substr_word_wrap(strip_tags($blog["html"]), 300) ?>">
    <meta property="twitter:image" content="">
    <?php endif; ?>
</head>
<body>
    <?php  $title="Max's Blog"; include "../utils/nav.php"  ?>
    <div id="body">
        <?php if($blog): ?>
            <h1 class="blog-title"><?= $blog["title"]; ?></h1>
            <span class="time">Published <?= Max\nyTime($blog["published_time"]) ?></span>
            <br>
            <span class="author">by <?= $blog["author"]; ?></span>
            <div id="blog-content"><?= $blog["html"] ?></div>
            <br>
            <div>
                <b>Tags:</b>
                <?php $tags = explode(" ", $blog["tags"]);
                for($j = 0, $size = count($tags); $j < $size; $j++): ?>
                    <a href="/blog/index.php?tags=<?= $tags[$j] ?>" class="blog-tag"><?= $tags[$j] ?></a>
                <?php endfor; ?>
            </div>
            <?php if($blog["include_highlighter"]): ?>
                <script type="text/javascript" src="/assets/rainbow-custom.min.js"></script>
            <?php endif; ?>
        <?php else: ?>
            <h1>Couldn't find this blog...</h1>
            <p>The blog "<?=$blog_url;?>" wasn't found. Check the url?</p>
        <?php endif; ?>
    </div>

    <script type="text/javascript" src="/assets/waves.js"></script>

</body>
</html>