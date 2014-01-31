<?php

//version 4.2
class Gekkon {

    var $version = 4.2;
    var $bin_path;
    var $tpl_path;
    var $gekkon_path;
    var $data;

    function __construct($tpl_path, $bin_path)
    {
        $this->bin_path = $bin_path;
        $this->tpl_path = $tpl_path;
        $this->gekkon_path = dirname(__file__).'/';
        $this->compiler = false;
        $this->display_errors = ini_get('display_errors') == 'on';
        $this->tpl_name = '';
        $this->settings = array();
        $this->loaded = array();
        $this->preloaded = array(); //loadede but not verified
        $this->data = new ArrayObject();
        $this->data['global'] = $this->data;
        $this->tplProvider = new TemplateProviderFS($this->tpl_path);
        $this->binTplProvider = new BinTplProviderFS($this->bin_path);
        $this->cacheProvider = new CacheProviderFS();
    }

    function assign($name, $data)
    {
        $this->data[$name] = $data;
    }

    function register($name, $data)
    {
        $this->data[$name] = $data;
    }

    function display($tpl_name, $scope_data = false)
    {
        if(($binTemplate = $this->template($tpl_name)) !== false)
                $binTemplate->display($this, $this->getScope($scope_data));
    }

    function get_display($tpl_name, $scope_data = false)
    {
        ob_start();
        $this->display($tpl_name, $scope_data);
        return ob_get_clean();
    }

    function template($tpl_name)//refactor
    {
        if(isset($this->loaded[$tpl_name])) return $this->loaded[$tpl_name];

        if(($template = $this->tplProvider->load($tpl_name)) === false)
                return $this->error('Template '.$tpl_name.' cannot be found at '.$tpl_file,
                            'gekkon');

        if(isset($this->preloaded[$tpl_name]))
        {
            $binTpl = $this->preloaded[$tpl_name];
        }
        elseif(($binTplSet = $this->binTplProvider->load($template->association)) !== false)
        {
            $this->preloaded = array_merge($this->preloaded, $binTplSet);
            $binTpl = $this->preloaded[$tpl_name];
        }
        else $binTpl = false;

        if($binTpl === false || !$template->checkBin($binTpl))
        {
            if(($binTplSet = $this->compile($template)) === false)
                    return $this->error('Cannot compile '.$tpl_name, 'gekkon');
            $this->loaded = array_merge($this->loaded, $binTplSet);
            return $this->loaded[$tpl_name];
        }
        $this->loaded[$tpl_name] = $binTpl;
        unset($this->preloaded[$tpl_name]);
        return $binTpl;
    }

    function getScope($data = false)
    {
        if($data !== false && $data !== $this->data)
        {
            $scope = new ArrayObject($data);
            $scope['global'] = $this->data;
            return $scope;
        }

        return $this->data;
    }

    function compile($template)
    {
        if(!$this->compiler)
        {
            include $this->gekkon_path.'settings.php';
            $this->settings += $settings;
            Gekkon::include_dir($this->gekkon_path.'Compiler');
            $this->compiler = new Gekkon\Compiler($this);
        }
        $binTplSetSourse = $this->compiler->compile($template);
        $this->binTplProvider->save($template->association, $binTplSetSourse);
        return $this->binTplProvider->load($template->name);
    }

    function error($msg, $object = false)
    {
        $message = 'Gekkon:';
        if($object !== false) $message .= ' ['.$object.']';
        $message .= ' '.nl2br($msg."\n");

        if($this->display_errors)
                echo '<div class="gekkon_error">'.$message.'</div>';

        error_log(trim(strip_tags($message)));
        return false;
    }

    function include_dir($path)
    {
        $path = rtrim($path, '/');
        if(is_dir($path))
        {
            $dirs = array();
            foreach(scandir($path) as $file)
            {
                if($file[0] != '.')
                {
                    $to_include = $path.'/'.$file;
                    if(is_dir($to_include)) $dirs[] = $to_include;
                    elseif(strtolower(strrchr($to_include, '.')) === '.php')
                    {
                        include_once $to_include;
                    }
                }
            }
            foreach($dirs as $dir) Gekkon::include_dir($dir);
        }
    }

    function clear_dir($path)
    {
        $path = rtrim($path, '/').'/';
        if(is_dir($path))
        {
            foreach(scandir($path) as $file)
            {
                if($file[0] != '.')
                {
                    if(is_dir($path.$file)) Gekkon::clear_dir($path.$file.'/');
                    else unlink($path.$file);
                }
            }
        }
    }

    function create_dir($path)
    {
        $path = rtrim($path, '/');
        if(!is_dir($path))
        {
            Gekkon::create_dir(dirname($path));
            mkdir($path);
        }
    }

}

//end of class -----------------------------------------------------------------

class TemplateProviderFS {

    private $base_dir;

    function __construct($base_dir)
    {
        $this->base_dir = $base_dir;
    }

    function load($name)
    {
        $file = $this->fullPath($name);
        if(is_file($file))
                return new TemplateFS($name, $this->getAssociationName($name),
                    $file);
        return false;
    }

    private function fullPath($name)
    {
        return $this->base_dir.$name;
    }

    function getAssociationName($name)
    {
        if(($t = strrpos($name, '_')) !== false) return substr($name, 0, $t);
        return $name;
    }

    function getAssociated($template)
    {
        $rez = array();
        $dir = dirname($template->name).'/';
        if($dir === './') $dir = '';
        $list = scandir($this->base_dir.$dir);
        foreach($list as $file)
        {
            if($file[0] != '.')
            {
                if(strrchr($file, '.') === '.tpl' && $template->association === $this->getAssociationName($dir.$file))
                        $rez[] = $this->load($dir.$file);
            }
        }
        return $rez;
    }

}

//end of class -----------------------------------------------------------------

class TemplateFS {

    var $file;
    var $name;
    var $association;

    function __construct($name, $association, $file)
    {
        $this->file = $file;
        $this->name = $name;
        $this->association = $association;
    }

    function checkBin($binTemplate)
    {
        return filemtime($this->file) < $binTemplate->info['created'];
    }

    function source()
    {
        return file_get_contents($this->file);
    }

}

//end of class -----------------------------------------------------------------

class CacheProviderFS {

    function cache_dir($tpl_name)
    {
        return dirname($this->full_bin_path($tpl_name)).'/cache/';
    }

    function cache_file($tpl_name, $id = '')
    {
        $name = md5(serialize($id).$tpl_name);
        return $name[0].$name[1].'/'.$name;
    }

    function clear_cache($tpl_name, $id = '')
    {
        if($id !== '')
        {
            $cache_file = $this->cache_dir($tpl_name).
                    $this->cache_file($tpl_name, $id);

            if(is_file($cache_file)) unlink($cache_file);
        }
        else $this->clear_dir(dirname($this->full_bin_path($tpl_name)).'/');
    }

}

//end of class -----------------------------------------------------------------

class BinTplProviderFS {

    var $base_dir;

    function __construct($base)
    {
        $this->base_dir = $base;
    }

    function fullPath($association)
    {
        $bin_name = basename($association);
        $bin_path = $this->base_dir.abs(crc32($association)).'/';
        return $bin_path.$bin_name.'.php';
    }

    function load($association)
    {
        $file = $this->fullPath($association);
        if(is_file($file))
        {
            $bins = include($file);
            $rez = array();
            foreach($bins as $name => $blocks)
            {
                $rez[$name] = new CompiledTemplate($blocks);
            }
            return $rez;
        }
        return false;
    }

    function save($association, $binTplCodeSet)
    {
        Gekkon::create_dir(dirname($file = $this->fullPath($association)));
        file_put_contents($file, '<?php return '.$binTplCodeSet->code());
    }

    function exists($association)
    {
        return is_file($this->fullPath($association));
    }

}

//end of class -----------------------------------------------------------------

class CompiledTemplate {

    var $blocks = array();
    var $info = array();

    function __construct($blocks)
    {
        $this->blocks = $blocks['blocks'];
        $this->info = $blocks['info'];
    }

    function display($gekkon, $_scope = false)
    {
        $this->blocks['main']($gekkon, $gekkon->getScope($_scope));
    }

}

