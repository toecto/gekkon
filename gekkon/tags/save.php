<?php

function gekkon_tag_save($gekkon_compiler,$_tag)
{
	return $gekkon_compiler->compile_static($_tag['content']);
}