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

// Process the delete account form
function delete_account(user_id) {
                
    // Confirm the account deletion
    if (confirm("Are you sure?")) {
                    
        // Post the request
        $.post("/", {
                        
            // Variables to post
            delete_account: true, 
            user_id: user_id
        }, function(result) {
                        
            // Process the returned value
            switch(result) {
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
        }, function(result) {
                        
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
        }, function(result) {
                        
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
    }, function(result) {
                    
        // Process the returned value
        switch(result) {
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
        }, function(result) {
                        
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
    }, function(result) {
                    
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
        }, function(result) {
            switch(result) {
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
        }, function(result) {
                        
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
        }, function(result) {
                        
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
            subscribe: form.subscribe.value,
            edit: form.edit.value,
            admin: form.admin.value,
            about: form.about.value
        }, function(result) {
                        
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
        }, function(result) {
                        
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
