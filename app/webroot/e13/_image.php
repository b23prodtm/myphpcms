<?php

/* ! 
  @copyrights b23|prod:www.b23prodtm.info - 2004 (all rights reserved to author)
  @author	www.b23prodtm.infoNA
  @date   :brunorakotoarimanana:20050110
  @filename	_image.php
 */

/* !                                             
  @script	image
  @param	id, size. fourni en _GET ou _POST
  #id: id de l'image de la table SQL "image".
  #size: dimensions de l'image: int(3)[%] ou array(int(4)[px],int(4)[px])
  ou
  #file: urlencode($filename)
 */
ob_start();
include("include/php_index.inc.php");
$r = new Index(filter_input(INPUT_SERVER, 'PHP_SELF'));
require($GLOBALS["include__php_image.class.inc"]);
require($GLOBALS["include__php_captcha.class.inc"]);
require($GLOBALS["include__php_SQL.class.inc"]);
ob_clean();
$image = new Image();
$w = filter_input(INPUT_GET, 'w');
$h = filter_input(INPUT_GET, 'h');
$id = filter_input(INPUT_GET, 'id');
$captchaSize = filter_input(INPUT_GET, 'captcha'); // must have been sent through session
if ($id) {
        // connexion SQL
        $sql = new SQL(SERVEUR, BASE, CLIENT, CLIENT_MDP);
        if ($sql->connect_succes()) {
                $image->FromSQL($sql, $id);
                $sql->close();
        } else {
                $image->load_error(ERROR_DB_CONNECT);
        }
} elseif ($captchaSize) {
        $captchaSize = new Captcha($captchaSize);
        $image = $captchaSize->image($_SESSION['captcha']);
} else {
        i_debug("image : missing a valid parameter like w,h id or captcha");
        $image->load_error(ERROR_IMG_PARAM);
}
if ($w != 0 && $h != 0) {
        $image->setSize($w, $h);
}
/* toujours ecrire un fichier cache sur le serveur si possible */
$output = $GLOBALS["images"] . "/db/" . $image->nom;
$image->raw_http_bytes(1, is_writable($output) ? $output : NULL);
?>