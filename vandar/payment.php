<?php

include(dirname(__FILE__).'/../../config/config.inc.php');
include(dirname(__FILE__).'/../../header.php');
include_once(dirname(__FILE__).'/vandar.php');

if (!$cookie->isLogged())
    Tools::redirect('authentication.php?back=order.php');

$vandar= new vandar();
echo $vandar->execPayment($cart);

include_once(dirname(__FILE__).'/../../footer.php');
