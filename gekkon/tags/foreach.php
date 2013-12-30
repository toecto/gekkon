<?php

function gekkon_tag_foreach($compiler, $_tag)
{
    $args = $compiler->parse_args($_tag['raw_args']);
    if($args === false)
            return r_error('gekkon: tag foreach: Cannot compile args');

    $meta_init = '';
    $meta_body = '';
    if(isset($args['meta']))
    {
        $meta_name = $args['meta'];
        $meta_init = "\$t=count(".$args['from'].");
        ".$meta_name."=array(
        'first'=>1,
        'last'=>(\$t==1?1:0),
        'counter0'=>0,
        'counter'=>1,
        'revcounter0'=>\$t-1,
        'revcounter'=>\$t,
        'total'=>\$t,
        );";

        $meta_body = "
        ".$meta_name."['counter0']++;
        ".$meta_name."['counter']++;
        ".$meta_name."['revcounter0']--;
        ".$meta_name."['revcounter']--;
        ".$meta_name."['first']=0;
        ".$meta_name."['last']=(".$meta_name."['revcounter0']==0?1:0);
        ";
    }

    if(isset($args['key']))
            $loop_start = 'foreach('.$args['from'].' as '.$args['key'].'=>'.$args['item']."){\n";
    else $loop_start .= 'foreach('.$args['from'].' as '.$args['item']."){\n";

    return $meta_init.
        $loop_start.
        $compiler->compile_str($_tag['content'], $_tag).
        $meta_body.
        "}\n";
}

