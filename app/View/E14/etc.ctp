<?php
if ($p && isset($r)) {
        require APP . $r->r['etc'] . DS . $p;
} else if (isset($r)){
        require APP . $r->r['etc__index'];
} else {
  // code...
}
?>
