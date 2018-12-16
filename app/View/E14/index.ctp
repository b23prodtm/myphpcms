<?php
if(isset($p) && isset($r) && array_key_exists("e13__" . $p, $r->r)){
        include APP . $r->r["e13__" . $p];
} else if (isset($r) && isset($offset)) {
        require APP . $r->r["include__php_constantes.inc"];
        App::uses('HTMLHelper', 'Helper');
        $sql = new SQL(SERVEUR, BASE, CLIENT, CLIENT_MDP);
        /** test de la connexion */
        if ($sql->connect_succes()) {
                $pageUrl = $r->sitemap[$pIndex];
                /** infos flashs pagination ($p : offset) */
                $pages = array();
                $np = isset($offset) ? $offset : 1;
                echo $this->Info->getInfoFlashN($np, $pages);
                foreach($pages as $text => $offset) {
                        $this->Html->addCrumb($text, $pageUrl . "/" . $offset);
                }
                echo "[ " . $this->Html->getCrumbs(" - ", array('escape' => false)) . " ]";
        } else {
                echo $this->Html->link("MySQL configuration error : " . ERROR_DB_CONNECT, $r->sitemap["e13__index"] . "?debug=1");
        }
} else {
  throw new Exception("ERROR: the template " . basename(__FILE__) . " missed the Index instance and/or \$offset parameter.");
}
