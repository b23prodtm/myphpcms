<?php

App::uses('Page', 'Cms');
require APP . $r->r["include__php_module_html.inc"];
require APP . $r->r["include__php_module_DVD.inc"];

$clefPage = "admin__";
$ajouter = $r->sitemap[$clefPage . 'ajouter'];
$modifier = $r->sitemap[$clefPage . 'modifier'];
if ($pMethod === 'ajouter') {
        if (filter_input(INPUT_GET, 'do')) {
                ajouterDVD($webroot . DS, filter_input(INPUT_POST, 'dossier') . DS, filter_input(INPUT_POST, 'nom'), filter_input(INPUT_POST, 'contenu'));
        }
        echo formAjouterDVD($ajouter . "&do", $webroot . DS, $base . DS);
}
if ($pMethod === 'modifier') {
        if (filter_input(INPUT_GET, 'do')) {
                if (filter_input(INPUT_POST, 'action') === "modifier") {
                        if (filter_input(INPUT_GET, 'exec')) {
                                modifierDVD($webroot . DS . $base, filter_input(INPUT_POST, 'nom'), filter_input(INPUT_POST, 'contenu'), filter_input(INPUT_POST, 'fichier_original'));
                        }
                        echo formModifierDVD($modifier . "&do&exec", filter_input(INPUT_POST, 'nom'), $base);
                }
                if (filter_input(INPUT_POST, 'action') === "supprimer") {
                        supprimerDVD($webroot . DS . $base . DS . filter_input(INPUT_POST, 'nom') . ".inc");
                }
        } else {
                echo formModiSuppDVD($modifier . "&do", $webroot . DS, $base . DS);
        }
}
?>
