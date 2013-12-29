<?php

function gekkon_tag_if($compiler, $_tag)
{
    //print_r($_tag);die();
    $_rez = '';
    $exp = $compiler->compile_exp($_tag['raw_args']);

    if($exp === false)
            return $compiler->error('Cannot compile expression "'.$_tag['raw_args'].'"',
                'Tag IF', $_tag['line']);

    $_rez = "if(".$exp."){\n";
    return $_rez.
        $compiler->compile_str($_tag['content'], $_tag).
        "}\n";
}

