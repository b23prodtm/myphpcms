<?php
/*
* MessageController
* Created by www.b23prodtm.info on 08/11/17.*/

App::uses('AppController', 'Controller');
App::uses('SQL', 'Cms');
/**
 * CakePHP MessageController
 * @author wwwb23prodtminfo <b23prodtm at sourceforge.net>
 */
class MessageController extends AppController {

	public function index($fk_identifiant = null) {
		if ($fk_identifiant === null) {
			$this->set('messages', $this->Message->find('all'));
		} else {
			$this->set('messages', $this->Message->find($fk_identifiant));
		}
		$this->set("pIndex","contactus__index");
		$this->render(null, "default-e14");
	}

	/**
	 * @param String $p method name
	 */
	public function admin_index($p = null) {
		//debug($this->request->params);
		//debug($GLOBALS);
		$this->set('pIndex', 'admin__contactus');
		$this->set('pMethod', $p);
		$this->render(null, "default-e14");
	}

	public function view($id) {
		if (!$id) {
			throw new NotFoundException(__('Invalid message'));
		}

		$message = $this->Message->findById($id);
		if (!$message) {
			throw new NotFoundException(__('Invalid message'));
		}
		$this->set("pIndex","contactus__view");
		$this->set('message', $message);
		$this->render(null, "default-e14");
	}

	public function add($id = null) {
		if (empty($this->request->data)) {
			$this->request->data = $this->Message->findById($id);
		} else {
			/* sauvegarde du message */
			require APP . $r->r['include__php_constantes.inc'];
			/* ajouter dans la base de donnees */
			$sql = new SQL(SERVEUR, BASE, CLIENT, CLIENT_MDP);
		}
		$this->set("pIndex","contactus__write");
		$this->render(null, "default-e14");
	}

}
?>
