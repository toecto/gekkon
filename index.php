<?php

ini_set("display_errors", 'on');
include "gekkon/Gekkon.php";



$gekkon = new Gekkon(dirname(__file__).'/tpl/', dirname(__file__).'/tpl_bin/');


$gekkon->display("index.tpl");


