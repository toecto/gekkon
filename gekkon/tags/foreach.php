<?php

function gekkon_tag_foreach($compiler, $_tag)
{
    $args = $compiler->parse_args($_tag['raw_args']);
    if($args === false)
            return $compiler->error_in_tag('Cannot parse args "'.$_tag['raw_args'].'"',
                $_tag);

    $meta_init = '';
    $meta_body = '';
    if(isset($args['meta']))
    {
        $meta_name = $args['meta'];
        $meta_init = "\$_gkn_temp=count(".$args['from'].");
        ".$meta_name."=array(
        'first'=>1,
        'last'=>(\$_gkn_temp==1?1:0),
        'counter0'=>0,
        'counter'=>1,
        'revcounter0'=>\$_gkn_temp-1,
        'revcounter'=>\$_gkn_temp,
        'total'=>\$_gkn_temp,
        );\n";

        $meta_body = "
        ".$meta_name."['counter0']=".$meta_name."['counter']++;
        ".$meta_name."['revcounter']=".$meta_name."['revcounter0']--;
        ".$meta_name."['first']=0;
        ".$meta_name."['last']=(".$meta_name."['revcounter0']==0?1:0);
        ";
    }

    $loop_start = 'if(!empty('.$args['from'].")){\n";
    if(isset($args['key']))
            $loop_start .= 'foreach('.$args['from'].' as '.$args['key'].'=>'.$args['item']."){\n";
    else $loop_start .= 'foreach('.$args['from'].' as '.$args['item']."){\n";

    $content = $compiler->parse_tag_content($_tag['content'], $_tag);
    $content = $compiler->split_parsed_str($content, 'empty');
    $empty = '';
    if(isset($content[1]))
    {
        $empty = 'else {'.
            $compiler->compile_parsed_str($content[1]).
            "}\n";
    }
    return $meta_init.
        $loop_start.
        $compiler->compile_parsed_str($content[0]).
        $meta_body.
        "}}\n".
        $empty;
}

