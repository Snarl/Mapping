<?php

// Title: removeExit.php DEPRECATED
// Description: Delete (actually archive) an exit
// Author: Samuel Gaus
// Args: title

require("config.php");

callAPI("renameExit.php?title={$_GET['title']}&del=true");

?>
