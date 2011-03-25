<?php

// Title: removeZone.php DEPRECATED
// Description: Delete (actually, archive) a zone
// Author: Samuel Gaus
// Args: title

require("config.php");

callAPI("renameZone.php?title={$_GET['title']}&del=true");

?>
