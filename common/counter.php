<?php
/**
name:counter
version:1.0.2
*/

/**
拦截直接访问 //todo
*/
//defined('INDEX') or die('Invalid visit!');


/**
接收参数
*/
$aid  = isset( $_GET['aid'] )?$_GET['aid']:'';
$class = isset( $_GET['c'] )?$_GET['c']:'';
$mode=isset( $_GET['m'] )?$_GET['m']:'';//是查询or累加

if($aid!=''){
	if($class==''){
		$num=counter($aid,$mode);
	}else{
		$num=counter($aid,$mode,$class);
	}
	
	//print_r();//使用函数计数
	echo "document.write('".$num."');";
}


//连接数据库
if(!isset($conn)){
	//$conn = mysql_connect('192.168.1.100','root','');
	//mysql_select_db('myBlog');
	include('conn.php');
}

//如果不存在则建立表格
$sql = <<< "HereDocs"
CREATE TABLE IF NOT EXISTS `counter` (
  `id` int(20) NOT NULL auto_increment PRIMARY KEY ,
  `aid` varchar(20) default NULL UNIQUE KEY,
  `click_num` int(20) default NULL,
  `class` varchar(20) default 'blog'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
HereDocs;

//执行该sql语句
mysql_query($sql) or die('create Err: '.mysql_error());



/**
name:单页计数器
aim:根据传入的aid，确定aid一共出现的次数。
aid是字符串。记录博客时，可以用字母a+文章编号。
$mode是1的时候累加（默认），是0的时候仅仅是查询点击次数
$class是该计数器的类别，默认为blog类型；
*/	
function counter($aid,$mode=1,$class='blog'){	
	//连接数据库
	if(!isset($conn)){
		//$conn = mysql_connect('192.168.1.100','root','');
		//mysql_select_db('myBlog');
		include('conn.php');
	}
	
	//查询条目是否已有
	$sql="select click_num from counter where aid ='{$aid}'";
	$rows=mysql_query($sql,$conn);
	
	//如果不存在则创建，存在则自增
	if(mysql_affected_rows()<=0){
		//创建条目
		$sql="insert into counter(aid,class,click_num) values('{$aid}','{$class}',1);";
		mysql_query($sql,$conn) or die('insert Err: ' . mysql_error());
		$num=0;
	}else{
		//增加浏览次数
		$row=mysql_fetch_assoc($rows);
		$num=$row['click_num'];
		if($mode==1){
			//echo "document.write('这里是显示浏览次数,可以从数据库读出来');";
			$sql = "Update counter set click_num = click_num+1 where aid ='$aid'";
			mysql_query($sql,$conn) or die('update Err: ' . mysql_error());
		}
	}
	//返回条目浏览次数
	//使用时 echo "document.write('".$num."');";
	return $num;
}


/*
--
-- 表的结构 `counter`
--
CREATE TABLE IF NOT EXISTS `counter` (
  `id` int(20) NOT NULL auto_increment PRIMARY KEY ,
  `aid` varchar(20) default NULL UNIQUE KEY,
  `click_num` int(20) default NULL,
  `class` varchar(20) default 'blog'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
+-----------+---------+------+-----+---------+----------------+
| Field     | Type    | Null | Key | Default | Extra          |
+-----------+---------+------+-----+---------+----------------+
| id        | int(11) | NO   | PRI | NULL    | auto_increment |
| aid       | int(11) | YES  | UNI | NULL    |                |
| click_num | int(11) | YES  |     | NULL    |                |
+-----------+---------+------+-----+---------+----------------+
unique ky 与primary key的区别：
http://zccst.iteye.com/blog/1697043
*/


?>

