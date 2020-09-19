<!-- vandar Payment Module -->
<p class="payment_module">
    <a href="javascript:$('#vandar_form').submit();" title="{l s='Pay by vandar' mod='vandar'}">
        {l s='Pay by vandar' mod='vandar'}
		{l s='پرداخت با درگاه پرداخت vandar' mod='vandar'}
<br>
</a></p>
<a class="exclusive_large" href="javascript:$('#vandar_form').submit();" title="{l s='Pay by vandar' mod='vandar'}">{l s='پرداخت آنلاین' mod='vandar'}</a>
<form action="modules/vandar/payment.php" method="post" id="vandar_form" class="hidden">
    <input type="hidden" name="orderId" value="{$orderId}" />
</form>
<br><br>
<!-- End of vandar Payment Module-->
