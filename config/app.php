<?php
return array(
    "test"=>"app1",
    "debug" => true,
    "namespaces" => array('tr','fn','bs'),
    "apps" => array( 'bscreen'),
    "db" => array(
        "auto_time" => false,
        "prefix" => "2tag_",
        "encode" => "",
        "master"=> array(
            "host" => "localhost",
            "user" => "root",
            "port"=>"3306",
            "password" => "root",
            "db_name" => "2tag",
     )
    ),

);
