<?php
/*
 * @copyrights www.b23prodtm.info - 2017 (all rights reserved to author)
 * @author T. Arimanana
 */

App::uses('AppController', 'Controller');

/**
 * CakePHP ArticleController
 * @author wwwb23prodtminfo <b23prodtm at sourceforge.net>
 */
class ArticleController extends AppController {

        /* functions d'affichage  -----  privées */

        function publierImages() {
                $i = 0; //compteur
                foreach ($_FILES as $image) {
                        /* controle final type de fichier */
                        if (is_uploaded_file($image['tmp_name']) && array_search(substr($image['type'], strlen("image/")), getTypes())) {
                                $dest = WWW_ROOT . DS . "images" . DS . $images['name'];
                                if (move_uploaded_file($image['tmp_name'], $dest)) {
                                        echo "<div class='console'><b>Image " . $image['name'] . " " . $r->lang("actionsucces", "admin") . "</b></div>";
                                } else {
                                        echo "<div class='console'><b>Image " . $image['name'] . " " . $r->lang("actionechec", "admin") . "</b></div>";
                                }
                        }
                }
        }

        /* END fonctions  -----  privées */

        public $validate = array(
            'entete' => array(
                'rule' => 'notBlank'
            ),
            'corps' => array(
                'rule' => 'notBlank'
            )
        );

        public function index($fk_reference_categorie = null) {
                if ($fk_reference_categorie === null) {
                        $this->set('articles', $this->Article->find('all'));
                } else {
                        $this->set('articles', $this->Article->find($fk_reference_categorie));
                }
                $this->set("pIndex", "activites__index");
                $this->render(null, "default-e14");
        }

        /**
         * @param String $p method name (defined in view/admin_content.ctp template)
         */
        public function admin_index($p = null) {
                //debug($this->request->params);
                //debug($GLOBALS);
                $this->set('pIndex', 'admin__activites');
                $this->set('pMethod', $p);
                $this->render(null, "admin_default-e14");
        }

        public function view($id) {
                if (!$id) {
                        return $this->redirect(array("action" => "index"));
                }
                $article = $this->Article->findById($id);
                if (!$article) {
                        throw new NotFoundException(__d('article', 'Invalid article'));
                }
                $this->set("pIndex", "activites__view");
                $this->set('article', $article);
                $this->render(null, "default-e14");
        }

        public function add() {
                if ($this->request->is('post')) {
                        /* ---- reception puis chargement des images ----- */
                        if (filter_input(INPUT_GET, 'images') === 'publie') {
                                $this->publierImages();
                        }
                        $this->Article->create();
                        if ($this->Article->save($this->request->data)) {
                                $this->Flash->success(__d('article', 'Your article has been saved.'));
                                /* seconde etape, charger les images */
                                return $this->redirect(array('action' => 'add', '?' => "images"));
                        }
                        $this->Flash->error(__d('article', 'Unable to add your article.'));
                }
                $this->set('pIndex', 'activites__write');
                $this->render(null, "admin_default-e14");
        }

        public function edit($id = null) {
                if (!$id) {
                        throw new NotFoundException(__d('article', 'Invalid article'));
                }

                $article = $this->Article->findById($id);
                if (!$article) {
                        throw new NotFoundException(__d('article', 'Invalid article'));
                }

                if ($this->request->is(array('post', 'put'))) {
                        $this->Article->id = $id;
                        /* la sauvegarde est assuree par la classe parente AppModel */
                        if ($this->Article->save($this->request->data)) {
                                $this->Flash->success(__d('article', 'Your article has been updated.'));
                                /* seconde etape, charger les images */
                                return $this->redirect(array('action' => 'edit', '?' => "images"));
                        }
                        $this->Flash->error(__d('article', 'Unable to update your article.'));
                }

                if (!$this->request->data) {
                        $this->request->data = $article;
                }
                $this->set('pIndex', 'activites__edit');
                $this->render(null, "admin_default-e14");
        }

        public function delete($id) {
                if ($this->request->is('get')) {
                        throw new MethodNotAllowedException();
                }

                if ($this->Article->delete($id)) {
                        $this->Flash->success(
                                __d('article', 'Article with id : %s was deleted.', h($id))
                        );
                } else {
                        $this->Flash->error(
                                __d('article', "Article with id: %s couldn't be deleted.", h($id))
                        );
                }

                return $this->redirect(array('action' => 'admin_index', '?' => 'images'));
        }

        /**/

        public function licenses() {
                //debug($this->request->params);
                //debug($GLOBALS);
                $this->set("pIndex", "activites__licence");
                $this->render(null, "default-e14");
        }

}
