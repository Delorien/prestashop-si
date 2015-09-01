{capture name=path}
	<a href="{$link->getPageLink('order', true, NULL, "step=3")|escape:'html':'UTF-8'}" title="Go back to the Checkout">Finalizar a compra</a><span class="navigation-pipe">{$navigationPipe}</span>Pagamento com Bcash
{/capture}

<div class="bcash" id="bcash">
	<form>
		<h3>Selecione a Forma de Pagamento</h3>

		<div class="forma-pagamento">
			<h4>Cartão de Crédito</h4>

			<ul id="credit_list">
				{foreach from=$cards item=card}
				<li>

					<label class="bandeira band-{$card->id}" for="payment-method-{$card->id}">
						<input id="payment-method-{$card->id}" name="payment-method" class="noUniform" type="radio" value="{$card->id}"/>
					 </label>

				</li>
				{/foreach}
			</ul>

			<!-- card form -->
			<div id="card-data" class="b-form-horizontal" style="display:none;">
				<div class="b-form-group">
					<label>Número do cartão</label>
					<input type="text" />
				</div>

				<div class="b-form-group">
					<label>Validade</label>

					<select name="validade_mes_cartao" id="validade_mes_cartao">
						<option value="" selected="selected">Mês</option>
						{foreach from=$mesesVencimento item=mes}
							<option value="{$mes}">{$mes}</option>
						{/foreach}
					</select>

					<select name="validade_ano_cartao" id="validade_ano_cartao">
						<option value="" selected="selected">Ano</option>
						{foreach from=$anosVencimento item=ano}
							<option value="{$ano}">{$ano}</option>
						{/foreach}
					</select>

				</div>

				<div class="b-form-group">
					<label>Nome no cartão</label>
					<input type="text" />
				</div>

				<div class="b-form-group">
					<label>Código de Segurança</label>
					<input type="text" style="width: 60px;" />
				</div>
			</div>
			<!-- card form -->

		</div>


		<div class="forma-pagamento">
			<h4>Transferência Bancária</h4>

			<ul id="credit_list">
				{foreach from=$tefs item=tef}
				<li>

					<label class="bandeira band-{$tef->id}" for="payment-method-{$tef->id}">
						<input id="payment-method-{$tef->id}" name="payment-method" class="noUniform" type="radio" value="{$tef->id}"/>
					 </label>

				</li>
				{/foreach}
			</ul>
		</div>

		<div class="forma-pagamento">
			<h4>Boleto Bancário</h4>

			<ul id="credit_list">
				{foreach from=$bankSlips item=bankSlip}
				<li>

					<label class="bandeira band-{$bankSlip->id}" for="payment-method-{$bankSlip->id}">
						<input id="payment-method-{$bankSlip->id}" name="payment-method" class="noUniform" type="radio" value="{$bankSlip->id}"/>
					 </label>

				</li>
				{/foreach}
			</ul>
		</div>
	</form>
</div>