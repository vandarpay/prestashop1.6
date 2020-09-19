<?php

class vandar extends PaymentModule
{

    private $_html = '';
    private $_postErrors = [];

    public function __construct()
    {

        $this->name = 'vandar';
        $this->tab = 'payments_gateways';
        $this->version = '1.0';
        $this->author = 'Vandar.io';

        $this->currencies = true;
        $this->currencies_mode = 'radio';

        parent::__construct();

        $this->page = basename(__FILE__, '.php');
        $this->displayName = $this->l('درگاه پرداخت vandar');
        $this->description = $this->l('ماژول رایگان درگاه پرداخت vandar');
        $this->confirmUninstall = $this->l('Are you sure, you want to delete your details?');

        if (!sizeof(Currency::checkPaymentCurrencies($this->id)))
            $this->warning = $this->l('No currency has been set for this module');

        $config = Configuration::getMultiple(['VANDAR_PIN', '']);
        if (!isset($config['VANDAR_PIN']))
            $this->warning = $this->l('Your Vandar Pin Code must be configured in order to use this module');


        if ($_SERVER['SERVER_NAME'] == 'localhost')
            $this->warning = $this->l('Your are in localhost, vandar Payment can\'t validate order');


    }

    public function install()
    {
        if (!parent::install()
            OR !Configuration::updateValue('VANDAR_PIN', '')
            OR !$this->registerHook('payment')
            OR !$this->registerHook('paymentReturn')
        ) {
            return false;
        } else {
            return true;
        }
    }

    public function uninstall()
    {
        if (!Configuration::deleteByName('VANDAR_PIN')
            OR !parent::uninstall()
        )
            return false;

        return true;
    }

    public function displayFormSettings()
    {
        $this->_html .= '
        <form action="' . $_SERVER['REQUEST_URI'] . '" method="post">
            <fieldset>
                <legend><img src="../img/admin/cog.gif" alt="" class="middle" />' . $this->l('Settings') . '</legend>
                <label>' . $this->l('Vandar API') . '</label>
                <div class="margin-form"><input type="text" size="30" name="VandarPin" value="' . Configuration::get('VANDAR_PIN') . '" /></div>
                <p class="hint clear" style="display: block; width: 501px;">' . $this->l('This hash key should be a secret code for your site.(Please combine an string contain your site name and a date string)') . '</p></div>
                <center><input type="submit" name="submitPay" value="' . $this->l('Update Settings') . '" class="button" /></center>
            </fieldset>
        </form>';
    }

    public function displayConf()
    {
        $this->_html .= '
        <div class="conf confirm">
            <img src="../img/admin/ok.gif" alt="' . $this->l('Confirmation') . '" />
            ' . $this->l('Settings updated') . '
        </div>';
    }

    public function displayErrors()
    {
        foreach ($this->_postErrors AS $err)
            $this->_html .= '<div class="alert error">' . $err . '</div>';
    }

    public function getContent()
    {
        $this->_html = '<h2>' . $this->l('vandar Payment') . '</h2>';
        if (isset($_POST['submitPay'])) {
            if (empty($_POST['VandarPin']))
                $this->_postErrors[] = $this->l('vandar API is required.');
            if (!sizeof($this->_postErrors)) {
                Configuration::updateValue('VANDAR_PIN', $_POST['VandarPin']);
                $this->displayConf();
            } else
                $this->displayErrors();
        }

        $this->displayFormSettings();

        return $this->_html;
    }

    private function displayvandar()
    {
        $this->_html .= 'درگاه پرداخت vandar<b>' . $this->l('This module allows you to accept payments by vandar.') . '</b><br /><br />
        ' . $this->l('Any cart from Shetab Banks are accepted.') . '<br /><br /><br />';

    }

    public function execPayment($cart)
    {
        include('sender.php');
        global $cookie, $smarty;

        include_once("sender.php");
        $api = Configuration::get('VANDAR_PIN');
        $purchase_currency = $this->GetCurrency();
        $OrderDesc = Configuration::get('PS_SHOP_NAME') . $this->l(' Order');
        $amount = number_format($cart->getOrderTotal(true, 3), 0, '', '');
        if($this->GetCurrency()->iso_code == 'IRT' || $this->GetCurrency()->sign == 'تومان'){
            $amount = $amount * 10;
        }
        $OrderId = (Configuration::get('PS_SSL_ENABLED') ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . __PS_BASE_URI__ . 'modules/vandar/validation.php';
        // $OrderId = intval($OrderId) - 1;
        $redirect = (Configuration::get('PS_SSL_ENABLED') ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . __PS_BASE_URI__ . 'modules/vandar/validation.php';

        $result = send($api, $amount, $redirect);
        $result = json_decode($result);

        if ($result->status) {
            setcookie("OrderId", $cart->id, time() + 1800);
            setcookie("PurchaseAmount", $amount, time() + 1800);
            $go = "https://vandar.io/ipg/$result->token";
            Tools::redirectLink($go);
        } else {
            echo $result->errors[0];
            exit();
            die();
        }

        return $this->_html;
    }

    public function confirmPayment()
    {
        include('sender.php');

        $api = Configuration::get('VANDAR_PIN');
        $token = $_GET['token'];
        $result = verify($api, $token);
        $result = json_decode($result);

        return $result;
    }

    public function showMessages($result)
    {
        $this->_postErrors[] = $this->l($result->errorMessage);
        $this->displayErrors();
        echo $this->_html;

        return $result;
    }

    public function hookPayment($params)
    {
        if (!$this->active)
            return;

        return $this->display(__FILE__, 'payment.tpl');
    }

    public function hookPaymentReturn($params)
    {
        echo 1;
        if (!$this->active)
            return;

        return $this->display(__FILE__, 'confirmation.tpl');
    }

}
