<?php

/**
 * Application level Controller
 *
 * This file is application-wide controller file. You can put all
 * application-wide controller-related methods here.
 *
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link          https://cakephp.org CakePHP(tm) Project
 * @package       app.Controller
 * @since         CakePHP(tm) v 0.2.9
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 */
App::uses('Controller', 'Controller');
App::import('file', 'Index',
  array('file' =>
    WWW_ROOT . DS .'php_cms' . DS . 'e13' . DS . 'include' . DS . 'Index.php'
  )
);
/*TODO: autoloader plugin
App::build(array('Vendor/Cms' => array(WWW_ROOT . DS .'php_cms' . DS . 'e13' . DS . 'include')));
var_dump(App::path('Vendor/Cms'));
App::uses('Index', 'Vendor/Cms');*/

/**
 * Application Controller
 *
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 *
 * @package		app.Controller
 * @link		http://book.cakephp.org/2.0/en/controllers.html#the-app-controller
 */
class AppController extends Controller {

        /**
         * Add in the DebugKit toolbar
         */
        public $components = array('DebugKit.Toolbar',
            'Flash' => array(
                'className' => 'MyFlash'));
        public $helpers = array('Markdown.Markdown', 'Flash');
        var $r;

        public function __construct($request = null, $response = null) {
                parent::__construct($request, $response);

                /* initialise les $GLOBALS et le sitemap */
                $this->r = new Index($this->View, ROOT . DS . 'index.php', false, WWW_ROOT . 'php_cms/');
                /* map pIndex -> URL */
                $this->set("i_sitemap", $this->r->sitemap);
        }

        public function beforeFilter() {
                /* internationalisation (i18n) */
                Configure::write('Config.language', $this->r->getLanguage());
        }

        /**
         * @param String $page SITEMAP.PROPERTIES key in [images]
         */
        public function images($p = NULL) {
                //debug($this->request->params);
                $this->response->file($GLOBALS["images"] . DS . $p);
                $this->response->send();
        }

}
