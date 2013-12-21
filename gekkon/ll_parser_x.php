<?php
//version 1.1
class gekkon_ll_parser{

	function __construct($_raw_grammar)
	{
		$this->grammar_init($_raw_grammar);
		$this->fsm_init();
		$this->ff_sets_init();
		$this->fsm_fill();
	}

	function fsm_init()
	{
		$this->_fsm_map=array();
		foreach($this->_grammar as $k=>$rule)
		{
			$this->_fsm_map[$rule['left']]=$this->_terminals;
		}
	}

	function ff_sets_init()
	{
		foreach($this->_n_terminals as $s=>$k)
		{
			$this->find_first_term($s);
		}

		foreach($this->_n_terminals as $s=>$k)
		{
			$this->find_follow_term($s);
		}
	}

	function fsm_fill()
	{
		foreach($this->_firsts as $left=>$term)
		{
			foreach($term as $s=>$t)
			{
				if($s=='EMPTY')
				{
					foreach($this->_follow[$left] as $fs=>$x)
					{
						if($this->_fsm_map[$left][$fs]=='none')
							$this->_fsm_map[$left][$fs]=$t;	
					}
				}
			}
		}
	}

	function grammar_init($_raw_grammar)
	{
		$this->_terminals=array();
		$this->_n_terminals=array();
		$this->_grammar=array();
		foreach($_raw_grammar as $nt=>$rules)
		{
			$rules=explode('|', $rules);
			foreach($rules as $rule)
			{
				if($rule=='')
					$rule='EMPTY';
				
				preg_match_all('/(([A-Z]+)|([^A-Z\s]))/u', $rule, $matched);
				$this->_grammar[]=array('left'=>$nt,'right'=>$matched[1]);
				$not_terminals=$matched[3];

				foreach($not_terminals as $symbol)
					if($symbol!='')$this->_terminals[$symbol]='none';
			}
			$this->_n_terminals[$nt]='none';
		}
		$this->_terminals['STOP']='none';
	}


	function find_first_term($left)
	{
		foreach($this->_grammar as $r_num=>$rule)
		if($rule['left']==$left)
		{
			$t=$rule['right'][0];
			if(isset($this->_n_terminals[$rule['right'][0]]))
			{
				$t=$this->find_first_term($t);
				foreach($t as $i=>$n)
				{
					$this->_fsm_map[$left][$i]=$r_num;
					$this->_firsts[$left][$i]=$r_num;
				}
			}
			else
			{
				$this->_fsm_map[$left][$t]=$r_num;
				$this->_firsts[$left][$t]=$r_num;
			}
		}
		return $this->_firsts[$left];
	}

	function find_follow_term($left)
	{
		if($this->_grammar[0]['left']==$left) $this->_follow[$left]['$']='0';
		foreach($this->_grammar as $r_num=>$rule)
		{
			if(($t=array_search($left,$rule['right']))!==false)
			{
				if(isset($rule['right'][$t+1]))
				{
					$s=$rule['right'][$t+1];
					if($this->isup($s))
					{
						foreach($this->_firsts[$s] as $k=>$tt)
						{
							if($k!='EMPTY')
							{				
								$this->_follow[$left][$k]=$tt;
							}						
							else
							{
								$this->add_left_follows($s,$left);
							}
						}
					}
					else
					{
						$this->_follow[$left][$s]=$r_num;
					}
				}
				else 
				{
					if($rule['left']!=$left)
					{
						$this->add_left_follows($rule['left'],$left);
					}
				}
			}
		}
		return $this->_follow[$left];
	}

	function add_left_follows($from,$to)
	{
		if($from==$to)return;
		if(!isset($this->_follow[$from]))return;
		foreach($this->_follow[$from] as $k=>$tt)
		{
			if($k!='EMPTY')
			{
				$this->_follow[$to][$k]=$tt;
			}
		}
	}

	function isup($char)
	{
		return preg_match('/[A-Z]/', $char);
	}

	function parse($_str)
	{
		r_log('Parce '.$_str,'gekkon_parser');
		$_end_of_text=chr(3);
		if(is_string($_str))
		{
			$this_str=true;
			$_str.=$_end_of_text;
			$cnt=strlen($_str);
		}
		else
		{
			$this_str=false;
			$_str[]=array('STOP'=>'STOP');
			$cnt=count($_str);
		}

		$_stack=array();
		$_tree=new gekkon_tree();
		
		array_push($_stack, array('s'=>'STOP','k'=>0));
		array_push($_stack, 
			array(
			's'=>($t=$this->_grammar[0]['left']),
			'k'=>$_tree->go($_tree->add($t))
			)
		);
		
		$limit=0;
		for($now=0;$now<$cnt;)
		{
			if($limit++>1000){return r_trace('gekkon_ll_parser: parsing limit reached');}
			r_log($_stack,'gekkon_parser');
			$t=array_pop($_stack);
			$st=$t['s'];
			$_tree->go($t['k']);
			if($this_str)
			{
				$char_value=$char_type=$_str[$now];	
				if($char_value==$_end_of_text)
					$char_value=$char_type='STOP';
			}
			else
			{
				$char_value=current($_str[$now]);
				$char_type=key($_str[$now]);
			}

			r_log($st.'=='.$char_type,'gekkon_parser');
			if($st==$char_type)
			{
				if($st=='STOP' && count($_stack)<=1)break;
				$now++;
				$_tree->add($char_value);
			}
			else
			{
				if($this->isup($st))
				{ 
					if(''.$this->_fsm_map[$st][$char_type]!='none')
					{
						$tt=count($x=$this->_grammar[$this->_fsm_map[$st][$char_type]]['right']);
						$save_st=$st;

						for($j=0;$j<$tt;$j++)
						{
							$t=$x[$tt-$j-1];
						    array_push($_stack,array('s'=>$t,'k'=>$_tree->add($t)));
						}
					}
				}
				else if($st!='EMPTY')
				{ 
					$tt='';
					if($this_str)
						$tt=substr($_str,$now);
					else
					{
						foreach($_str as $t)
						{
							if($now--<1)
								$tt.=current($t);
						}
					}	
					return r_error('Cannot parse from '.substr($tt,0,-1),'gekkon_ll_parser');
				}
			}
		}
		if(count($_stack)>1)
		{
			$tt='';
			if($this_str)
				$tt=$_str;
			else
			{
				foreach($_str as $t)
				{
					$tt.=current($t);
				}
			}	
			return r_error('Cannot parse from the end of '.substr($tt,0,-1),'gekkon_ll_parser');
		}
	return $_tree;
	}


} // end of class -----------------------------------------------------


class gekkon_tree{

	function __construct()
	{
		$this->data=array();
		$this->current=-1;
		$this->pk_cnt=-1;
		$this->add('root');
		$this->current=0;
	}

	function up()
	{
		$this->current=$this->data[$this->current]['fk'];
	}

	function add($data)
	{
		$this->pk_cnt++;
		$this->data[$this->pk_cnt]=array('fk'=>$this->current,'data'=>$data);
		return $this->pk_cnt;
	}

	function go($key)
	{
		if($key!==false)
		$this->current=$key;
		return $key;
	}

	function real($l=0)
	{
		$_rez=array();
		foreach($this->data as $k=>$v)
			if($v['fk']==$l && $v['data']!='EMPTY')
			{
				//$_rez[$v['data'].$k]['name']=$v['data'];
				$t=$this->real($k);
				if(count($t)>0)
				{
					$_rez[$v['data']]=$t;
				}
				else
					$_rez[$v['data']]=$v['data'];
			}
		return array_reverse($_rez);
	}

} // end of class -----------------------------------------------------


function gekkon_lexer($_str)
{
	r_log($_str,'gekkon_parser');
	$_rez=array();
	$cnt=strlen($_str);
	$lexems=' .:,;?!()[]{}<>-+/*=&^@#$%~\\';
	$word='';
	for($i=0;$i<$cnt;$i++)
	{
		$c=$_str[$i];
		if(strpos($lexems,$c)!==false)
		{
			if($word!='')
			{
				if(is_numeric($word))
					$_rez[]=array('d'=>$word);
				else
					$_rez[]=array('w'=>$word);
			}
				
			$word='';
			$_rez[]=array($c=>$c);
		}
		else if($c=='"' || $c=="'")
		{
			if($word!='')
			{
				if(is_numeric($word))
					$_rez[]=array('d'=>$word);
				else
					$_rez[]=array('w'=>$word);
			}
				
			$word=$c;
			for($j=$i+1;$j<$cnt;$j++)
			{
				$t=$_str[$j];
				$word.=$t;
				if($t==$c && $_str[$j-1]!='\\')
				{
					$_rez[]=array('s'=>$word);
					$word='';
					$i=$j;
					break;
				}
			}
		} 
		else
		$word.=$c;
	}
			
	if($word!='')
	{
		if(is_numeric($word))
			$_rez[]=array('d'=>$word);
		else
			$_rez[]=array('w'=>$word);
	}
	return $_rez;
}


