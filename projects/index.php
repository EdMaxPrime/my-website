<!DOCTYPE html>
<html>
<head>
    <title>Max Zlotskiy</title>
    <link href="https://fonts.googleapis.com/css?family=Lato|Raleway" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="/assets/base.css">
    <link rel="stylesheet" type="text/css" href="/assets/cards.css">
</head>
<body>
    <?php  $title="My Creations"; include "../utils/nav.php"  ?>
    <div id="body">
        This is a gallery of my finished projects:
    </div>
    <div class="card">
        <div class="card-body">
            <div class="card-body-left"><img src="/projects/Reverse-Painting-2.png" class="card-image"></div>
            <div class="card-body-right"><p class="card-title">Reverse Painting 2</p>This is a small puzzle game that runs in the browser (via Processing). It's similar to its predecessor, "Reverse Painting" (2014), in that the goal of the game is to subtract or un-paint from a grid of colored squares until it looks like the picture provided by the level. When you paint a square, you toggle its color between dark and light - which starts a chain reaction that spreads to its four adjacent neighbors. Plan your moves carefully! Reverse Painting 2 comes with thousands of puzzles, 12 difficulties, a demo, a points system based on time and moves, the ability to share puzzles with friends, and translations in 2 languages.</div>
        </div>
        <div class="card-footer">
            <a class="card-link" href="https://www.openprocessing.org/sketch/157979">Check it out</a>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <div class="card-body-left"><img src="/projects/polyforms.png" class="card-image"></div>
            <div class="card-body-right"><p class="card-title">Polyforms</p>Polyforms is a light-weight form creator website. Users create forms(surveys) and gather responses. You can then view the responses in a spreadsheet layout, download them, or make charts. It's easy to change the look, questions, answer formats, and sharing permissions on forms. Most importantly, it's free and you don't need an email to sign up. </div>
        </div>
        <div class="card-footer">
            <a class="card-link card-link-left" href="https://github.com/EdMaxPrime/flash_polyforms">Source Code</a><!--
            --><a class="card-link card-link-right" href="http://polyforms.me">Go to the website</a>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <div class="card-body-left"><img src="/projects/SING.jpg" class="card-image"></div>
            <div class="card-body-right"><p class="card-title">Senior SING! 2018: Ancient Greece</p> This is an hour-long play that I and four other high school seniors wrote in January 2018. It was performed at Stuyvesant's "battle of the bands" style play showcase that doubles as a competition between the grades. Our theme was Ancient Greece, which we tried to portray as authentically as we could without straying into Percy Jackson territory too much. It's full of jokes about high school, monster tropes, and 2018. </div>
        </div>
        <div class="card-footer">
            <a class="card-link card-link-left" href="https://docs.google.com/document/d/e/2PACX-1vQZuK-hq0u0FHjRvVP4_0rD6qFdqui7rLC-jIN_JRmhetOBgQiNeVrPwyIrRFTNa8Pbp52-X9aEFJ4s/pub">Read it</a><!--
            --><a class="card-link card-link-right" href="https://www.youtube.com/watch?v=ERA8RbEKyI0&amp;list=PL809oAkRIAcO_4o-4jChHxfyww2vTNtah">Watch clips</a>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <div class="card-body-left"><img src="/projects/ufo-sightings.gif" class="card-image"></div>
            <div class="card-body-right"><p class="card-title">UFO Sightings</p>This is a data analysis project that Jeffrey Luo, Jason Kao, Khyber Sen and I did as high school seniors as a capstone for a program with Two Sigma. We found a dataset of all UFO sightings in North America, which we cleaned and made pretty cool charts with.</div>
        </div>
        <div class="card-footer">
            <a class="card-link" href="https://github.com/luojeff/aliens-revised">Check it out</a>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <div class="card-body-left"><img src="/projects/Exhibition-1.png" class="card-image"></div>
            <div class="card-body-right"><p class="card-title">Exhibition 1</p>A series of animations that run in a loop.</div>
        </div>
        <div class="card-footer">
            <a class="card-link" href="https://www.openprocessing.org/sketch/407947">Check it out</a>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <div class="card-body-left"><img src="/projects/Dynamic-Equilibrium-16.png" class="card-image"></div>
            <div class="card-body-right"><p class="card-title">Dynamic Equilibrium 1.6</p>This is a simulation of dynamic equilibrium, with LOTS of colors. In this web-based visualization, you get to make a grid of colors and then watch them meld into each other until equilibrium is reached. But it's not "dynamic" until it's interactive; that's why you have the ability to paint with a brush. Watch your strokes fade into the background, or build borders that the colors have to travel around. </div>
        </div>
        <div class="card-footer">
            <a class="card-link" href="https://www.openprocessing.org/sketch/181154">Check it out</a>
        </div>
    </div>

    <script type="text/javascript" src="/assets/waves.js"></script>

    <?php 
        include "../utils/db.php";
        Max\increase_page_view_count("projects");
    ?>

</body>
</html>