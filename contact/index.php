<?php 

$show_status = false;
$status = "";
$status_text = "";
if(isset($_POST['message'])) {
    $show_status = true;
    $msg = filter_var($_POST['message'], FILTER_SANITIZE_STRING, FILTER_FLAG_ENCODE_AMP);
    $name = isset($_POST['name']) ? filter_var($_POST['name'], FILTER_SANITIZE_STRING, FILTER_FLAG_ENCODE_AMP) : "";
    $email = isset($_POST['email']) ? filter_var($_POST['email'], FILTER_SANITIZE_EMAIL) : "";

    try {
        $db = new SQLite3("../data/database.db");
        $statement = $db->prepare("INSERT INTO mail VALUES (datetime('now'), :name, :email, :message);");
        $statement->bindValue(":name", $name);
        $statement->bindValue(":email", $email);
        $statement->bindValue(":message", $msg);
        if($statement->execute()) {
            $status = "success";
            $status_text = "Thanks " . $name . ", I got your message";
        }
        $statement->close();
        $db->close();
    } catch (Exception $e) {
        $status = "error";
        $status_text = "Sorry, this feature is not working right now. Please check back later.";
    }
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Max Zlotskiy</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css?family=Lato|Raleway" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="/assets/base.css">
    <link rel="stylesheet" type="text/css" href="/assets/form.css">
</head>
<body>
    <?php  $title="Talk To Me"; include "../utils/nav.php"  ?>
    <div id="body">
        <p class="form-description">Looking for an easy way to get in touch with me? Please fill out this form! Leaving your email makes it easier to get back to you, but anonymous praise is accepted as well.</p>
        <?php if(show_status): ?>
        <div class="<?=$status ?>"><?=$status_text ?></div>
        <?php endif; ?>
        <form method="POST" action="/contact/index.php" class="form-fancy">
            <label for="name">From:</label>
            <input type="text" name="name" id="name" placeholder="Your name" class="long">
            
            <label for="email">Email:</label>
            <input type="email" name="email" id="email" placeholder="Email" class="long">
        
            <label for="message">Your note:</label>
            <textarea name="message" placeholder="Questions, comments, concerns and compliments here..." required rows="20"></textarea>
            
            <input type="submit" value="Send Me a Note">
        </form>
    </div>

    <script type="text/javascript" src="/assets/waves.js"></script>

</body>
</html>