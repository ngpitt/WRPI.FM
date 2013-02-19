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

// Initialize the page load timer
$start_time = microtime(true);

// Get the site's domain
$domain = array();
preg_match("/[^.]+\.[^.]+$/", $_SERVER["HTTP_HOST"], $domain);
$_SERVER["site"]["domain"] = $domain[0];

// Get the site's url
$url = $_SERVER["HTTPS"] ? "https://" : "http://";
$url .= "{$_SERVER["HTTP_HOST"]}/";
$_SERVER["site"]["url"] = $url;

// Open a connection to the MySQL database
$_SERVER["mysqli"] = new mysqli($_SERVER["database"]["host"], $_SERVER["database"]["user"], $_SERVER["database"]["pass"], $_SERVER["database"]["name"]);

// Set the default timezone
date_default_timezone_set($_SERVER["site"]["timezone"]);

// Set session expiration
ini_set("session.cookie_lifetime", 60 * 60 * 24);
ini_set("session.gc_maxlifetime", 60 * 60 * 24);

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
        $_SERVER["mysqli"]->query("DELETE FROM users WHERE user_id = '{$_POST["user_id"]}'");

        // Delete the account's posts
        $_SERVER["mysqli"]->query("DELETE FROM posts WHERE user_id = '{$_POST["user_id"]}'");

        exit("1");
    } else {

        // Delete the account
        $_SERVER["mysqli"]->query("DELETE FROM users WHERE user_id = '{$_SESSION["user_id"]}'");

        // Delete the account's posts
        $_SERVER["mysqli"]->query("DELETE FROM posts WHERE user_id = '{$_SESSION["user_id"]}'");

        // Remove session variables (log the user out)
        session_unset();

        exit("0");
    }
}

// Process the delete post form
if (isset($_POST["delete_post"]) && isset($_POST["post_id"]) && isset($_SESSION["logged_in"]) && $_SESSION["edit"]) {

    // Check for valid permissions
    $result = $_SERVER["mysqli"]->query("SELECT * FROM posts WHERE post_id = '{$_POST["post_id"]}'");
    $row = $result->fetch_assoc();
    if (!$row || ($row["user_id"] !== $_SESSION["user_id"]))
        exit("1");

    // Delete the post
    $_SERVER["mysqli"]->query("DELETE FROM posts WHERE post_id = '{$_POST["post_id"]}'");

    exit("0");
}

// Process finish password reset form
if (isset($_POST["finish_password_reset"]) && isset($_POST["id"]) && isset($_POST["password"]) && isset($_SESSION["id"]) && isset($_SESSION["user_id"]) && isset($_SESSION["admin"])) {

    // Check the verification ID
    if ($_POST["id"] !== $_SESSION["id"])
        exit("1");

    // Generate a new password hash
    $_POST["password"] = hash("sha256", $_POST["password"] . $_SESSION["user_id"]);

    // Update the account
    $_SERVER["mysqli"]->query("UPDATE users SET password = '{$_POST["password"]}', login_date = CURRENT_TIMESTAMP, login_ip = '{$_SERVER["REMOTE_ADDR"]}' WHERE user_id = '{$_SESSION["user_id"]}'");

    // Remove temporary session variables
    unset($_SESSION["id"]);

    // Log the user in
    $_SESSION["logged_in"] = true;

    exit("0");
}

// Process the finish registration form
if (isset($_POST["finish_registration"]) && isset($_POST["id"]) && isset($_SESSION["id"]) && isset($_SESSION["user_id"]) && isset($_SESSION["username"]) && isset($_SESSION["password"]) && isset($_SESSION["email"]) && isset($_SESSION["subscribe"]) && isset($_SESSION["admin"])) {

    // Check the verification ID
    if ($_POST["id"] !== $_SESSION["id"])
        exit("1");

    // Check for username in use
    $result = $_SERVER["mysqli"]->query("SELECT * FROM users WHERE username = '{$_SESSION["username"]}'");
    $row = $result->fetch_assoc();
    if ($row)
        exit("2");

    // Check for email in use
    $result = $_SERVER["mysqli"]->query("SELECT * FROM users WHERE email = '{$_SESSION["email"]}'");
    $row = $result->fetch_assoc();
    if ($row)
        exit("3");

    // Create the account
    $_SERVER["mysqli"]->query("INSERT INTO users SET user_id = '{$_SESSION["user_id"]}', username = '{$_SESSION["username"]}', password = '{$_SESSION["password"]}', email = '{$_SESSION["email"]}', edit = {$_SESSION["edit"]}, admin = {$_SESSION["admin"]}, subscribe = {$_SESSION["subscribe"]}, login_date = CURRENT_TIMESTAMP, login_ip = '{$_SERVER["REMOTE_ADDR"]}'");

    // Remove temporary session variables
    unset($_SESSION["id"]);
    unset($_SESSION["password"]);

    // Log the user in
    $_SESSION["logged_in"] = true;

    exit("0");
}

// Process the login form
if (isset($_POST["login"]) && isset($_POST["email"]) && isset($_POST["password"])) {

    // Check for a valid email address
    $result = $_SERVER["mysqli"]->query("SELECT * FROM users WHERE email = '{$_POST["email"]}'");
    $row = $result->fetch_assoc();
    if (!$row)
        exit("1");

    // Check the user's password
    $_POST["password"] = hash("sha256", $_POST["password"] . $row["user_id"]);
    if ($_POST["password"] !== $row["password"])
        exit("1");

    // Log the user in
    $_SESSION["logged_in"] = true;
    $_SESSION["user_id"] = $row["user_id"];
    $_SESSION["username"] = $row["username"];
    $_SESSION["email"] = $row["email"];
    $_SESSION["edit"] = $row["edit"] ? true : false;
    $_SESSION["admin"] = $row["admin"] ? true : false;
    $_SESSION["subscribe"] = $row["subscribe"] ? true : false;

    // Update the last login time and ip
    $_SERVER["mysqli"]->query("UPDATE users SET login_date = CURRENT_TIMESTAMP, login_ip = '{$_SERVER["REMOTE_ADDR"]}' WHERE user_id = '{$_SESSION["user_id"]}'");

    exit("0");
}

// Process logout requests
if (isset($_POST["logout"])) {

    // Remove session variables (log the user out)
    session_unset();

    exit("0");
}

// Process the new post form
if (isset($_POST["new_post"]) && isset($_POST["title"]) && isset($_POST["content"]) && isset($_SESSION["logged_in"]) && $_SESSION["edit"]) {

    // Create the new post
    $post_id = uniqid();
    $_SERVER["mysqli"]->query("INSERT INTO posts SET post_id = '{$post_id}', user_id = '{$_SESSION["user_id"]}', date_created = CURRENT_TIMESTAMP, title = '{$_POST["title"]}', content = '{$_POST["content"]}'");

    // Get the post
    $result = $_SERVER["mysqli"]->query("SELECT * FROM posts INNER JOIN users ON posts.user_id = users.user_id WHERE posts.post_id = '{$post_id}'");
    $row = $result->fetch_assoc();
    $date_created = strtotime($row["date_created"]);

    // Create the new post email
    $headers = "From: {$_SERVER["site"]["title"]} <do-not-reply@{$_SERVER["site"]["domain"]}>\r\nMIME-Version: 1.0\r\nContent-type: text/html; charset=utf-8\r\n";
    $subject = $row["title"];
    $date_created = date("F jS Y", $date_created);

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
                    by <a style=\"color: {$_SERVER["style"]["link_color"]};\" href=\"{$_SERVER["site"]["url"]}#about={$_SESSION["user_id"]}\">{$row["username"]}</a> on {$date_created}
                </div>
                <div style=\"margin-top: 10px;\">
                    {$row["content"]}
                </div>
            </div>
        </div>
    </body>
</html>";

    // Send the email to subscribed users
    $result = $_SERVER["mysqli"]->query("SELECT * FROM users WHERE subscribe = 1");
    while ($row = $result->fetch_assoc())
        mail($row["email"], $subject, $body, $headers);

    exit("0");
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
    $result = $_SERVER["mysqli"]->query("SELECT * FROM users WHERE user_id = '{$_GET["rss"]}'");
    $row = $result->fetch_assoc();

    // Check if the feed is for a specific user
    $user_specific = $row ? true : false;

    echo "        <title>", $_SERVER["site"]["title"], " - ", $user_specific ? "Posts by {$row["username"]}" : "All Posts", "</title>
        <description>Latest Posts @ ", $_SERVER["site"]["description"], "</description>
        <link>", $_SERVER["site"]["url"], "</link>
        <pubDate>", date("D, d M Y H:i:s T", $pub_date), "</pubDate>\n";

    // Load the most recent 5 posts
    if ($user_specific)
        $result = $_SERVER["mysqli"]->query("SELECT * FROM posts WHERE user_id = '{$_GET["rss"]}' ORDER BY date_created DESC LIMIT 5");
    else
        $result = $_SERVER["mysqli"]->query("SELECT * FROM posts ORDER BY date_created DESC LIMIT 5");
    $row = $result->fetch_assoc();

    // Get the last build date from the most recent post
    if ($row) {
        $last_build_date = strtotime($row["date"]);
        if ($last_build_date < $pub_date)
            $last_build_date = $pub_date;
    } else
        $last_build_date = $pub_date;

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
            <pubDate>", date("D, d M Y H:i:s T", strtotime($row["date_created"])), "</pubDate>
        </item>";
    } while ($row = $result->fetch_assoc());
    echo "\n    </channel>
</rss>\n";
    exit;
}

// Process the start password reset form
if (isset($_POST["start_password_reset"]) && isset($_POST["email"])) {

    // Check if the email address exists
    $result = $_SERVER["mysqli"]->query("SELECT * FROM users WHERE email = '{$_POST["email"]}'");
    $row = $result->fetch_assoc();
    if (!$row)
        exit("1");

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

    exit("0");
}

// Process the start registration form
if (isset($_POST["start_registration"]) && isset($_POST["username"]) && isset($_POST["password"]) && isset($_POST["email"]) && isset($_POST["subscribe"])) {

    // Check for username in use
    $result = $_SERVER["mysqli"]->query("SELECT * FROM users WHERE username = '{$_POST["username"]}'");
    $row = $result->fetch_assoc();
    if ($row)
        exit("1");

    // Check for invalid email address
    if (!filter_var($_POST["email"], FILTER_VALIDATE_EMAIL))
        exit("2");

    // Check for email address in use
    $result = $_SERVER["mysqli"]->query("SELECT * FROM users WHERE email = '{$_POST["email"]}'");
    $row = $result->fetch_assoc();
    if ($row)
        exit("3");

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

    exit("0");
}

// Process the update account form
if (isset($_POST["update_account"]) && isset($_POST["user_id"]) && isset($_POST["username"]) && isset($_POST["password"]) && isset($_POST["email"]) && isset($_POST["about"]) && isset($_POST["edit"]) && isset($_POST["admin"]) && isset($_POST["subscribe"]) && isset($_SESSION["logged_in"])) {

    // Check for username in use
    $result = $_SERVER["mysqli"]->query("SELECT * FROM users WHERE username = '{$_POST["username"]}'");
    $row = $result->fetch_assoc();
    if ($row && ($row["user_id"] !== $_POST["user_id"]))
        exit("2");

    // Check for invalid email address
    if (!filter_var($_POST["email"], FILTER_VALIDATE_EMAIL))
        exit("3");

    // Check for email address in use
    $result = $_SERVER["mysqli"]->query("SELECT * FROM users WHERE email = '{$_POST["email"]}'");
    $row = $result->fetch_assoc();
    if ($row && ($row["user_id"] !== $_POST["user_id"]))
        exit("4");

    // Check for valid permissions
    if ($_SESSION["admin"]) {

        // Update the account
        $_SERVER["mysqli"]->query("UPDATE users SET username = '{$_POST["username"]}', email = '{$_POST["email"]}', about = '{$_POST["about"]}', edit = {$_POST["edit"]}, admin = {$_POST["admin"]}, subscribe = {$_POST["subscribe"]} WHERE user_id = '{$_POST["user_id"]}'");

        // Update the password if specified
        if ($_POST["password"] !== "") {

            // Generate a new password hash
            $_POST["password"] = hash("sha256", $_POST["password"] . $_POST["user_id"]);

            // Update the account
            $_SERVER["mysqli"]->query("UPDATE users SET password = '{$_POST["password"]}' WHERE user_id = '{$_POST["user_id"]}'");
        }

        // Update session variables
        $_SESSION["username"] = $_POST["username"];
        $_SESSION["email"] = $_POST["email"];
        $_SESSION["edit"] = $_POST["edit"] ? true : false;
        $_SESSION["admin"] = $_POST["admin"] ? true : false;
        $_SESSION["subscribe"] = $_POST["subscribe"] ? true : false;

        exit("1");
    } else {

        // Update the account
        $_SERVER["mysqli"]->query("UPDATE users SET username = '{$_POST["username"]}', email = '{$_POST["email"]}', about = '{$_POST["about"]}', subscribe = {$_POST["subscribe"]} WHERE user_id = '{$_SESSION["user_id"]}'");

        // Update the password if specified
        if ($_POST["password"] !== "") {

            // Generate a new password hash
            $_POST["password"] = hash("sha256", $_POST["password"] . $_SESSION["user_id"]);

            // Update the account
            $_SERVER["mysqli"]->query("UPDATE users SET password = '{$_POST["password"]}' WHERE user_id = '{$_SESSION["user_id"]}'");
        }

        // Update session variables
        $_SESSION["username"] = $_POST["username"];
        $_SESSION["email"] = $_POST["email"];
        $_SESSION["subscribe"] = $_POST["subscribe"] ? true : false;

        exit("0");
    }
}

// Process the update post form
if (isset($_POST["update_post"]) && isset($_POST["post_id"]) && isset($_POST["title"]) && isset($_POST["content"]) && isset($_SESSION["logged_in"]) && $_SESSION["edit"]) {

    // Check for valid permissions
    $result = $_SERVER["mysqli"]->query("SELECT * FROM posts WHERE post_id = '{$_POST["post_id"]}'");
    $row = $result->fetch_assoc();
    if ($row && ($row["user_id"] !== $_SESSION["user_id"]))
        exit("1");

    // Update the post
    $_SERVER["mysqli"]->query("UPDATE posts SET updated = 1, title = '{$_POST["title"]}', content = '{$_POST["content"]}' WHERE post_id = '{$_POST["post_id"]}'");

    exit("0");
}
?>
