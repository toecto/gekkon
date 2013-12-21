<?php


function gekkon_tag_foreach($gekkon_compiler,$_tag)
{
	$args=$gekkon_compiler->parse_args($_tag['raw_args']);
	if($args===false)
		return r_error('gekkon: tag foreach: Cannot compile args');
	
	if(isset($args['key']))
	$_rez='foreach('.$args['from'].' as '.$args['key'].'=>'.$args['item']."){\n";
		else
	$_rez='foreach('.$args['from'].' as '.$args['item']."){\n";
		
	return $_rez.
		$gekkon_compiler->compile_str($_tag['content']).
		"}\n";
}