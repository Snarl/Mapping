<?php

// Title: removeZone.php
// Description: Delete (actually archive) a zone
// Author: Samuel Gaus
// Args: title

require("config.php");

$title = str_replace("'","^",$_GET['title']);

$old = "{$settings['data_dir']}/{$title}.zone";
$new = "{$settings['data_dir']}/deleted/{$title}." . time() . ".zone";
rename($old,$new);

?>
