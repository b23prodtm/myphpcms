<?php

if (stristr($p, ".php")) {
        include APP . $r->r["shop"] . DS . $p;
} else {
        include APP . $r->r["shop__index"];
}
