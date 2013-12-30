<?php

function gekkon_tag_set_single($compiler, $_tag)
{
    //print_r($_tag);die();
    $_rez = '';
    $exp = $compiler->compile_exp($_tag['raw_args']);

    if($exp === false)
            return $compiler->error_in_tag('Cannot compile expression "'.$_tag['raw_args'].'"',
                $_tag);

    return $exp.";\n";
}

