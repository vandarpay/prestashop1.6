<?php

include(dirname(__FILE__).'/../../config/config.inc.php');
include(dirname(__FILE__).'/../../header.php');
include_once(dirname(__FILE__).'/vandar.php');

	if (!$cookie->isLogged())
		Tools::redirect('authentication.php?back=order.php');

        $currency_default = Currency::getCurrency(intval(Configuration::get('PS_CURRENCY_DEFAULT')));
        $vandar= new vandar(); // Create an object for order validation and language translations

		$order_cart = new Cart(intval($_COOKIE["OrderId"]));

		$PurchaseAmount=number_format(Tools::convertPrice(intval($_COOKIE["PurchaseAmount"]), $currency_default), 0, '', '');
		$OrderAmount=number_format(Tools::convertPrice($order_cart->getOrderTotal(true, 3), $currency_default), 0, '', '');
        $result = $vandar->confirmPayment();
	// We now think that the response is valid, so we can look at the result
	// if we have a valid completed order, validate it
	if ($result->status == 1)
	{
		if($PurchaseAmount==$OrderAmount)
			 $vandar->validateOrder(intval($_COOKIE["OrderId"]), _PS_OS_PAYMENT_,$order_cart->getOrderTotal(true, 3), $vandar->displayName, $vandar->l('Payment Accepted'), array(), $cookie->id_currency);
		else
			 $vandar->validateOrder(intval($_COOKIE["OrderId"]), _PS_OS_PAYMENT_,$PurchaseAmount/10, $vandar->displayName, $vandar->l('Payment Accepted'), array(), $cookie->id_currency);

        setcookie("OrderId", "", -1);
        setcookie("PurchaseAmount","", -1);

        Tools::redirect('history.php');
	} else {
    	$vandar->showMessages($result);
	}

include_once(dirname(__FILE__).'/../../footer.php');

?>