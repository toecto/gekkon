<?php

//version 1.0


class GekkonCompiler {

    function __construct(&$gekkon)
    {
        $this->errors = array();
        $this->gekkon = $gekkon;
        $this->tpl_name = '';
        $this->uid = 0;
        include_once $gekkon->gekkon_path.'ll_parser.php';
        include_once $gekkon->gekkon_path.'lexer.php';
        $this->exp_compiler = new GekkonExpCompiler($this);
    }

    function compile($tpl_name)
    {
        $this->error = array();
        $this->tpl_path = $this->gekkon->tpl_path;
        $tpl_file = $this->gekkon->full_tpl_path($tpl_name);
        if(!is_file($tpl_file))
        {
            $this->error('Cannot find '.$tpl_file, 'gekkon_compiller');
            return false;
        }

        $this->file_list = array();
        $this->bin_file = $this->gekkon->full_bin_path($tpl_name);
        if(strpos($this->bin_file, '/!') !== false) $this->tpl_path = '';

        $this->get_file_list();

        $rez_data = "<?php\n";
        $rez_flag = true;

        if(!is_dir($t = dirname($this->bin_file))) mkdir($t, 0777);

        foreach($this->file_list as $tpl)
        {
            if(($t = $this->compile_file($tpl)) !== false) $rez_data .= $t;
            else $rez_flag = false;
        }
        file_put_contents($this->bin_file, $rez_data);
        return $rez_flag;
    }

    function compile_file($tpl_name)
    {
        $this->tpl_name = $tpl_name;
        return "\nfunction ".$this->gekkon->fn_name($tpl_name)."(&\$gekkon){\n".
            '//Template '.$this->tpl_path.$tpl_name.";\n".
            $this->compile_str(file_get_contents($this->gekkon->full_tpl_path($tpl_name))).
            "}\n";
        $this->tpl_name = '';
    }

    function compile_static($_str)
    {
        if($_str == '') return '';
        return 'echo '.var_export($_str, true).";\n";
    }

    function compile_str($_str, $parent = false)
    {
        if($parent === false)
        {
            $parent = array(
                'line' => 1,
                'name' => 'root',
                'parent_name' => 'none'
            );
        }
        $data = $this->parse_str($_str, $parent);
        $this->flush_errors();
        if($data === false) return false;
        return $this->compile_parsed_str($data);
    }

    function compile_parsed_str($data)
    {
        $rez = '';
        foreach($data as $tag)
        {
            if($tag['name'] == '<static>')
                    $rez.=$this->compile_static($tag['content']);
            else
            {
                $t = $tag['handler']($this, $tag);
                if($t !== false) $rez.=$t;
                else $this->flush_errors();
            }
        }

        return $rez;
    }

    function parse_str($_str, $_parent)
    {
        $rez = array();
        $line = 0;
        while($_str != '')
        {
            if(!preg_match('/\{(\s*([\@\$\(a-z_A-Z]+)(\s*[^\}\n]+)?)\}/us',
                    $_str, $_tag, PREG_OFFSET_CAPTURE))
            {
                $rez[] = array('name' => '<static>', 'content' => $_str);
                break;
            }

            $open_start = $_tag[0][1];
            $open_len = strlen($_tag[0][0]);
            if($open_start > 0)
                    $line+=substr_count($_str, "\n", 0, $open_start);
            $_tag = array(
                'parent_name' => $_parent['name'],
                'name' => $_tag[2][0],
                'raw_args' => isset($_tag[3][0]) ? $_tag[3][0] : '',
                'line' => $_parent['line'] + $line,
                'raw' => $_tag[0][0],
                'raw_in' => $_tag[1][0],
            );

            $rez[] = array('name' => '<static>', 'content' => substr($_str, 0,
                    $open_start));
            $_str = substr($_str, $open_start + $open_len);

            $_tag = $this->load_tag($_tag);

            if($_tag['type'] != 0)
            {
                if(($t = $this->parse_end_of_tag($_tag, $_str)) === false)
                {
                    $this->error_in_tag('Cannot find closing tag'
                        , $_tag);
                    return false;
                }
                $_tag = $t;
                if($_tag['close_start'] != 0)
                {
                    $line+=substr_count($_tag['content'], "\n");
                    $_str = substr($_str,
                        $_tag['close_start'] + $_tag['close_length']);
                }
            }
            else
            {
                $_tag = array('name' => '<static>', 'content' => $_tag['raw']);
            }
            $rez[] = $_tag;
        }
        return $rez;
    }

    function load_tag($_tag)
    {
        $_tag['type'] = 0;

        if($_tag['name'][0] == '@' || $_tag['name'][0] == '$' || $_tag['name'][0] == '(')
        {
            $_tag['raw_args'] = $_tag['raw_in'];
            $_tag['name'] = 'echo';
        }

        if(is_file($tag_file = $this->gekkon->gekkon_path.'tags/'.$_tag['name'].'.php'))
        {
            include_once $tag_file;
            if(function_exists($_tag['handler'] = 'gekkon_tag_'.$_tag['name']))
                    $_tag['type'] = 2;
            else
            {
                if(function_exists($_tag['handler'] .= '_single'))
                        $_tag['type'] = 1;
            }
        }
        return $_tag;
    }

    function parse_end_of_tag($_tag, $_str)
    {
        if($_tag['type'] == 1) $_tag['close_start'] = 0;
        else
        {
            $m1 = array();
            $m2 = array();
            $r = array();
            $now = 0;

            preg_match_all('/{\s*'.$_tag['name'].'\b/Us', $_str, $m1,
                PREG_OFFSET_CAPTURE);
            preg_match_all('/{\s*\/'.$_tag['name'].'\s*}/Us', $_str, $m2,
                PREG_OFFSET_CAPTURE);

            foreach($m1[0] as $item)
            {
                if($item[1] > $now)
                {
                    $r[$item[1]]['type'] = 1;
                    $r[$item[1]]['len'] = strlen($item[0]);
                }
            }

            foreach($m2[0] as $item)
            {
                if($item[1] > $now)
                {
                    $r[$item[1]]['type'] = -1;
                    $r[$item[1]]['len'] = strlen($item[0]);
                }
            }

            ksort($r);
            $f = 1;
            foreach($r as $pos => $item)
            {
                $f += $item['type'];
                if($f == 0)
                {
                    $now = $pos;
                    $end_len = $item['len'];
                    break;
                }
            }
            if($f != 0) return false;

            $_tag['content'] = substr($_str, 0, $pos);
            $_tag['close_start'] = $pos;
            $_tag['close_length'] = $end_len;
        }
        return $_tag;
    }

    function get_file_list($dir = '')
    {
        $list = scandir($this->gekkon->tpl_base_path.$this->tpl_path.$dir);
        foreach($list as $file)
        {
            if($file[0] != '.')
            {
                if(is_dir($this->gekkon->tpl_base_path.$this->tpl_path.$dir.$file))
                        $this->get_file_list($dir.$file.'/');
                else if(strrchr($file, '.') == '.tpl' && $this->bin_file == $this->gekkon->full_bin_path($dir.$file))
                        $this->file_list[] = $dir.$file;
            }
        }
    }

    function error_in_tag($msg, $tag)
    {
        return $this->error($msg, 'Tag: '.$tag['name'], $tag['line']);
    }

    function error($msg, $object = false, $line = false)
    {
        $message = '';
        if($object !== false) $message .= '[<b>'.$object.'</b>] ';
        $message .= $msg.' ';

        if($line !== false)
        {
            if($this->tpl_name !== '')
                    $message .= 'in <b>"'.$this->tpl_name.'"</b> ';
            $message .= 'on line '.$line.' ';
        }

        $this->errors[] = $message;

        return false;
    }

    function flush_errors()
    {
        if(count($this->errors) > 0)
        {
            $this->errors = array_reverse($this->errors);
            $message = implode("\n", $this->errors);

            $this->gekkon->error($message, 'Compiler');
            $this->errors = array();
        }
        return false;
    }

    function getUID()//it is a relatively unique id, for uid inside of one template
    {
        return $this->uid++;
    }

    function split_parsed_str($data, $tag_name, $keep_spliter = false)
    {
        $rez = array();
        $key = 0;
        $rez[$key] = array();
        foreach($data as $tag)
        {

            if($tag['name'] == $tag_name)
            {
                $key++;
                $rez[$key] = array();
                if($keep_spliter) $rez[$key][] = $tag;
            }
            else $rez[$key][] = $tag;
        }
        return $rez;
    }

}

// End Of Class ----------------------------------------------------------------

class GekkonExpCompiler {

    function __construct(&$compiler)
    {
        $this->compiler = $compiler;
        $this->arg_compiler = new gekkon_arg_compiler($this);
        $this->arg_lexer = new GekkonLexer();
    }

    function compile_construction_expressions($data)
    {
        $cnt = count($data);
        for($i = 0; $i < $cnt; $i++)
        {
            if($data[$i]['t'] == '<exp>')
            {
                $t = $this->compile_parsed_exp($data[$i]['v']);
                if($t === false) return false;
                $data[$i]['v'] = $t;
            }
        }
        return $data;
    }

    function compile_exp($str)
    {
        $data = $this->arg_lexer->parse_expression($str);
        if($data === false)
        {
            $this->compiler->error($this->arg_lexer->error, 'arg_lexer');
            return false;
        }
        if(($rez = $this->compile_parsed_exp($data)) === false) return false;
        return $rez;
    }

    function compile_parsed_exp($data)
    {
        $rez = '';
        $orig = '';
        foreach($data as $item)
        {
            if($item['t'] == 'l') $rez.=$item['v'];
            else
            {
                $t = $this->compile_arg($item['v']);
                if($t === false) return false;
                $rez.=$t;
            }
            $orig.=$item['v'];
        }
        if(!$this->check_exp_syntax($rez))
                return $this->compiler->error('Wrong expression syntax "'.$orig.'"',
                    'compile_exp');

        return $rez;
    }

    function compile_arg($str)
    {
        $rez = $this->arg_compiler->compile($str);
        if($rez === false)
        {
            $this->compiler->error($this->arg_compiler->error, 'arg_compiler');
        }
        return $rez;
    }

    function parse_args($_str)
    {
        $_str = explode('=', $_str);
        $_rez = array();
        $cnt = count($_str) - 1;
        $name = trim($_str[0]);
        $i = 1;
        while($i < $cnt)
        {
            $t = strrpos($_str[$i], ' ');
            $val = substr($_str[$i], 0, $t);
            if(($_rez[$name] = $this->compile_exp($val)) === false)
                    return false;

            $name = trim(substr($_str[$i], $t));
            $i++;
        }
        if(isset($_str[$cnt]))
        {
            $val = $_str[$cnt];
            if(($_rez[$name] = $this->compile_exp($val)) === false)
                    return false;
        }
        return $_rez;
    }

    function check_syntax($code)
    {
        ob_start();

        $code = "if(0){{$code}\n}";
        $result = eval($code);
        ob_get_clean();

        return false !== $result;
    }

    function check_exp_syntax($code)
    {
        return GekkonExpCompiler::check_syntax('$x='.$code.';');
    }

    function parse_expression($str)
    {
        return $this->arg_lexer->parse_expression($str);
    }

}

// End Of Class ----------------------------------------------------------------

class gekkon_arg_compiler {

    function __construct(&$exp_compiler)
    {
        $this->exp_compiler = $exp_compiler;

        $this->parser = new GekkonLLParser(array(
            '<argument>' => '<variable><extention> | <word><extention> | s<extention> | (e)<extention> | <digit> ',
            '<digit>' => 'd<long>',
            '<long>' => '| .<value><extention>',
            '<word>' => 'w<function>',
            '<function>' => '| (<parameters>)<extention> | ::<static><extention>',
            '<parameters>' => '| e<parameters> | ,e<parameters>',
            '<variable>' => '$w | @w',
            '<extention>' => '| .<value><extention> | <objectmember><extention>',
            '<value>' => ' <variable> | <word> | s | (e) | d',
            '<objectmember>' => '-><word>',
            '<static>' => '$w | w(<parameters>)',
        ));
    }

    function compile($_str)
    {
        $this->error = '';
        $_str = trim($_str);
        if($_str == '') return '';
        if($_str == '@') return '@';

        $_data = $this->exp_compiler->arg_lexer->parse_variable($_str);

        if(($_data = $this->parser->parse($_data)) === false)
        {
            $this->error .= 'Cannot compile '.$_str.'; '.$this->parser->error;
            return false;
        }
        $this->rez = '';
        $this->n_argument($_data->real());
        return $this->rez;
    }

    function n_argument($_data)
    {
        if(isset($_data['<variable>'])) $this->n_variable($_data['<variable>']);

        else if(isset($_data['<word>'])) $this->n_word($_data['<word>']);

        else if(isset($_data['s'])) $this->t_s($_data['s']);

        else if(isset($_data['<argument>']))
                $this->n_argument($_data['<argument>']);

        else if(isset($_data['<digit>'])) $this->n_digit($_data['<digit>']);

        if(isset($_data['d'])) $this->rez.=current($_data['d']);

        if(isset($_data['e'])) $this->t_e($_data['e'], true);

        if(isset($_data['<extention>']) && is_array($_data['<extention>']))
                $this->n_extention($_data['<extention>']);
    }

    function t_e($_data, $scope = false)
    {
        $save_rez = $this->rez;
        $this->rez = '';
        $rez = $this->exp_compiler->compile_exp(current($_data));
        if($scope === true) $rez = '('.$rez.')';
        $this->rez = $save_rez.$rez;
    }

    function t_s($_data)
    {
        $this->rez .= current($_data);
    }

    function n_digit($_data)//done
    {
        if(isset($_data['d'])) $this->rez .= current($_data['d']);
        if(isset($_data['<long>']) && is_array($_data['<long>']))
                $this->n_long($_data['<long>']);
    }

    function n_long($_data)//done
    {
        if(isset($_data['<value>']['d']))
        {
            $this->rez .= '.'.current($_data['<value>']['d']);
        }
        else if(isset($_data['<value>']['<word>']['<function>']) && is_array($_data['<value>']['<word>']['<function>']) && !isset($_data['<value>']['<word>']['<function>']['<static>']))
        {
            $this->n_function($_data['<value>']['<word>']);
        }

        if(isset($_data['<extention>']) && is_array($_data['<extention>']))
                $this->n_extention($_data['<extention>']);
    }

    function n_word($_data)
    {
        if(isset($_data['<function>']) && is_array($_data['<function>']))
        {
            $this->n_function($_data); //sent with function name
        }
        else if(isset($_data['w']))
        {
            $t = current($_data['w']);
            if(is_numeric($t)) $this->rez .= $t;
            else $this->rez .= "'".$t."'";
        }
    }

    function n_parameters($_data)
    {
        if(isset($_data[','])) $this->rez .= ',';

        /**/
        if(isset($_data['e'])) $this->t_e($_data['e']);


        if(isset($_data['<parameters>']) && is_array($_data['<parameters>']))
                $this->n_parameters($_data['<parameters>']);
    }

    function n_function($_data)
    {
        $fname = current($_data['w']);
        $_data = $_data['<function>'];

        if(isset($_data['(']))
        {
            if(isset($_data['<parameters>']) && is_array($_data['<parameters>']))
            {
                $save_rez = $this->rez;
                if($save_rez != '') $this->rez = ',';
                $this->n_parameters($_data['<parameters>']);
                $ins = $this->rez;
                $this->rez = $fname.'('.$save_rez.$ins.')';
            }
            else $this->rez = $fname.'('.$this->rez.')';
        }
        else if(isset($_data[':']))
        {
            $this->rez.=$fname.'::';
            $this->n_static($_data['<static>']);
        }

        if(isset($_data['<extention>']) && is_array($_data['<extention>']))
        {
            $this->n_extention($_data['<extention>']);
        }
    }

    function n_variable($_data)//done
    {
        if(isset($_data['$']))
                $this->rez .= "\$gekkon->data['".current($_data['w'])."']";
        if(isset($_data['@']))
        {
            $this->rez .= '$'.current($_data['w']);
        }
    }

    function n_extention($_data)//done
    {
        if(isset($_data['<value>']['<word>']['<function>']) && is_array($_data['<value>']['<word>']['<function>']) && !isset($_data['<value>']['<word>']['<function>']['<static>']))
        {
            $this->n_function($_data['<value>']['<word>']);
        }
        else if(isset($_data['.']))
        {
            $save_rez = $this->rez;
            $this->rez = '';
            $this->n_argument($_data['<value>']);
            $ins = $this->rez;
            $this->rez = $save_rez.'['.$ins.']';
        }

        if(isset($_data['<objectmember>']) && is_array($_data['<objectmember>']))
                $this->n_objectmember($_data['<objectmember>']);

        if(isset($_data['<extention>']) && is_array($_data['<extention>']))
                $this->n_extention($_data['<extention>']);
    }

    function n_objectmember($_data)//done
    {
        $this->rez.='->';
        if(isset($_data['<word>'])) $this->n_object_word($_data['<word>']);
    }

    function n_object_word($_data)
    {
        if(isset($_data['<function>']) && is_array($_data['<function>']))
        {
            $this->n_object_function($_data); //sent with function name
        }
        else if(isset($_data['w'])) $this->rez .= current($_data['w']);
    }

    function n_object_function($_data)
    {
        $mname = current($_data['w']);
        $_data = $_data['<function>'];
        if(isset($_data['<parameters>']) && is_array($_data['<parameters>']))
        {
            $save_rez = $this->rez;
            $this->rez = '';
            $this->n_parameters($_data['<parameters>']);
            $ins = $this->rez;
            $this->rez = $save_rez.$mname.'('.$ins.')';
        }
        else $this->rez .= $mname.'()';

        if(isset($_data['<extention>']) && is_array($_data['<extention>']))
                $this->n_extention($_data['<extention>']);
    }

    function n_static($_data)
    {
        if(isset($_data['$']))
        {
            $this->rez .= '$'.current($_data['w']);
        }
        else
        {
            $nname = current($_data['w']);
            if(isset($_data['<parameters>']) && is_array($_data['<parameters>']))
            {
                $this->rez .= $nname;
                $save_rez = $this->rez;
                $this->rez = '';
                $this->n_parameters($_data['<parameters>']);
                $ins = $this->rez;
                $this->rez = $save_rez.'('.$ins.')';
            }
            else
            {
                $this->rez .= $nname.'()';
            }
        }
    }

}

//end of class ----------------------------------------------------
