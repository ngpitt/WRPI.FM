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

// Import configuration
require_once "config.php";

// Import functions
require_once "include.php";

// Import server functionality 
require_once "server.php";
?>
<!DOCTYPE html>
<html>
    <head>
        <title><?php echo $_SERVER["site"]["title"]; ?> - <?php echo $_SERVER["site"]["description"]; ?></title>
        <link rel="alternate" type="application/rss+xml" title="<?php echo $_SERVER["site"]["title"]; ?>" href="/?rss"/>
        <script type="text/javascript" src="include.js" defer></script>
        <script type="text/javascript" src="client.js" defer></script>
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
                width: 660px;
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
                            <?php $result = $_SERVER["mysqli"]->query("SELECT * FROM users WHERE user_id = '{$_GET["about"]}'"); ?>
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
                            <?php $result = $_SERVER["mysqli"]->query("SELECT * FROM users WHERE user_id = '{$_GET["account"]}'"); ?>
                            <?php $row = $result->fetch_assoc(); ?>
                            <?php if ($row): ?>
                                <form action="/" onSubmit="update_account(this); return false;">
                                    <input name="user_id" type="hidden" value="<?php echo $row["user_id"]; ?>"/>
                                    <input class="textbox" name="username" type="text" maxlength="32" value="<?php echo $row["username"]; ?>" placeholder="Username"/><br/>
                                    <input class="textbox" name="email" type="email" maxlength="64" value="<?php echo $row["email"]; ?>" placeholder="Email"/><br/>
                                    <input class="textbox" name="password" type="password" maxlength="128" placeholder="Password" autocomplete="off"/><br/>
                                    <input class="textbox" name="confirmation" type="password" maxlength="128" placeholder="Password (confirmation)" autocomplete="off"/><br/>
                                    <?php if ($_SESSION["admin"]): ?>
                                        Edit: <select name="edit">
                                            <?php if ($row["edit"]): ?>
                                                <option value="1">yes</option>
                                                <option value="0">no</option>
                                            <?php else: ?>
                                                <option value="0">no</option>
                                                <option value="1">yes</option>
                                            <?php endif; ?>
                                        </select>
                                        Admin: <select name="admin">
                                            <?php if ($row["admin"]): ?>
                                                <option value="1">yes</option>
                                                <option value="0">no</option>
                                            <?php else: ?>
                                                <option value="0">no</option>
                                                <option value="1">yes</option>
                                            <?php endif; ?>
                                        </select>
                                    <?php else: ?>
                                        <select name="edit" value="0" style="display: none;"/>
                                        <select name="admin" value="0" style="display: none;"/>
                                    <?php endif; ?>
                                    Subscribed: <select name="subscribe">
                                        <?php if ($row["subscribe"]): ?>
                                            <option value="1">yes</option>
                                            <option value="0">no</option>
                                        <?php else: ?>
                                            <option value="0">no</option>
                                            <option value="1">yes</option>
                                        <?php endif; ?>
                                    </select>
                                    <br/><textarea name="about" maxlength="4096" placeholder="About"><?php echo $row["about"]; ?></textarea>
                                    <input type="submit" value="Save"/> <input type="button" value="Cancel" onClick="<?php echo $_SESSION["admin"] ? "load('admin', '#content')" : "load('search=' + search + '&amp;page=' + page, '#content');"; ?>"/>
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
                            <?php $result = $_SERVER["mysqli"]->query("SELECT * FROM users"); ?>
                            <div class="post">
                                <table>
                                    <tr>
                                        <th>Username</th>
                                        <th>Email</th>
                                        <th>Edit</th>
                                        <th>Admin</th>
                                        <th>Subscribed</th>
                                        <th>Last Login</th>
                                        <th>Last IP</th>
                                    </tr>
                                    <?php if ($result): ?>
                                        <?php while ($row = $result->fetch_assoc()): ?>
                                            <?php $date = strtotime($row["date"]); ?>
                                            <tr class="highlight" onClick="load('account=<?php echo $row["user_id"]; ?>', '#content');">
                                                <td><?php echo $row["username"]; ?></td>
                                                <td><?php echo $row["email"]; ?></td>
                                                <td><?php echo $row["edit"] ? "yes" : "no"; ?></td>
                                                <td><?php echo $row["admin"] ? "yes" : "no"; ?></td>
                                                <td><?php echo $row["subscribe"] ? "yes" : "no"; ?></td>
                                                <td><?php echo date("F jS Y", $date); ?></td>
                                                <td><?php echo $row["ip"]; ?></td>
                                            </tr>
                                        <?php endwhile; ?>
                                    <?php endif; ?>
                                </table>
                            </div>
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
                                    <textarea class="content" name="content" maxlength="4096" placeholder="Content"></textarea><br/>
                                    <input type="submit" value="Save"/>
                                    <input type="button" value="Cancel" onClick="load('search=' + search + '&amp;page=' + page, '#content');"/>
                                </form>
                                <hr/>
                            <?php endif; ?>
                            <?php
                            // Initialize the search term, page, and number of page tabs
                            $search = isset($_GET["search"]) ? $_GET["search"] : "";
                            $_SERVER["page"] = $_SERVER["page_tabs"] = 1;
                            if (isset($_GET["page"]))
                                $_SERVER["page"] = (int) $_GET["page"];

                            // Load the page
                            $limit = ($_SERVER["page"] - 1) * 5;
                            $result = $_SERVER["mysqli"]->query("SELECT * FROM posts INNER JOIN users ON posts.user_id = users.user_id WHERE users.username LIKE '%{$search}%' OR posts.post_id LIKE '{$search}' OR posts.date LIKE '%{$search}%' OR posts.title LIKE '%{$search}%' OR posts.content LIKE '%{$search}%' ORDER BY posts.date DESC LIMIT {$limit}, 50");
                            $row = $result->fetch_assoc();

                            // Determine how many page tabs to show
                            $_SERVER["page_tabs"] = ceil($result->num_rows / 5);
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
                                            <textarea class="content" name="content" maxlength="4096" placeholder="Content"><?php echo $row["content"]; ?></textarea>
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
