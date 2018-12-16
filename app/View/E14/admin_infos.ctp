<?php
App::uses(array('SQL', 'Page', 'Formulaire', 'Info', 'Image'), 'Cms');
require APP . $r->r['include__php_constantes.inc'];

echo "<br><center><b>" . $r->lang("admininfos") . "</b></center><br>";
$liste = HTML_listeDebut();
$liste .= HTML_listeElement(HTML_lien($r->sitemap['admin__infos'] . "/ajouter", $r->lang("ajouter", "infos")));
$liste .= HTML_listeElement(HTML_lien($r->sitemap['admin__infos'] . "/modifier", $r->lang("modifier", "infos")));
$liste .= HTML_listeElement(HTML_lien($r->sitemap['admin__infos'] . "/supprimer", $r->lang("supprimer", "infos")));
$liste .= HTML_listeElement(HTML_lien($r->sitemap['admin__infos'] . "/afficher", $r->lang("voirtous", "infos")));
$liste .= HTML_listeElement(HTML_lien($r->sitemap['admin__index'], $r->lang("retouradmin", "infos")));
$liste .= HTML_listeFin();
echo $liste;

/* scripts pour ajouter, modifier puis publier une info dans la table SQL info */
$sql = new SQL(SERVEUR, BASE, CLIENT, CLIENT_MDP);

// publication d'une info dans la base SQL
if (filter_input(INPUT_GET, "publie") && ($pMethod === 'modifier' || $pMethod === 'ajouter')) {

        /* definition de la date */
        $date = date('Y-m-d');
        $lang = filter_input(INPUT_POST, 'i_lang');
        $info = new Info($sql, $result, filter_input(INPUT_POST, 'i_titre' . $lang), filter_input(INPUT_POST, 'i_auteur'), filter_input(INPUT_POST, 'i_categorie'), $date, $lang, filter_input(INPUT_POST, 'i_published'));
        $info->ajouterContenu(filter_input(INPUT_POST, 'i_contenu' . $lang), $lang);
        // verifier l'image champ info_image:_nom:_desc postee et la stocker en base SQL deja
        /* -- stockage de l'image -- */
        if (is_uploaded_file($_FILES['i_image']['tmp_name'])) {
                $image = new Image();
                $image->setFile($_FILES['i_image']['tmp_name']);
                // format d'image
                $image->setMime(filter_input(INPUT_POST, 'i_image_mime'));
                if (!filter_input(INPUT_POST, 'i_image_nom') || strlen(filter_input(INPUT_POST, 'i_image_nom')) < 1) {
                        $image_nom = $_FILES['i_image']['name'];
                } else {
                        $image_nom = filter_input(INPUT_POST, 'i_image_nom');
                }
                $image->setNom($image_nom);
                $image->setDesc(filter_input(INPUT_POST, 'i_image_desc'));
                $image->saveToSQL($sql);
                $info->ajouterImageSQL($image->id);
        }

        if ($pMethod === 'modifier') {
                // ajouter les id des images existantes a l'info
                $post = filter_input_array(INPUT_POST);
                $key = 'i_images';
                if ($post && array_key_exists($key, $post)) {
                        foreach ($post[$key] as $id) {
                                $info->ajouterImageSQL($id);
                        }
                }
                $update = $_SESSION['i_id'];
        } else {
                $update = FALSE;
        } // donner l'id stockÈ en session pour une modification de l'info
        if ($info->publier($sql, $update)) {
                echo "<b><center><br>[ " . $info->getId() . " " . $info->getTitre($lang) . "] " . $r->lang("publiesucces", "infos") . "</center></b>";
                if (filter_input(INPUT_GET, 'modifier')) {
                        // effacer l'id de l'info en session
                        unset($_SESSION['i_id']);
                }
        } else {
                $sql->afficheErreurs();
        }
}
// formulaire ajouter
if ($pMethod === 'ajouter') {
        // ajouter une info, affichage d'un formulaire
        $form = Info::FormAjouter($r->sitemap['admin__infos'] . "/ajouter?publie=1", $sql);
        echo $form;
}

// liste des infos - formulaire modifier
if ($pMethod === 'modifier') {
        if (filter_input(INPUT_POST, 'info_a_modifier')) {
                // placer l'id de l'info en session pour la retrouver dans une requete UPDATE, prochaine etape
                $_SESSION['i_id'] = filter_input(INPUT_POST, 'info_a_modifier');
                //debug("post");
                $dbinfo = $sql->query("SELECT * FROM info WHERE id=" . filter_input(INPUT_POST, 'info_a_modifier'));
                //debug("query");
                $info = new Info($sql, $dbinfo);
                i_debug("info");
                $form = $info->formModifier($sql, $r->sitemap['admin__infos'] . "/modifier?publie=1");
                i_debug("form");
                echo $form;

                // vidage de la mÈmoire
                mysqli_free_result($dbinfo);
        }

        /* affichage de la liste des infos avec des coches radio */
        $listeInfosModif = Info::GetListe($sql, "modifier");
        echo $listeInfosModif;
}

// liste des infos - supprimer
if ($pMethod === 'supprimer') {
        $post = filter_input_array(INPUT_POST);
        $key = "info_a_supprimer";
        if ($post && array_key_exists($key, $post)) {
                $query = postArrayVersQueryID($key, $post);
                $infos_a_supp = $sql->query("SELECT * FROM info WHERE id IN (" . $query . ")");
                for ($i = 0; $i < mysqli_num_rows($infos_a_supp); $i++) {
                        $info = new Info($sql, $infos_a_supp);
                        $info->supprimer($sql);
                }
                mysqli_free_result($infos_a_supp);
        }
        /* affichage de la liste des infos avec des coches checkbox */
        $listeInfosSupp = Info::GetListe($sql, "supprimer");
        echo $listeInfosSupp;
}

if ($pMethod === 'afficher') {
        $post = filter_input_array(INPUT_POST);
        $key = "info_a_afficher";
        if ($post && array_key_exists($key, $post)) {
                $query = postArrayVersQueryID($key, $post);
                $infos = $sql->query("SELECT * FROM info WHERE id IN (" . $query . ") ORDER BY date DESC");
                for ($i = 0; $i < mysqli_num_rows($infos); $i++) {
                        $info = new Info($sql, $infos);
                        $info->setContenu($this->Markdown->transform($info->getContenu()));
                        echo "<br>" . $info->getTableauMultiLang($sql, "id: " . $info->getId() . "<br>");
                }
                mysqli_free_result($infos);
        }
        $listeInfos = Info::GetListe($sql, "afficher", Info::GetGlobalLanguages());
        echo $listeInfos;
}
$sql->close();
?>
