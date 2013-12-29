<?php

function gekkon_tag_echo_single($gekkon_compiler, $_tag)
{
    $_rez = '';
    $exp = $gekkon_compiler->compile_exp($_tag['raw_args']);

    if($exp === false) return r_error('gekkon: tag echo: Cannot compile args');

    return 'echo '.$exp.";\n";
}