<?php

function gekkon_tag_if($gekkon_compiler, $_tag)
{
    //print_r($_tag);die();
    $_rez = '';
    $exp = $gekkon_compiler->compile_exp($_tag['raw_args']);
    if($exp === false)
            return r_error('gekkon: tag if: Cannot compile expression');
    $_rez = "if(".$exp."){\n";
    return $_rez.
            $gekkon_compiler->compile_str($_tag['content'], $_tag).
            "}\n";
}