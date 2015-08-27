{capture name=path}
	<a href="{$link->getPageLink('order', true, NULL, "step=3")|escape:'html':'UTF-8'}" title="Go back to the Checkout">Finalizar a compra</a><span class="navigation-pipe">{$navigationPipe}</span>Pagamento com Bcash
{/capture}

<h2>Selecione a Forma de Pagamento</h2>