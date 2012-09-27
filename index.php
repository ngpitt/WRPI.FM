<?php
/*
  Copyright Xphysics 2012. All Rights Reserved.

  WRPI.FM is free software: you can redistribute it and/or modify it under the
  terms of the GNU General Public License as published by the Free Software
  Foundation, either version 3 of the License, or (at your option) any later
  version.

  WRPI.FM is distributed in the hope that it will be useful, but WITHOUT ANY
  WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR
  A PARTICULAR PURPOSE. See the GNU General Public License for more details.

  <http://www.gnu.org/licenses/>
 */

// Convert BB formatting to HTML formatting
function bb2html($bb) {
    $html = "";
    $code = false;
    $list = false;
    $bullet = false;
    $table = false;
    $alt = false;

    // Create a mixed array of BB tags and content
    $tokens = tokenize($bb);

    // Loop through each token
    for ($i = 0; $i < count($tokens); $i++) {
        $token = $tokens[$i];

        // Remove content after equal signs
        if (strpos($token, "=")) {
            $token = strstr($token, "=", true);
        }

        switch ($token) {
            case "[b]":
                $html .= "<b>";
                break;
            case "[/b]":
                $html .= "</b>";
                break;
            case "[i]":
                $html .= "<i>";
                break;
            case "[/i]":
                $html .= "</i>";
                break;
            case "[u]":
                $html .= "<u>";
                break;
            case "[/u]":
                $html .= "</u>";
                break;
            case "[s]":
                $html .= "<s>";
                break;
            case "[/s]":
                $html .= "</s>";
                break;
            case "[color":
                $color = strstr($tokens[$i], "=");
                $color = substr($color, 1, -1);
                $html .= "<span style=\"color: {$color}\">";
                break;
            case "[/color]":
                $html .= "</span>";
                break;
            case "[size":
                $size = strstr($tokens[$i], "=");
                $size = substr($size, 1, -1);
                $html .= "<span style=\"font-size: {$size}em\">";
                break;
            case "[/size]":
                $html .= "</span>";
                break;
            case "[center]":
                $html .= "<div style=\"margin-bottom: -18px; text-align: center;\">";
                break;
            case "[/center]":
                $html .= "</div>";
                break;
            case "[list]":
            case "[ul]":
                $html .= "<ul style=\"margin: 5px; margin-bottom: -10px;\">";
                $list = true;
                break;
            case "[ol]":
                $html .= "<ol style=\"margin: 5px; margin-bottom: -10px;\">";
                $list = true;
                break;
            case "[*]":
                $html .= $bullet ? "</li><li>" : "<li>";
                $bullet = true;
                break;
            case "[li]":
                $html .= $bullet ? "</li><li>" : "<li>";
                $bullet = false;
                break;
            case "[/li]":
                $html .= "</li>";
                break;
            case "[/list]":
            case "[/ul]":
                $html .= $bullet ? "</li></ul>" : "</ul>";
                $list = $bullet = false;
                break;
            case "[/ol]":
                $html .= $bullet ? "</li></ol>" : "</ol>";
                $bullet = false;
                break;
            case "[table]":
                $html .= "<table style=\"border-collapse: collapse; margin: auto; margin-top: 10px; margin-bottom: -10px; width: 90%;\">";
                $table = true;
                break;
            case "[tr]":
                $html .= "<tr>";
                break;
            case "[th]":
                $html .= "<th style=\"border: 1px solid #000000;\">";
                break;
            case "[/th]":
                $html .= "</th>";
                break;
            case "[td]":
                $html .= "<td style=\"border: 1px solid #000000;\">";
                break;
            case "[/td]":
                $html .= "</td>";
                break;
            case "[/tr]":
                $html .= "</tr>";
                break;
            case "[/table]":
                $html .= "</table>";
                $table = false;
                break;
            case "[quote]":
                $html .= "<blockquote style=\"background-color: {$_SERVER["style"]["highlight_color"]}; font-style: italic; margin: auto; margin-top: 10px; margin-bottom: -10px; padding: 10px; width: 90%;\">";
                break;
            case "[quote":
                $quote = strstr($tokens[$i], "=");
                $quote = substr($quote, 1, -1);
                $html .= "<blockquote style=\"background-color: {$_SERVER["style"]["highlight_color"]}; font-style: italic; margin: auto; margin-top: 10px; margin-bottom: -10px; padding: 10px; width: 90%;\"><b>{$quote}:</b> ";
                break;
            case "[/quote]":
                $html .= "</blockquote>";
                break;
            case "[code]":
                $html .= "<blockquote style=\"background-color: {$_SERVER["style"]["highlight_color"]}; font-family: Courier, Monospace; margin: auto; margin-top: 10px; margin-bottom: -10px; padding: 10px; width: 90%;\">";
                $code = true;
                break;
            case "[/code]":
                $html .= "</blockquote>";
                $code = false;
                break;
            case "[url]":
                $html .= "<a style=\"color: {$_SERVER["style"]["link_color"]};\" href=\"{$tokens[$i + 1]}\">";
                break;
            case "[url":
                $url = strstr($tokens[$i], "=");
                $url = substr($url, 1, -1);
                $html .= "<a style=\"color: {$_SERVER["style"]["link_color"]};\" href=\"{$url}\">";
                break;
            case "[/url]":
                $html .= "</a>";
                break;
            case "[img]":
                $html .= "<div style=\"background-color: {$_SERVER["style"]["highlight_color"]}; margin: auto; margin-bottom: -13px; margin-top: 10px; padding: 10px; padding-bottom: 7px; width: 90%;\"><img src=\"";
                break;
            case "[img":
                $img = strstr($tokens[$i], "=");
                $img = substr($img, 1, -1);
                $width = strstr($img, "x", true);
                $height = strstr($img, "x");
                $height = substr($height, 1);
                $html .= "<div style=\"background-color: {$_SERVER["style"]["highlight_color"]}; margin: auto; margin-bottom: -13px; margin-top: 10px; padding: 10px; padding-bottom: 7px; width: 90%;\"><img width=\"{$width}\" height=\"{$height}\" src=\"";
                break;
            case "[img width":
                $img = substr($tokens[$i], 1, -1);
                $img = str_replace("&quot;", "\"", $img);
                $html .= "<div style=\"background-color: {$_SERVER["style"]["highlight_color"]}; margin: auto; margin-bottom: -13px; margin-top: 10px; padding: 10px; padding-bottom: 7px; width: 90%;\"><{$img} src=\"";
                $alt = true;
                break;
            case "[/img]":
                $html .= $alt ? "\"/>" : "\" alt=\"\"/></div>";
                $alt = false;
                break;
            case "[youtube]":
                $html .= "<div style=\"margin-bottom: -13px; margin-top: 10px; padding: 10px; text-align: center;\"><iframe id=\"ytplayer\" width=\"640\" height=\"390\" src=\"http://www.youtube.com/embed/";
                break;
            case "[/youtube]":
                $html .= "/\" style=\"border: 0px\"></iframe></div>";
                break;
            default:

                // Check if within code tags
                if ($code) {

                    // Replace spaces with HTML spaces
                    $tokens[$i] = str_replace(" ", "&nbsp;", $tokens[$i]);
                }

                // Check if within list or table tags
                if ($list || $table) {

                    // Remove new lines
                    $tokens[$i] = str_replace("\n", "", $tokens[$i]);
                } else {

                    // Replace new lines with HTML break tags
                    $tokens[$i] = str_replace("\n", "<br/>", $tokens[$i]);
                }

                $html .= $tokens[$i];
        }
    }

    // Add a break tag to the end of each post
    $html .= "<br/>";

    return $html;
}

// Remove BB formatting
function bb2rss($bb) {
    $text = "";
    $omit = false;

    // Create a mixed array of BB tags and content
    $tokens = tokenize($bb);

    // Loop through each token
    for ($i = 0; $i < count($tokens); $i++) {
        $token = $tokens[$i];

        // Remove content after equal signs
        if (strpos($token, "=")) {
            $token = strstr($token, "=", true);
        }

        // Process the token
        switch ($token) {
            case "[b]":
            case "[/b]":
            case "[i]":
            case "[/i]":
            case "[u]":
            case "[/u]":
            case "[s]":
            case "[/s]":
            case "[color":
            case "[/color]":
            case "[size":
            case "[/size]":
            case "[center]":
            case "[/center]":
                break;
            case "[list]":
            case "[ul]":
            case "[ol]":
                $text .= "(list)";
                $omit = true;
            case "[*]":
            case "[li]":
            case "[/li]":
                break;
            case "[/list]":
            case "[/ul]":
            case "[/ol]":
                $omit = false;
                break;
            case "[table]":
                $text .= "(table)";
                $omit = true;
            case "[tr]":
            case "[th]":
            case "[/th]":
            case "[td]":
            case "[/td]":
            case "[/tr]":
                break;
            case "[/table]":
                $omit = false;
                break;
            case "[quote]":
            case "[quote":
                $text .= "(quote)";
                $omit = true;
            case "[/quote]":
                $omit = false;
                break;
            case "[code]":
                $text .= "(code)";
                $omit = true;
                break;
            case "[/code]":
                $omit = false;
            case "[url]":
            case "[url":
            case "[/url]":
                break;
            case "[img]":
            case "[img":
            case "[img width":
                $text .= "(image: ";
                break;
            case "[/img]":
                $text .= ")";
                break;
            case "[youtube]":
                $text .= "(YouTube viedo: ";
                break;
            case "[/youtube]":
                $text .= ")";
                break;
            default:
                if (!$omit) {
                    $text .= $tokens[$i];
                }
        }
    }

    // Replace new lines with spaces
    $text = str_replace("\n", " ", $text);

    return $text;
}

// Sanitize arrays (HTML and SQL safe)
function sanitize($array) {

    // Loop through each string
    foreach ($array as &$string) {

        // Remove leading and trailing formatting characters
        $string = trim($string);

        // Encode HTML operators
        $string = htmlentities($string);

        // Comment out SQL operators
        $string = $_SERVER["database"]["mysqli"]->real_escape_string($string);
    }

    return $array;
}

// Create a mixed array of BB tags and content
function tokenize($string) {
    $tokens = array();
    $token = "";

    // Convert the string into an array
    $array = str_split($string);

    // Loop through each character
    foreach ($array as $character) {

        // Create a new token before open brackets
        if ($character === "[") {
            array_push($tokens, $token);
            $token = "";
        }

        // Add the character to the current token
        $token .= $character;

        // Create a new token after close brackets
        if ($character === "]") {
            array_push($tokens, $token);
            $token = "";
        }
    }

    // Flush the current token
    array_push($tokens, $token);

    return $tokens;
}

// Initialize the page load timer
$start_time = microtime(true);

// Import the configuration
require_once "./config.php";

// Get the site's domain
$domain = array();
preg_match("/[^.]+\.[^.]+$/", $_SERVER["HTTP_HOST"], $domain);
$_SERVER["site"]["domain"] = $domain[0];

// Get the site's url
$url = $_SERVER["HTTPS"] ? "https://" : "http://";
$url .= "{$_SERVER["HTTP_HOST"]}/";
$_SERVER["site"]["url"] = $url;

// Open a connection to the MySQL database
$_SERVER["database"]["mysqli"] = new mysqli($_SERVER["database"]["host"], $_SERVER["database"]["user"], $_SERVER["database"]["pass"], $_SERVER["database"]["name"]);

// Set the default timezone
date_default_timezone_set($_SERVER["site"]["timezone"]);

// Start (or reopen) a PHP session
session_start();

// Sanitize GET variables
$_GET = sanitize($_GET);

// Sanitize POST variables
$_POST = sanitize($_POST);

// Process the delete account form
if (isset($_POST["delete_account"]) && isset($_POST["user_id"]) && isset($_SESSION["logged_in"])) {

    // Check for valid permissions
    if ($_SESSION["admin"] && ($_POST["user_id"] !== $_SESSION["user_id"])) {

        // Delete the account
        $_SERVER["database"]["mysqli"]->query("DELETE FROM users WHERE user_id = '{$_POST["user_id"]}'");

        // Delete the account's posts
        $_SERVER["database"]["mysqli"]->query("DELETE FROM posts WHERE user_id = '{$_POST["user_id"]}'");

        echo 1;
    } else {

        // Delete the account
        $_SERVER["database"]["mysqli"]->query("DELETE FROM users WHERE user_id = '{$_SESSION["user_id"]}'");

        // Delete the account's posts
        $_SERVER["database"]["mysqli"]->query("DELETE FROM posts WHERE user_id = '{$_SESSION["user_id"]}'");

        // Remove session variables (log the user out)
        session_unset();

        echo 0;
    }
    return;
}

// Process the delete post form
if (isset($_POST["delete_post"]) && isset($_POST["post_id"]) && isset($_SESSION["logged_in"]) && $_SESSION["edit"]) {

    // Check for valid permissions
    $result = $_SERVER["database"]["mysqli"]->query("SELECT * FROM posts WHERE post_id = '{$_POST["post_id"]}'");
    $row = $result->fetch_assoc();
    if (!$row || ($row["user_id"] !== $_SESSION["user_id"])) {
        echo 1;
        return;
    }

    // Delete the post
    $_SERVER["database"]["mysqli"]->query("DELETE FROM posts WHERE post_id = '{$_POST["post_id"]}'");

    echo 0;
    return;
}

// Process finish password reset form
if (isset($_POST["finish_password_reset"]) && isset($_POST["id"]) && isset($_POST["password"]) && isset($_SESSION["id"]) && isset($_SESSION["user_id"]) && isset($_SESSION["admin"])) {

    // Check the verification ID
    if ($_POST["id"] !== $_SESSION["id"]) {
        echo 1;
        return;
    }

    // Generate a new password hash
    $_POST["password"] = hash("sha256", $_POST["password"] . $_SESSION["user_id"]);

    // Update the account
    $_SERVER["database"]["mysqli"]->query("UPDATE users SET password = '{$_POST["password"]}' WHERE user_id = '{$_SESSION["user_id"]}'");

    // Remove temporary session variables
    unset($_SESSION["id"]);

    // Log the user in
    $_SESSION["logged_in"] = true;

    echo 0;
    return;
}

// Process the finish registration form
if (isset($_POST["finish_registration"]) && isset($_POST["id"]) && isset($_SESSION["id"]) && isset($_SESSION["user_id"]) && isset($_SESSION["username"]) && isset($_SESSION["password"]) && isset($_SESSION["email"]) && isset($_SESSION["subscribe"]) && isset($_SESSION["admin"])) {

    // Check the verification ID
    if ($_POST["id"] !== $_SESSION["id"]) {
        echo 1;
        return;
    }

    // Check for username in use
    $result = $_SERVER["database"]["mysqli"]->query("SELECT * FROM users WHERE username = '{$_SESSION["username"]}'");
    $row = $result->fetch_assoc();
    if ($row) {
        echo 2;
        return;
    }

    // Check for email in use
    $result = $_SERVER["database"]["mysqli"]->query("SELECT * FROM users WHERE email = '{$_SESSION["email"]}'");
    $row = $result->fetch_assoc();
    if ($row) {
        echo 3;
        return;
    }

    // Create the account
    $_SERVER["database"]["mysqli"]->query("INSERT INTO users SET user_id = '{$_SESSION["user_id"]}', username = '{$_SESSION["username"]}', password = '{$_SESSION["password"]}', email = '{$_SESSION["email"]}', edit = {$_SESSION["edit"]}, admin = {$_SESSION["admin"]}, subscribe = {$_SESSION["subscribe"]}");

    // Remove temporary session variables
    unset($_SESSION["id"]);
    unset($_SESSION["password"]);

    // Log the user in
    $_SESSION["logged_in"] = true;

    echo 0;
    return;
}

// Process the login form
if (isset($_POST["login"]) && isset($_POST["email"]) && isset($_POST["password"])) {

    // Check for a valid email address
    $result = $_SERVER["database"]["mysqli"]->query("SELECT * FROM users WHERE email = '{$_POST["email"]}'");
    $row = $result->fetch_assoc();
    if (!$row) {
        echo 1;
        return;
    }

    // Check the user's password
    $_POST["password"] = hash("sha256", $_POST["password"] . $row["user_id"]);
    if ($_POST["password"] !== $row["password"]) {
        echo 1;
        return;
    }

    // Log the user in
    $_SESSION["logged_in"] = true;
    $_SESSION["user_id"] = $row["user_id"];
    $_SESSION["username"] = $row["username"];
    $_SESSION["email"] = $row["email"];
    $_SESSION["edit"] = $row["edit"] ? true : false;
    $_SESSION["admin"] = $row["admin"] ? true : false;
    $_SESSION["subscribe"] = $row["subscribe"] ? true : false;

    // Update last login time
    $_SERVER["database"]["mysqli"]->query("UPDATE users SET login_date = CURRENT_TIMESTAMP, login_ip = '{$_SERVER["REMOTE_ADDR"]}' WHERE user_id = '{$_SESSION["user_id"]}'");

    echo 0;
    return;
}

// Process logout requests
if (isset($_POST["logout"])) {

    // Remove session variables (log the user out)
    session_unset();

    echo 0;
    return;
}

// Process the new post form
if (isset($_POST["new_post"]) && isset($_POST["title"]) && isset($_POST["content"]) && isset($_SESSION["logged_in"]) && $_SESSION["edit"]) {

    // Create the new post
    $post_id = uniqid();
    $_SERVER["database"]["mysqli"]->query("INSERT INTO posts SET post_id = '{$post_id}', user_id = '{$_SESSION["user_id"]}', date = CURRENT_TIMESTAMP, title = '{$_POST["title"]}', content = '{$_POST["content"]}'");

    // Get the post
    $result = $_SERVER["database"]["mysqli"]->query("SELECT * FROM posts INNER JOIN users ON posts.user_id = users.user_id WHERE posts.post_id = '{$post_id}'");
    $row = $result->fetch_assoc();
    $date = strtotime($row["date"]);

    // Create the new post email
    $headers = "From: {$_SERVER["site"]["title"]} <do-not-reply@{$_SERVER["site"]["domain"]}>\r\nMIME-Version: 1.0\r\nContent-type: text/html; charset=utf-8\r\n";
    $subject = $row["title"];
    $date = date("F jS Y", $date);

    // Convert BB formatting to HTML formatting
    $row["content"] = bb2html($row["content"]);
    $body = "<html>
    <head>
        <title>{$_SERVER["site"]["title"]} - {$_SERVER["site"]["description"]}</title>
    </head>
    <body style=\"background-color: {$_SERVER["style"]["background_color"]}; color: {$_SERVER["style"]["font_color"]}; font-family: Tahoma, Sans-serif; font-size: 0.75em; padding-top: 20px; padding-bottom: 20px; text-align: center;\">
        <div style=\"background-color: {$_SERVER["style"]["foreground_color"]}; margin: auto; width: 80%; text-align: left; word-wrap: break-word;\">
            <div style=\"padding: 10px;\">
                <div style=\"font-size: 1.2em; font-weight: bold; margin-bottom: 2px;\">
                    {$row["title"]}
                </div>
                <div>
                    by <a style=\"color: {$_SERVER["style"]["link_color"]};\" href=\"{$_SERVER["site"]["url"]}#about={$_SESSION["user_id"]}\">{$row["username"]}</a> on {$date}
                </div>
                <div style=\"margin-top: 10px;\">
                    {$row["content"]}
                </div>
            </div>
        </div>
    </body>
</html>";

    // Send the email to subscribed users
    $result = $_SERVER["database"]["mysqli"]->query("SELECT * FROM users WHERE subscribe = 1");
    while ($row = $result->fetch_assoc()) {
        mail($row["email"], $subject, $body, $headers);
    }

    echo 0;
    return;
}

// Generate RSS feeds
if (isset($_GET["rss"])) {

    // Set the content type to RSS
    header("Content-Type: application/rss+xml; charset=utf-8");

    echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>
<rss version=\"2.0\" xmlns:atom=\"http://www.w3.org/2005/Atom\">
    <channel>\n";

    // Get the site publication date
    $pub_date = filemtime($_SERVER["DOCUMENT_ROOT"] . $_SERVER["PHP_SELF"]);

    // Get the specified feed
    $result = $_SERVER["database"]["mysqli"]->query("SELECT * FROM users WHERE user_id = '{$_GET["rss"]}'");
    $row = $result->fetch_assoc();

    // Check if the feed is for a specific user
    $user_specific = $row ? true : false;

    echo "        <title>", $_SERVER["site"]["title"], " - ", $user_specific ? "Posts by {$row["username"]}" : "All Posts", "</title>
        <description>Latest Posts @ ", $_SERVER["site"]["description"], "</description>
        <link>", $_SERVER["site"]["url"], "</link>
        <pubDate>", date("D, d M Y H:i:s T", $pub_date), "</pubDate>\n";

    // Load the most recent 5 posts
    if ($user_specific) {
        $result = $_SERVER["database"]["mysqli"]->query("SELECT * FROM posts WHERE user_id = '{$_GET["rss"]}' ORDER BY date DESC LIMIT 5");
    } else {
        $result = $_SERVER["database"]["mysqli"]->query("SELECT * FROM posts ORDER BY date DESC LIMIT 5");
    }
    $row = $result->fetch_assoc();

    // Get the last build date from the most recent post
    if ($row) {
        $last_build_date = strtotime($row["date"]);
        if ($last_build_date < $pub_date) {
            $last_build_date = $pub_date;
        }
    } else {
        $last_build_date = $pub_date;
    }

    echo "        <lastBuildDate>", date("D, d M Y H:i:s T", $last_build_date), "</lastBuildDate>
        <atom:link href=\"", $_SERVER["site"]["url"], "?rss", $user_specific ? "={$_GET["rss"]}" : "", "\" rel=\"self\" type=\"application/rss+xml\"/>";

    // Loop through the most recent 5 posts
    do {

        // Remove BB formatting
        $row["content"] = bb2rss($row["content"]);

        echo "\n        <item>
            <title>", $row["title"], "</title>
            <description>", $row["content"], "</description>
            <link>", $_SERVER["site"]["url"], "#search=", $row["post_id"], "&amp;page=1</link>
            <guid>", $_SERVER["site"]["url"], "#search=", $row["post_id"], "&amp;page=1</guid>
            <pubDate>", date("D, d M Y H:i:s T", strtotime($row["date"])), "</pubDate>
        </item>";
    } while ($row = $result->fetch_assoc());
    echo "\n    </channel>
</rss>\n";
    return;
}

// Process the start password reset form
if (isset($_POST["start_password_reset"]) && isset($_POST["email"])) {

    // Check if the email address exists
    $result = $_SERVER["database"]["mysqli"]->query("SELECT * FROM users WHERE email = '{$_POST["email"]}'");
    $row = $result->fetch_assoc();
    if (!$row) {
        echo 1;
        return;
    }

    // Set temporary session variables
    $_SESSION["id"] = uniqid();
    $_SESSION["user_id"] = $row["user_id"];
    $_SESSION["username"] = $row["username"];
    $_SESSION["email"] = $row["email"];
    $_SESSION["edit"] = $row["edit"] ? true : false;
    $_SESSION["admin"] = $row["admin"] ? true : false;
    $_SESSION["subscribe"] = $row["subscribe"] ? true : false;

    // Create password reset request email
    $headers = "From: {$_SERVER["site"]["title"]} <do-not-reply@{$_SERVER["site"]["domain"]}>\r\nMIME-Version: 1.0\r\nContent-type: text/html; charset=utf-8\r\n";
    $subject = "Password Reset Link";
    $url = "{$_SERVER["site"]["url"]}#finish_password_reset={$_SESSION["id"]}";
    $body = "<html>
    <head>
        <title>{$_SERVER["site"]["title"]} - {$_SERVER["site"]["description"]}</title>
    </head>
    <body style=\"background-color: {$_SERVER["style"]["background_color"]}; color: {$_SERVER["style"]["font_color"]}; font-family: Tahoma, Sans-serif; font-size: 0.75em; padding-top: 20px; padding-bottom: 20px; text-align: center;\">
        <div style=\"background-color: {$_SERVER["style"]["foreground_color"]}; margin: auto; width: 80%; text-align: left; word-wrap: break-word;\">
            <div style=\"padding: 10px;\">
                <div style=\"font-size: 1.2em; font-weight: bold; margin-bottom: 2px;\">
                    Password Reset Link
                </div>
                <div>
                    If you did not request this email, please disregard it.
                </div>
                <div style=\"margin-top: 10px;\">
                    Your password reset link: <a style=\"color: {$_SERVER["style"]["link_color"]};\" href=\"{$url}\">{$url}</a>
                </div>
            </div>
        </div>
    </body>
</html>";

    // Send the email
    mail($_POST["email"], $subject, $body, $headers);

    echo 0;
    return;
}

// Process the start registration form
if (isset($_POST["start_registration"]) && isset($_POST["username"]) && isset($_POST["password"]) && isset($_POST["email"]) && isset($_POST["subscribe"])) {

    // Check for username in use
    $result = $_SERVER["database"]["mysqli"]->query("SELECT * FROM users WHERE username = '{$_POST["username"]}'");
    $row = $result->fetch_assoc();
    if ($row) {
        echo 1;
        return;
    }

    // Check for invalid email address
    if (!filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)) {
        echo 2;
        return;
    }

    // Check for email address in use
    $result = $_SERVER["database"]["mysqli"]->query("SELECT * FROM users WHERE email = '{$_POST["email"]}'");
    $row = $result->fetch_assoc();
    if ($row) {
        echo 3;
        return;
    }

    // Set temporary session variables
    $_SESSION["id"] = uniqid();
    $_SESSION["user_id"] = uniqid();
    $_SESSION["username"] = $_POST["username"];
    $_SESSION["password"] = hash("sha256", $_POST["password"] . $_SESSION["user_id"]);
    $_SESSION["email"] = $_POST["email"];
    $_SESSION["edit"] = "0";
    $_SESSION["admin"] = "0";
    $_SESSION["subscribe"] = $_POST["subscribe"];

    // Create registration request email
    $headers = "From: {$_SERVER["site"]["title"]} <do-not-reply@{$_SERVER["site"]["domain"]}>\r\nMIME-Version: 1.0\r\nContent-type: text/html; charset=utf-8\r\n";
    $subject = "Registration Link";
    $url = "{$_SERVER["site"]["url"]}#finish_registration={$_SESSION["id"]}";
    $body = "<html>
    <head>
        <title>{$_SERVER["site"]["title"]} - {$_SERVER["site"]["description"]}</title>
    </head>
    <body style=\"background-color: {$_SERVER["style"]["background_color"]}; color: {$_SERVER["style"]["font_color"]}; font-family: Tahoma, Sans-serif; font-size: 0.75em; padding-top: 20px; padding-bottom: 20px; text-align: center;\">
        <div style=\"background-color: {$_SERVER["style"]["foreground_color"]}; margin: auto; width: 80%; text-align: left; word-wrap: break-word;\">
            <div style=\"padding: 10px;\">
                <div style=\"font-size: 1.2em; font-weight: bold; margin-bottom: 2px;\">
                    Registration Link
                </div>
                <div>
                    If you did not request this email, please disregard it.
                </div>
                <div style=\"margin-top: 10px;\">
                    Your registration link: <a style=\"color: {$_SERVER["style"]["link_color"]};\" href=\"{$url}\">{$url}</a>
                </div>
            </div>
        </div>
    </body>
</html>";

    // Send the email
    mail($_POST["email"], $subject, $body, $headers);

    echo 0;
    return;
}

// Process the update account form
if (isset($_POST["update_account"]) && isset($_POST["user_id"]) && isset($_POST["username"]) && isset($_POST["password"]) && isset($_POST["email"]) && isset($_POST["about"]) && isset($_POST["edit"]) && isset($_POST["admin"]) && isset($_POST["subscribe"]) && isset($_SESSION["logged_in"])) {

    // Check for username in use
    $result = $_SERVER["database"]["mysqli"]->query("SELECT * FROM users WHERE username = '{$_POST["username"]}'");
    $row = $result->fetch_assoc();
    if ($row && ($row["user_id"] !== $_POST["user_id"])) {
        echo 2;
        return;
    }

    // Check for invalid email address
    if (!filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)) {
        echo 3;
        return;
    }

    // Check for email address in use
    $result = $_SERVER["database"]["mysqli"]->query("SELECT * FROM users WHERE email = '{$_POST["email"]}'");
    $row = $result->fetch_assoc();
    if ($row && ($row["user_id"] !== $_POST["user_id"])) {
        echo 4;
        return;
    }

    // Check for valid permissions
    if ($_SESSION["admin"]) {

        // Update the account
        $_SERVER["database"]["mysqli"]->query("UPDATE users SET username = '{$_POST["username"]}', email = '{$_POST["email"]}', about = '{$_POST["about"]}', edit = {$_POST["edit"]}, admin = {$_POST["admin"]}, subscribe = {$_POST["subscribe"]} WHERE user_id = '{$_POST["user_id"]}'");

        // Update the password if specified
        if ($_POST["password"] !== "") {

            // Generate a new password hash
            $_POST["password"] = hash("sha256", $_POST["password"] . $_POST["user_id"]);

            // Update the account
            $_SERVER["database"]["mysqli"]->query("UPDATE users SET password = '{$_POST["password"]}' WHERE user_id = '{$_POST["user_id"]}'");
        }

        // Update session variables
        $_SESSION["username"] = $_POST["username"];
        $_SESSION["email"] = $_POST["email"];
        $_SESSION["edit"] = $_POST["edit"] ? true : false;
        $_SESSION["admin"] = $_POST["admin"] ? true : false;
        $_SESSION["subscribe"] = $_POST["subscribe"] ? true : false;

        echo 1;
    } else {

        // Update the account
        $_SERVER["database"]["mysqli"]->query("UPDATE users SET username = '{$_POST["username"]}', email = '{$_POST["email"]}', about = '{$_POST["about"]}', subscribe = {$_POST["subscribe"]} WHERE user_id = '{$_SESSION["user_id"]}'");

        // Update the password if specified
        if ($_POST["password"] !== "") {

            // Generate a new password hash
            $_POST["password"] = hash("sha256", $_POST["password"] . $_SESSION["user_id"]);

            // Update the account
            $_SERVER["database"]["mysqli"]->query("UPDATE users SET password = '{$_POST["password"]}' WHERE user_id = '{$_SESSION["user_id"]}'");
        }

        // Update session variables
        $_SESSION["username"] = $_POST["username"];
        $_SESSION["email"] = $_POST["email"];
        $_SESSION["subscribe"] = $_POST["subscribe"] ? true : false;

        echo 0;
    }

    return;
}

// Process the update post form
if (isset($_POST["update_post"]) && isset($_POST["post_id"]) && isset($_POST["title"]) && isset($_POST["content"]) && isset($_SESSION["logged_in"]) && $_SESSION["edit"]) {

    // Check for valid permissions
    $result = $_SERVER["database"]["mysqli"]->query("SELECT * FROM posts WHERE post_id = '{$_POST["post_id"]}'");
    $row = $result->fetch_assoc();
    if ($row && ($row["user_id"] !== $_SESSION["user_id"])) {
        echo 1;
        return;
    }

    // Update the post
    $_SERVER["database"]["mysqli"]->query("UPDATE posts SET updated = 1, title = '{$_POST["title"]}', content = '{$_POST["content"]}' WHERE post_id = '{$_POST["post_id"]}'");

    echo 0;
    return;
}
?>
<!DOCTYPE html>
<html>
    <head>
        <title><?php echo $_SERVER["site"]["title"]; ?> - <?php echo $_SERVER["site"]["description"]; ?></title>
        <link rel="alternate" type="application/rss+xml" title="<?php echo $_SERVER["site"]["title"]; ?>" href="/?rss"/>
        <script type="text/javascript" src="jquery.js" defer></script>
        <script type="text/javascript">
            
            // Process the delete account form
            function delete_account(user_id) {
                
                // Confirm the account deletion
                if (confirm("Are you sure?")) {
                    
                    // Post the request
                    $.post("/", {
                        
                        // Variables to post
                        delete_account: true, 
                        user_id: user_id
                    }, function (result) {
                        
                        // Process the returned value
                        switch(result){
                            case "0":
                                load("search=" + search + "&page=" + page, "#content");
                                break;
                            case "1":
                                load("admin", "#content");
                        }
                    });
                }
            }
            
            // Process the delete post form
            function delete_post(post_id) {
                
                // Confirm the post deletion
                if (confirm("Are you sure?")) {
                    
                    // Post the request
                    $.post("/", {
                        
                        // Variables to post
                        delete_post: true, 
                        post_id: post_id
                    }, function (result) {
                        
                        // Process the returned value
                        switch(result) {
                            case "0":
                                load("search=" + search + "&page=" + page, "#content");
                                break;
                            case "1":
                                alert("Invalid permissions.");
                        }
                    });
                }
            }
            
            // Process the finish password reset form
            function finish_password_reset(form) {
                
                // Check for a password
                if (form.password.value.length == 0) {
                    alert("Password required.");
                    form.password.focus(); 
                }
                
                // Check if the password matches the confirmation
                else if (form.password.value !== form.confirmation.value) {
                    alert("Passwords do not match.");
                    form.password.focus(); 
                }
                else {
                    
                    // Post the request
                    $.post("/", {
                        
                        // Variables to post
                        finish_password_reset: true, 
                        id: form.id.value,
                        password: form.password.value
                    }, function (result) {
                        
                        // Process the returned value
                        switch(result) {
                            case "0":
                                load("search=" + search + "&page=" + page, "#content");
                                alert("Password reset successful.");
                                break;
                            case "1":
                                alert("Invalid verification ID.");
                        }
                    });
                }
            }
            
            // Process the finish registration form
            function finish_registration(form) {
                
                // Post the request
                $.post("/", {
                    
                    // Variables to post
                    finish_registration: true, 
                    id: form.id.value
                }, function (result) {
                    
                    // Process the returned value
                    switch(result){
                        case "0":
                            load("search=" + search + "&page=" + page, "#content");
                            alert("Registration successful.");
                            break;
                        case "1":
                            alert("Invalid verification ID.");
                            break;
                        case "2":
                            alert("Username in use.");
                            load("start_registration", "#content");
                            break;
                        case "3":
                            alert("Email address in use.");
                            load("start_registration", "#content");
                    }
                });
            }
            
            // Load the requested URL (the 2nd argument specifies the element to be loaded)
            function load(url_argument, element) {
                
                // Update the address bar and the local hash
                window.location.hash = previous_hash = url_argument;
                
                // Convert invalid URL characters to hex values
                url_argument = encodeURI(url_argument);
                
                // Load the requested url
                $(element).load("/?" + url_argument + " " + element, function () {
                    
                    // Submit auto-submit forms (used for redirects)
                    $(".auto_submit").submit();
                    
                    // Auto size textareas
                    $("textarea").each(function() {
                        
                        // Create a new hidden div
                        var auto_size = $(document.createElement("div"));
                        auto_size.addClass("auto_size");
                        $("#content").append(auto_size);
                        
                        // Update the content of the hidden div
                        auto_size.text(this.value);
                        auto_size.text(auto_size.text() + "\n");
                        
                        // Update the size of the text area
                        $(this).css("height", auto_size.height());
                        
                        // Update on key press
                        $(this).keypress(function() {
                            
                            // Update the text in the hidden div
                            auto_size.text(this.value);
                            auto_size.text(auto_size.text() + "\n");
                            
                            // Update the size of the text area
                            $(this).css("height", auto_size.height());
                        });
                    });
                });
            }
            
            // Process the login form
            function login(form) {
                
                // Check for a email address
                if (form.email.value.length == 0) {
                    alert("Email required.");
                    form.email.focus(); 
                }
                
                // Check for a password
                else if (form.password.value.length == 0) {
                    alert("Password required.");
                    form.password.focus();
                }
                else {
                    
                    // Post the request
                    $.post("/", {
                        
                        // Variables to post
                        login: true, 
                        email: form.email.value, 
                        password: form.password.value
                    }, function (result) {
                        
                        // Process the returned value
                        switch(result) {
                            case "0":
                                load("search=" + search + "&page=" + page, "#content");
                                break;
                            case "1":
                                alert("Invalid login.");
                        }
                    });
                }
            }
            
            // Process logout requests
            function logout() {
                
                // Post the request
                $.post("/", {
                    
                    // Variables to post
                    logout: true
                }, function (result) {
                    
                    // Process the returned value
                    switch(result) {
                        case "0":
                            load("search=" + search + "&page=" + page, "#content");
                    }
                });
            }
            
            // Process the new post form
            function new_post(form) {
                
                // Check for a title
                if (form.title.value.length == 0) {
                    alert("Title required.");
                    form.title.focus();
                }
                
                // Check for content
                else if (form.content.value.length == 0) {
                    alert("Content required.");
                    form.content.focus();
                }
                else {
                    $.post("/", {
                        new_post: true,
                        title: form.title.value, 
                        content: form.content.value
                    }, function (result) {
                        switch(result){
                            case "0":
                                load("search=" + search + "&page=" + page, "#content");
                        }
                    });
                }
            }
            
            // Load the requested page
            function Page(new_page) {
                
                // Check if the requested page is different from the currently loaded page
                if (new_page !== page) {
                    
                    // Update the local page
                    page = new_page;
                    
                    // Load the page;
                    load("search=" + search + "&page=" + page, "#content");
                }
            }
            
            // Load the requested search
            function Search(new_search) {
                
                // Check if the requested search is different from the currently loaded search
                if (new_search !== search) {
                    
                    // Update the local search
                    search = new_search;
                    
                    // Reset the local page
                    page = 1;
                    
                    // Load the search
                    load("search=" + search + "&page=" + page, "#search");
                }
            }
            
            // Process the start password reset form
            function start_password_reset(form) {
                
                // Check for a email address
                if (form.email.value.length == 0) {
                    alert("Email required.");
                    form.email.focus(); 
                }
                else {
                    
                    // Post the request
                    $.post("/", {
                        
                        // Variables to post
                        start_password_reset: true,
                        email: form.email.value
                    }, function (result) {
                        
                        // Process the returned value
                        switch(result) {
                            case "0":
                                load("search=" + search + "&page=" + page, "#content");
                                alert("Verification email sent.");
                                break;
                            case "1":
                                alert("Email address not found.");
                        }
                    });
                }
            }
            
            // Process the start registration form
            function start_registration(form) {
                
                // Check for a username
                if (form.username.value.length == 0) {
                    alert("Username required.");
                    form.username.focus(); 
                }
                
                // Check for a password
                else if (form.password.value.length == 0) {
                    alert("Password required.");
                    form.password.focus();
                }
                
                // Check for a email address
                else if (form.email.value.length == 0) {
                    alert("Email required.");
                    form.email.focus();
                }
                
                // Check if the password matches the confirmation
                else if (form.password.value !== form.confirmation.value) {
                    alert("Passwords do not match.");
                    form.password.focus();
                }
                else {
                    
                    // Post the request
                    $.post("/", {
                        
                        // Variables to post
                        start_registration: true, 
                        username: form.username.value, 
                        password: form.password.value, 
                        email: form.email.value,
                        subscribe: form.subscribe.checked ? 1 : 0
                    }, function (result) {
                        
                        // Process the returned value
                        switch(result) {
                            case "0":
                                alert("Verification email sent.");
                                load("search=" + search + "&page=" + page, "#content");
                                break;
                            case "1":
                                alert("Username in use.");
                                form.username.focus();
                                break;
                            case "2":
                                alert("Invalid email address.");
                                form.email.focus();
                                break;
                            case "3":
                                alert("Email address in use.");
                                form.email.focus();
                        }
                    });
                }
            }
            
            // Process the update account form
            function update_account(form) {
                
                // Check for a username
                if (form.username.value.length == 0) {
                    alert("Username required.");
                    form.username.focus(); 
                }
                
                // If a password exists, check if it matches the confirmation
                else if (form.password.value.length != 0 && form.password.value !== form.confirmation.value) {
                    alert("Passwords do not match.");
                    form.password.focus();
                }
                
                // Check for a email address
                else if (form.email.value.length == 0) {
                    alert("Email required.");
                    form.email.focus();
                }
                else {
                        
                    // Post the request
                    $.post("/", {
                        
                        // Variables to post
                        update_account: true, 
                        user_id: form.user_id.value, 
                        username: form.username.value, 
                        password: form.password.value, 
                        email: form.email.value,
                        subscribe: form.subscribe.checked ? 1 : 0,
                        edit: form.edit.checked ? 1 : 0,
                        admin: form.admin.checked ? 1 : 0,
                        about: form.about.value
                    }, function (result) {
                        
                        // Process the retured value
                        switch(result) {
                            case "0":
                                load("search=" + search + "&page=" + page, "#content");
                                break;
                            case "1":
                                load("admin", "#content");
                                break;
                            case "2":
                                alert("Username in use.");
                                form.username.focus();
                                break;
                            case "3":
                                alert("Invalid email address.");
                                form.email.focus();
                                break;
                            case "4":
                                alert("Email address in use.");
                                form.email.focus();
                        }
                    });
                }
            }
            
            // Process the update post form
            function update_post(form) {
                
                // Check for a title
                if (form.title.value.length == 0) {
                    alert("Title required.");
                    form.title.focus(); 
                }
                
                // Check for content
                else if (form.content.value.length == 0) {
                    alert("Content required.");
                    form.content.focus();
                }
                else {
                    
                    // Post the request
                    $.post("/", {
                        
                        // Variables to post
                        update_post: true,
                        post_id: form.post_id.value,
                        title: form.title.value, 
                        content: form.content.value
                    }, function (result) {
                        
                        // Process the returned value
                        switch(result) {
                            case "0":
                                load("search=" + search + "&page=" + page, "#content");
                        }
                    });
                }
            }
            
            // Variables to keep the local state
            var previous_hash = "", search = "", page = 1;
            
            // Things to do when the page loads
            window.onload = function () {
                
                // Check if a hash was specified
                if (window.location.hash.substr(1).length > 0) {
                    
                    // Get the hash from the address bar
                    previous_hash = window.location.hash.substr(1);
                    
                    // Load the url
                    load(previous_hash, "#content");
                }
                else {
                    
                    // Get the default hash
                    previous_hash = "search=" + search + "&page=" + page;
                    
                    // Update the address bar
                    window.location.hash = previous_hash;
                }
                
                // Check the URL bar every 1/10 of a second
                setInterval(function () {
                    
                    // Get the hash from the address bar
                    new_hash = window.location.hash.substr(1);
                    
                    // Check if the hash changed
                    if (new_hash !== previous_hash) {
                        
                        // Load the url
                        load(new_hash, "#content");
                    }
                }, 100);
            }
        </script>
        <style type="text/css">
            a {
                color: <?php echo $_SERVER["style"]["link_color"]; ?>;
                text-decoration: none;
            }
            a:hover {
                text-decoration: underline;
            }
            .active_button {
                color: <?php echo $_SERVER["style"]["active_button_color"]; ?>;
                margin-left: 10px;
                margin-right: 10px;
            }
            .active_button:hover {
                color: <?php echo $_SERVER["style"]["active_button_color"]; ?>;
                text-decoration: underline;
            }
            .auto_size {
                display: none;
                font-family: Tahoma, Sans-serif;
                line-height: 1.5em;
                padding: 0px;
                white-space: pre-wrap;
                width: 654px;
                word-wrap: break-word;
            }
            #background {
                background-color: <?php echo $_SERVER["style"]["background_color"]; ?>;
                padding-top: 20px;
                padding-bottom: 20px;
            }
            body {
                background-color: <?php echo $_SERVER["style"]["button_background_color"]; ?>;
                color: <?php echo $_SERVER["style"]["active_button_color"]; ?>;
                font-family: Tahoma, Sans-serif;
                font-size: 0.75em;
                line-height: 1.5em;
                margin: 0px;
                overflow-y: scroll;
                text-align: center;
            }
            #copyright {
                color: <?php echo $_SERVER["style"]["copyright_color"]; ?>;
                margin: auto;
                width: 680px;
            }
            #footer {
                font-size: 1.1em;
                font-weight: bold;
                letter-spacing: 1px;
                margin: auto;
                width: 680px;
                padding: 3px;
            }
            #foreground {
                background-color: <?php echo $_SERVER["style"]["foreground_color"]; ?>;
                color: <?php echo $_SERVER["style"]["font_color"]; ?>;
                margin: auto;
                text-align: left;
                width: 680px;
            }
            form{
                padding: 10px;
                text-align: center;
            }
            form .content {
                margin-top: 9px;
            }
            form .title {
                border: 1px solid #000000;
                font-family: Tahoma, Sans-serif;
                font-size: 1.2em;
                font-weight: bold;
                margin-left: -2px;
                margin-top: -2px;
                width: 660px;
            }
            #header {
                font-size: 1.1em;
                font-weight: bold;
                letter-spacing: 1px;
                margin: auto;
                padding: 3px;
                width: 680px;
            }
            .highlight:hover {
                background-color: <?php echo $_SERVER["style"]["highlight_color"]; ?>;
                cursor: pointer;
            }
            hr {
                margin: 0px;
            }
            .inactive_button {
                color: <?php echo $_SERVER["style"]["inactive_button_color"]; ?>;
                margin-left: 10px;
                margin-right: 10px;
            }
            #load_stats {
                font-size: 0.8em;
            }
            .post {
                padding: 10px;
                word-wrap: break-word;
            }
            .post .content {
                margin-top: 10px;
            }
            .post .title {
                font-size: 1.2em;
                font-weight: bold;
                margin-bottom: 2px;
            }
            table {
                border-collapse: collapse;
                width: 680px;
            }
            table td{
                border-top: 1px solid #000000;
            }
            textarea {
                font-family: Tahoma, Sans-serif;
                font-size: 12px;
                line-height: 1.5em;
                margin-left: -2px;
                overflow: hidden;
                resize: none;
                width: 660px;
            }
            .textbox {
                width: 175px;
            }
            #title {
                background-color: <?php echo $_SERVER["style"]["title_background_color"]; ?>;
            }
            #title div {
                background: url(title.jpg) no-repeat center;
                cursor: pointer;
                height: 100px;
                margin: auto;
                width: 680px;
            }
        </style>
    </head>
    <body>
        <div id="title">
            <div onClick="search = ''; page = 1; load('search=' + search + '&amp;page=' + page, '#content'); return false;"></div>
        </div>
        <div id="content">
            <div id="header">
                <input class="textbox" name="search" type="text" maxlength="128" placeholder="Search" onKeyUp="Search(this.value);"/>
                <?php if (isset($_SESSION["logged_in"])): ?>
                    <a class="active_button" href="/" onClick="logout(); return false;">Logout</a>
                    <?php if ($_SESSION["edit"]): ?>
                        <a class="active_button" href="/" onClick="search = ''; page = 1; load('new_post&amp;search=' + search + '&amp;page=' + page, '#content', false); return false;">New Post</a>
                    <?php endif; ?>
                    <?php if ($_SESSION["admin"]): ?>
                        <a class="active_button" href="/" onClick="load('admin', '#content'); return false;">Admin</a>
                    <?php else: ?>
                        <a class="active_button" href="/" onClick="load('account=<?php echo $_SESSION["user_id"]; ?>', '#content'); return false;">Account</a>
                    <?php endif; ?>
                <?php else: ?>
                    <a class="active_button" href="/" onClick="load('login', '#content'); return false;">Login</a>
                    <a class="active_button" href="/" onClick="load('start_registration', '#content'); return false;">Register</a>
                <?php endif; ?>
            </div>
            <div id="search">
                <div id="background">
                    <div id="foreground">
                        <?php if (isset($_GET["about"])): ?>
                            <?php $result = $_SERVER["database"]["mysqli"]->query("SELECT * FROM users WHERE user_id = '{$_GET["about"]}'"); ?>
                            <?php $row = $result->fetch_assoc(); ?>
                            <?php if ($row): ?>
                                <div class="post">
                                    <div class="title">
                                        <?php echo $row["username"]; ?>
                                    </div>
                                    <div>
                                        <?php echo "<a href=\"/?rss=", $row["user_id"], "\">RSS Feed</a>"; ?>
                                    </div>
                                    <div class="content">
                                        <?php echo bb2html($row["about"]); ?>
                                    </div>
                                </div>
                            <?php else: ?>
                                <div class="post">
                                    <div class="title">
                                        Invalid Account
                                    </div>
                                </div>
                            <?php endif; ?>
                        <?php elseif (isset($_GET["account"]) && isset($_SESSION["logged_in"])): ?>
                            <?php $result = $_SERVER["database"]["mysqli"]->query("SELECT * FROM users WHERE user_id = '{$_GET["account"]}'"); ?>
                            <?php $row = $result->fetch_assoc(); ?>
                            <?php if ($row): ?>
                                <form action="/" onSubmit="update_account(this); return false;">
                                    <input name="user_id" type="hidden" value="<?php echo $row["user_id"]; ?>"/>
                                    <input class="textbox" name="username" type="text" maxlength="32" value="<?php echo $row["username"]; ?>" placeholder="Username"/><br/>
                                    <input class="textbox" name="email" type="email" maxlength="64" value="<?php echo $row["email"]; ?>" placeholder="Email"/><br/>
                                    <input class="textbox" name="password" type="password" maxlength="128" placeholder="Password" autocomplete="off"/><br/>
                                    <input class="textbox" name="confirmation" type="password" maxlength="128" placeholder="Password (confirmation)" autocomplete="off"/><br/>
                                    Subscribe: <input name="subscribe" type="checkbox" <?php echo $row["subscribe"] ? "checked" : ""; ?>/>
                                    <?php if ($_SESSION["admin"]): ?>
                                        Edit: <input name="edit" type="checkbox" <?php echo $row["edit"] ? "checked" : ""; ?>/>
                                        Admin: <input name="admin" type="checkbox" <?php echo $row["admin"] ? "checked" : ""; ?>/>
                                    <?php else: ?>
                                        <input name="edit" type="checkbox" style="display: none;"/>
                                        <input name="admin" type="checkbox" style="display: none;"/>
                                    <?php endif; ?>
                                    <br/><textarea name="about" maxlength="1024" placeholder="About"><?php echo $row["about"]; ?></textarea>
                                    <input type="submit" value="Save"/> <input type="button" value="Cancel" onClick="load('search=' + search + '&amp;page=' + page, '#content');"/>
                                    <input type="button" value="Delete" onClick="delete_account('<?php echo $row["user_id"]; ?>');"/>
                                </form>
                            <?php else: ?>
                                <div class="post">
                                    <div class="title">
                                        Invalid Account
                                    </div>
                                </div>
                            <?php endif; ?>
                        <?php elseif (isset($_GET["admin"]) && isset($_SESSION["logged_in"]) && $_SESSION["admin"]): ?>
                            <?php $result = $_SERVER["database"]["mysqli"]->query("SELECT * FROM users"); ?>
                            <table>
                                <tr>
                                    <th>Username</th>
                                    <th>Email</th>
                                    <th>Edit</th>
                                    <th>Admin</th>
                                    <th>Subscribed</th>
                                    <th>Login Date</th>
                                    <th>Login IP</th>
                                </tr>
                                <?php if ($result): ?>
                                    <?php while ($row = $result->fetch_assoc()): ?>
                                        <?php $date = strtotime($row["login_date"]); ?>
                                        <tr class="highlight" onClick="load('account=<?php echo $row["user_id"]; ?>', '#content');">
                                            <td><?php echo $row["username"]; ?></td>
                                            <td><?php echo $row["email"]; ?></td>
                                            <td><?php echo $row["edit"] ? "&#10004;" : "&#10008;"; ?></td>
                                            <td><?php echo $row["admin"] ? "&#10004;" : "&#10008;"; ?></td>
                                            <td><?php echo $row["subscribe"] ? "&#10004;" : "&#10008;"; ?></td>
                                            <td><?php echo date("F jS Y", $date); ?></td>
                                            <td><?php echo $row["login_ip"]; ?></td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php endif; ?>
                            </table>
                        <?php elseif (isset($_GET["finish_password_reset"]) && isset($_SESSION["id"]) && isset($_SESSION["user_id"]) && isset($_SESSION["admin"]) && $_GET["finish_password_reset"] === $_SESSION["id"]): ?>
                            <form action="/" onSubmit="finish_password_reset(this); return false;">
                                <input name="id" type="hidden" value="<?php echo $_GET["finish_password_reset"]; ?>"/>
                                <input class="textbox" name="password" type="password" maxlength="128" placeholder="Password" autocomplete="off"/><br/>
                                <input class="textbox" name="confirmation" type="password" maxlength="128" placeholder="Password (confirmation)" autocomplete="off"/><br/>
                                <input type="submit" value="Reset"/>
                            </form>
                        <?php elseif (isset($_GET["finish_registration"]) && isset($_SESSION["id"]) && isset($_SESSION["user_id"]) && isset($_SESSION["username"]) && isset($_SESSION["password"]) && isset($_SESSION["email"]) && isset($_SESSION["subscribe"]) && isset($_SESSION["admin"]) && $_GET["finish_registration"] === $_SESSION["id"]): ?>
                            <form class="auto_submit" action="/" onSubmit="finish_registration(this); return false;">
                                <input name="id" type="hidden" value="<?php echo $_GET["finish_registration"]; ?>"/>
                            </form>
                        <?php elseif (isset($_GET["login"])): ?>   
                            <form action="/" onSubmit="login(this); return false;">
                                <input class="textbox" name="email" type="text" maxlength="64" placeholder="Email"/><br/>
                                <input class="textbox" name="password" type="password" maxlength="128" placeholder="Password" autocomplete="off"/><br/>
                                <input type="submit" value="Login"/><br/>
                                <a href="/" onClick="load('start_password_reset', '#content'); return false;">Reset Your Password</a>
                            </form>
                        <?php elseif (isset($_GET["start_registration"])): ?>
                            <form action="/" onSubmit="start_registration(this); return false;">
                                <input class="textbox" name="username" type="text" maxlength="32" placeholder="Username"/><br/>
                                <input class="textbox" name="password" type="password" maxlength="128" placeholder="Password" autocomplete="off"/><br/>
                                <input class="textbox" name="confirmation" type="password" maxlength="128" placeholder="Password (confirmation)" autocomplete="off"/><br/>
                                <input class="textbox" name="email" type="email" maxlength="64" placeholder="Email"/><br/>
                                Subscribe: <input name="subscribe" type="checkbox"/><br/>
                                <input type="submit" value="Verify"/>
                            </form>
                        <?php elseif (isset($_GET["start_password_reset"])): ?>
                            <form action="/" onSubmit="start_password_reset(this); return false;">
                                <input class="textbox" name="email" type="email" maxlength="64" placeholder="Email"/><br/>
                                <input type="submit" value="Verify"/>
                            </form>
                        <?php else: ?>
                            <?php if (isset($_GET["new_post"]) && isset($_SESSION["logged_in"]) && $_SESSION["edit"]): ?>
                                <form action="/" onSubmit="new_post(this); return false;">
                                    <input class="title" name="title" type="text" maxlength="128" placeholder="Title"><br/>
                                    <div style="text-align: left">
                                        by <a href="/" onClick="load('about=<?php echo $_SESSION["user_id"]; ?>', '#content'); return false;"><?php echo $_SESSION["username"]; ?></a> on <?php echo date("F jS Y") ?><br/>
                                    </div>
                                    <textarea class="content" name="content" maxlength="1024" placeholder="Content"></textarea><br/>
                                    <input type="submit" value="Save"/>
                                    <input type="button" value="Cancel" onClick="load('search=' + search + '&amp;page=' + page, '#content');"/>
                                </form>
                                <hr/>
                            <?php endif; ?>
                            <?php
                            // Initialize the search term, page, and number of page tabs
                            $search = isset($_GET["search"]) ? $_GET["search"] : "";
                            $_SERVER["page"] = $_SERVER["page_tabs"] = 1;
                            if (isset($_GET["page"])) {
                                $_SERVER["page"] = (int) $_GET["page"];
                            }

                            // Load the page
                            $limit = ($_SERVER["page"] - 1) * 5;
                            $result = $_SERVER["database"]["mysqli"]->query("SELECT * FROM posts INNER JOIN users ON posts.user_id = users.user_id WHERE users.username LIKE '%{$search}%' OR posts.post_id LIKE '{$search}' OR posts.date LIKE '%{$search}%' OR posts.title LIKE '%{$search}%' OR posts.content LIKE '%{$search}%' ORDER BY date DESC LIMIT {$limit}, 50");

                            // Determine how many page tabs to show
                            $_SERVER["page_tabs"] = ceil($result->num_rows / 5);

                            $row = $result->fetch_assoc();
                            ?>
                            <?php if (!$row): ?>
                                <?php if ($_SERVER["page"] !== 1): ?>
                                    <?php unset($_SERVER["page"]); ?>
                                    <?php unset($_SERVER["page_tabs"]); ?>
                                    <div class="post">
                                        <div class="title">
                                            Invalid Page
                                        </div>
                                    </div>
                                <?php else: ?>
                                    <div class="post">
                                        <div class="title">
                                            No Posts
                                        </div>
                                    </div>
                                <?php endif; ?>
                            <?php else: ?>
                                <?php for ($i = 0; $row && $i < 5; $i++): ?>
                                    <?php $date = strtotime($row["date"]); ?>
                                    <?php $date_updated = strtotime($row["date_updated"]); ?>
                                    <?php if (isset($_GET["update_post"]) && $_GET["update_post"] === $row["post_id"] && isset($_SESSION["logged_in"]) && ($row["user_id"] === $_SESSION["user_id"] || $_SESSION["admin"])): ?>
                                        <form action="/" onSubmit="update_post(this); return false;">
                                            <input name="post_id" type="hidden" value="<?php echo $_GET["update_post"]; ?>"/>
                                            <input class="title" name="title" type="text" maxlength="128" value="<?php echo $row["title"]; ?>" placeholder="Title"><br/>
                                            <div style="text-align: left">
                                                by <a href="/" onClick="load('about=<?php echo $row["user_id"]; ?>', '#content'); return false;"><?php echo $row["username"]; ?></a> on <?php echo date("F jS Y", $date); ?> <i>(updated <?php echo date("F jS Y"); ?>)</i><br/>
                                            </div>
                                            <textarea class="content" name="content" maxlength="1024" placeholder="Content"><?php echo $row["content"]; ?></textarea>
                                            <input type="submit" value="Save"/> <input type="button" value="Cancel" onClick="load('search=' + search + '&amp;page=' + page, '#content');"/>
                                            <input type="button" value="Delete" onClick="delete_post('<?php echo $row["post_id"]; ?>');"/>
                                        </form>
                                    <?php else: ?>
                                        <div class="post">
                                            <div class="title" <?php echo (isset($_SESSION["logged_in"]) && ($_SESSION["edit"] && $row["user_id"] === $_SESSION["user_id"]) && !isset($_GET["update_post"])) ? "onClick=\"load('update_post={$row["post_id"]}&amp;search=' + search + '&amp;page=' + page, '#content');\" style=\"cursor: pointer;\"" : ""; ?>>
                                                <?php echo $row["title"]; ?>
                                            </div>
                                            <div>
                                                <?php echo "by <a href=\"/\" onClick=\"load('about=", $row["user_id"], "', '#content'); return false;\">", $row["username"], "</a> on ", date("F jS Y", $date); ?>
                                                <?php if ($row["updated"]): ?>
                                                    <?php echo "<i>(updated ", date("F jS Y", $date_updated), ")</i>"; ?>
                                                <?php endif; ?>
                                            </div>
                                            <div class="content">
                                                <?php echo bb2html($row["content"]); ?>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                    <?php $row = $result->fetch_assoc(); ?>
                                    <?php if ($row && $i < 4): ?>
                                        <hr/>
                                    <?php endif; ?>
                                <?php endfor; ?>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                </div>
                <div id="footer">
                    <?php if (isset($_SERVER["page"])): ?>
                        <?php if ($_SERVER["page"] > 1): ?>
                            <a class="active_button" href="/" onClick="Page(<?php echo $_SERVER["page"] - 1; ?>); return false;">Newer</a>
                        <?php else: ?>
                            <span class="inactive_button">Newer</span>
                        <?php endif; ?>
                        |
                    <?php endif; ?>
                    <?php if (isset($_SERVER["page_tabs"])): ?>
                        <?php
                        $page_tabs_before = 2;
                        $page_tabs_after = 2;

                        // If the current page is less than three, move the current page closer to the 1st page
                        if ($_SERVER["page"] < 3) {
                            $page_tabs_before = $_SERVER["page"] - 1;
                            $page_tabs_after = $_SERVER["page_tabs"] - 1;
                        }

                        // If the number of pages ahead of the current page is less than three, move the current page closer to the last page
                        else if ($_SERVER["page_tabs"] < 3) {
                            $page_tabs_before = $_SERVER["page"] - $_SERVER["page_tabs"];
                            $page_tabs_after = $_SERVER["page_tabs"] - 1;
                        }
                        ?>
                        <?php for ($i = $_SERVER["page"] - $page_tabs_before; $i <= $_SERVER["page"] + $page_tabs_after; $i++): ?>
                            <?php if ($i == $_SERVER["page"]): ?>
                                <span class="inactive_button"><?php echo $i; ?></span>
                            <?php else: ?>
                                <a class="active_button" href="/" onClick="Page(<?php echo $i; ?>); return false;"><?php echo $i; ?></a>
                            <?php endif; ?>
                            |
                        <?php endfor; ?>
                    <?php endif; ?>
                    <?php if (isset($_SERVER["page_tabs"])): ?>
                        <?php if ($_SERVER["page_tabs"] > 1): ?>
                            <a class="active_button" href="/" onClick="Page(<?php echo $_SERVER["page"] + 1; ?>); return false;">Older</a>
                        <?php else: ?>
                            <span class="inactive_button">Older</span>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
                <div id="copyright">
                    &copy; Copyright <?php echo date("Y"); ?> Xphysics<br/>
                    <?php echo "<a href=\"http://validator.w3.org/check?uri=", $_SERVER["site"]["url"], "&amp;charset=utf-8&amp;doctype=HTML5&amp;group=0&amp;user-agent=W3C_Validator%2F1.3\">Valid HTML 5</a> | <a href=\"http://jigsaw.w3.org/css-validator/validator?uri=", $_SERVER["site"]["url"], "&amp;profile=css3&amp;usermedium=all&amp;warning=no&amp;vextwarning=&amp;lang=en\">Valid CSS 3</a> | <a href=\"http://validator.w3.org/feed/check.cgi?url=", $_SERVER["site"]["url"], "?rss\">Valid RSS 2</a>"; ?><br/>
                    <span id="load_stats">Page loaded on <?php echo date("l, F j, Y g:i A T"); ?> in <?php echo round(microtime(true) - $start_time, 4); ?> seconds.</span>
                </div>
            </div>
        </div>
    </body>
</html>
