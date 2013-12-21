<?php
//version 1.0


class gekkon_compiler extends gekkon{

	function __construct(&$gekkon)
	{
		$this->gekkon_path = $gekkon->gekkon_path;
		$this->tpl_path = $gekkon->tpl_path;
		$this->original_tpl_path = $gekkon->tpl_path;
		$this->tpl_base_path = $gekkon->tpl_base_path;
		$this->bin_path = $gekkon->bin_path;
		
                include_once $gekkon->gekkon_path.'ll_parser.php';
                include_once $gekkon->gekkon_path.'lexer.php';
                
                $this->arg_compiler=new gekkon_arg_compiler(&$this);
                $this->arg_lexer=new gekkon_lexer();
	}

	function compile_exp($str)
	{
                $data=$this->arg_lexer->parse_expression($str);
                $rez='';
                foreach($data as $item)
                {
                    if($item['t']=='l')
                        $rez.=$item['v'];
                    else
                        $rez.=$this->compile_arg($item['v']);
                }

                return $rez;
	}

	function compile_arg($str)
	{
		return $this->arg_compiler->compile($str);
	}

	function compile($tpl_name)
	{
		r_log('gekkon_compiler: compile '.$tpl_name,'gekkon');
		$this->tpl_path=$this->original_tpl_path;
		$tpl_file = $this->full_tpl_path($tpl_name);
		if(!is_file($tpl_file)) return r_error('Cannot find '.$tpl_file,'gekkon_compiller');

		$this->file_list = array();
		$this->bin_file = $this->full_bin_path($tpl_name);
		if(strpos($this->bin_file,'/!') !== false)
			$this->tpl_path = '';

		$this->get_file_list();
		
		$rez_data = "<?php\n";
		$rez_flag = true;

		if(!is_dir($t=dirname($this->bin_file)))
		mkdir($t,0777);

		foreach($this->file_list as $tpl)
			if(($t = $this->compile_file($tpl)) !== false)
				$rez_data .= $t;
			else
				$rez_flag = false;

		file_put_contents($this->bin_file, $rez_data);
		return $rez_flag;
	}

	function compile_file($tpl_name)
	{
		r_log('compile_file '.$tpl_name,'gekkon_compiler');
		$this->tpl_name=$tpl_name;
		return "\nfunction ".$this->fn_name($tpl_name)."(&\$gekkon){\n".
		'//Template '.$this->tpl_path.$tpl_name.";\n".
		$this->compile_str(file_get_contents($this->full_tpl_path($tpl_name))).
		"}\n";
	}
	
	function compile_static($_str)
	{
		if(trim($_str)=='')return '';
		return 'echo '.var_export($_str,true).";\n";
	}

	function compile_str($_str)
	{
		$_tag = array();

		if(!preg_match('/\{(\s*[\@\$a-z_A-Z]+)(\s*[^\}\n]+)?\}/us', $_str, $_tag, PREG_OFFSET_CAPTURE )) return $this->compile_static($_str);
		$open_start = $_tag[0][1];
		$open_len = strlen($_tag[0][0]);
		$_tag = array(
			'name' => $_tag[1][0],
			'raw_args' => isset($_tag[2][0])?$_tag[2][0]:'',
			);

		$_bin_before = $this->compile_static(substr($_str, 0, $open_start));
		$_raw_tag = substr($_str, $open_start, $open_len);
		$_str = substr($_str, $open_start + $open_len);

		$_tag = $this->load_tag($_tag);
		if($_tag['type'] == 0) 
			return $_bin_before.
				//Unknown tag is treated as a comment $this->compile_static($_raw_tag).
				$this->compile_str($_str);

		if(($_tag = $this->parse_end_of_tag($_tag, $_str)) === false) 
			return false;

		if($_tag['close_end'] != 0)
			$_str = substr($_str, $_tag['close_end']);
	
		return $_bin_before.
			$_tag['content_handler']($this,$_tag).
			$this->compile_str($_str);
	}

	function load_tag($_tag)
	{
		$_tag['type'] = 0;
		
		if($_tag['name'][0]=='@' || $_tag['name'][0]=='$')
		{
			$_tag['raw_args']=$_tag['name'].$_tag['raw_args'];
			$_tag['name']='echo';
		}
		if(is_file($tag_file = $this->gekkon_path.'tags/'.$_tag['name'].'.php'))
		{
			include_once $tag_file;
			if(function_exists($_tag['content_handler'] = 'gekkon_tag_'.$_tag['name']))
				$_tag['type'] = 2;
			else
			{
				if(function_exists($_tag['content_handler'] .= '_single'))
				$_tag['type'] = 1;
			}
		}
		return $_tag;
	}

	function parse_end_of_tag($_tag,$_str)
	{
		if($_tag['type'] == 1)
			$_tag['close_end'] = 0;
		else
		{
			$m1 = array();	
			$m2 = array();
			$r = array();
			$now = 0;

			preg_match_all('/{\s*'.$_tag['name'].'\b/Us',$_str,$m1,PREG_OFFSET_CAPTURE);
			preg_match_all('/{\s*\/'.$_tag['name'].'\s*}/Us',$_str,$m2,PREG_OFFSET_CAPTURE);

			foreach($m1[0] as $item)
			{
				if($item[1]>$now)
				{
					$r[$item[1]]['type'] = 1;
					$r[$item[1]]['len'] = strlen($item[0]);
				}
			}

			foreach($m2[0] as $item)
			{
				if($item[1]>$now)
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
			if($f != 0)
			return r_error('Cannot fine close tag for '.$_tag['name'],'gekkon_compiler');

			$_tag['content'] = substr($_str, 0, $pos);
			$_tag['close_start'] = $pos;
			$_tag['close_end'] = $pos + $end_len;
		}
		return $_tag;
	}

	function get_file_list($dir = '')
	{
		$list=scandir($this->tpl_base_path.$this->tpl_path.$dir);
		foreach($list as $file)
		{
			if($file[0] != '.')
			{
				if(is_dir($this->tpl_base_path . $this->tpl_path.$dir.$file))
					$this->get_file_list($dir.$file.'/');
				else if(strrchr($file, '.') == '.tpl' && $this->bin_file == $this->full_bin_path($dir.$file))
					$this->file_list[] = $dir.$file;
			}

		}
	}

	function parse_args($_str)
	{
		$_str = explode('=',$_str);
		$_rez = array();
		$cnt = count($_str)-1;
		$name = trim($_str[0]);
		$i = 1;
		while($i < $cnt)
		{
			$t = strrpos($_str[$i], ' ');
			$val = substr($_str[$i], 0, $t);
			$_rez[$name] = $this->compile_exp($val);
				
			$name = trim(substr($_str[$i],$t));
			$i++;
		}
		if(isset($_str[$cnt]))
		{
			$val = $_str[$cnt];
			$_rez[$name] = $this->compile_exp($val);
		}
		return $_rez;
	}
}

// End Of Class ----------------------------------------------------------------



class gekkon_arg_compiler
{

	function __construct(&$gekkon)
	{
            $this->gekkon=$gekkon;
		
		$this->parser=new gekkon_ll_parser(array(
							'S'=>'VX | IX | sX | (e)X | D ',
							'D'=> 'dL',
							'L'=>'| .NX',
							'I'=>'wF',
							'F'=>'| (E)X | ::ZX',
							'E'=>'| eE | ,eE', 
							'V'=>'$w | @w',
							'X'=>'| .NX | MX',
							'N'=>' V | I | s | (e) | d',
							'M'=>'->I',
							'Z'=>'$w | w(E)',
							));
	}

	function compile($_str)
	{
		$_str=trim($_str);
		if($_str=='')return '';
		if($_str=='@')return '@';

		//$_data = gekkon_lexer($_str);
                $_data=$this->gekkon->arg_lexer->parse_variable($_str);

		//if(!(isset($_data[0]['@']) || isset($_data[0]['$']) || isset($_data[0]['s']) || isset($_data[0]['w']) || isset($_data[0]['d']) ))
		//	return $_str;

		//if(isset($_data[0]['w'])&&!(isset($_data[1]['(']) || isset($_data[1][':'])))
		//	return "'".$_str."'";
		
		//print_r($_data);
		if(($_data = $this->parser->parse($_data))===false)return r_error('Cannot compile '.$_str,'gekkon_arg_compiler');

		//print_r($_data->real());
		$this->rez = '';
		$this->n_S($_data->real());
		return $this->rez;
	}

	function n_S($_data)
	{

		if(isset($_data['V']))
			$this->n_V($_data['V']);
		
		else if(isset($_data['I']))
			$this->n_I($_data['I']);

		else if(isset($_data['s']))
			$this->t_s($_data['s']);
		
		else if(isset($_data['S']))
			$this->n_S($_data['S']);

		else if(isset($_data['D']))
			$this->n_D($_data['D']);

		if(isset($_data['d']))
			$this->rez.=current($_data['d']);
                
		if(isset($_data['e']))
			$this->t_e($_data['e'],true);

		if(isset($_data['X']) && is_array($_data['X']))
			$this->n_X($_data['X']);
	}

	function t_e($_data,$scope=false)
	{
                $save_rez = $this->rez;
                $this->rez = '';
                $rez = $this->gekkon->compile_exp(current($_data));
                if($scope===true)
                    $rez='('.$rez.')';
                $this->rez=$save_rez.$rez;
	}
        
	function t_s($_data)
	{
		$this->rez .= current($_data);
	}

	function n_D($_data)//done
	{
		if(isset($_data['d']))
			$this->rez .= current($_data['d']);
		if(isset($_data['L']) && is_array($_data['L']))
			$this->n_L($_data['L']);
	}

	function n_L($_data)//done
	{
		if(isset($_data['N']['d']))
                {
			$this->rez .= '.'.current($_data['N']['d']);
                }
                else if(isset($_data['N']['I']['F']) && is_array($_data['N']['I']['F']) && !isset($_data['N']['I']['F']['Z']))
		{
			$this->n_F($_data['N']['I']);
		}
                
                if(isset($_data['X']) && is_array($_data['X']))
			$this->n_X($_data['X']);
	}

	function n_I($_data)
	{
		if(isset($_data['F']) && is_array($_data['F']))
		{
			$this->n_F($_data);//sent with function name
		}
		else if(isset($_data['w']))
		{
			$t=current($_data['w']);
			if(is_numeric($t))
				$this->rez .= $t;
			else
				$this->rez .= "'".$t."'";
		}
	}

	function n_E($_data)
	{
		if(isset($_data[',']))
			$this->rez .= ',';

                /**/
                if(isset($_data['e']))
			$this->t_e($_data['e']);

                
		if(isset($_data['E']) && is_array($_data['E']))
			$this->n_E($_data['E']);
	}

	function n_F($_data)
	{

		$fname = current($_data['w']);
		$_data = $_data['F'];

		if(isset($_data['(']))
		{
			if(isset($_data['E']) && is_array($_data['E']))
			{
				$save_rez = $this->rez;
				if($save_rez!='')
				$this->rez = ',';
				$this->n_E($_data['E']);
				$ins=$this->rez;
				$this->rez = $fname.'('.$save_rez.$ins.')';
			}
			else
			$this->rez=$fname.'('.$this->rez.')';
		}
		else if(isset($_data[':']))
		{
			$this->rez.=$fname.'::';
			$this->n_Z($_data['Z']);
		}

		if(isset($_data['X']) && is_array($_data['X']))
		{
			$this->n_X($_data['X']);
		}

	}

	function n_V($_data)//done
	{
		if(isset($_data['$']))
			$this->rez .= "\$gekkon->data['".current($_data['w'])."']";
		if(isset($_data['@']))
		{
			$this->rez .= '$'.current($_data['w']);
		}
	}

	function n_X($_data)//done
	{
		if(isset($_data['N']['I']['F']) && is_array($_data['N']['I']['F']) && !isset($_data['N']['I']['F']['Z']))
		{
			$this->n_F($_data['N']['I']);
		}
		else if (isset($_data['.']) )
		{	
			$save_rez =$this->rez;
			$this->rez ='';
			$this->n_S($_data['N']);
			$ins =$this->rez;
			$this->rez =$save_rez.'['.$ins.']';
		}

		if(isset($_data['M']) && is_array($_data['M']))
			$this->n_M($_data['M']);

		if(isset($_data['X']) && is_array($_data['X']))
			$this->n_X($_data['X']);
	}

	function n_M($_data)//done
	{
		$this->rez.='->';
		if(isset($_data['I']))
			$this->n_I_obj($_data['I']);
		
	}

	function n_I_obj($_data)
	{
		if(isset($_data['F']) && is_array($_data['F']))
		{
			$this->n_F_obj($_data);//sent with function name
		}
		else if(isset($_data['w']))
			$this->rez .= current($_data['w']);
	}

	function n_F_obj($_data)
	{
		$mname = current($_data['w']);
		$_data = $_data['F'];
		if(isset($_data['E']) && is_array($_data['E']))
		{
			$save_rez = $this->rez;
			$this->rez = '';
			$this->n_E($_data['E']);
			$ins = $this->rez;
			$this->rez = $save_rez.$mname.'('.$ins.')';
		}
		else
			$this->rez .= $mname.'()';

		if(isset($_data['X']) && is_array($_data['X']))
			$this->n_X($_data['X']);
	}

	function n_Z($_data)
	{
		if(isset($_data['$']))
		{
			$this->rez .= '$'.current($_data['w']);
		}
		else 
		{
			$nname=current($_data['w']);
			if(isset($_data['E']) && is_array($_data['E']))
			{
				$this->rez .= $nname;
				$save_rez = $this->rez;
				$this->rez = '';
				$this->n_E($_data['E']);
				$ins=$this->rez;
				$this->rez = $save_rez.'('.$ins.')';
			}
			else
			{
				$this->rez .= $nname.'()';
			}
		}
	}

}//end of class ----------------------------------------------------
