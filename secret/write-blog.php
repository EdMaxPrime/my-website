<?php 

include '../utils/db.php';
include '../utils/common.php';
include '../utils/admin.php';


$title = "Edit Blog";
$debug = "";
$table = array();
$page_content = "";
$action = isset($_POST["action"])? $_POST["action"] : "";

//php adds \ backslashes to Magic Quotes for some reason. This will remove slashes
if(get_magic_quotes_gpc()) {
    foreach ($_POST as $key => $value) {
        $_POST[$key] = stripslashes($value);
    }
}

$blog_title = isset($_POST["title"])? $_POST["title"] : "";
$blog_url = isset($_GET["id"])? $_GET["id"] : "";
$blog_author = isset($_POST["author"])? $_POST["author"] : "Max Zlotskiy";
$blog_tags = isset($_POST["tags"])? $_POST["tags"] : "";
$blog_markdown = isset($_POST["markdown"])? $_POST["markdown"] : "";
$blog_html = isset($_POST["html"])? $_POST["html"] : "";
$blog_public = (isset($_POST["public"]) && $_POST["public"]=="public")? 1 : 0;
$blog_highlighter = (isset($_POST["include_highlighter"]) && $_POST["include_highlighter"]=="yes")? 1 : 0;
$blog_last_edited = "";

$my_errors = "";

//function for mapping pictures to picture names
function map_picture($array) {
    $array[] = "../data/uploads/" . $array[0] . '-' . $array[1];
    return $array;
}

if(Admin\loggedin()) {
    try {
        $db = new SQLite3("../data/database.db");
        //First, try to save the blog
        if($action == "Save") {
            $blog_url_new = isset($_POST["url"]) ? $_POST["url"] : $blog_url;
            $query = "INSERT INTO blogs (url, title, author, published_time, last_edited, tags, markdown, html, views, public, include_highlighter, custom_scripts, inline_scripts) VALUES (:url, :title, :author, datetime('now'), datetime('now'), :tags, :markdown, :html, 0, :public, :include_highlighter, '', '');";
            if(Max\load_blog($blog_url)) {
                $query = "UPDATE blogs SET title = :title, author = :author, last_edited = datetime('now'), tags = :tags, markdown = :markdown, html = :html, public = :public, include_highlighter = :include_highlighter WHERE url = :url;";
            }
            $statement = $db->prepare($query);
            $statement->bindValue(":url", $blog_url);
            $statement->bindValue(":title", $blog_title);
            $statement->bindValue(":author", $blog_author);
            $statement->bindValue(":tags", $blog_tags);
            $statement->bindValue(":markdown", $blog_markdown);
            $statement->bindValue(":html", $blog_html);
            $statement->bindValue(":public", $blog_public);
            $statement->bindValue(":include_highlighter", $blog_highlighter);
            if(!$statement->execute()) {
                $my_errors .= "Could not update blog.<br>";
            }
            $statement->close();
            if($blog_url != $blog_url_new && $blog_url_new != "") {
                $statement = $db->prepare("UPDATE blogs SET url = :url WHERE url = :old_url;");
                $statement->bindValue(":old_url", $blog_url);
                $statement->bindValue(":url", $blog_url_new);
                if(!$statement->execute()) {
                    $my_errors .= "Could not change blog url.<br>";
                }
                $statement->close();
                $blog_url = $blog_url_new;
            }
        }
        //Next, try to load the blog into the form
        if($blog_url != "") {
            $statement = $db->prepare("SELECT url, title, author, last_edited, tags, markdown, html, public, include_highlighter FROM blogs WHERE url = :url;");
            $statement->bindValue(":url", $blog_url);
            $result = $statement->execute();
            if($result) {
                $result_array = $result->fetchArray(SQLITE3_ASSOC);
                if($result_array) {
                    $blog_title = $result_array["title"];
                    $blog_author = $result_array["author"];
                    $blog_last_edited = $result_array["last_edited"];
                    $blog_tags = $result_array["tags"];
                    $blog_markdown = $result_array["markdown"];
                    $blog_html = $result_array["html"];
                    $blog_public = $result_array["public"];
                    $blog_highlighter = $result_array["include_highlighter"];
                }
                else {
                    $my_errors .= "This blog could not be found<br>";
                }
                $result->finalize();
            }
            $statement->close();
        }
        $db->close();
        //try to load pictures from database, empty array on failure
        $pictures = Max\fetch_many("SELECT rowid, file_name, description FROM resources ORDER BY timestamp DESC;", array(), SQLITE3_NUM);
        $pictures = array_map(map_picture, $pictures);
    } catch(Exception $e) {
        $my_errors .= $e->getMessage();
    }
}

ob_start();

?>
    <div id="blog-fields">
        <form method="POST" action="/secret/write-blog.php<?php if($blog_url != ""): echo '?id=' . $blog_url; endif; ?>" class="form-fancy" id="blog-editor">
            <p class="form-description">
                <?php if($blog_last_edited != "") {echo "Last edited: " . Max\nyTime($blog_last_edited);} else {echo "Fill out information about the blog below";} ?>
                Preview
                <a href="/blog/view.php?id=<?=$blog_url?>" class="link">here</a>
                or
                <a href="/blog/view.php?id=<?=$blog_url?>" class="link" target="_blank">new tab</a>
            </p>

            <?php if($my_errors != ""): ?>
                <div class="error"><?= $my_errors; ?></div>
            <?php endif; ?>

            <div class="horizontal-options">
                <b>Visibility:</b>
                <input type="radio" id="blog-public" name="public" value="public" <?php if($blog_public == 1) {echo "checked";} ?> >
                <label for="blog-public">Public</label>
                <input type="radio" id="blog-private" name="public" value="" <?php if($blog_public == 0) {echo "checked";} ?> >
                <label for="blog-private">Private</label>
            </div>

            <label for="blog-title">Title</label>
            <input type="text" name="title" value="<?= $blog_title; ?>" id="blog-title">
            
            <label for="blog-url">Short url <span id="blog-url-status"></span></label>
            <input type="text" name="url" value="<?= $blog_url; ?>" id="blog-url">

            <label for="blog-author">Author</label>
            <input type="text" name="author" value="<?= $blog_author; ?>" id="blog-author">

            <label for="blog-tags">Space-separated tags</label>
            <input type="text" name="tags" value="<?= $blog_tags; ?>" id="blog-tags">

            <div class="horizontal-options">
                <b>Code Highlighting:</b>
                <input type="radio" id="blog-highlighter-on" name="include_highlighter" value="yes" <?php if($blog_highlighter == 1) {echo "checked";} ?> >
                <label for="blog-highlighter-on">On</label>
                <input type="radio" id="blog-highlighter-off" name="include_highlighter" value="" <?php if($blog_highlighter == 0) {echo "checked";} ?> >
                <label for="blog-highlighter-off">Off</label>
            </div>

            <div>
                <select id="textarea-font"><option value="sans-serif" selected>Sans-serif</option><option value="monospace">Monospace</option></select>
                <button type="button" id="toggle-media-browser">Media Browser</button>
                <button type="button" id="toggle-reference">Markdown Cheatsheet</button>
                <button type="button" id="insert-tableOfContents">Table Of Contents</button>
                <div id="reference" style="display: none;">
                    <table>
                        <tr><td>Italic</td><td><em>*italic EM tag* or _italic_</em></td></tr>
                        <tr><td>Bold</td><td><strong>**bold STRONG tag** or __bold__</strong></td></tr>
                        <tr><td>Line break</td><td>end the line with 2 or more spaces, then press enter</td></tr>
                        <tr><td>Paragraph</td><td>leave a blank line between paragraphs, don't indent</td></tr>
                        <tr><td>Headings</td><td>H1 with ===== under it, or H2 with ---- under it, or for a one-liner do <code>###### Header Text</code> for H1-H6</td></tr>
                        <tr><td>Headings with IDs</td><td>With extended markdown, you can add the ID for a heading like so: <code># Heading {#custom-id}</code> and then link to it <code>[top](#custom-id)</code></td></tr>
                        <tr><td>Horizontal Rule</td><td>Three or more asterisks ***, dashes ---, or underscores ___ on a line</td></tr>
                        <tr><td>Links</td><td><code>&lt;link or email&gt;</code> OR <code>[link text](address "optional hover tooltip")</code></td></tr>
                        <tr><td>References</td><td>First place the reference in text: <code>[link text][id]</code> then define the ID somewhere else in the document: <code>[id]: address (hover tooltip)</code>. The ID is <i>case-insensitive</i> and can include spaces, numbers, punctuation. The address can be enclosed in angle brackets. The hover tooltip can be enclosed in single or double quotes instead of parenthesis.</td></tr>
                        <tr><td>Images</td><td><code>![image alt text](image_url "hover tooltip")</code>. To add a link around the image: <code>[![image alt text](image_url "hover tooltip")](link_url)</code></td></tr>
                        <tr><td>Code</td><td>Indent code block with four spaces or a tab. To avoid indenting, put three tick marks (`) on the lines before and after the code block. Write the language name after the tick marks on the line before the code block.</td></tr>
                        <tr><td>Inline code</td><td>Surround phrase with tick marks. To escape tick marks, use two: <code>``Escaped `code` here``</code></td></tr>
                        <tr><td>Lists</td><td>Ordered lists are created with numbers followed by dots followed by the list item. The order of numbers don't matter as long as the list starts with 1. Unordered lists can be created using +, * or -; mixing them is allowed. To make a sublist, indent the list items four spaces or one tab, To include block elements in a list item, indent them four spaces or one tab. Make sure to indent code blocks twice.</td></tr>
                        <tr><td>Tables</td><td>Extended markdown syntax allows for tables. Columns are separated with pipes (|). To include a pipe in a table, use <code>&amp;#124;</code>. The first row should have three or more dashes (---) below it. Cell widths don't matter. To change the alignment of a column, add a colon before(:--- left), after(---: right), or on both sides (:---: centered) of the dashes under the first row. It is optional to add pipes to the edges of a table. You can't use html, code <i>blocks</i>, blockquotes, lists, horizontal rules, headings or images in tables.<br><code>
| column | centered|
|--------|:-------:|
|row|cell content|
                        </code></td></tr>
                        <tr><td>More</td><td><a href="https://www.markdownguide.org/basic-syntax/">Full Markdown reference</a></td></tr>
                    </table>
                </div>
                <div id="media-browser-container" style="border: 1px solid black; display: none;">
                    <b>Click on a picture, choose a style, then click insert</b>
                    <button type="button" id="media-browser-insert">Insert</button>
                    <select id="media-browser-style">
                        <option value="centered" selected>Full size, centered</option>
                        <option value="full-caption">Full size, centered, with caption</option>
                        <option value="emoji">Inline with text, like emoji</option>
                    </select>
                    <div id="media-browser-list" style="width: 100%; height: 200px; overflow-y: scroll; overflow-x: hidden; background-color: white;">
                        <?php for($i = 0; $i < count($pictures); $i++): ?>
                        <img class="preview" src="<?= $pictures[$i][3] ?>" onclick="selectImage(<?= $i ?>);" id="media-browser-item-<?= $i ?>" />
                        <?php endfor; ?>
                    </div>
                </div>
            </div>

            <label for="blog-markdown">Markdown content</label>
            <textarea name="markdown" id="blog-markdown" rows="20"><?= $blog_markdown; ?></textarea>

            <input type="hidden" name="html" id="blog-html" value="">

            <input type="submit" name="action" value="Save">
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
    <script src="../assets/behave.js"></script>
    <script src="../assets/reqwest.min.js"></script>
    <script src="../assets/rainbow-custom.min.js"></script>
    <script>
        /* Stop Rainbow code highlighter from activating on this page */
        Rainbow.defer = true;
        var textarea = document.getElementById("blog-markdown");

        /* Compile markdown into HTML and create table of contents */
        var markdownRenderer = new marked.Renderer();
        var headings = [];
        //wrap <pre><code> blocks in a container div that can be scrolled
        markdownRenderer.code = function(code, infoString, escaped) {
            return '\n<div class="code-container">\n' + marked.Renderer.prototype.code.call(this, code, infoString, escaped) + '\n</div>\n';
        }
        //get info about headings to be used in table of contents
        markdownRenderer.heading = function(text, level, raw, uniqueIdGenerator) {
            var id = uniqueIdGenerator.slug(raw);
            if(headings.length == 0) {
                headings.push({text: text, id: id, level: level, levelChange: 0});
            } else {
                headings.push({text: text, id: id, level: level, levelChange: level - headings[headings.length - 1].level});
            }
            return "<h" + level + ' id="' + id + '">' + text + "</h" + level + ">\n";
        }
        markdownRenderer.toc = function(sections) {
            var html = '<div class="tableOfContents-container">\n    <p class="tableOfContents-title">Table Of Contents</p>\n    <ol>\n';
            var closingTags = ["</ol></div>"];
            var indent = "    ";
            for(var i = 0; i < sections.length; i++) {
                //if unindenting, then insert closing tags for sublist
                for(var j = sections[i].levelChange; j < 0; j++) {
                    html += closingTags.pop();
                    indent = indent.substring(4);
                }
                //this is the current section's link and list item
                var listItem = indent + '<li><a href="#' + sections[i].id + '">' + sections[i].text + '</a>';
                //if the next item starts a sublist, put a closing <li> on the stack instead of putting it at the end of the line
                if(i < sections.length - 1 && sections[i + 1].levelChange > 0) {
                    html += listItem + "\n" + indent + "<ol>\n";
                    closingTags.push(indent + "</ol>\n" + indent + "</li>\n");
                    indent += "    ";
                }
                //this is the last section or there is no list nesting change. Put closing <li> at the end of the line.
                else {
                    html += listItem + "</li>\n";
                }
            }
            while(closingTags.length > 0) {
                html += closingTags.pop();
            }
            console.log(html);
            return html;
        }
        document.getElementById("blog-editor").addEventListener("submit", function() {
            document.getElementById('blog-html').value = marked(textarea.value, {
                headerIds: true,
                gfm: true, 
                langPrefix: "language-",
                tables: true,
                renderer: markdownRenderer
            }).replace(/<p>{{[ ]?table of contents[ ]?}}<\/p>/mi, markdownRenderer.toc(headings));
        });

        /* Indent block of markdown at press of a tab key (supports multiline indent) */
        var editor = new Behave({
            textarea: textarea,
            replaceTab: true,  //tabs don't cycle input focus, instead allows for multiline indentation and de-indentation (hold shift and tab)
            softTabs: true,    //replace tabs with spaces
            tabSize: 4,        //how many spaces per tab
            autoOpen: true,    //typing an opening character will also insert closing character
            overwrite: true,   //allows you to skip over closing characters while typing
            autoStrip: true,   //deleting an opening character also deletes the closing one if they're next to each other
            autoIndent: true,  //tries to auto-indent code inside of {}
            fence: false       //set to a string to limit behavior to fenced code area
        });
        
        /* Change font of textarea */
        document.getElementById("textarea-font").addEventListener("change", function() {
            textarea.style.fontFamily = this.options[this.selectedIndex].value;
        });
        
        /* Toggle display of markdown reference via button */
        var showingReference = false;
        var reference = document.getElementById("reference");
        document.getElementById("toggle-reference").addEventListener("click", function() {
            if(showingReference == true) {
                reference.style.display = "none";
            } else {
                reference.style.display = "block";
            }
            showingReference = !showingReference;
        });
        
        /* Returns a DOM object. The tag name is a string, as is innerHTML. Attributes should be a JSON object*/
        var createElement = function(tag, innerHTML, attributes) {
            var element = document.createElement(tag);
            element.innerHTML = innerHTML;
            for(var key in attributes) {
                element.setAttribute(key, attributes[key]);
            }
            return element;
        };
        
        /* Image selection and insert html code */
        var pictures = <?= json_encode($pictures) ?> ;
        var selectedImageIndex = 0;
        var insertStyle = document.getElementById("media-browser-style");
        var selectImage = function(imageID) {
            document.getElementById("media-browser-item-"+selectedImageIndex).classList.remove("selected-borders");
            selectedImageIndex = imageID;
            document.getElementById("media-browser-item-"+imageID).classList.add("selected-borders");
        };
        var insertImage = function() {
            document.getElementById("media-browser-item-"+selectedImageIndex).classList.remove("selected-borders");
            var insertIndex = textarea.selectionStart;
            var str;
            var style = insertStyle.options[insertStyle.selectedIndex].value;
            if(style == "emoji") {
                str = '<img src="' + pictures[selectedImageIndex][3] + '" alt="' + pictures[selectedImageIndex][2] + '" class="inline-small">';
            }
            else if(style == "full-caption") {
                str = '<figure class="centered"><a href="' + pictures[selectedImageIndex][3] + '"><img src="' + pictures[selectedImageIndex][3] + '" alt="' + pictures[selectedImageIndex][2] + '" /></a><figcaption>CAPTION</figcaption></figure>';
            }
            else {
                str = '<div class="centered"><a href="' + pictures[selectedImageIndex][3] + '"><img src="' + pictures[selectedImageIndex][3] + '" alt="' + pictures[selectedImageIndex][2] + '" /></a></div>';
            }
            textarea.value = textarea.value.substring(0, insertIndex) + str + textarea.value.substring(textarea.selectionEnd);
        }
        document.getElementById("media-browser-insert").addEventListener("click", insertImage);
        
        /* Toggle display of media browser via button */
        var showingMediaBrowser = false;
        var mediaBrowser = document.getElementById("media-browser-container");
        document.getElementById("toggle-media-browser").addEventListener("click", function() {
            if(showingMediaBrowser == true) {
                mediaBrowser.style.display = "none";
            } else {
                mediaBrowser.style.display = "block";
            }
            showingMediaBrowser = !showingMediaBrowser;
        });

        /* Generate short blog url while typing title */
        var blogURL = document.getElementById("blog-url");
        var blogURLChanged = false;
        document.getElementById("blog-title").addEventListener("input", function() {
            if(blogURLChanged == false) {
                blogURL.value = this.value.replace(/[^A-Za-z0-9\-_+]/g, "-");
                updateBlogURLStatus();
            }
        });
        blogURL.addEventListener("change", function() {
            blogURLChanged = true;
        });

        /* Check if the blog url is taken */
        var blogURLStatus = document.getElementById("blog-url-status");
        var blogURLTimeout = false;
        var updateBlogURLStatus = function() {
            if(blogURL.value.length == 0) {
                blogURLStatus.textContent = "";
            } else {
                if(blogURLTimeout != false) {
                    clearTimeout(blogURLTimeout);
                }
                blogURLTimeout = setTimeout(function() {
                    reqwest({
                        url: "../utils/api.php?ask=blog_exists&arg=" + blogURL.value,
                        method: "GET",
                        type: "json",
                        success: function(resp) {
                            if(resp["blog_exists"]) {
                                blogURLStatus.textContent = "TAKEN";
                                blogURLStatus.style.color = "red";
                            } else {
                                blogURLStatus.textContent = "AVAILABLE";
                                blogURLStatus.style.color = "#006400";
                            }
                        },
                        error: function(err) {
                            blogURLStatus.textContent = "(Error: " + err.status + " " + err.statusText + ")";
                            blogURLStatus.style.display = "black";
                        } 
                    });
                }, 1000);
            }
        }
        blogURL.addEventListener("input", updateBlogURLStatus);

        /* Insert table of contents */
        document.getElementById("insert-tableOfContents").addEventListener("click", function() {
            textarea.value = textarea.value.substring(0, textarea.selectionStart) + "{{ table of contents }}" + textarea.value.substring(textarea.selectionEnd);
        });
    </script>
<?php

$page_content = ob_get_contents();
ob_end_clean();

include "template.php";
?>