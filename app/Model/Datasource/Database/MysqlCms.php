<?php
App::uses('Mysql', 'Model/Datasource/Database');

class MysqlCms extends Mysql
{
	public function __construct($config = null, $autoConnect = true) {
		parent::__construct($config, $autoConnect);
		$this->columns['mediumbinary'] = array('name' => 'mediumblob');
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
				$col = str_replace(')', '', $real);
				$limit = $this->length($real);
				if (strpos($col, '(') !== false) {
					list($col, $vals) = explode('(', $col);
				}
				if (strpos($col, 'mediumblob') !== false || $col === 'mediumbinary') {
					return 'mediumbinary';
				}
			}
			return $s;
		}

		/**
		 * Connects to the database using options in the given configuration array.
		 *
		 * MySQL supports a few additional options that other drivers do not:
		 *
		 * - `unix_socket` Set to the path of the MySQL sock file. Can be used in place
		 *   of host + port.
		 * - `ssl_key` SSL key file for connecting via SSL. Must be combined with `ssl_cert`.
		 * - `ssl_cert` The SSL certificate to use when connecting via SSL. Must be
		 *   combined with `ssl_key`.
		 * - `ssl_ca` The certificate authority for SSL connections.
		 *
		 * @return bool True if the database could be connected, else false
		 * @throws MissingConnectionException
		 */
			public function connect() {
				if (Configure::read('debug') > 1) {
					var_dump($this->config);
				}
				parent::connect();
			}
}
?>
