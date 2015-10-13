{capture name=path}
	<a href="{$link->getPageLink('order', true, NULL, "step=3")|escape:'html':'UTF-8'}" title="Go back to the Checkout">Finalizar a compra</a><span class="navigation-pipe">{$navigationPipe}</span>Pagamento com Bcash
{/capture}

<div class="bcash" id="bcash">


	{if $b_erros_messages|@count > 0}
	<div class="box-error">
	  <h3 class="title">Ocorreram alguns erros durante o processamento da transação</h3>

	  <ul>
	  	{foreach from=$b_erros_messages item=b_error}
	    	<li>{$b_error}</li>
	    {/foreach}
	  </ul>
	</div>
	{/if}

	<form id="b-form-checkout" action="{$action_post|escape:'html'}" method="post">

		{if $campo_cpf == 'exibir'}
			<h3>Seus Dados</h3>
			<div class="forma-pagamento b-form-horizontal">

				<div class="b-form-group">
					<label style="margin-right: 10px">CPF</label>
					<input name="bcash_cpf" type="text" placeholder="CPF"/>
				</div>
			</div>
		{/if}

		<h3>Selecione a Forma de Pagamento</h3>
{if ($cardsInstallments != false)}
		<div class="forma-pagamento">
			<div class="payment-type-name">
				<h4>Cartão de Crédito</h4>
				{if $cardsNoDiscount != null}
					<span>Desconto especial de {$cardsPercent}% + Frete</span>
				{/if}
			</div>
	
			<ul id="credit_list">
				{foreach from=$cardsInstallments item=card}
				<li>

					<label class="bandeira band-{$card->id}" for="payment-method-{$card->id}">
						<input id="payment-method-{$card->id}" name="payment-method" class="noUniform" type="radio" value="{$card->id}"/>
					 </label>

				</li>
				{/foreach}
			</ul>

			<!-- card form -->
			<div id="card-data" class="b-form-horizontal" style="display:none;">
				<h4 class="parcelamentosCartao">Parcelamentos</h4>
				{foreach from=$cardsInstallments item=cardInstallments}
				<div class="card-installments" id="card-installment-{$cardInstallments->id}" style="display:none;">

					<div class="coluna-com-6">
					{foreach from=$cardInstallments->installments item=installment name=installfor}

						{if $smarty.foreach.installfor.first}
						<label class="installment-option" for="card-installment-{$cardInstallments->id}-{$installment->number}">
							<input id="card-installment-{$cardInstallments->id}-{$installment->number}" name="card-installment" class="noUniform" type="radio" value="{$installment->number}"/>
						 	<span>
						 		<span>{$installment->number}X </span>R$ {$cardsAmount}
						 	</span>
						</label>
						{else}
						<label class="installment-option" for="card-installment-{$cardInstallments->id}-{$installment->number}">
							<input id="card-installment-{$cardInstallments->id}-{$installment->number}" name="card-installment" class="noUniform" type="radio" value="{$installment->number}"/>
						 	<span>
						 		<span>{$installment->number}X </span>R$ {$installment->installmentAmount}
						 	</span>
						</label>
						{/if}

						{if $installment@iteration is div by 6}
							</div>
							<div class="coluna-com-6">
						{/if}


					{/foreach}
					</div>

				</div>
				{/foreach}

				<h4>Dados do Cartão</h4>
				<div class="b-form-group">
					<label>Número do cartão</label>
					<input id="card-number" name="card-number" type="text" />
				</div>

				<div id="validadeCartao" class="b-form-group">
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
					<input id="card-owner-name" name="card-owner-name" type="text" style="text-transform:uppercase;" />
				</div>

				<div class="b-form-group codigo-seguranca">
					<label>Código de Segurança</label>
					<input id="card-security-code" name="card-security-code" type="text" maxlength="4" style="width: 60px;" />
				</div>
			</div>
			<!-- card form -->

			{if $cardsNoDiscount != null} 
			<div class="b-no-discount">
				De: <span>R$ {$cardsNoDiscount}</span>
			</div>
			{/if}

			<div class="b-amount">
				R$ {$cardsAmount}
				{if $cardsNoDiscount != null}
				<span>(à vista)</span>
				{/if}
			</div>

			<input id="b-button-sucess-credit" class="b-button b-button-sucess" name="btn_submit" type="submit" value="Finalizar Pagamento" />
			<p id="b-termos-credit" class="bcash-termos">
				Ao prosseguir o pagamento você concorda com o <a target="_blank" href="https://www.bcash.com.br/checkout/pay/contrato">Contrato de Gestão de Pagamentos</a>
			</p>
		</div>
{/if}

{if ($tefsInstallments != false)}
		<div class="forma-pagamento">
			<div class="payment-type-name">
				<h4>Transferência Bancária</h4>
				{if $tefsNoDiscount != null}
					<span>Desconto especial de {$tefsPercent}% + Frete</span>
				{/if}
			</div>

			<ul id="tef_list">
				{foreach from=$tefsInstallments item=tef}
				<li>

					<label class="bandeira band-{$tef->id}" for="payment-method-{$tef->id}">
						<input id="payment-method-{$tef->id}" name="payment-method" class="noUniform" type="radio" value="{$tef->id}"/>
					 </label>

				</li>
				{/foreach}
			</ul>

			{if $tefsNoDiscount != null} 
			<div class="b-no-discount">
				De: <span>R$ {$tefsNoDiscount}</span>
			</div>
			{/if}

			<div class="b-amount">
				R$ {$tefsAmount}
			</div>

			<input id="b-button-sucess-tef" class="b-button b-button-sucess" name="btn_submit" type="submit" value="Finalizar Pagamento" />
			<p id="b-termos-tef" class="bcash-termos">
				Ao prosseguir o pagamento você concorda com o <a target="_blank" href="https://www.bcash.com.br/checkout/pay/contrato">Contrato de Gestão de Pagamentos</a>
			</p>
		</div>
{/if}

{if ($bankSlipsInstallments != false)}
		<div class="forma-pagamento">
			<div class="payment-type-name">
				<h4>Boleto Bancário</h4>
				{if $bankSlipsNoDiscount != null}
					<span>Desconto especial de {$bankSlipsPercent}% + Frete</span>
				{/if}
			</div>

			<ul id="bankslip_list">
				{foreach from=$bankSlipsInstallments item=bankSlip}
				<li>

					<label class="bandeira band-{$bankSlip->id}" for="payment-method-{$bankSlip->id}">
						<input id="payment-method-{$bankSlip->id}" name="payment-method" class="noUniform" type="radio" value="{$bankSlip->id}"/>
					 </label>

				</li>
				{/foreach}
			</ul>

			{if $bankSlipsNoDiscount != null}
			<div class="b-no-discount">
				De: <span>R$ {$bankSlipsNoDiscount}</span>
			</div>
			{/if}

			<div class="b-amount">
				R$ {$bankSlipsAmount}
			</div>

			<input id="b-button-sucess-bankslip" class="b-button b-button-sucess" name="btn_submit" type="submit" value="Finalizar Pagamento" />
			<p id="b-termos-bankslip" class="bcash-termos">
				Ao prosseguir o pagamento você concorda com o <a target="_blank" href="https://www.bcash.com.br/checkout/pay/contrato">Contrato de Gestão de Pagamentos</a>
			</p>
		</div>
{/if}
	</form>
</div>

<div class="block-load">
	<div class="loader">
	  <div class="title">Por favor aguarde enquanto o pagamento é processado.</div>
	  <div class="load">
	    <div class="bar"></div>
	  </div>
	</div>
</div>
