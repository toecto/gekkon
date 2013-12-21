<?php

function gekkon_tag_case_single($gekkon_compiler,$_tag)
{
	$exp=$gekkon_compiler->compile_exp($_tag['raw_args']);
	if($exp===false)
		return r_error('gekkon: tag if: Cannot compile expression');

	return $exp.":\n";
}

