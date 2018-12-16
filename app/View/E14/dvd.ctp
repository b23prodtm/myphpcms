<?php

require APP . $r->r["include__php_module_DVD.inc"];
$pageUrl = $r->sitemap[$pIndex];
if ($nom && $base) {

        // recuperer les infos du dvd
        $dvd = lireFichier($nom, $GLOBALS[$pIndex] . DS . $base . DS);
        if (i_isdebug()) {
                print_array_r($dvd);
        }
        $liste = get_dir_files($GLOBALS[$pIndex] . DS . $base . DS);

        $tbl = new Tableau(3, 1);
        $tbl->setOptionsArray(array("HTML" => array("class" => "info")));
        $tbl->setContenu_cellule(0, 0, $dvd[0], array(array("class" => "info_titre")));
        $tbl->setContenu_cellule(1, 0, $this->Markdown->transform($dvd[1]));

        /* lier les entrees precedant et suivant le film courant */
        sort($liste);
        $courant = array_search($dvd[0] . OBJET_ext, $liste);
        if (i_isdebug()) {
                print_array_r($liste);
        }
        if ($courant < count($liste) - 1) {
                $suivant = $courant + 1;
        } else {
                $suivant = $courant;
        }
        if ($courant > 0) {
                $precedent = $courant - 1;
        } else {
                $precedent = $courant;
        }
        $this->Html->addCrumb('<<', $pageUrl . "/" . $base . "/" . substr($liste[$precedent], 0, -4));
        $this->Html->addCrumb('O', $pageUrl . "/" . $base . "/");
        $this->Html->addCrumb('>>', $pageUrl . "/" . $base . "/" . substr($liste[$suivant], 0, -4));
        $tbl->setContenu_cellule(2, 0, $this->Html->getCrumbs(" * "), array("class" => 'breadcrumb'));

        echo $tbl->fin();
} else if (isset($base)) {
        echo afficherListeDVD($GLOBALS[$pIndex] . DS, $base . DS, $pageUrl);
} else {
        echo "NO BASE FOLDER";
}
?>
