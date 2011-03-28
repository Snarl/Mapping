<?php

// Title: getcolorwheeldata.php DEPRECATED
// Description: Returns XML containing set of triplets indicating start-angle, end-angle, exit-status
// Author: Bernie Gaus and Samuel Gaus
// Args: loc="lat,lng", [zone="zonename"]

require("./config.php");

echo callAPI("getCompassData.php?loc={$_GET['loc']}&zone={$_GET['zone']}");

?>
