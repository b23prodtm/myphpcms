<?php
App::uses('Sqlite', 'Model/Datasource/Database');

class SqliteCms extends Sqlite
{
	public function __construct($config = null, $autoConnect = true) {
		parent::__construct($config, $autoConnect);
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

		public function connect() {
			if (Configure::read('debug') > 1) {
				var_dump($this->config);
			}
			parent::connect();
		}
}
?>
