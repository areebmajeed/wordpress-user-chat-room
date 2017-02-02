<?php
define('WP_USE_THEMES', false);
require_once ('../../../wp-load.php');

if (is_user_logged_in() == true) {

    $user_d = wp_get_current_user();
    $user_name = $user_d->user_nicename;

    $method = $_GET['method'];
    $prefix = $wpdb->prefix;

    if ($method == "load") {

        $load = $wpdb->get_results("SELECT user,message,date FROM (select id,user,message,date from {$prefix}wucr_chat ORDER BY id DESC LIMIT 30) {$prefix}wucr_chat ORDER BY {$prefix}wucr_chat.id ASC");

        foreach ($load as $msg) {

            echo '<div class="shout_msg"><time>' . $msg->date . '</time><span class="username">' . $msg->user . '</span> <span class="message">' . substr($msg->message, 0, 100) . '</span></div>';

        }

    }
    elseif ($method == "message") {

        $load = $wpdb->get_results("SELECT date FROM {$prefix}wucr_chat WHERE user = '{$user->user_login}' ORDER BY id DESC LIMIT 1");

        $c = array();

        foreach ($load as $msg) {

            $c['time'] = $msg['date'];

        }

        $rn = time();
        $rl = strtotime($c['date']);

        $diff = $rn - $rl;

        $message = substr($_POST['message'], 0, 100);

        $regex = "((https?|ftp)\:\/\/)?"; // SCHEME
        $regex .= "([a-z0-9+!*(),;?&=\$_.-]+(\:[a-z0-9+!*(),;?&=\$_.-]+)?@)?"; // User and Pass
        $regex .= "([a-z0-9-.]*)\.([a-z]{2,3})"; // Host or IP
        $regex .= "(\:[0-9]{2,5})?"; // Port
        $regex .= "(\/([a-z0-9+\$_-]\.?)+)*\/?"; // Path
        $regex .= "(\?[a-z+&\$_.-][a-z0-9;:@&%=+\/\$_.-]*)?"; // GET Query
        $regex .= "(#[a-z_.-][a-z0-9+\$_.-]*)?"; // Anchor
        if (!preg_match("/^$regex$/", $message)) {

            $regex = "@(https?://([-\w\.]+[-\w])+(:\d+)?(/([\w/_\.#-]*(\?\S+)?[^\.\s])?).*$)@";
            $message = preg_replace($regex, ' ', $message);

            if ($diff >= 2 && $message != "" && strlen($message) > 0) {

                $user = $user_name;

                if (current_user_can('manage_options')) {

                    $user = "<span style='color:#" . get_option("wucr_chat_colour") . "'>" . $user_name . "</span>";

                }

                $wpdb->insert($prefix . 'wucr_chat', array(
                    'user' => $user,
                    'date' => date("Y-m-d H:i:s") ,
                    'message' => $message
                ) , array(
                    '%s',
                    '%s',
                    '%s'
                ));

            }
            else {

                header('HTTP/1.1 406 Please wait for at-least 2 seconds before sending another message.');

            }

        }
        else {

            header('HTTP/1.1 406 URLs are not allowed.');

        }

    }

}
else {

    exit("You are not logged in");

}

