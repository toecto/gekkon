<?php
function gekkon_tag_wrap($gekkon_compiler,$_tag)
{
	if($_tag['raw_args']=='')
		return $gekkon_compiler->compile_r($_tag['content']);
	

	$exp=$gekkon_compiler->compile_exp('@_ob_buffer.'.$_tag['raw_args']);
	if($exp===false)
		return r_error('gekkon: tag wrap: Cannot compile arg');

	return "ob_start();\n".
	$gekkon_compiler->compile_str($_tag['content']).
	'$_ob_buffer'."=ob_get_contents();\n".
	"ob_end_clean();\n".
	'echo '.$exp.";\n";

}