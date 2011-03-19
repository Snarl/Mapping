<?php

// Title: removeZone.php
// Description: Delete (actually archive) a zone
// Author: Samuel Gaus
// Args: title

require("config.php");

$old = "{$settings['data_dir']}/{$_GET['id']}.zone";
$new = "{$settings['data_dir']}/deleted/{$_GET['id']}." . time() . ".zone";
rename($old,$new);

?>
