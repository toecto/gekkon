<?php

function gekkon_tag_exec_single($gekkon_compiler,$_tag)
{
	$args=$gekkon->parse_args($_tag['raw_args']);
	if($args===false)
		return r_error('gekkon: tag exec: Cannot compile args');

	if(!isset($args['interface']))$args['interface']='""';
	if(!isset($args['action']))$args['action']='""';
	if(!isset($args['module']))$args['module']='""';
	if(!isset($args['template']))$args['template']='""';

	return 'execute('.$args['interface'].','.$args['action'].','.$args['template'].','.$args['module'].")\n";
}

