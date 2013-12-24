<?php
error_reporting(-1);
session_start();
include_once("gamifyws.php");

use gamifyws\gamifyws;

$gamify = new gamifyws('testing', 'e2156f086656426f2b6cc1ff8909d534', '7146df91deaddbcb581d26daa29b95b3');

$details = $gamify->create_action('Someone Does Something');
echo $details;

$actions = $gamify->get_awarded("actions");
echo "<pre>";
var_dump(json_decode($actions));


