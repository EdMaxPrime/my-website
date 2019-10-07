<?php 
include "../utils/db.php";
include "../utils/common.php";

Max\increase_page_view_count("blogs");

$search = isset($_GET['q']) ? $_GET['q'] : "";
$search_tags = isset($_GET['tags']) ? $_GET['tags'] : "";

if($search_tags == "") 
    $search_tags = array();
else
    $search_tags = explode(" ", $search_tags);                  
$results = Max\search_blogs($search, $search_tags);
$num_results = count($results);
?>
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
    <link rel="stylesheet" type="text/css" href="/assets/base.css">
    <link rel="stylesheet" type="text/css" href="/assets/blog.css">
    <link rel="stylesheet" type="text/css" href="/assets/cards.css">
    <link rel="stylesheet" type="text/css" href="/assets/form.css">
</head>
<body>
    <?php  $title="Max's Blog"; include "../utils/nav.php"  ?>
    <div id="body">
        <p>This is where you can find my articles and musings that I have released to the internet. </p>
        <div class="form-fancy">
            <form action="<?= $_SERVER['PHP_SELF'] ?>" method="GET" id="search-form">
                <input type="search" name="q" id="search-field" placeholder="Search by title or #tags">
                <input type="hidden" name="tags" id="tag-field" value="">
                <input type="submit" value="Search">
            </form>
        </div>
        <?php if($num_results == 0): ?>
            <p>Sorry, no results were found.</p>
        <?php endif; ?>
    </div>
    <?php for($i = 0; $i < $num_results; $i += 1): ?>
    <?php if($results[$i]['public'] == 0) continue; ?>
    <a class="blog-link-invisible" href="view.php?id=<?= $results[$i]['url'] ?>">
        <div class="card">
            <div class="card-body">
                <div class="card-body-right">
                    <p class="card-title"><?= $results[$i]["title"] ?></p>
                    <span class="card-subtitle"><?= Max\nyDate($results[$i]["published_time"]) ?></span>
                    <p><?= Max\substr_word_wrap(strip_tags($results[$i]["html"]), 300) ?></p>
                </div>
            </div>
            <div class="card-footer">
                <?php $tags = explode(" ", $results[$i]["tags"]);
                for($j = 0, $size = count($tags); $j < $size; $j += 1): ?>
                    <a href="<?= $_SERVER['PHP_SELF'] ?>?tags=<?= $tags[$j] ?>" class="blog-tag"><?= $tags[$j] ?></a>
                <?php endfor; ?>
            </div>
        </div>
    </a>
    <?php endfor; ?>
    

    <script type="text/javascript" src="/assets/waves.js"></script>
    <script type="text/javascript">
    var searchField = document.getElementById("search-field");
    var tagField = document.getElementById("tag-field");
    var tagRegex = (/#[a-zA-Z0-9_\-]+/g);
    document.getElementById("search-form").addEventListener("submit", function() {
        var tags = searchField.value.match(tagRegex);
        searchField.value = searchField.value.replace(tagRegex, ""); //get rid of tags
        searchField.value = searchField.value.replace(/  /g, " "); //get rid of double spaces
        searchField.value = searchField.value.replace(/ $/g, ""); //get rid of space at the end of the search query
        for(var i = 0; i < tags.length; i++) {
            tagField.value += tags[i].substring(1);
            if(i < tags.length - 1) {
                tagField.value += " ";
            }
        }
    });
    </script>

</body>
</html>