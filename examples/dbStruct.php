<?php
/**
 * 数据库结构导出小工具 v1.0   
 * 用于程序升级过程中，生成参照数据库的数据库表结构的数组文件
 * @file  dbUpdate.php
 * @author fuzuchang <fuhaojunsf@gmail.com>
 * @time  2014-6-11 下午 22:15:09
 */
include '../config.php';
require '../src/DbUtilities.php';
$DbUtilities = new DbUtilities();
$DbUtilities->dbStructureGeneration();
