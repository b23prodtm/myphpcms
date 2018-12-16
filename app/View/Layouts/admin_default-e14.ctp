<?php
App::uses('AdminPage', 'Cms');
if (isset($pIndex) && isset($r)) {
        /** no header sent to allow header() redirections */
        $page = new AdminPage($r, $pIndex, session_id(), array(), $pIndex === "admin__log_off");
        $page->ajouterContenu($this->fetch('content'));
        $page->ajouterContenu($page->menu->ouvrirBonneRubrique($page->getURL())->getRubriquesListe(true));
        echo "<div id='console_js'></div>";
        echo $this->Html->script("media-query.js");
        $page->fin();
} else {
        throw new Exception('No pIndex was set.');
}
?>
