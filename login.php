<?php
session_start();
require "class/db.class.php";

// LOGIN PROCESSING
if (isset($_POST['username']) && isset($_GET['action']) && $_GET['action']=="login")
{
    $config = new config;
    if($_POST['username']!='root' && $config->returnItem("ldap_auth"))
    {
        $external='ldap';
        //$_POST['remember']='off';
        $users = new users;
        $userID = $users->login_ldap($_POST['username'],$_POST['password']);
    }
    else
    {
        $external=0;
        $users = new users;
        $userID = $users->login($_POST['username'],$_POST['password']);
    }
    

    // search for the user
    if($userID > 0)
    {
        $config = new config;
        
        // if we've found the user set their session values
        $user = new user($userID);
        $_SESSION['userid']=$userID;
        $_SESSION['username']=$user->UserName;
        $_SESSION['external']=$external;
        $_SESSION['dateFormat']=$user->dateformat;                          // format passed to date() to display timestamps
        $_SESSION['metric']=$user->metric;                                  // we should move these calculations into a single class
        $_SESSION['uploads']=$config->returnItem("attachments_enabled");    // bool to use throughout the site to toggle upload stats

        // If the user wants to be remembered set a cookie
        if(isset($_POST['remember']) && $_POST['remember']=="on")// && !$config->returnItem("ldap_auth"))
        {
            // Make a rand md5 key and save it to the DB
            $key=md5(mt_rand());
            $users->setSessionKey($userID,$key);

            // & set a cookie for them locally to last 2 months
            setcookie("racksmith", $key,time()+(60*60*24*60));
        }

        $redirect='index.php';
        if(isset($_POST['redirect']))
        {
            $url = urldecode($_POST['redirect']);
            $cleanURL=strip_tags($url);
            $URLcomponents=parse_url($cleanURL);
            //$domain=$URLcomponents['host'];

            $redirect=$URLcomponents['path'];
            if(isset($URLcomponents['query']))
                $redirect.="?".$URLcomponents['query'];
        }

        header("Location: ".$redirect);
        /*print_r($_SESSION);
        echo "We should be reidrecting";
        echo "<br/><br/>".$redirect;
        exit();*/
    }
    else
    {
        session_destroy();
        header("Location: login.php?error=invalidLogin");
        exit();
    }
}

// LOGOUT KILLS SESSION AND REDIRECTS TO INDEX 
else if(isset($_GET['action']) && $_GET['action'] == "logout")
{
    setcookie("racksmith", "", time()-3600);
    session_destroy();
    header("Location: login.php");
}
// IF THE USER IS LOGGED IN SEND TO INDEX
else if(isset($_SESSION['userid']) && $_SESSION['userid'] != 'Guest')
    header("Location: index.php");

// STANDARD LOGIN FORM
else
{
    // detect if we have an iPad & if the mobile folder exists send them there
    if(strstr($_SERVER['HTTP_USER_AGENT'],'iPad') && file_exists("mobile"))
            header("Location: mobile/login.php");
             
    $errorMsg = false;
    if(isset($_GET['error']))
        switch($_GET['error'])
        {
            case "noUser":
                $errorMsg = "No user was found with those credentials";
                break;
            case "invalidEmail":
                $errorMsg = "Please check the email address you entered  ";
                break;
            case "invalidUser":
                $errorMsg = "The username and password you entered was incorrect";
                break;
            case "invalidLogin":
                $errorMsg = "Invalid username or password";
                break;
            default:
                $errorMsg='';
                break;
        }
    
    $globalTopic = "Login Required";
    include "theme/" . $theme . "/top.php";
?>
    <script type="text/javascript">
    $(document).ready(function(){
      $("#username").focus();
    });
    </script>
    <div class="module" id="loginBox" >
        <strong>Login</strong>
        <p>
            <form method="post" action="login.php?action=login" id="login">
            <?php
            if(isset($_GET['redirect']))
            {
                $redirect=strip_tags($_GET['redirect']);
                echo "<input type='hidden' name='redirect' value='".$redirect."' />";
            }
            ?>
            <table class="formTable">
            <tbody>
                <tr>
                    <td><label for='username' >Username:</label></td>
                    <td><input name="username" type="text" id="username" value="" /></td>
                </tr>
                <tr>
                  <td><label for='password' >Password:</label></td>
                  <td><input name="password" type="password" id="password" value="" /></td>
                </tr>
                <tr>
                    <td></td>
                    <td><input type="checkbox" NAME="remember"> Remember me</td>
                </tr>
                <tr style="background-color: #ffffff;"><td></td>
                    <td><input class="login" value="Login" name="btnSubmit" type="submit"></td>
                </tr>
            </tbody></table>
            </form>
    </p>

    </div>
    <?php
        if(isset($errorMsg) && $errorMsg)
            echo '<div class="error" style="margin: 0px auto;width: 365px;border-top:0px;">
                <strong>Error:</strong> '.$errorMsg.'</div>';

include "theme/" . $theme . "/base.php";
}
?>