<?php
class font_index extends tr_controller{
    function get(){
        $data =array("get");
        $this->response($data);
    }

    function put(){
        $rs = $this->getParam();
        $this->response($rs);
    }

    function test(){
        $data =array("test");
        $this->response($data);
    }
}