<?php

function gekkon_tag_url_single($gekkon_compiler,$_tag)
{
	$args=$gekkon->parse_args($_tag['raw_args']);
	if($args===false)
		return r_error('gekkon: tag url: Cannot compile args');

	return 'echo arrToUrl(array('.$args['name'].'=>'.$args['value']."))\n";
}

