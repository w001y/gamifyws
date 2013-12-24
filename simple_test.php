<?php
error_reporting(-1);
session_start();
include_once("gamifyws.php");

use gamifyws\gamifyws;

$gamify = new gamifyws('demo', 'd7fa79e4b54614bd4befcb25edf07d3d', '641dc005bb8c0333b8ecabde819bbe6b');

$details = $gamify->create_action('Someone Does Something');
echo $details;

$actions = $gamify->get_awarded("actions");
echo "<pre>";
var_dump(json_decode($actions));


