<?php
if ($pIndex === "admin__log_off") :
        ?>
        <p>&nbsp;</p>
        <p>&nbsp;</p>
        <p>&nbsp;</p>
        <p>&nbsp;</p>
        <p>&nbsp;</p>
        <div align="center"><font face="Verdana, Arial, Helvetica, sans-serif"><b><?php echo $r->lang("logoff", "admin"); ?></b></font></div>
        <?php
        session_destroy();
        header('Refresh: 2; url=' . $r->sitemap["e13__index"]);
        exit;
else :
        ?><?php
        ?><br><br><b><center><?php
                                echo $r->lang("bienvenue", "admin");
                                ?></center></b><br>
        <br><?php echo $r->lang("choosemodule", "admin"); ?>:<br><?php
        echo $this->get('i_menu');
endif;
?>
