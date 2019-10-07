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
    <link rel="stylesheet" type="text/css" href="/assets/text.css">
</head>
<body>
    <?php  $title="Let Me Introduce Myself"; include "../utils/nav.php"  ?>
    <div id="body">
        <h2>Thinker, Coder, Writer</h2>
        <p>Those three words sum me up pretty well, but allow me to dive into them a little deeper.</p>
        <p>I think a lot. I'm predisposed to it because I'm an introspective person by nature. I also really like figuring things out. The satisfaction of understanding cause and effect is what makes me tick. That's why I spend a lot of time analyzing and probing the world around me - car commercials, programming language designs, lyrics, and, in a very concrete sense, people's behaviors.</p>
        <p>Coding is something I picked up a few years ago, but which I hadn't imagined to be in my orbit at all. I was technologically illiterate for most of my life. What drew me to coding was a pretty childish desire to make something flashy appear on a computer screen.  I stumbled around with that for a while before I got interested in software design. Navigating the intersection of hardware capabilities and user experience is a tricky deal. I got a taste of that in high school when I took a class called Software Development. We made web-based applications in groups of four, which was fun and frustrating. I got to work backend, frontend, and as project manager, able to experience the woes and joys of all parties when it came to executing a design plan.</p>
        <p>Writing is a long-time hobby of mine. I keep up with it by journaling and sometimes short stories.</p>
        <h2>I Like...</h2>
        <p><b>Science Fiction</b> because it is the only genre that deals with the future and how (whether) technology affects mankind.</p>
        <p><b>Wordplay.</b> I like Vladimir Nabokov's writing style for this reason.</p>
        <p><b>Music.</b> Right now, I'm into soundtracks from musicals.</p>
        <p><b>Esoteric</b> languages and programs. I'm still in the process of exploring the vast <a href="https://esolangs.org/Wiki/Main_Page">Esolangs Wiki</a>. My favorite one is <a href="https://esolangs.org/wiki/.box">(dot) Box</a> for its crafty use of line numbers.</li>
        <p>I thought about presenting this section as a bulleted list, but that would turn it into something I don't like: a series of paragraph-length bullet points. That defeats the purpose of a list: to relay tidbits of information quickly.</p>
        <h2>Details</h2>
        <p>I'm a student in the Macaulay Honors Program at Hunter College in New York City. Previously, I went to Stuyvesant High School.</p>
    </div>

    <script type="text/javascript" src="/assets/waves.js"></script>
    <?php
        include "../utils/db.php";
        Max\increase_page_view_count("about");
    ?>

</body>
</html>