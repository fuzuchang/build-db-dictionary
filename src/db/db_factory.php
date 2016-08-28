<?php

class keke_db {
	private $_db_provider;
	private $_dbtype = DBTYPE;
	public $_mydb;
	private static $dbs = array ('mysql'=>null,'mysqli'=>null,'odbc'=>null);
	public static function &get_instance($dbtype = DBTYPE) {
		static $obj = null;
		! is_object ( $obj ) && $obj = new keke_db ( $dbtype );
		return $obj;
	}
	function __construct($dbtype = DBTYPE) {
		if (is_object ( self::$dbs [$dbtype] )) {
			$this->_mydb = self::$dbs [$dbtype];
		} else {
			$this->_mydb = $this->create ( $dbtype );
		}
	}

	public function create($dbtype) {
		if (is_object ( self::$dbs [$dbtype] )) {
			return self::$dbs [$dbtype];
		} else {

			switch ($dbtype) {
				case "mysqli" :
					$this->_dbtype = "mysqli";
					if (empty ( self::$dbs [$dbtype] )) {

						require_once (  'mysqli_driver.php' );
						return self::$dbs [$dbtype] = new mysqli_drver ();
					} else {
						return self::$dbs [$dbtype];
					}
					break;
				default :
					$this->_dbtype = $dbtype;
					if (empty ( self::$dbs [$dbtype] )) {
                        require_once (  'mysql_driver.php' );
						return self::$dbs [$dbtype] = new mysql_drver ();
					} else {
						return self::$dbs [$dbtype];
					}

					break;
			}
		}

	}

	/**
	 * 通用插入与替换数据方法
	 *
	 * @param $tablename string
	 * @param $insertsqlarr array
	 * 数组
	 * @param $returnid int
	 * @param $replace boolean
	 * @return int lastinsert_id
	 */
	public function inserttable($tablename, $insertsqlarr, $returnid = 0, $replace = false) {
		return $this->_mydb->insert ( $tablename, $insertsqlarr, $returnid, $replace );
	}
	/**
	 * 通用数据更新方法
	 *
	 * @param $tablename string
	 * @param $setsqlarr array
	 * @param $wheresqlarr array
	 * @return int $affectrows
	 */
	public function updatetable($tablename, $setsqlarr, $wheresqlarr) {
		return $this->_mydb->update ( $tablename, $setsqlarr, $wheresqlarr );
	}
	/**
	 * 执行sql语句
	 *
	 * @param $sql string
	 * @return 返回执行影响的行数
	 */
	public function execute($sql) {
		$res = $this->_mydb->execute ( $sql );
		return $res ? $res : 0;
	}
	public function get_query_num() {
		return $this->_mydb->get_query_num ();
	}
	public function select($fileds = '*', $table, $where = '', $order = '', $group = '', $limit = '', $pk = '') {
		return $this->_mydb->select ( $fileds, $table, $where, $order, $group, $limit, $pk );
	}
	public function getCount($sql, $row = 0, $filed = null) {
		return $this->_mydb->getCount ( $sql, $row, $filed );
	}
	public function get_one($sql) {
		return $this->_mydb->get_one_row ( $sql );
	}
	// 返回查询的结果数组
	public function query($sql, $is_unbuffer = 0) {
		return $this->_mydb->query ( $sql, $is_unbuffer );
	}
	public function __destruct() {
		$this->_mydb->close ();
	}

}
class db_factory {

	private static $db_obj = null;

	public static function init($dbtype =DBTYPE) {
		$db_obj = &keke_db::get_instance ( $dbtype );
		return self::$db_obj = $db_obj;
	}
	public static function execute($sql) {
		self::init ();
		return self::$db_obj->execute ( $sql );

	}
	public static function query($sql,$is_unbuffer = 0) {
        $db = self::init ();
        return $result = $db->query ( $sql, $is_unbuffer );
	}
	public static function inserttable($tablename, $insertsqlarr, $returnid = 1, $replace = false) {
		$db = self::init ();
		$result = $db->inserttable ( $tablename, $insertsqlarr, $returnid, $replace );
		return $result == 0 ? true : $result;
	}
	public static function updatetable($tablename, $setsqlarr, $wheresqlarr) {
		$db = self::init ();
		return $db->updatetable ( $tablename, $setsqlarr, $wheresqlarr );
	}
	public static function create($dbtype = DBTYPE) {
		return self::init ( $dbtype );
	}
	/**
	 * 返回一个一维数组
	 * @param $sql string
	 * @param $cache_time 缓存时间(0表示不缓存)
	 */
	public static function get_one($sql) {
        $db = self::init ();
        return $result =$db->get_one ( $sql);
	}
	/**
	 * 返回指定行的指定字段值，没有没有指定。默认取第一行的第一个字段
	 * @param $sql string
	 * @param $row int 行数
	 * @param $filed string 字段名
	 * @param $cache_time 缓存时间, 0表示不缓存
	 */
	public static function get_count($sql, $row = 0, $filed = null) {
		$db = self::init ();
        return $result = $db->getCount ( $sql, $row, $filed );

	}

	public static function get_table_data($fileds = '*', $table, $where = '', $order = '', $group = '', $limit = '', $pk = '') {
		global $_K;
		$wh = "";
		if(is_array($where)){
			while ( list ( $k, $v ) = each ( $where ) ) {
				$wh .= " 1=1 and " . $k . " = '$v'";
			}
		}
		$wh and $where = $wh;
		$db = self::init ();
        return $result = $db->select ( $fileds, $table, $where, $order, $group, $limit, $pk );

	}
	/**
	  * 检查表是否存在
	  * @example
	  * <code>
	  * </code>
	  * @author fuzuchang <fuhaojunsf@163.com>
	  * @time 2015-6-11上午10:02:12
	  * @param unknown_type $tablename  完整表名
	  * @return boolean
	 */
	public static function checkTableIsExists($tablename){
		$table_exist = db_factory::query("SHOW   TABLES   LIKE   '{$tablename}'");
		if($table_exist[0]){
			return true;
		}
		return false;
	}
}

?>