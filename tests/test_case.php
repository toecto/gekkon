<?php

class TestCase {
    private $called_cls;
    private $stat = array(
        "tests" => 0,
        "asserts" => 0,
        "failures" => 0
    );

    function __construct()
    {
        $this->called_cls = get_called_class();
    }

    function run()
    {
        $test_list = array_filter(get_class_methods($this->called_cls), function ($name)
        {
            return substr($name, 0, 5) === 'test_';
        });
        $this->stat['tests'] = count($test_list);
        $i = 0;
        foreach ($test_list as $method_name)
        {
            $i++;
            echo $i.") ".$this->called_cls.'::'.$method_name;
            $this->$method_name();
            echo "\n";
        }
        echo "\n";
        if ($this->stat['failures'])
        {
            echo "FAILURES!\n";
        } else
        {
            echo "SUCCESS!\n";
        }
        echo "Tests: ".$this->stat['tests'].", Assertions: ".$this->stat['asserts'].", Failures: ".$this->stat['failures'].".";
    }

    function assertEquals($expected, $actual)
    {
        $this->stat['asserts'] += 1;
        if ($expected === $actual)
        {
            return true;
        } else
        {
            echo "\nFailed asserting that \"".$actual."\" matches expected \"".$expected."\".\n";
            $this->stat['failures'] += 1;
            return false;
        }
    }
}