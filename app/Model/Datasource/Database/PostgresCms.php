<?php
App::uses('Postgres', 'Model/Datasource/Database');

class PostgresCms extends Postgres
{
	public function __construct($config = null, $autoConnect = true) {
		parent::__construct($config, $autoConnect);
		$this->columns['mediumbinary'] = array('name' => 'bytea');
	}
	public function connect() {
		if (Configure::read('debug') > 1) {
			var_dump($this->config);
		}
		parent::connect();
	}
}
?>
