<?php

ini_set("display_errors", 'on');
include "gekkon/gekkon.php";
include_once "gekkon/ll_parser.php";



$gekkon = new gekkon(dirname(__file__).'/', dirname(__file__).'/tpl_bin/',
        'tpl/');
$gekkon->display("test.tpl");

