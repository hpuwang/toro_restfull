1.	网址规则

	例如: http://xxx.com/bscreen/i100/goos/detail
	
	(a) 参数: data={“body”:{},”elapsedTime”=>"","errorCode"=>0,"errorDesc"=>"成功"}
	
	(b) 请求方法：post
	
	(c) bscreen 为应用标识
	
	(d) i100 为版本标识:
	
		(1)、i标识ios，a标识，android，t 表示触屏,
		
		(2)、100是1.0.0这个版本
		
	(e) goods/detail 为指向的接口
	
(f) {"body":{"password":"1111","username":"username"},"appVersionNo":"1.0"}

(g)、返回结果

2.	目录结构介绍

   app-
   controller层代码
   
   cache-文件缓存目录
   
   config-配置文件目录
   
   docs--文档目录
   
   lib-代码库目录
   
   public-项目对外公开目录
   
   vendor-composer 代码目录
   
   view-view层模板目录
   
   /app/hook.php-钩子代码文件
   
   /app/bscreen-电子屏controller层代码，以后可以扩展比如/app/mobile表示手机controller层代码
   
3、config获取方法

   tr::config()->get("app.test");
   
   表示获取config/app.php 里面的key为test的配置的值
   
   如果config有环境配置文件夹，比如config/developer/app.php
   
   程序会优先查看config/developer/app.php是否有key：test
   
   如果有会优先取config/developer/app.php里面的test值
   
   配置数据里面的key不要带"."
   
   tr::config()->appGet("app.test");//应用内获取配置
   
比如获取bscreen里面的配置

tr::config()->appGet("app.test"); 或取

config/bscreen/developer/app.php里面的值

3.	config/route.php 配置介绍

   return array(
    "bscreen"=>array(
        "200"=>array(
            "/user/:number/:string"=>"bscreen_test_index",
            "/v"=>"bscreen_test_index@test2",
        )
    )
);
    bscreen 是应用标识
    
    200 是版本号,对应api 2.0.0
    
    /user/:number/:string 是网址匹配
    
    bscreen_test_index@test2 里面"bscreen_test_index" 是controller 层类对象,test2是它的方法,
    
    类对象与方法之间用“@”隔开，如果没有方法，可以用bscreen_test_index，这样默认会指向它的请求方法
    
    比如，get，post等
    
5、mysql db的使用

   配置app.php 里面的db配置
   ```
	"default"=>array(
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
	)
	```
	(1)、"default"为默认配置,
	
        (2)、dao.php 为table，申明文件，类命名规则为:
        
	     xxxDao，继承tr_db,有2个属性,$tablename,$dbAdapter
	     
	 (3)、dao方法简介
	 
	 ```
	 dao::insert 插入数据
	 dao::delete 删除数据
	 dao::update 更新数据
	 dao::inCrease 字段自增
	 dao::deCrement 字段自减
	 dao::exec  更新，插入，删除sql执行
	 dao::query sql执行
	 dao::selectRow 获取一行数据，sql
	 dao::selectAll 获取所有数据
	 dao::selectAllByField 获取单个字段的所有数据
	 dao::gets 批量获取数据
	 dao::getField 得到单个字段的结果
	 dao::get 得到单个数据
	 dao::getCount 获取数据行数
	 dao::getSql 获取本次运行所有sql
	 ```

