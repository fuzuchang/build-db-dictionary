<?php
/**
* 数据字典生成小工具 1.0
* @file  dbdoc.php
* @author fuzuchang <fuhaojunsf@gmail.com>
* @time  2014-6-03 上午11:21:00
*/

include '../config.php';
require '../src/DbUtilities.php';

$DbUtilities = new DbUtilities();
$DbUtilities->dbDocGeneration();
