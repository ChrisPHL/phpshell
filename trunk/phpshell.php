<?php

define('PHPSHELL_VERSION', '1.5');

/*

  **************************************************************
  *                        PHP Shell                           *
  **************************************************************
  $Id: phpshell.php,v 1.15 2002/03/22 23:39:09 gimpster Exp gimpster $

  An interactive PHP-page that will execute any command entered.
  See the files README and INSTALL or http://www.gimpster.com  for
  further information. 
  Copyright (C) 2000-2002 Martin Geisler <gimpster@gimpster.com>

  This program is free software; you can redistribute it and/or
  modify it under the terms of the GNU General Public License
  as published by the Free Software Foundation; either version 2
  of the License, or (at your option) any later version.
  
  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.
  
  You can get a copy of the GNU General Public License from this
  address: http://www.gnu.org/copyleft/gpl.html#SEC1
  You can also write to the Free Software Foundation, Inc., 59 Temple
  Place - Suite 330, Boston, MA  02111-1307, USA.
  
*/
?>

<html>
<head>
<title>PHP Shell <?php echo PHPSHELL_VERSION ?></title>
</head>
<body>
<h1>PHP Shell <?php echo PHPSHELL_VERSION ?></h1>

<?php
/* First we check if there has been asked for a working directory. */
if (!empty($work_dir)) {
  /* A workdir has been asked for */
  if (!empty($command)) {
    if (ereg('^[[:blank:]]*cd[[:blank:]]+([^;]+)$', $command, $regs)) {
      if ($regs[1][0] == '/') {
        $new_dir = $regs[1];
      } else {
        $new_dir = $work_dir . '/' . $regs[1];
      }
      if (file_exists($new_dir) && is_dir($new_dir)) {
        $work_dir = $new_dir;
      }
      unset($command);
    }
  }
}

/* we chdir to that dir. */
if (file_exists($work_dir) && is_dir($work_dir)) {
  chdir($work_dir);
  $work_dir = exec("pwd");
} else {
  /* No work_dir - we chdir to $DOCUMENT_ROOT */
  chdir($DOCUMENT_ROOT);
  $work_dir = $DOCUMENT_ROOT;
}
?>

<form name="myform" action="<?php echo $PHP_SELF ?>" method="post">
<p>Current working directory: <b>
<?php
$work_dir_splitted = explode("/", substr($work_dir, 1));
echo "<a href=\"$PHP_SELF?work_dir=" . urlencode($url) . "/&command=" . urlencode($command) . "\">Root</a>/";
if ($work_dir_splitted[0] == "") {
    $work_dir = "/";  /* Root directory. */
} else {
  for ($i = 0; $i < count($work_dir_splitted); $i++) {
    /*  echo "i = $i";*/
    $url .= "/".$work_dir_splitted[$i];
    echo "<a href=\"$PHP_SELF?work_dir=" . urlencode($url) . "&command=" . urlencode($command) . "\">$work_dir_splitted[$i]</a>/";
  }
}
?></b></p>
<p>Choose new working directory:
<select name="work_dir" onChange="this.form.submit()">
<?php
/* Now we make a list of the directories. */
$dir_handle = opendir($work_dir);
/* Run through all the files and directories to find the dirs. */
while ($dir = readdir($dir_handle)) {
  if (is_dir($dir)) {
    if ($dir == ".") {
      echo "<option value=\"$work_dir\" selected>Current Directory</option>\n";
    } elseif ($dir == "..") {
      /* We have found the parent dir. We must be carefull if the parent 
	 directory is the root directory (/). */
      if (strlen($work_dir) == 1) {
	/* work_dir is only 1 charecter - it can only be / There's no
          parent directory then. */
      } elseif (strrpos($work_dir, "/") == 0) {
	/* The last / in work_dir were the first charecter.
	   This means that we have a top-level directory
	   eg. /bin or /home etc... */
      echo "<option value=\"/\">Parent Directory</option>\n";
      } else {
      /* We do a little bit of string-manipulation to find the parent
	 directory... Trust me - it works :-) */
      echo "<option value=\"". strrev(substr(strstr(strrev($work_dir), "/"), 1)) ."\">Parent Directory</option>\n";
      }
    } else {
      if ($work_dir == "/") {
	echo "<option value=\"$work_dir$dir\">$dir</option>\n";
      } else {
	echo "<option value=\"$work_dir/$dir\">$dir</option>\n";
      }
    }
  }
}
closedir($dir_handle);
?>

</select></p>

<p>Command: <input type="text" name="command" size="60">
<input name="submit_btn" type="submit" value="Execute Command"></p>

<p>Enable <code>stderr</code>-trapping? <input type="checkbox" name="stderr"></p>
<textarea cols="80" rows="20" readonly>

<?php
if (!empty($command)) {
  if ($stderr) {
    $command .= " 1> /tmp/output.txt 2>&1; " .
    "cat /tmp/output.txt; rm /tmp/output.txt";
  } else if ($command == 'ls') {
    /* ls looks much better with ' -F', IMHO. */
    $command .= ' -F';
  }
  system($command);
}
?>

</textarea>
</form>

<script language="JavaScript" type="text/javascript">
document.forms[0].command.focus();
</script>

<hr>
<i>Copyright &copy; 2000-2002, <a
href="mailto:gimpster@gimpster.com">Martin Geisler</a>. Get the latest
version at <a href="http://www.gimpster.com">www.gimpster.com</a>.</i>
</body>
</html>
