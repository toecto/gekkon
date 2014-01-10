<?php
require_once "../gekkon/gekkon.php";
require_once "test_case.php";

//error_reporting(0);

class Test extends TestCase {

    function get_gekkon()
    {
        return new Gekkon(dirname(__file__), dirname(__file__).'/tpl/tpl_bin/', '/tpl/');
    }

    function test_var()
    {
        $gekkon = $this->get_gekkon();
        $gekkon->register('var', '1');
        $output = $gekkon->get_display('test_var.tpl');
        $this->assertEquals("1", trim($output));
    }

    function test_array()
    {
        $gekkon = $this->get_gekkon();
        $gekkon->register('var', array(1, 2));
        $output = $gekkon->get_display('test_array.tpl');
        $this->assertEquals("1", trim($output));
    }

    function test_array_sub()
    {
        $gekkon = $this->get_gekkon();
        $gekkon->register('var', array(array(1, 2), array(1, 2)));
        $output = $gekkon->get_display("test_array_sub.tpl");
        $this->assertEquals("2", trim($output));
    }

    function test_hash()
    {
        $gekkon = $this->get_gekkon();
        $gekkon->register('var', array('key' => "true"));
        $output = $gekkon->get_display("test_hash.tpl");
        $this->assertEquals("true", trim($output));
    }

    function test_hash_var()
    {
        $gekkon = $this->get_gekkon();
        $gekkon->register('key', 'key');
        $gekkon->register('var', array('key' => "true"));
        $output = $gekkon->get_display("test_hash_var.tpl");
        $this->assertEquals("true", trim($output));
    }

    function test_hash_sub()
    {
        $gekkon = $this->get_gekkon();
        $gekkon->register('var', array('key' => array("subkey" => "true")));
        $output = $gekkon->get_display("test_hash_sub.tpl");
        $this->assertEquals("true", trim($output));
    }

    function test_hash_var_sub()
    {
        $gekkon = $this->get_gekkon();
        $gekkon->register('key', 'key');
        $gekkon->register('subkey', 'subkey');
        $gekkon->register('var', array('key' => array("subkey" => "true")));
        $output = $gekkon->get_display("test_hash_var_sub.tpl");
        $this->assertEquals("true", trim($output));
    }

    function test_function()
    {
        $gekkon = $this->get_gekkon();
        $gekkon->register('var', 'test');
        $output = $gekkon->get_display("test_function.tpl");
        $this->assertEquals("4", trim($output));
    }

    function test_function_in()
    {
        $gekkon = $this->get_gekkon();
        $gekkon->register('var', 'test');
        $output = $gekkon->get_display("test_function_in.tpl");
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
        $gekkon = $this->get_gekkon();
        $gekkon->register('obj', $obj);
        $output = $gekkon->get_display("test_method_call.tpl");
        $this->assertEquals("true", trim($output));
    }

    function test_property_call()
    {
        eval("class TestProperty {
            public \$name = \"default\";
        }");
        $obj = new TestProperty();
        $gekkon = $this->get_gekkon();
        $gekkon->register('obj', $obj);
        $output = $gekkon->get_display("test_property_call.tpl");
        $this->assertEquals("default", trim($output));
    }

    function test_expression()
    {
        $gekkon = $this->get_gekkon();
        $gekkon->register('var1', 2);
        $gekkon->register('var2', 2);
        $output = $gekkon->get_display("test_expression.tpl");
        $this->assertEquals("8", trim($output));
    }

    function test_tag_set()
    {
        $gekkon = $this->get_gekkon();
        $output = $gekkon->get_display("test_tag_set.tpl");
        $this->assertEquals("1", trim($output));
    }

    function test_tag_if()
    {
        $gekkon = $this->get_gekkon();
        $gekkon->register('var', 1);
        $output = $gekkon->get_display("test_tag_if.tpl");
        $this->assertEquals("1", trim($output));
    }

    function test_tag_if_elseif()
    {
        $gekkon = $this->get_gekkon();
        $gekkon->register('var', 2);
        $output = $gekkon->get_display("test_tag_if.tpl");
        $this->assertEquals("2", trim($output));
    }

    function test_tag_if_else()
    {
        $gekkon = $this->get_gekkon();
        $gekkon->register('var', false);
        $output = $gekkon->get_display("test_tag_if.tpl");
        $this->assertEquals("0", trim($output));
    }

    function test_tag_foreach()
    {
        $gekkon = $this->get_gekkon();
        $gekkon->register('items', array(1, 2, 3));
        $output = $gekkon->get_display("test_tag_foreach.tpl");
        $this->assertEquals("123", trim($output));
    }

    function test_tag_foreach_new()
    {
        $gekkon = $this->get_gekkon();
        $gekkon->register('items', array(1, 2, 3));
        $output = $gekkon->get_display("test_tag_foreach_new.tpl");
        $this->assertEquals("123", trim($output));
    }

    function test_tag_foreach_empty()
    {
        $gekkon = $this->get_gekkon();
        $gekkon->register('items', array());
        $output = $gekkon->get_display("test_tag_foreach.tpl");
        $this->assertEquals("empty", trim($output));
    }

    function test_tag_foreach_empty_new()
    {
        $gekkon = $this->get_gekkon();
        $gekkon->register('items', array());
        $output = $gekkon->get_display("test_tag_foreach_new.tpl");
        $this->assertEquals("empty", trim($output));
    }

    function test_tag_foreach_meta()
    {
        $gekkon = $this->get_gekkon();
        $gekkon->register('items', array(1, 2, 3));
        $output = $gekkon->get_display("test_tag_foreach_meta.tpl");
        $this->assertEquals("_|1-0-1-3-2||2-1-2-2-1||3-2-3-1-0|_", trim($output));
    }


    function test_tag_foreach_key()
    {
        $gekkon = $this->get_gekkon();
        $gekkon->register('items', array("one" => 1, "two" => 2, "three" => 3));
        $output = $gekkon->get_display("test_tag_foreach_key.tpl");
        $this->assertEquals("one=1|two=2|three=3", trim($output));
    }

    function test_tag_foreach_cycle()
    {
        $gekkon = $this->get_gekkon();
        $gekkon->register('items', array("one" => 1, "two" => 2, "three" => 3));
        $gekkon->register('var3', '3');
        $output = $gekkon->get_display("test_tag_foreach_cycle.tpl");
        $this->assertEquals("123", trim($output));
    }

    function test_tag_foreach_ifchange()
    {
        $gekkon = $this->get_gekkon();
        $gekkon->register('date_list', array(array("day" => 1, "month" => 1), array("day" => 2, "month" => 1), array("day" => 3, "month" => 2)));
        $output = $gekkon->get_display("test_tag_foreach_ifchange.tpl");
        $this->assertEquals("1=123=2", trim($output));
    }

//    function test_tag_include()
//    {
//        $gekkon = $this->get_gekkon();
//        $output = $gekkon->get_display("test_tag_include.tpl");
//        echo $output;
//        $this->assertEquals("1", trim($output));
//    }

    function test_tag_no_parse()
    {
        $gekkon = $this->get_gekkon();
        $gekkon->register('var', '1');
        $output = $gekkon->get_display("test_tag_no_parse.tpl");
        $this->assertEquals("\$var", trim($output));
    }
}

$tests = new Test();
$tests->run();

