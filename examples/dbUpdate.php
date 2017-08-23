<?php
/**
 * 数据库结构同步小工具 v1.0
 * *用于程序升级过程中，批量更新表结构
 * @file dbUpdate.php
 * 
 * @author fuzuchang <fuhaojunsf@gmail.com>
 *         @time 2014-6-12 上午 1:18:00
 *           
 */
// 参考表结构数组文件
require '../file/kppw.php';
include '../config.php';
require '../src/DbUtilities.php';

set_time_limit(0);

if ($_POST) {
	$DbUtilities = new DbUtilities ();
	// 升级表结构
	if ($_POST ['upgrade'] == '开始升级') {
		
		$DbUtilities->dbUpdate ( $dbArr );
	}

	// 删除多余的表
	if ($_POST ['droptable'] == 'true') {
		$DbUtilities->removeExcessTable ();
	}
	// 删除多余的字段
	if ($_POST ['dropfield'] == 'true') {
		$DbUtilities->removeExcessTableFields ( $dbArr );
	}


	unset ( $dbArr );
	unset ( $DbUtilities );

	header('Refresh: 3; url=index.php');
	echo '升级成功...<br />';
	echo '页面跳转中...<br />';
}
