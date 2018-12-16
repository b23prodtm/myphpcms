<?php
App::uses('Page', 'Cms');
if (isset($pIndex) && isset($r)) {
        $page = new Page($r, $pIndex);
        $script = $this->element("Adsense/analytics");
        $page->entete($script);
        $page->ajouterContenu($this->fetch('content'));
        $page->fin();
} else {
   throw new Exception("ERROR: the template " . basename(__FILE__) . " missed the Index and Page instances.");
}
?>
