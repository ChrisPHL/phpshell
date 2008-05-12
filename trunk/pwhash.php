<?php
/*
 * pwhash.php file for PHP Shell @VERSION@
 * Copyright (C) 2005, 2006, 2008 Martin Geisler <mgeisler@mgeisler.net>
 * Licensed under the GNU GPL.  See the file COPYING for details.
 *
 * $Rev$ $Date$
 */


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
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN"
   "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
  <title>Password Hasher for PHP Shell @VERSION@</title>
  <link rel="stylesheet" href="style.css" type="text/css">
</head>

<body>

<h1>Password Hasher for PHP Shell @VERSION@</h1>

<form action="<?php $_SERVER['PHP_SELF']; ?>" method="POST">

<fieldset>
  <legend>Username</legend>
  <input name="username" type="text" value="<?php echo $username ?>">
</fieldset>

<fieldset>
  <legend>Password</legend>
  <input name="password" type="text" value="<?php echo $password ?>">
</fieldset>

<fieldset>
  <legend>Result</legend>

<?php
if ($username == '' || $password == '') {
  echo "  <p><i>Enter a username and a password and update.</i></p>\n";
} else {

  $u = strtolower($username);

  if (preg_match('/[[ |&~!()]/', $u) || $u == 'null' ||
      $u == 'yes' || $u == 'no' || $u == 'true' || $u == 'false') {

    echo '  <p class="error">Your username cannot contain any of the following reserved
  word: "<tt>null</tt>", "<tt>yes</tt>", "<tt>no</tt>", "<tt>true</tt>", or
  "<tt>false</tt>".  The following characters are also prohibited:
  "<tt>&nbsp;</tt>" (space), "<tt>[</tt>" (left bracket), "<tt>|</tt>" (pipe),
  "<tt>&</tt>" (ampersand), "<tt>~</tt>" (tilde), "<tt>!</tt>" (exclamation
  mark), "<tt>(</tt>" (left parenthesis), or "<tt>)</tt>" (right
  parenthesis).</p>' . "\n";

    echo '  <p>Please choose another username and try again.</p>' . "\n";

  } else {
    echo "  <p>Write the following line into <tt>config.php</tt> " .
      "in the <tt>users</tt> section:</p>\n";

    $fkt = 'md5'; // Change to sha1 is you feel like it...
    $salt = dechex(mt_rand());

    $hash = $fkt . ':' . $salt . ':' . $fkt($salt . $password);

    echo "<pre>\n";
    echo htmlentities(str_pad($username, 8) . ' = "' . $hash . '"') . "\n";
    echo "</pre>\n";
  }
}
?>

<p><input type="submit" value="Update"></p>

</fieldset>

</form>


<hr>

<address>
  Copyright © <a href="mailto:mgeisler@mgeisler.net">Martin
  Geisler</a> and others, please see <a href="AUTHORS">AUTHORS</a>.
  This is PHP Shell @VERSION@, get the latest version at <a
  href="http://phpshell.sourceforge.net/">phpshell.sf.net</a>.
</address>

</body>
</html>
