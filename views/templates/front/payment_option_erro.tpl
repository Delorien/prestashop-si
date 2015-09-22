{capture name=path}
	<a href="{$link->getPageLink('order', true, NULL, "step=3")|escape:'html':'UTF-8'}" title="Go back to the Checkout">Finalizar a compra</a><span class="navigation-pipe">{$navigationPipe}</span>Pagamento com Bcash
{/capture}

<div class="payment-message payment-message-warning">
	<span class="text">	Desculpe, meio de pagamento indisponível no momento.</span>
	<a class="b-button-blue" href="{$payment_action_url|escape}">Tentar novamente</a>
</div>