<?php
add_action('admin_menu', 'wucr_initOption');

function wucr_initOption() {
    add_options_page('WordPress User Chatroom', 'WordPress User Chatroom', 'manage_options', 'wucr-settings', 'wucr_initPage');
}

function wucr_initPage() {

    echo '<h2 class="wucr_headline"><b>WordPress User Chatroom</b></h2>';

    if (!current_user_can('manage_options')) {
        wp_die(__('You do not have sufficient permissions to access this page.'));
    }

    if (isset($_POST['submit'])) {

        $chat_colour = $_POST['chat_colour'];
        $refresh_interval = $_POST['refresh_interval'];

        if (!preg_match('/^[a-f0-9]{6}$/i', $chat_colour)) {

            echo '<div class="alert">
The chat colour is not a valid HEX code.
</div>';

        }
        elseif (!is_numeric($refresh_interval)) {

            echo '<div class="alert">
The refresh interval is not an integer.
</div>';

        }
        else {

            update_option("wucr_chat_colour", $chat_colour);
            update_option("wucr_refresh_interval", $refresh_interval);

            echo '<div class="alert">
The chat has been successfully updated.
</div>';

        }

    }

    $wucr_chat_colour = get_option("wucr_chat_colour");
    $wucr_refresh_interval = get_option("wucr_refresh_interval");

    echo '<form action="" method="post">

<div class="field-group">
<span class="field-title">Chat Colour:</span>
<br>
<input type="text" name="chat_colour" value="' . $wucr_chat_colour . '" class="jscolor form-control">
<br>
The default colour is <b>#2780e3</b>.
</div>

<div class="field-group">
<span class="field-title">Chat Refresh Interval:</span>
<br>
<input type="number" name="refresh_interval" value="' . $wucr_refresh_interval . '" class="form-control" min="1">
<br>
The default time period is <b>3</b>.
</div>

<input type="submit" name="submit" class="button button-primary" value="Save Changes">

</form>';

    echo '<br>';
    echo '<br>';

    echo 'Did you like the plugin? Visit us at <b><a href="https://overfeat.com/" class="link">Overfeat.com</a></b>.';

    echo '<style>

.link {
	
text-decoration:none;
	
}

.alert {
	
background:#FFFDDB;
padding:15px;
font-size:90%;
margin-bottom:10px;
width:70%;
	
}

.wucr_headline {
	
font-size:200%;
text-transform:uppercase;
	
}

.form-control {
	
margin-top:7px;
min-height:40px;
margin-bottom:6px;
	
}

input.form-control {
	
width:40%;
	
}

.field-title {
	
font-size:125%;
font-weight:bold;
	
}

.field-group {
	
padding-bottom:25px;
	
}

</style>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jscolor/2.0.4/jscolor.min.js"></script>';

}

function wucr_initChat() {

    if (is_user_logged_in() == true) {

        echo '<div id="chatbox_div" style="display:none;">
<div class="shout_box">
<div class="header" id="chat_header" onclick="loaderShoutBox();">Chat</div>
<div id="chattoken">
<div class="message_box">
</div>
<div class="user_info">
<input name="shout_message" id="shout_message" type="text" placeholder="Enter your message and hit Enter" maxlength="100"> 
</div>
</div>
</div>
</div>';

        $chat_colour = get_option("wucr_chat_colour");
        $efresh_interval = get_option("wucr_refresh_interval");

        echo '<script>

jQuery(document).ready(function($){

if(typeof jQuery == "undefined") {

var headTag = document.getElementsByTagName("head")[0];
var jqTag = document.createElement("script");
jqTag.type = "text/javascript";
jqTag.src = "https://cdnjs.cloudflare.com/ajax/libs/jquery/3.1.1/jquery.min.js";
jqTag.onload = wucr_js;
headTag.appendChild(jqTag);

} else {

if($(window).width() > 768) {
	
document.getElementById("chatbox_div").style.display = "block";

load_data = {"fetch":1};

window.setInterval(function(){
	
$.post("' . plugins_url() . '/wordpress-user-chat-room/chat.php?method=load", load_data, function(data) {
$(".message_box").html(data);
var scrolltoh = $(".message_box")[0].scrollHeight;
$(".message_box").scrollTop(scrolltoh);
});

}, ' . ($efresh_interval * 1000) . ');

$("#shout_message").keypress(function(evt) {
     if(evt.which == 13) {
             var imessage = $("#shout_message").val();
             post_data = {"message":imessage};
             
             //send data to "shout.php" using jQuery $.post()
             $.post("' . plugins_url() . '/wordpress-user-chat-room/chat.php?method=message", post_data, function(data) {
                 
                 //append data into messagebox with jQuery fade effect!
                 $(data).hide().appendTo(".message_box").fadeIn();

                 //keep scrolled to bottom of chat!
                 var scrolltoh = $(".message_box")[0].scrollHeight;
                 $(".message_box").scrollTop(scrolltoh);
                 
                 //reset value of message box
                 $("#shout_message").val("");
				 
			 $.post("' . plugins_url() . '/wordpress-user-chat-room/chat.php?method=load", load_data, function(data) {
			$(".message_box").html(data);
			var scrolltoh = $(".message_box")[0].scrollHeight;
			$(".message_box").scrollTop(scrolltoh);
			});
                 
             }).fail(function(err) { 
             
             alert(err.statusText);
			 
             });
         }
})

var keyValue = document.cookie.match(\'(^|;) ?\' + "chatbox_visibility" + "=([^;]*)(;|$)");
var visib = keyValue ? keyValue[2] : null;

if(visib == "") {
	
var expires = new Date();
expires.setTime(expires.getTime() + (1 * 24 * 60 * 60 * 1000 * 365));
document.cookie = "chatbox_visibility" + \'=\' + "0" + \';expires=\' + expires.toUTCString();

document.getElementById("chattoken").style.display = "block";
	
} else if(visib == 1) {

document.getElementById("chattoken").style.display = "block";

} else {
	
document.getElementById("chattoken").style.display = "none";
	
}

}

}

});

function loaderShoutBox() {
	
var aval = document.getElementById("chattoken").style.display;

if(aval == "block") {
	
document.getElementById("chattoken").style.display = "none";

var expires = new Date();
expires.setTime(expires.getTime() + (1 * 24 * 60 * 60 * 1000 * 365));
document.cookie = "chatbox_visibility" + \'=\' + "0" + \';expires=\' + expires.toUTCString();

} else {
	
document.getElementById("chattoken").style.display = "block";

var expires = new Date();
expires.setTime(expires.getTime() + (1 * 24 * 60 * 60 * 1000 * 365));
document.cookie = "chatbox_visibility" + \'=\' + "0" + \';expires=\' + expires.toUTCString();
	
}

}

</script>';

        echo '<style>

.shout_box {

background:#' . $chat_colour . ';
width:260px;
position:fixed;
bottom:0;
right:0%;
z-index:2147483647;

}

.shout_box .header .close_btn {

float:right;
width:15px;
height: 15px;

}

.shout_box .header .close_btn:hover {


}

.shout_box .header .open_btn {

float:right;
width:15px;
height:15px;

}

.shout_box .header .open_btn:hover {


}

.shout_box .header {
	
padding: 5px 3px 5px 5px;
font: 11px "lucida grande", tahoma, verdana, arial, sans-serif;
font-weight: bold; color:#fff;
border: 1px solid rgba(0, 39, 121, .76);
border-bottom:none; cursor:pointer;

}

.shout_box .header:hover {
	
background-color: #' . $chat_colour . ';

}

.shout_box .message_box {
	
background: #FFFFFF; height: 200px;
overflow:auto; border: 1px solid #CCC;
height:400px;

}

.shout_msg {

margin-bottom: 10px; display: block;
border-bottom: 1px solid #F3F3F3; padding: 0px 5px 5px 5px;
font: 11px "lucida grande", tahoma, verdana, arial, sans-serif; color:#7C7C7C;

}

.message_box:last-child { 

border-bottom:none;

}

time { 

font: 11px "lucida grande", tahoma, verdana, arial, sans-serif;
font-weight: normal; float:right; color: #D5D5D5;

}

.shout_msg .username {

margin-bottom: 10px;
margin-top: 10px;

}

.user_info input {

width: 98%; height: 25px; border: 1px solid #CCC; 
border-top: none; padding: 3px 0px 0px 3px;
font: 11px "lucida grande", tahoma, verdana, arial, sans-serif;

}

.shout_msg .username {
	
font-weight: bold; display: block;

}

</style>';

    }

}

add_action('wp_footer', 'wucr_initChat');

