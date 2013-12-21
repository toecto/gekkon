<?php

function gekkon_tag_switch($gekkon_compiler,$_tag)
{

	$exp=$gekkon_compiler->compile_exp($_tag['raw_args']);
	if($exp===false)
		return r_error('gekkon: tag if: Cannot compile args');

	return 'switch('.$exp."){\ndefault:\n".
	$gekkon_compiler->compile_str($_tag['content']).
	"}\n";
}

?>