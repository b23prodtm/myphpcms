<?php
App::uses('Postgresql', 'Model/Datasource/Database');

class Sqlite_cms extends Sqlite
{
	public function __construct()
	{
		parent::__construct();
		$this->columns['mediumbinary'] = array('name' => 'bytea');
	}


	/**
	 * Converts database-layer column types to basic types
	 *
	 * @param string $real Real database-layer column type (i.e. "varchar(255)")
	 * @return string Abstract column type (i.e. "string")
	 */
		public function column($real) {
			$s = parent::column($real);
			if($s === "text") {
				$col = strtolower(str_replace(')', '', $real));
				if (strpos($col, '(') !== false) {
					list($col) = explode('(', $col);
				}

				if (in_array($col, array('mebiumblob', 'mediumclob'))) {
					return 'mediumbinary';
				}
			}
			return $s;
}
?>