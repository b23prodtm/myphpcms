<?php

if (stristr($p, ".php") && isset($r)) {
        include APP . $r->r["blog"] . DS . $p;
} else if(isset($r)){
        include APP . $r->r["blog__index"];
} else {
  // code...
}
