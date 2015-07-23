<?php
class bscreen_test_index extends tr_controller{
    function get($id,$str){
        d($this->getParam());
        echo $id.$str;
    }

    function post(){
        echo "post";
    }

    function put(){
        print_r(tr::config()->get("app.test"));exit;
        echo "put";
    }

    function test1(){
        echo "test1";
    }

    function test2(){
        echo "test2";
    }

    function test3(){
        echo "test3";
    }

    function test4(){
        echo "test4";
    }

    function mobile(){
//        echo "mobile";


    }
}