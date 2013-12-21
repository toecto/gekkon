<?php


class gekkon_lexer{
    
    var $str;
    var $length;
    var $rez;
    var $current;
    var $error;
    function parse_expression($str) 
    {
        $this->init($str);
        $state='main';
        while($state!='exit')
        {
            $fn='state_'.$state;
            $state=$this->$fn();
        }
        return $this->rez;
    }
    
    function init($str)
    {
        $this->str=$str;
        $this->step=0;
        $this->error='';
        $this->rez=array();
        $this->length=mb_strlen($this->str);
        $this->reccursive_cnt=0;
    }
    
    function state_main()
    {
        $lexems=' .:,;?![]{}<>-+/*=&^#%~';
        $before=$current='';
        while($this->step<$this->length)
        {
            $before=$current;
            $current=$this->str[$this->step];
            if(strpos($lexems, $current)!==false)
            {
                if($current!=' ')
                    $this->save($current, 'l');
            }
            else
            {
                return 'variable';
            }
            $this->step++;
        }
        return 'exit';
    }
        
    function state_variable()
    {
        $before=$current='';
        $buffer='';
        $lexems=' ,;?![]{}<>-+/*=&^#%~';
        while($this->step<$this->length)
        {
            $before=$current;
            $current=$this->str[$this->step];
            if($current=='(')
            {
                $i=$this->findClose($this->step,'(',')');
                if($i===false)   
                {
                    $this->error.='Cannot find the end of the string, '.$current.' - expected; ';
                    return 'exit';
                }
                else
                {
                    $buffer.=substr($this->str, $this->step,$i-$this->step+1);
                    $this->step=$i;
                }
            }
            else if($current=='"' || $current=='\'')
            {
                $i=$this->findClose($this->step,$current,$current);
                if($i===false)   
                {
                    $this->error.='Cannot find the end of the string, '.$current.' - expected; ';
                    return 'exit';
                }
                else
                {
                    $buffer.=substr($this->str, $this->step,$i-$this->step+1);
                    $this->step=$i;
                }
            }
            else if($current=='-')
            {
                if($this->step+1<$this->length && $this->str[$this->step+1]=='>')
                {
                    $this->step++;
                    $buffer.='->';
                }
                else
                {
                    $this->save($buffer, 'v');
                    return 'main';
                }
            }
            else if(strpos($lexems, $current)!==false)
            {
                $this->save($buffer, 'v');
                return 'main';
            }
            else
            {
                $buffer.=$current;
            }
            $this->step++;
        }
        $this->save($buffer, 'v');
        return 'exit';
    }
    
    function save($buffer,$type)
    {
        if($buffer!='')
            $this->rez[]=array('t'=>$type,'v'=>$buffer);
    }
    

    function findClose($start,$opener,$closer,$alt='')
    {
        $this->reccursive_cnt++;
        if($this->reccursive_cnt>500)die('not ok');
        $nested=1;
        $before=$current='';
        for($i=$start+1;$i<$this->length;$i++)
        {
            $before=$current;
            $current=$this->str[$i];
            if($before!='\\')
            {
                if($opener!='"' && $opener!='\'')
                {
                    if($current=='"' || $current=='\'')
                    {
                        $i=$this->findClose($i,$current, $current);
                        if($i===false)   
                        {
                            $this->error.='Cannot find the end of the string, '.$current.' - expected; ';
                            return false;
                        }
                        continue;
                    }
                }

                if($current==$opener && $opener!=$closer)
                    $nested++;
                else if($current==$closer)
                    $nested--;
                else if($nested==1 && $current==$alt)
                    $nested--;
            }
            if($nested==0)
                break;
        }
        if($nested!=0)
            return false;

        return $i;
    }
    
    function parse_variable($str)
    {
        $this->init($str);
	$lexems=' .:,;?!()[]{}<>-+/*=&^@#$%~\\"\'';
	$word='';
        $before=$c='';
	for($this->step=0;$this->step<$this->length;$this->step++)
	{
		$before=$c;
                $c=$this->str[$this->step];
		if(strpos($lexems,$c)!==false)
		{
			if($word!='')
			{
                            if(is_numeric($word))
                                $this->save($word, 'd');
                            else
                                $this->save($word, 'w');
			}
                        
                        if($c=='"' || $c=="'")
                        {
                            
                            $i=$this->findClose($this->step, $c, $c);
                            if($i===false)   
                            {
                                $this->error.='Cannot find the end of the string, '.$c.' - expected; ';
                                return 'exit';
                            }
                            else
                            {
                                $word=substr($this->str, $this->step, $i-$this->step+1);
                                $this->save($word, 's');
                                $word='';
                                $this->step=$i;
                            }
                        } 
                        else if($c=='(')
                        {
                            $this->save('(', '(');
                            $i=$this->findClose($this->step, '(', ')');
                            if($i===false)   
                            {
                                $this->error.='Cannot find the end of the string, '.$c.' - expected; ';
                                return 'exit';
                            }
                            else
                            {
                                do
                                {
                                    $i2=$this->findClose($this->step, '(', ')',',');
                                    $word=substr($this->str, $this->step+1, $i2-$this->step-1);
                                    $this->save($word, 'e');
                                    
                                    $word='';
                                    $this->step=$i2;
                                    if($i2<$i)
                                        $this->save(',', ',');
                                }while($i2<$i);
                                $this->save(')', ')');
                            }
                        }
                        else
                        {
                            $word='';
                            if($c!=' ' || ($c==' ' && $before!=$c))
                                $this->save($c, $c);
                        }
		}
		else 
		$word.=$c;
	}
			
	if($word!='')
	{
            if(is_numeric($word))
                $this->save($word, 'd');
            else
                $this->save($word, 'w');
	}
	return $this->rez;
    }
    
    
}


// end of class gekkon_lexer ---------------------------------------------------

