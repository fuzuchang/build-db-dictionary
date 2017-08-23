<?php
/**
* 数据字典生成小工具 1.0
* @file  dbdoc.php
* @author fuzuchang <fuhaojunsf@gmail.com>
* @time  2014-6-03 上午11:21:00
*/

include '../config.php';
require_once '../vendor/autoload.php';
require '../src/DbUtilities.php';
set_time_limit(0);
$DbUtilities = new DbUtilities();
$DbUtilities->dbDocGeneration();
