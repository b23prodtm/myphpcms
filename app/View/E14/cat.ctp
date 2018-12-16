<?php

App::uses('SQL', 'Cms');
require APP . $r->r['include__php_constantes.inc'];
require APP . $r->r['include__php_module_cat.inc'];

$sql = new SQL(SERVEUR, BASE, CLIENT, CLIENT_MDP);

if ($sql->connect_succes()) {
		$list = array();
		$cats = CAT_getArray($sql, " > ");
		foreach ($cats as $nom => $id) {
				$list[] = $this->Html->link(
					$nom,
					'/cat/'.$id.'/1/10'
				, array('class' => 'titre'))."\n<br><br>";
		}
		echo $this->Html->nestedList($list);
}
