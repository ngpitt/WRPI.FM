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
        if (strpos($token, "="))
            $token = strstr($token, "=", true);

        // Process the token
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
                if ($code)

                // Replace spaces with HTML spaces
                    $tokens[$i] = str_replace(" ", "&nbsp;", $tokens[$i]);

                // Check if within list or table tags
                if ($list || $table)

                // Remove new lines
                    $tokens[$i] = str_replace("\n", "", $tokens[$i]);

                else

                // Replace new lines with HTML break tags
                    $tokens[$i] = str_replace("\n", "<br/>", $tokens[$i]);

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
        if (strpos($token, "="))
            $token = strstr($token, "=", true);

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
                $text .= "(image)";
                $omit = true;
                break;
            case "[/img]":
                $omit = false;
                break;
            case "[youtube]":
                $text .= "(viedo)";
                $omit = true;
                break;
            case "[/youtube]":
                $omit = false;
                break;
            default:
                if (!$omit)
                    $text .= $tokens[$i];
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
        $string = $_SERVER["mysqli"]->real_escape_string($string);
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

?>
