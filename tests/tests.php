<?php

require_once "../gekkon/gekkon.php";
require_once "test_case.php";

//error_reporting(0);

class Test extends TestCase {
    protected $gekkon;

    function setUp()
    {
        $this->gekkon = new Gekkon(dirname(__file__).'/tpl/',
                                   dirname(__file__).'/tpl/tpl_bin/');
    }

    function tearDown()
    {
        $this->gekkon = NULL;
    }

    function test_var()
    {
        $this->gekkon->assign('var', '1');
        $output = $this->gekkon->get_display('test_var.tpl');
        $this->assertEquals("1", trim($output));
    }

    function test_array()
    {
        $this->gekkon->assign('var', array(1, 2));
        $output = $this->gekkon->get_display('test_array.tpl');
        $this->assertEquals("1", trim($output));
    }

    function test_array_sub()
    {
        $this->gekkon->assign('var', array(array(1, 2), array(1, 2)));
        $output = $this->gekkon->get_display("test_array_sub.tpl");
        $this->assertEquals("2", trim($output));
    }

    function test_hash()
    {
        $this->gekkon->assign('var', array('key' => "true"));
        $output = $this->gekkon->get_display("test_hash.tpl");
        $this->assertEquals("true", trim($output));
    }

    function test_hash_var()
    {
        $this->gekkon->assign('key', 'key');
        $this->gekkon->assign('var', array('key' => "true"));
        $output = $this->gekkon->get_display("test_hash_var.tpl");
        $this->assertEquals("true", trim($output));
    }

    function test_hash_sub()
    {
        $this->gekkon->assign('var', array('key' => array("subkey" => "true")));
        $output = $this->gekkon->get_display("test_hash_sub.tpl");
        $this->assertEquals("true", trim($output));
    }

    function test_hash_var_sub()
    {
        $this->gekkon->assign('key', 'key');
        $this->gekkon->assign('subkey', 'subkey');
        $this->gekkon->assign('var', array('key' => array("subkey" => "true")));
        $output = $this->gekkon->get_display("test_hash_var_sub.tpl");
        $this->assertEquals("true", trim($output));
    }

    function test_function()
    {
        $this->gekkon->assign('var', 'test');
        $output = $this->gekkon->get_display("test_function.tpl");
        $this->assertEquals("4", trim($output));
    }

    function test_function_in()
    {
        $this->gekkon->assign('var', 'test');
        $output = $this->gekkon->get_display("test_function_in.tpl");
        $this->assertEquals("2", trim($output));
    }

    function test_method_call()
    {
        eval("class TestMethod {
            public \$name = \"default\";

            function action ()
            {
                return \"true\";
            }
        };");
        $obj = new TestMethod();
        $this->gekkon->assign('obj', $obj);
        $output = $this->gekkon->get_display("test_method_call.tpl");
        $this->assertEquals("true", trim($output));
    }

    function test_property_call()
    {
        eval("class TestProperty {
            public \$name = \"default\";
        }");
        $obj = new TestProperty();
        $this->gekkon->assign('obj', $obj);
        $output = $this->gekkon->get_display("test_property_call.tpl");
        $this->assertEquals("default", trim($output));
    }

    function test_expression()
    {
        $this->gekkon->assign('var1', 2);
        $this->gekkon->assign('var2', 2);
        $output = $this->gekkon->get_display("test_expression.tpl");
        $this->assertEquals("8", trim($output));
    }

    function test_tag_set()
    {
        $output = $this->gekkon->get_display("test_tag_set.tpl");
        $this->assertEquals("1", trim($output));
    }

    function test_tag_if()
    {
        $this->gekkon->assign('var', 1);
        $output = $this->gekkon->get_display("test_tag_if.tpl");
        $this->assertEquals("1", trim($output));
    }

    function test_tag_if_elseif()
    {
        $this->gekkon->assign('var', 2);
        $output = $this->gekkon->get_display("test_tag_if.tpl");
        $this->assertEquals("2", trim($output));
    }

    function test_tag_if_else()
    {
        $this->gekkon->assign('var', false);
        $output = $this->gekkon->get_display("test_tag_if.tpl");
        $this->assertEquals("0", trim($output));
    }

    function test_tag_foreach()
    {
        $this->gekkon->assign('items', array(1, 2, 3));
        $output = $this->gekkon->get_display("test_tag_foreach.tpl");
        $this->assertEquals("123", trim($output));
    }

    function test_tag_foreach_new()
    {
        $this->gekkon->assign('items', array(1, 2, 3));
        $output = $this->gekkon->get_display("test_tag_foreach_new.tpl");
        $this->assertEquals("123", trim($output));
    }

    function test_tag_foreach_empty()
    {
        $this->gekkon->assign('items', array());
        $output = $this->gekkon->get_display("test_tag_foreach.tpl");
        $this->assertEquals("empty", trim($output));
    }

    function test_tag_foreach_empty_new()
    {
        $this->gekkon->assign('items', array());
        $output = $this->gekkon->get_display("test_tag_foreach_new.tpl");
        $this->assertEquals("empty", trim($output));
    }

    function test_tag_foreach_meta()
    {
        $this->gekkon->assign('items', array(1, 2, 3));
        $output = $this->gekkon->get_display("test_tag_foreach_meta.tpl");
        $this->assertEquals("_|1-0-1-3-2||2-1-2-2-1||3-2-3-1-0|_", trim($output));
    }

    function test_tag_foreach_key()
    {
        $this->gekkon->assign('items', array("one" => 1, "two" => 2, "three" => 3));
        $output = $this->gekkon->get_display("test_tag_foreach_key.tpl");
        $this->assertEquals("one=1|two=2|three=3", trim($output));
    }

    function test_tag_foreach_cycle()
    {
        $this->gekkon->assign('items', array("one" => 1, "two" => 2, "three" => 3));
        $this->gekkon->assign('var3', '3');
        $output = $this->gekkon->get_display("test_tag_foreach_cycle.tpl");
        $this->assertEquals("123", trim($output));
    }

    function test_tag_foreach_ifchange()
    {
        $this->gekkon->assign('date_list',
                array(array("day" => 1, "month" => 1), array("day" => 2, "month" => 1),
            array("day" => 3, "month" => 2)));
        $output = $this->gekkon->get_display("test_tag_foreach_ifchange.tpl");
        $this->assertEquals("1=1203=2", trim($output));
    }

    function test_tag_for()
    {
        $output = $this->gekkon->get_display("test_tag_for.tpl");
        $this->assertEquals("0123456789", trim($output));
    }

    function test_tag_for_2()
    {
        $output = $this->gekkon->get_display("test_tag_for_2.tpl");
        $this->assertEquals("0123456789", trim($output));
    }

    function test_tag_include()
    {
        $output = $this->gekkon->get_display("test_tag_include.tpl");
        $this->assertEquals("1", trim($output));
    }

    function test_tag_no_parse()
    {
        $this->gekkon->assign('var', '1');
        $output = $this->gekkon->get_display("test_tag_no_parse.tpl");
        $this->assertEquals("\$var", trim($output));
    }

}

$tests = new Test();
$tests->run();

