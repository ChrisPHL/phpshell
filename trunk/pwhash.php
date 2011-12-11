<?php
/*
 * pwhash.php file for PHP Shell
 * Copyright (C) 2005-2011 the Phpshell-team
 * Licensed under the GNU GPL. See the file COPYING for details.
 *
 */

define('PHPSHELL_VERSION', '2.3');

function stripslashes_deep($value) {
  if (is_array($value))
    return array_map('stripslashes_deep', $value);
  else
    return stripslashes($value);
}

if (get_magic_quotes_gpc())
  $_POST = stripslashes_deep($_POST);

$username = isset($_POST['username']) ? $_POST['username'] : '';
$password = isset($_POST['password']) ? $_POST['password'] : '';

?>
<?php echo '<?xml version="1.0" ?>' ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
     "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
  <meta http-equiv="Content-type" content="text/html;charset=UTF-8" />
  <title>Password Hasher for PHP Shell <?php echo PHPSHELL_VERSION ?></title>
  <meta http-equiv="Content-Style-Type" content="text/css"/>
  <meta name="generator" content="phpshell"/>
  <link rel="shortcut icon" type="image/x-icon" href="phpshell.ico"/>
  <link rel="stylesheet" href="style.css" type="text/css"/>
</head>

<body>

<h1>Password Hasher for PHP Shell <?php echo PHPSHELL_VERSION ?></h1>

<form action="<?php $_SERVER['PHP_SELF']; ?>" method="post">

<fieldset>
  <legend>Username/Password</legend>
  <label for="username">Username:</label>
  <input name="username" id="username" type="text" value="<?php echo $username ?>"/>
  <br/>
  <label for="password">Password:</label>
  <input name="password" id="password" type="text" 
         value="<?php echo htmlspecialchars($password) ?>"/>
</fieldset>

<fieldset>
  <legend>Result</legend>

<?php
if ($username == '' || $password == '') {
    echo '  <p><i>Enter a username and a password and update.</i></p><br/>';
} else {
    $u = strtolower($username);
    if (!preg_match('/^[[:alpha:]][[:alnum:]]*$/', $u) || $u == 'null' ||
       $u == 'yes' || $u == 'no' || $u == 'true' || $u == 'false'
       ) {
        echo '<p class="error">Your username cannot contain any of the following reserved
  words: "<tt>null</tt>", "<tt>yes</tt>", "<tt>no</tt>", "<tt>true</tt>", or
  "<tt>false</tt>".  It can contain only letters and digits and must start with a letter.' . "\n";

    echo '  <p>Please choose another username and try again.</p>' . "\n";

  } else {
    echo "  <p>Write the following line into <tt>config.php</tt> " .
      "in the <tt>[users]</tt> section:</p>\n";

    if ( function_exists('sha1') ) {
       $fkt = 'sha1' ; 
    } else {
       $fkt = 'md5' ; 
    } ;
    $salt = dechex(mt_rand());

    $hash = $fkt . ':' . $salt . ':' . $fkt($salt . $password);

    echo "<pre>\n";
    echo "$u = &quot;$hash&quot;\n";
    echo "</pre>\n";
  }
}
?>
<p><input type="submit" value="Update"/></p>
</fieldset>
</form>
<hr/>

<address>
  Copyright &copy; the Phpshell-team, please see <a href="AUTHORS">AUTHORS</a>.
  This is PHP Shell <?php echo PHPSHELL_VERSION ?>, get the latest version at <a
  href="http://phpshell.sourceforge.net/">http://phpshell.sourceforge.net/</a>.
</address>

</body>
</html>
