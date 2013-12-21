<?php

function gekkon_tag_box($gekkon_compiler,$_tag)
{
	$args=$gekkon->parse_args($_tag['raw_args']);
	if($args===false)
		return r_error('gekkon: tag box: Cannot compile args');

	return 
	'init_module('.$args['module'].');
	$gekkon->display('.$args['name'].".'_open.tpl');\n";
	$gekkon_compiler->compile_str($_tag['content']).
	'$gekkon->display('.$args['name']."'._close.tpl');
	uninit_module();\n";
}

