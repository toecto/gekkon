<?php

function gekkon_tag_cache($gekkon_compiler,$_tag)
{
	static $var_cnt=0;
	$var_cnt++;

	$stream='"none'.$var_cnt.'"';
	if($_tag['raw_args']!='')
	{
		$stream=$gekkon_compiler->parse_expression($_tag['raw_args']);
		if($stream===false)
			return r_error('gekkon: tag cache: Cannot compile extression');
	}


	$cache_path="'".$gekkon_compiler->cache_path($gekkon_compiler->tpl_name)."'";

	$rez=
	'$_cache_file='.$cache_path.'.$gekkon->cache_file('.$stream.');
	if(is_file($_cache_file))
	{ 
		r_log(\'Using cache\',\'gekkon\');
		readfile($_cache_file);
	}
	else
	{
		r_log(\'Creating cache\',\'gekkon\');
		ob_start();
		'.$gekkon_compiler->compile_str($_tag['content']).'
		$_ob_buffer=ob_get_flush();
		$_dir_name=dirname($_cache_file);
		if(!is_dir($_dir_name))
			mkdir($_dir_name,0777,true);
		file_put_contents($_cache_file, $_ob_buffer);
	}
	';

if(isset($args['time']))
{}


return $rez;
}


?>