<?php

/*
 * @copyrights www.b23prodtm.info - 2017 (all rights reserved to author)
 * @author T. Arimanana
 */

App::uses('AppController', 'Controller');

/**
 * CakePHP phpcms E13
 * les indexes de pages 'pIndex' se trouvent dans webroot/.../etc/menu.properties (menu deroulant) et sitemap.properties (plan de site general)
 * @author wwwb23prodtminfo <b23prodtm at sourceforge.net>
 */
class E14Controller extends AppController {

        public $helpers = array();


        public function __construct($request = null, $response = null) {
                parent::__construct($request, $response);
        }
        /** @param string $p page filename.php (optional)
         */
        public function index($p = NULL, $images = NULL) {
                if ($p === "images") {
                        return $this->images($images);
                } else if ($p) {
                        /* parametre de page (toute extension mapage.php => p=mapage)*/
                        $this->set("p", $p);
                }
                //i_debug("p : " . $p);
                $this->set("offset", 1);
                $this->set("pIndex", "e13__index");
                $this->render("index", "default-e14");
        }

        /**
         * @param String $p SITEMAP.PROPERTIES key in [admin]
         */
        public function admin_index($p = NULL) {
                i_debug($this->request->params);
                $this->set("pIndex", "admin__" . $p);
                $this->render("admin_index", "admin_default-e14");
        }

        /**
         * @param int $offset paginate number
         * @param int $count count per page
         * @param int $YYYY 4-digit year
         * @param int $MM 2-digit month
         * @param int $DD 2-digit day
         */
        public function infos($offset = 1, $count = 10, $YYYY = NULL, $MM = NULL, $DD = NULL) {
                i_debug($this->request->params);
                $this->set("pIndex", "infos__index");
                $d = "";
                /** date selection */
                if (isset($YYYY)) {
                        $d = $YYYY;
                        if (isset($MM)) {
                                $d = $d . "-" . $MM;
                                if (isset($DD)) {
                                        $d = $d . "-" . $DD;
                                } else {
                                        $d = $d . "-*";
                                }
                        } else {
                                $d = $d . "-*";
                        }
                        $this->set("d", $d);
                }
                $this->set("count", $count);
                $this->set("offset", $offset);
                $this->render("infos", "default-e14");
        }

        /**
         * @param String $p  method name (defined in view/admin_infos.ctp)
         */
        public function admin_infos($p = NULL) {
                i_debug($this->request->params);
                $this->set("pIndex", "admin__infos");
                $this->set("pMethod", $p);
                $this->render(null, "admin_default-e14");
        }

        /**
         * @param int categorie
         * @param int $offset paginate number
         * @param int $count count per page
         * @param int $YYYY 4-digit year
         * @param int $MM 2-digit month
         * @param int $DD 2-digit day
         */
        public function cat($cat = NULL, $offset = 1, $count = 10, $YYYY = NULL, $MM = NULL, $DD = NULL) {
                i_debug($this->request->params);
                if (isset($cat)) {
                        $this->set("cat", $cat);
                        $this->infos($offset, $count, $YYYY, $MM, $DD);
                } else {
                        $this->set("pIndex", "cat__index");
                        $this->render(null, "default-e14");
                }
        }

        /**
         * @param String $p method name (defined in view/admin_cat.ctp)
         */
        public function admin_cat($p = NULL) {
                i_debug($this->request->params);
                $this->set("pIndex", "admin__cat");
                $this->set("pMethod", $p);
                $this->render(null, "admin_default-e14");
        }

        /** @param string $p page name in etc/\*.php, folder or NULL
          * @param string $subp file name if $p was a folder */
        public function etc($p = NULL, $subp = NULL) {
                //debug($this->request->params);
                if ($p === "locale" || $p === "js") {
                        $this->response->file($this->_r->r["etc"] . DS . $p . DS . $subp);
                        $this->response->send();
                } else if (stristr($p, ".php")) {
                        $this->set("p", $p);
                        $this->render(null, "default-e14");
                } else {
                        $this->response->file($this->_r->r["etc"] . DS . $p);
                        $this->response->send();
                }
        }

        /**
         * @param String $p SITEMAP.PROPERTIES key in [blog]
         */
        public function blog($p = NULL) {
                $this->set("p", $p);
                $this->render(null, "default-e14");
        }

        /**
         * @param String $p SITEMAP.PROPERTIES key in [library]
         */
        public function dvd($webdir = 'data', $file = '') {
                $this->set("pIndex", "library__index");
                $this->set('nom', $file);
                $this->set('base', $webdir);
                //echo $webdir . ' '. $file;
                $this->render(null, "default-e14");
        }

        /**
         * @param String $p method name (defined in view/admin_dvd.ctp)
         */
        public function admin_dvd($p = NULL, $webdir = 'data', $file = '') {
                i_debug($this->request->params);
                $this->set('pIndex', 'admin__library');
                $this->set('pMethod', $p);
                $this->set('nom', $file);
                $this->set('base', $webdir);
                $this->render(null, "admin_default-e14");
        }

        /**
         * @param String $p SITEMAP.PROPERTIES key in [shop]
         */
        public function shop($p = NULL) {
                i_debug($this->request->params);
                $this->set("p", $p);
                $this->render(null, "default-e14");
        }

}
