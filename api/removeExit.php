<?php

require("config.php");

$old = "{$settings['data_dir']}/{$_GET['id']}.exit";
$new = "{$settings['data_dir']}/deleted/{$_GET['id']}." . time() . ".exit";
rename($old,$new);

?>
