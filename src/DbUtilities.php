<?php

require "db/DataBase.php";
require "db/db_factory.php";
require "db/mysqli_driver.php";
require "db/mysql_driver.php";

class DbUtilities {
	private  $_searchPrefix;	//搜索前缀
	private  $_replacePrefix;	//替换前缀
	private  $_arrTotalUseTables;	//参照数据库的所有表名
	private  $_arrTotalUseTableFields; //参照数据库的所有表的字段名
	function __construct($searchPrefix = TABLEPRE,$replacePrefix = '') {
		$this->_searchPrefix = $searchPrefix;
		$this->_replacePrefix = $replacePrefix;
	}
	
	
	function addTotalUseTables($table){
		$this->_arrTotalUseTables[] = $table;
	}
	function addTotalUseTableFields($table,$field){
		$this->_arrTotalUseTableFields[$table][] = $field;
	}
	function getTotalUseTables(){
		return $this->_arrTotalUseTables;
	}
	function getTotalUseTableFields(){
		return $this->_arrTotalUseTableFields;
	}
	/**
	 *
	 * 返回数据库所有的表
	 * @param unknowtype
	 * @return return_type
	 * @example
	 * <code>
	 * </code>
	 * @author fuzuchang <fuhaojunsf@gmail.com>
	 * @time 2014-4-21下午4:30:10
	 */
	function getTablesByDbName(){
		$tables = db_factory::query("SHOW TABLES");
		$arrTables = array();
		foreach ($tables as $k=>$v){
			$arrTables[$k]['table_name'] = $v['Tables_in_'.strtolower(DBNAME)];
		}
		return $arrTables;
	}
	/**
	 *
	 * 返回数据库中某一表的所有字段
	 * @param $TbName 表名
	 * @return return_type
	 * @example
	 * <code>
	 * </code>
	 * @author fuzuchang <fuhaojunsf@gmail.com>
	 * @time 2014-4-21下午4:30:10
	 */
	function getFieldsByTable($TbName){
		$sql = "select * from information_schema.`COLUMNS` where TABLE_SCHEMA='".strtolower(DBNAME)."' and TABLE_NAME='".$TbName."'";
		return db_factory::query($sql);
	}
	/**
	 *
	 * 返回数据库的表结构数组
	 * @return unknown
	 * @example
	 * <code>
	 * </code>
	 * @author fuzuchang <fuhaojunsf@gmail.com>
	 * @time 2014-6-10下午4:30:23
	 */
	function getDbStructure(){
		$arrTables = $this->getTablesByDbName();
		foreach ($arrTables as $k=>$v){
			$arrFields = $this->getFieldsByTable($v['table_name']);
			$arrTmpFields = array();
			foreach ($arrFields as $k2=>$v2){
				$arrTmpFields[$k2]['fields_name'] 		= $v2['COLUMN_NAME'];
				$arrTmpFields[$k2]['fields_type'] 		= $v2['COLUMN_TYPE'];
				$arrTmpFields[$k2]['fields_extra'] 		= $v2['EXTRA'];
				$arrTmpFields[$k2]['fields_default'] 	= $v2['COLUMN_DEFAULT'];
				$arrTmpFields[$k2]['fields_comment'] 	= $v2['COLUMN_COMMENT'];
				$arrTmpFields[$k2]['fields_key'] 		= $v2['COLUMN_KEY'];
			}
			//替换表前缀
			if($this->_searchPrefix != ''){
				$tableName = str_replace($this->_searchPrefix, $this->_replacePrefix, $v['table_name']);
			}else{
				$tableName = $v['table_name'];
			}
			$arrDbStructure[$tableName] =  $arrTmpFields;
		}
		return $arrDbStructure;
	}
	/**
	 * 
	* 生成数据库字段文档
	* @param unknowtype
	* @return return_type
	* @example
	* <code>
	* </code>
	* @author fuzuchang <fuhaojunsf@gmail.com>
	* @time 2014-6-12上午10:55:25
	 */
	function dbDocGeneration(){
		$arrDbStructure = $this->getDbStructure();
		$strTable = '';
		$i = 0;
		foreach ($arrDbStructure as $k =>$v){
			$i += 1;
			$strTable .= '<h4>No.'.$i.'--表名:'.$k.'</h4>';
			$strTable .= '<table>';
			$strTable .= '<tr>
					<th width="150px">字段名</th>
					<th width="200px">字段类型</th>
					<th width="850px">字段含义</th>
				 </tr>';
			$str = '';
			foreach ($v as $k1=>$v1){
				$str.= '<tr><td>'.$v1['fields_name'].'</td><td>'.$v1['fields_type'].'</td><td>'.$v1['fields_comment'].'</td></tr>';
			}
			$strTable .= $str.'</table><br><br>';
		}
		
		$strTable  = '<!DOCTYPE html>
					<html lang="zh-cn">
					<head>
						<meta charset="utf-8" /><title>'.DBNAME.'数据字典</title>
					</head>
					<style type="text/css">
						table{border:1px solid black;border-collapse:collapse;}
						table, td, th{border:1px solid black;}
						tr{width:1200px;}
						td{text-align:left;padding-left:10px;}
							
					</style>
					<body>'.$strTable.'</body></html>';
		//生成html文件
		$filepath = S_ROOT.'/file/'.DBNAME.'.php';
		file_put_contents($filepath,$strTable);
		//生成doc文件
		$filepathdoc = S_ROOT.'/file/'.DBNAME.'.doc';
		file_put_contents($filepathdoc,$strTable);
		//生成excel文件
		$filepathxls = S_ROOT.'/file/'.DBNAME.'.xls';
		file_put_contents($filepathxls,$strTable);
		echo '<a href="../file/'.DBNAME.'.php">查看数据字典</a><br /><br />';
		echo '<a href="../file/'.DBNAME.'.doc">下载数据字典(.doc)</a><br /><br />';
		echo '<a href="../file/'.DBNAME.'.xls">下载数据字典(.xls)</a><br /><br />';
	}
	/**
	 * 
	* 生成数据库结构数组php文件
	* @param unknowtype
	* @return return_type
	* @example
	* <code>
	* </code>
	* @author fuzuchang <fuhaojunsf@gmail.com>
	* @time 2014-6-12上午10:56:27
	 */
	function dbStructureGeneration(){
        $str = '';
		$arrDbStructure = $this->getDbStructure();
		foreach ($arrDbStructure as $k=>$v){
			$vstr = '';
			foreach ($v as $k1=>$v1){
				$v1str = '';
				foreach ($v1 as $k2=>$v2){
					$v1str .= "\r\n\t\t\t\t'".$k2."' =>".'"'.addslashes($v2).'",';
				}
				$v1str = trim($v1str,',');
				$vstr.="\r\n\t\t\t'".$k1."'=>array(".$v1str."\r\n\t\t\t),";
			}
			$vstr = trim($vstr,',');
			$str .= "\r\n\t'".$k."'=> array(".$vstr."\r\n\t),";
		}
		$str = trim($str,',');
		$tmpStr = '<?php '."\r\n".' $dbArr = array('.$str."\r\n );\r\n";
		file_put_contents(S_ROOT.'/file/'.DBNAME.'.php', $tmpStr);
		echo '<a href="file/'.DBNAME.'.php">下载文件</a><br /><br />';
	}
	
	
	/**
	 *
	 * 修改列(修改和新增)
	 * @param $tablename 表名(无表前缀)
	 * @param $arrFields 表的所有列属性
	 * @return return_type
	 * @example
	 * <code>
	 * </code>
	 * @author fuzuchang <fuhaojunsf@gmail.com>
	 * @time 2014-6-11下午3:24:49
	 */
	function alterColumn($tablename,$arrFields){
		foreach ($arrFields as $k=>$v){
			$fieldname 		= trim($v['fields_name']); 		//列名称
			$fieldtype 		= trim($v['fields_type']);		//类型
			$fieldextra 	= trim($v['fields_extra']);		//扩展类型
			$fielddefault 	= trim($v['fields_default']);	//默认值
			$fieldcomment	= trim($v['fields_comment']);	//注释
			$col_info = db_factory::query("show COLUMNS FROM ".TABLEPRE.$tablename." WHERE Field='".$fieldname."' ");
			$col_info = $col_info[0];
			if($col_info){
				if($col_info['Type']!= $fieldtype ){
					//修改列名 类型 默认值 注释
					$strSql = ' ALTER TABLE '.TABLEPRE.$tablename.' CHANGE';
					$fieldname and $strSql .= ' `'.$fieldname.'` ';	//旧列名称
					$fieldname and $strSql .= ' `'.$fieldname.'` ';	//新列名称
					$fieldtype and $strSql .= ' '.$fieldtype.' ';	//字段类型
					$fieldextra!='' and $strSql .= ' '.$fieldextra.' ';	//字段扩展类型
					$fielddefault!='' and $strSql .= " default '".$fielddefault."' ";	//字段默认值
					$fieldcomment!='' and $strSql .= " comment '".$fieldcomment."' ";	//字段注释
					$res = db_factory::execute($strSql);
					if($res){
						$content = DBNAME.'库中'.$tablename.'表中修改'.$fieldname.'成功 --OK';
						$this->updateLog($content);
					}else{
						$content = DBNAME.'库中'.$tablename.'表中修改'.$fieldname.'失败 --fail';
						$this->updateLog($content);
					}
				}
				$this->addTotalUseTableFields($tablename, $fieldname);
			}else{
				//新增列
				$strSql = ' ALTER TABLE '.TABLEPRE.$tablename.' ADD';
				$fieldname and $strSql .= ' `'.$fieldname.'` ';	//列名称
				$fieldtype and $strSql .= ' '.$fieldtype.' ';	//字段类型
				$fieldextra!='' and $strSql .= ' '.$fieldextra.' ';	//字段扩展类型
				$fielddefault!='' and $strSql .= " default '".$fielddefault."' ";	//字段默认值
				$fieldcomment!='' and $strSql .= " comment '".$fieldcomment."' ";	//字段注释
				db_factory::execute($strSql);
				
				$col_info = array();
				$col_info = db_factory::query("show COLUMNS FROM ".TABLEPRE.$tablename." WHERE Field='".$fieldname."' ");
				$col_info = $col_info[0];
				if($col_info){
					$this->addTotalUseTableFields($tablename, $fieldname);
					$content = DBNAME.'库中'.$tablename.'表中新增'.$fieldname.'成功 --OK';
					$this->updateLog($content);
				}else{
					$content = DBNAME.'库中'.$tablename.'表中新增'.$fieldname.'失败 --fail';
					$this->updateLog($content);
				}
			}
		}
		
		$this->addTotalUseTables($tablename);
	}
	/**
	 *
	 * 创建表
	 * @param unknowtype
	 * @return return_type
	 * @example
	 * <code>
	 * </code>
	 * @author fuzuchang <fuhaojunsf@gmail.com>
	 * @time 2014-6-12上午9:34:44
	 */
	function createTable($tablename,$arrFields){
		$fields = '';
		$primaryKey = '';
		foreach ($arrFields as $k=>$v){
			$fieldname 		= trim($v['fields_name']); 		//列名称
			$fieldtype 		= trim($v['fields_type']);		//类型
			$fieldextra 	= trim($v['fields_extra']);		//扩展类型
			$fielddefault 	= trim($v['fields_default']);	//默认值
			$fieldcomment	= trim($v['fields_comment']);	//注释
			$fieldkey		= trim($v['fields_key']);		//主键
				
			$fieldname and $fields .= ' `'.$fieldname.'` ';	//列名称
			$fieldtype and $fields .= ' '.$fieldtype.' ';	//字段类型
			$fieldextra!='' and $fields .= ' '.$fieldextra.' ';	//字段扩展类型
			$fielddefault!='' and $fields .= " default '".$fielddefault."' ";	//字段默认值
			$fieldcomment!='' and $fields .= " comment '".$fieldcomment."' ";	//字段注释
			$fields .=',';
				
			if($fieldkey!=''){
				if($fieldkey == 'PRI'){
					$primaryKey .="PRIMARY KEY (`".$fieldname."`),";
				}
				if($fieldkey == 'UNI'){
					$primaryKey .=" UNIQUE  (`".$fieldname."`),";
				}
				if($fieldkey == 'MUL'){
					$primaryKey .=" KEY `".$fieldname."` (`".$fieldname."`),";
				}
			}
		}
	
		if($primaryKey){
			$strCreateFields = $fields.trim($primaryKey,',');
		}else{
			$strCreateFields = trim($fields,',');
		}
		$strCreate = "CREATE TABLE `".TABLEPRE.$tablename."` (".$strCreateFields.") ENGINE=MyISAM DEFAULT CHARSET=utf8;";
		db_factory::execute($strCreate);
		$table_exist = db_factory::query("SHOW   TABLES   LIKE   '".TABLEPRE.$tablename."'");
		$table_exist = $table_exist[0]?true:false;
		if($table_exist){
			$this->addTotalUseTables($tablename);
			$fieldsList = $this->getFieldsByTable(TABLEPRE.$tablename);
			foreach ($fieldsList as $k=>$v){
				$this->addTotalUseTableFields($tablename, $v['COLUMN_NAME']);
			}
			$content = DBNAME.'库中创建'.$tablename.'成功 --OK';
			$this->updateLog($content);
		}else{
			$content = DBNAME.'库中创建'.$tablename.'失败 --fail';
			$this->updateLog($content);
		}
	}
	/**
	 *
	 * 记录操作行为
	 * @param $content 操作内容
	 * @return return_type
	 * @example
	 * <code>
	 * </code>
	 * @author fuzuchang <fuhaojunsf@gmail.com>
	 * @time 2014-6-11下午3:27:38
	 */
	function updateLog($content){
		$fp=fopen(S_ROOT."/log/dbUpdate.log",'a');
		fwrite($fp,date("Y-m-d H:i:s",time()).' '.$content."\r\n");
		fclose($fp);
	}
	
	/**
	 * 
	* 版本升级数据库结构更新
	* 1.支持创建新表
	* 2.支持增加/修改表中的列
	* @param unknowtype
	* @return return_type
	* @example
	* <code>
	* </code>
	* @author fuzuchang <fuhaojunsf@gmail.com>
	* @time 2014-6-12上午11:41:49
	 */
	function dbUpdate($dbArr){
		if($dbArr){
			foreach ($dbArr as $table=>$fields){
				$table_exist = db_factory::query("SHOW   TABLES   LIKE   '".TABLEPRE.$table."'");
				$table_exist = $table_exist[0]?true:false;
				if($table_exist){
					$this->alterColumn($table, $fields);
				}else{
					$content = DBNAME.'不存在表：'.$table;
					$this->updateLog($content);
					//创建表.....
					$this->createTable($table, $fields);
				}
			}
		}else{
			exit('缺少参考数据库文件....') ;
		}
	}
	
	/**
	 * 
	* 删除当前数据库中(不用的表)冗余的表
	* @param unknowtype
	* @return return_type
	* @example
	* <code>
	* </code>
	* @author fuzuchang <fuhaojunsf@gmail.com>
	* @time 2014-6-12下午5:39:36
	 */
	function removeExcessTable(){
		//实际用的上的表
		$arrTotalTables = $this->getTotalUseTables();
		//当前数据库的所有表
		$currentDbTables = $this->getTablesByDbName();
		foreach ($currentDbTables as $v){
			$tablename = str_replace(TABLEPRE, '', $v['table_name']);
			if(!in_array($tablename, $arrTotalTables)){
				$table_exist = db_factory::query("SHOW   TABLES   LIKE   '".TABLEPRE.$tablename."'");
				$table_exist = $table_exist[0]?true:false;
				if($table_exist){
					db_factory::execute(' DROP TABLE '.TABLEPRE.$tablename);
					$content = '表'.TABLEPRE.$tablename.'被删除';
					$this->updateLog($content);
				}else{
					$table_exist = db_factory::query("SHOW   TABLES   LIKE   '".$tablename."'");
					$table_exist = $table_exist[0]?true:false;
					if($table_exist){
						db_factory::execute(' DROP TABLE '.$tablename);
						$content = '表'.$tablename.'被删除';
						$this->updateLog($content);
					}
				}
			}
		}
	}
	/**
	 * 删除本地数据库中多余的字段
	 * @param unknown $dbArr
	 */
	function removeExcessTableFields($dbArr){
		//参照表数组
		$dbPreferArr = array();
		foreach ($dbArr as $table=>$fields){
			foreach ($fields as $k=>$field){
				$dbPreferArr[$table][]=$field['fields_name'];
			}
		}
		
		//本地数据库数组
		$curaDbArr = $this->getDbStructure();
		$currentDbTablesFields = array();
		foreach ($curaDbArr as $table=>$fields){
			foreach ($fields as $k=>$field){
				$currentDbTablesFields[$table][]=$field['fields_name'];
			}
		}
		$tmpArr = array();
		foreach ($currentDbTablesFields as $tablename =>$fields){
			foreach ($dbPreferArr as $ptablename =>$pfields){
				if($ptablename == $tablename){
					foreach ($fields as $k=>$v){
						if (!in_array($v, $pfields)) {
							$tmpArr[$tablename][] = $v;
						}
					}
				}
			}
		}
		
		foreach ($tmpArr as $tablename =>$fields){
			foreach ($fields as $k=>$field){
				$arrFieldinfo = db_factory::query("SHOW FIELDS FROM ".TABLEPRE.$tablename." WHERE Field='".$field."' ");
				$arrFieldinfo = $arrFieldinfo[0];
				if ($arrFieldinfo) {
					db_factory::execute("ALTER TABLE ".TABLEPRE.$tablename." DROP ".$field);
					$content = TABLEPRE.$tablename.'中'.$field.'被删除';
					$this->updateLog($content);
				}
			}
			
		}
	
		
	}
	
	/**
	 *
	 * 超长超大数据分割文件执行
	 * 地区表
	 * @param unknowtype
	 * @return void|string
	 * @example
	 * <code>
	 * </code>
	 * @author fuzuchang <fuhaojunsf@gmail.com>
	 * @time 2014-5-16下午4:34:21
	 */
	function runQuery($sql) {
        $info='';
		if(!isset($sql) || empty($sql)) return;
		$ret = array();
		$num = 0;
		foreach(explode(";\n", trim($sql)) as $query) {
			$ret[$num] = '';
			$queries = explode("\n", trim($query));
			foreach($queries as $query) {
				$ret[$num] .= (isset($query[0]) && $query[0] == '#') || (isset($query[1]) && isset($query[1]) && $query[0].$query[1] == '--') ? '' : $query;
			}
			$num++;
		}
		unset($sql);
		foreach($ret as $query) {
			$query = trim($query);

			if($query) {
				db_factory::execute($query);
				$info.=$query."\n";
			}
		}
		return $info;
	}
}
