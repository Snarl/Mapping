<?php

require("config.php");

$old = "{$settings['data_dir']}/{$_GET['id']}.zone";
$new = "{$settings['data_dir']}/deleted/{$_GET['id']}." . time() . ".zone";
rename($old,$new);

?>
