<?php
	if (!$i_sitemap) { require_once '../include/php_index.inc.php'; }
	$r = new Index($this);
	require_once $GLOBALS['include__php_page.class.inc'];
	require_once $GLOBALS['include__php_formulaire.class.inc'];
	require_once $GLOBALS['include__php_SQL.class.inc'];
	require_once $GLOBALS['include__php_constantes.inc'];
	require_once $GLOBALS['include__php_module_html.inc'];
	require_once $GLOBALS['include__php_module_cat.inc'];

	$sql = new SQL(SERVEUR, BASE, CLIENT, CLIENT_MDP);

	$liste = HTML_listeDebut();
	$liste .= HTML_listeElement(HTML_lien($r->sitemap['admin__cat'] . "/ajouter", $r->lang("ajouter","categories")));
	$liste .= HTML_listeElement(HTML_lien($r->sitemap['admin__cat'] . "/supprimer", $r->lang("supprimer","categories")));
	$liste .= HTML_listeElement(HTML_lien($r->sitemap['admin__cat'] . "/modifier", $r->lang("modifier","categories")));
	$liste .= HTML_listeFin();
	echo $liste;

	/* AJOUTER */
	if ($pMethod === 'ajouter') {
		if (filter_input(INPUT_GET, 'publie')) {
			$cp = "";
			if (filter_input(INPUT_POST, 'newcat_parent') > 0) {
				$cp = filter_input(INPUT_POST, 'newcat_parent');
			} else {
				$cp = "0";
			}
			if (!$sql->query("INSERT INTO categorie (nom, parent) VALUES ('" . filter_input(INPUT_POST, 'newcat') . "', '$cp')")) {
				echo $r->lang("cat_sauve_echec", "categories");
				$sql->afficheErreurs();
			} else {
				echo $r->lang("cat_sauve", "categories") . " (".filter_input(INPUT_POST, 'newcat') . ").";
			}
			unset($_POST);
		}

		$f = new Formulaire($r->lang("ajouter", "categories"), $r->sitemap["admin__cat"] . "/ajouter?publie=1");

		$newCat = new ChampTexte("newcat", $r->lang("newcat_lab","categories"), $r->lang("newcat_dsc","categories"), 20, 15);

		$newCat_parent = CAT_getSelect($sql, "newcat_parent", $r->lang("newcat_parent_lab","categories"), $r->lang("newcat_parent_dsc","categories"));

		$effacer = new ChampEffacer($r->lang("effacer", "form"));
		$valider = new ChampValider($r->lang("valider", "form"), $r->lang("ajouter", "categories"));

		$f->ajouterChamp($newCat);
		$f->ajouterChamp($newCat_parent);
		$f->ajouterChamp($effacer);
		$f->ajouterChamp($valider);

		echo $f->fin();
	}
	/* SUPPRIMER */
	if ($pMethod === 'supprimer') {
		if (filter_input(INPUT_GET, 'publie')) {
			$post = filter_input_array(INPUT_POST);
			$key = "cat";
			if ($post && array_key_exists($key, $post)) {
				$catQuery = postArrayVersQueryID($key, $post);

				if ($catQuery != "") {
					$res = $sql->query("SELECT id FROM categorie WHERE parent IN ($catQuery)");
					while ($cat = $sql->LigneSuivante_Array($res)) {
						$catQuery .= $sep . $cat['id'];
						$sep = ',';
					}
					$catQuery .= $sep . $catQuery;
					mysqli_free_result($res);
				}
				// suppression
				$res = $sql->query("DELETE FROM categorie WHERE id IN($catQuery)");
				if ($res) {
					echo $r->lang("cat_effacees", "categories") . " (". $sql->lignesAffectees() . ")";
				}
			}
		}
		$f = new Formulaire($r->lang("supprimer","categories"), $r->sitemap['admin__cat'] . "/supprimer?publie=1", VERTICAL);
		// nombre de champs
		$n = 5;
		for ($i = 0; $i < $n; $i++) {
			$chSelect = CAT_getSelect($sql, "cat[]", $r->lang("cat_select_lab", "categories"), $r->lang("cat_select_dsc", "categories"));
			$f->ajouterChamp($chSelect);
		}
		$valid = new ChampValider($r->lang("valider","form"));
		$e = new ChampEffacer($r->lang("annuler","form"));
		$f->ajouterChamp($valid);
		$f->ajouterChamp($e);
		echo $f->fin();
	}

	/* MODIFIER */
	if ($pMethod === 'modifier') {
		if (filter_input(INPUT_POST, 'new_nom') && filter_input(INPUT_GET, 'publie')) {
			$cat = CAT_getCat(filter_input(INPUT_POST, 'cat_mod'), $sql);
			//les valeurs par defaut correspondent ‡:
			$nom = filter_input(INPUT_POST, 'new_nom');
			if (filter_input(INPUT_POST, 'new_parent') == -1) {
				$parent = "NULL";
			} else {
				$parent = filter_input(INPUT_POST, 'new_parent');
			}
			$id = $cat['id'];
			if ($nom === NULL || !$nom) {
				echo $r->lang("cat_incomplet","categories");
			} elseif (!$sql->query("UPDATE categorie SET nom = '" . $nom . "', parent = " . $parent . " WHERE id = " . $id)) {
				echo $r->lang("cat_sauve_echec","categories");
			} else {
				echo $r->lang("cat_sauve", "categories");
			}
		}
		// formulaire
		$f = new Formulaire($r->lang("modifier", "categories"), $r->sitemap['admin__cat'] . "/modifier?publie=1", VERTICAL);
		$chSelect = CAT_getSelect($sql, "cat_mod", $r->lang("cat_select_lab", "categories"));
		if (filter_input(INPUT_POST, 'cat_mod')) {
			$chSelect = new ChampCache("cat_mod", filter_input(INPUT_POST, 'cat_mod'));
			$cat = CAT_getCat(filter_input(INPUT_POST, 'cat_mod'), $sql); // donnÈes de la categorie
			$f->nom = $r->lang("modifier", "categories") . CAT_getNom($cat, $sql);

			$chNom = new ChampTexte("new_nom", $r->lang("newcat_lab", "categories"),$r->lang("newcat_dsc", "categories"), 10, NULL, $cat["nom"]);
			$f->ajouterChamp($chNom);
			$chParent = CAT_getSelect($sql, "new_parent", $r->lang("newcat_parent_lab", "categories"),$r->lang("newcat_parent_dsc", "categories")." (" . $cat["parent"] . ")", $cat['parent'],$cat["nom"]);
			$f->ajouterChamp($chParent);
		}
		$valid = new ChampValider($r->lang("valider", "form"));
		$e = new ChampEffacer($r->lang("effacer", "form"));
		$f->ajouterChamp($chSelect);
		$f->ajouterChamp($valid);
		$f->ajouterChamp($e);
		echo $f->fin(0);
	}

	$sql->close();
?>