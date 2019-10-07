<!DOCTYPE html>
<html>
<head>
    <title>Administrator</title>
    <link rel="stylesheet" type="text/css" href="/assets/form.css">
    <style type="text/css">
    body {background-color: #dfffdf;}
    table {background-color: white; border-collapse: collapse; margin: 20px 10px;}
    th, td {border: 1px solid black; border-collapse: collapse; padding: 0.5em 1em;}
    h1 {text-align: center;}
    a.nav {display: inline-block; color: #078CB8; padding: 10px;}
    div#navbar {text-align: center; background-color: #ffffdf;}
    form.inline {display: inline;}
    a.button {display: inline-block; line-height: 1.5; padding: 0.375rem 0.75rem; border-radius: 0.25rem; border: 1px solid #007bff; text-align: center; vertical-align: middle; background-color: #007bff; color: white; text-decoration: none;}
    a.link {display: inline-block; padding: 2px 6px; color: #2E5077; background-color: #D9DFE6;}
    #admin-panel .tab {display: inline-block; padding: 5px; text-decoration: none; color: black; border-top-left-radius: 5px; border-top-right-radius: 5px; border: 2px solid black; background-color: white; font-family: sans-serif; font-size: 1em;}
    #admin-panel .tab:hover {background-color: #C9E3FA;}
    .push-button {font-family: serif; font-size: 1em; text-decoration: none; color: black; display: inline-block; padding: 5px 10px; border: 2px outset #546F8F; background-color: #eee;}
    .push-button:hover {border: 2px inset #546F8F; background-color: #dfdfdf;}
    img.preview {max-height: 100px; display: inline-block;}
    .selected-borders {border: 2px solid blue;}
    tr.selected {background-color: #ffffdf;}
    </style>
</head>
<body>
    <h1><?=$title ?></h1>
    <div id="navbar">
        <a href="/" class="nav">Home</a>
        <a href="/about/" class="nav">About</a>
        <a href="/blog/" class="nav">Blog</a>
        <a href="/projects/" class="nav">Projects</a>
        <a href="/resume/" class="nav">Resume</a>
        <a href="/contact/" class="nav">Contact</a>
        <a href="/secret/" class="nav">Admin</a>
    </div>
    <?php if(!Admin\loggedin()): ?>
        <form method="POST" action="<?= $_SERVER['PHP_SELF'] ?>">
            <label for="username">Username:</label> <input type="text"     id="username" name="uname"><br>
            <label for="password">Password:</label> <input type="password" id="password" name="pword"><br>
            <input type="submit" name="action" value="Login"><br>
        </form>
    <?php else: ?>
        <div id="admin-panel">
            <a class="tab" href="/secret/index.php">Home</a>
            <a class="tab" href="/secret/mail.php">Fanmail</a>
            <a class="tab" href="/secret/resources.php">Media</a>
            <a class="tab" href="/secret/blogs.php">Blogs</a>
            <a class="tab" href="/secret/write-blog.php">New Blog</a>
            <form method="POST" action="/secret/index.php" class="inline"><input class="tab" type="submit" name="action" value="Logout"></form>
        </div>
        <?php  if(count($table) > 0):  ?>
        <table>
            <tr>
            <?php  for($i = 0; $i < count($table[0]); $i += 1):  ?>
                <th><?=$table[0][$i] ?></th>
            <?php endfor; ?>
            </tr>
            <?php for($row = 1; $row < count($table); $row += 1): ?>
            <tr>
                <?php for($col = 0; $col < count($table[$row]); $col += 1): ?>
                <td><?=nl2br($table[$row][$col]) ?></td>
                <?php endfor; ?>
            </tr>
            <?php endfor; ?>
        </table>
        <?php  endif;  ?>
        <div>
            <?php echo $page_content ?>
        </div>
    <?php endif; ?>
    
</body>
</html>