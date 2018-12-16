<?php
//
// require_once $GLOBALS['include__php_info.class.inc'];
// require_once $GLOBALS['include__php_SQL.class.inc'];
/**
 * Info helper
 *
 * @package       app.View.Helper
 */
 App::uses('Info', 'Cms');
 App::uses('SQL', 'Cms');
class InfoHelper extends AppHelper {

	var $r;
	var $pageCount;
	var $md;
	var $sql;
	public function __construct(View $view, $settings = array("index" => null, "countPerPage" => "5", "md" => true)) {
		parent::__construct($view, $settings);
		$this->r = $settings["index"];
    $this->r->view = $view;
		if(array_key_exists("countPerPage", $settings)) {
				$this->pageCount = $settings["countPerPage"];
		}
		if(array_key_exists("md", $settings)) {
				$this->md = $settings["md"];
		}
		$this->sql = new SQL(SERVEUR, BASE, CLIENT, CLIENT_MDP);
	}


	/**
	 * @return toutes les dimensions dans l'ordre des indices ['w'=>[..],'h'=>[..]]
	 */
	function __getInfoImageSize(Info $ifo) {
			$taille = array("w" => array(), "h" => array());
			for ($i = 0; $i < count($ifo->images); $i++) {
					$img = $ifo->getImage($this->sql, $i);
					$taille["w"][] = $img->getWidth();
					$taille["h"][] = $img->getHeight();
			}
			return $taille;
	}

	/**
	* @return Info instance par defaut
	*/
	private function __defInfo() {
			return new Info($this->sql, $null, $this->r->lang("titre_dsc", "infos"), "staff", $this->r->lang("contenu_dsc", "infos"));
	}
	/**
	 * @return string derniers posts publie en langue locale
	 *                  */
	private function __getInfo($limit, $offset = 0, &$result = array()) {
			$lastinfo = $this->__defInfo();
			if ($this->sql->connect_succes()) {
					$dateSelect = " AND published <= CURDATE() ";
					if($offset < 0) {
						throw new Exception("Wrong select offset !");
					}
					$infos = $this->sql->query("SELECT * FROM info WHERE langue ='" . getPrimaryLanguage() . "'" . $dateSelect . " ORDER BY published DESC LIMIT " . $offset . "," . $limit);
					if ($this->sql->select_succes($infos)) {
							$n = mysqli_num_rows($infos);
							/* recherche ordre inverse du tri */
							while($this->sql->selectLigne($infos, --$n)){
								$result[] = new Info($this->sql, $infos);
							}
							mysqli_free_result($infos);
					} else {
						i_debug("Query error (see more below)..", DEBUG_STANDARD | DEBUG_VERBOSE, 3);
						i_debug($this->sql->afficheErreurs($null, 0), DEBUG_STANDARD | DEBUG_VERBOSE, 2);
					}
			}
			return $lastinfo;
	}

	/**
	 * @param bool $slider active un slider photos
	 * @return string balise de la premiere photo du dernier post  dernier post photo publie en langue locale
	 */
	function getInfoFlashPhoto($slider = false, $n = 2) {
			$result = array();
			/* post par defaut pour rassembler les images*/
			$lastinfo = $this->__defInfo();
			/* recherche de n posts */
			$this->__getInfo($n, 0, $result);
			/* fusion des tableaux images de post */
			foreach($result as $ifo) {
					$lastinfo->images = array_merge($lastinfo->images, $ifo->images);
			}
      $c = $lastinfo->getContenu();
      $c = $this->md ? $this->r->view->Markdown->transform($c) : $c;
      $lastinfo->setContenu($c);
			/* restauration du tableau en HTML */
			$html = $lastinfo->tableauImages($this->sql, TBL_DIV);
			/* recuperer la taille de l'image la plus grande */
			$taille = $this->__getInfoImageSize($lastinfo);
			if (count($lastinfo->images) != 0) {
					if ($slider) {
							i_debug("Slide " . count($lastinfo->images) . " pics.");
							/*
								element cakephp View/Elements/E14/slideshow.jq.ctp
								dimensionnement en fonction de la plus grande image
							 */
							$slider = $this->r->view->element("E14/slideshow.jq", array("photos" => $html,
									"maxWidth" => max($taille["w"]),
									"maxHeight" => max($taille["h"])), array('cache' => array('config' => 'short', 'key' => 'unique value')));
							return $slider;
					}
			} else {
					return $html;
			}
	}

	/**
	 * affiche le post le plus recent avec son contenu ronque a 80 caracteres
	 * @return string dernier post publie en langue locale sans photos
	 * NOTE : le rendu peut etre errone si le contenu revele des balise HTML incompletes apres tronquage
	 */
	function getInfoFlash() {
			return $this->__getInfo(1);
	}

	/**
	* @return int nombre de posts enregistres
	*/
	function getInfoCount() {
			$res = $this->sql->query("SELECT id FROM info");
			if($this->sql->select_succes($res)) {
				return mysqli_num_rows($res);
			} else {
				 return 0;
			}
	}
	/**
	* recherche par page, $settings => pageCount
	* @param int $n numero de page a recuperer
	* @param array $pages variable de retour, les numeros d'offset de pages
	* @return chaine HTML */
	function getInfoFlashN($offset = 0, &$pages = array(), $characters = 160) {
			$html = "";
			$infos = array();
			/* SQL offset d'une page */
			$pages[$this->r->lang("previous", "infos")] = max(0, $offset - $this->pageCount);
			$pages[$this->r->lang("current", "infos")] = $offset;
			$pages[$this->r->lang("next", "infos")] = min($offset + $this->pageCount, $this->getInfoCount() - 1);
			$infos = array();
			/* selection des lignes de donnees */
			$this->__getInfo($this->pageCount, $offset, $infos);
			/* contenu de la page */
			foreach($infos as $lastinfo) {
						$c = $this->clearTags($lastinfo->getContenu(null, true, true, $characters));
						$c = $this->md ? $this->r->view->Markdown->transform($c) : $c;

						$html .= $this->r->view->HTML->para("info_flash", $lastinfo->getLien($lastinfo->getTitre()) . "<p>" . $c . "...</p>\n");
			}
			return $html;
	}

  function clearTags($text, $pattern = "|<[^>]+>(.*)</[^>]+>|U") {
      $sptn = "|<[^>]+>?(.*)|U";
      if(preg_match_all($pattern, $text, $match, PREG_PATTERN_ORDER) > 0) {
          return $this->clearTags(implode($match[1]), $pattern);
      } else if($sptn !== $pattern) {
          return $this->clearTags($text, $sptn);
      } else {
          $text = explode(" ", $text);
          return implode(" ",array_slice($text, 0, count($text) - 1));
      }
  }
}
