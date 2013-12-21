<?php


function gekkon_tag_set_single($gekkon_compiler,$_tag)
{
	//print_r($_tag);die();
	$_rez='';
	$exp=$gekkon_compiler->parse_expression($_tag['raw_args']);
	
	if($exp===false)
		return r_error('gekkon: tag set: Cannot compile args');
	
	return $exp.";\n";
}