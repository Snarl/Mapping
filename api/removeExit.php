<?php

// Title: removeExit.php
// Description: Delete (actually archive) an exit
// Author: Samuel Gaus
// Args: title

require("config.php");

$title = str_replace("'","^",$_GET['title']);

$old = "{$settings['data_dir']}/{$title}.exit";
$new = "{$settings['data_dir']}/deleted/{$title}." . time() . ".exit";
rename($old,$new);

?>
