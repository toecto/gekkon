<?php
require_once "../gekkon/gekkon.php";
require_once "test_case.php";

error_reporting(0);

class Test extends TestCase {
    
    function get_gekkon() {
        return new Gekkon(dirname(__file__), dirname(__file__).'/tpl/tpl_bin/', '/tpl/');
    }

    function get_display(&$gekkon, $tpl_name){
        ob_start();
        $gekkon->display($tpl_name);
        $content = ob_get_contents();
        ob_end_clean();
        return $content;
    }

    function test_var() {
        $gekkon = $this->get_gekkon();
        $gekkon->register('var', '1');
        $output = $this->get_display($gekkon, 'test_var.tpl');
        $this->assertEquals("1", trim($output));
    }

    function test_array() {
        $gekkon = $this->get_gekkon();
        $gekkon->register('var', array(1, 2));
        $output = $this->get_display($gekkon, 'test_array.tpl');
        $this->assertEquals("1", trim($output));
    }

    function test_array_sub() {
        $gekkon = $this->get_gekkon();
        $gekkon->register('var', array(array(1, 2), array(1, 2)));
        $output = $this->get_display($gekkon, "test_array_sub.tpl");
        $this->assertEquals("2", trim($output));
    }

    function test_hash() {
        $gekkon = $this->get_gekkon();
        $gekkon->register('var', array('key'=>"true"));
        $output = $this->get_display($gekkon, "test_hash.tpl");
        $this->assertEquals("true", trim($output));
    }

    function test_hash_var() {
        $gekkon = $this->get_gekkon();
        $gekkon->register('key', 'key');
        $gekkon->register('var', array('key'=>"true"));
        $output = $this->get_display($gekkon, "test_hash_var.tpl");
        $this->assertEquals("true", trim($output));
    }

    function test_hash_sub() {
        $gekkon = $this->get_gekkon();
        $gekkon->register('var', array('key'=> array("subkey"=> "true")));
        $output = $this->get_display($gekkon, "test_hash_sub.tpl");
        $this->assertEquals("true", trim($output));
    }

    function test_hash_var_sub() {
        $gekkon = $this->get_gekkon();
        $gekkon->register('key', 'key');
        $gekkon->register('subkey', 'subkey');
        $gekkon->register('var', array('key'=> array("subkey"=> "true")));
        $output = $this->get_display($gekkon, "test_hash_var_sub.tpl");
        $this->assertEquals("true", trim($output));
    }

    function test_function() {
        $gekkon = $this->get_gekkon();
        $gekkon->register('var', 'test');
        $output = $this->get_display($gekkon, "test_function.tpl");
        $this->assertEquals("4", trim($output));
    }

    function test_function_in() {
        $gekkon = $this->get_gekkon();
        $gekkon->register('var', 'test');
        $output = $this->get_display($gekkon, "test_function_in.tpl");
        $this->assertEquals("2", trim($output));
    }

    function test_method_call() {
        eval("class Mock {
            public \$name = \"default\";

            function action () {
                return \"true\";
            }
        };");
        $obj = new Mock();
        $gekkon = $this->get_gekkon();
        $gekkon->register('obj', $obj);
        $output = $this->get_display($gekkon, "test_method_call.tpl");
        $this->assertEquals("true", trim($output));
    }

    function test_property_call() {
        eval("class Mock {public \$name = \"default\"}");
        $obj = new Mock();
        $gekkon = $this->get_gekkon();
        $gekkon->register('obj', $obj);
        $output = $this->get_display($gekkon, "test_property_call.tpl");
        $this->assertEquals("default", trim($output));
    }

    function test_expression() {
        $gekkon = $this->get_gekkon();
        $gekkon->register('var1', 2);
        $gekkon->register('var2', 2);
        $output = $this->get_display($gekkon, "test_expression.tpl");
        $this->assertEquals("8", trim($output));
    }

    function test_tag_set() {
        $gekkon = $this->get_gekkon();
        $output = $this->get_display($gekkon, "test_tag_set.tpl");
        $this->assertEquals("1", trim($output));
    }

    function test_tag_if() {
        $gekkon = $this->get_gekkon();
        $gekkon->register('var', true);
        $output = $this->get_display($gekkon, "test_tag_if.tpl");
        $this->assertEquals("1", trim($output));
    }

    function test_tag_if_else() {
        $gekkon = $this->get_gekkon();
        $gekkon->register('var', false);
        $output = $this->get_display($gekkon, "test_tag_if.tpl");
        $this->assertEquals("0", trim($output));
    }
}

$tests = new Test();
$tests->run();

