{capture name=path}
	<a href="{$link->getPageLink('order', true, NULL, "step=3")|escape:'html':'UTF-8'}" title="Go back to the Checkout">Finalizar a compra</a><span class="navigation-pipe">{$navigationPipe}</span>Pagamento com Bcash
{/capture}

<div class="bcash" id="bcash">
	<form action="{$action_post|escape:'html'}" method="post">

		{if $campo_cpf == null || $campo_fone == null}
			<h3>Seus Dados</h3>
			<div class="forma-pagamento b-form-horizontal">
		{/if}

			{if $campo_cpf == null}
			<div class="b-form-group">
				<label style="margin-right: 10px">CPF</label>
				<input name="bcash_cpf" type="text" placeholder="CPF"/>
			</div>
			{/if}

			{if $campo_fone == null}
			<div class="b-form-group">
				<label style="margin-right: 10px">Telefone/Cel: </label>
				<input name="bcash_telefone" type="text" placeholder="Telefone/Cel"/>
			</div>
			{/if}

		{if $campo_cpf == null || $campo_fone == null}
			</div>
		{/if}

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
				<h4>Parcelamentos</h4>
				{foreach from=$cardsInstallments item=cardInstallments}					
				<div class="card-installments" id="card-installment-{$cardInstallments->id}" style="display:none;">

					<div class="coluna-com-6">
					{foreach from=$cardInstallments->installments item=installment}

						<label class="installment-option" for="card-installment-{$cardInstallments->id}-{$installment->number}">
							<input id="card-installment-{$cardInstallments->id}-{$installment->number}" name="card-installment" class="noUniform" type="radio" value="{$installment->number}"/>
						 	<span>
						 		<span>{$installment->number}X </span>R$ {$installment->amount}
						 	</span>
						</label>

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
					<input name="card-number" type="text" />
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
					<input name="card-owner-name" type="text" />
				</div>

				<div class="b-form-group">
					<label>Código de Segurança</label>
					<input name="card-security-code" type="text" style="width: 60px;" />
				</div>
			</div>
			<!-- card form -->

			<div class="b-amount">
				R$ {$cardsAmount}
			</div>

			<input id="b-button-sucess-credit" class="b-button b-button-sucess" name="btn_submit" type="submit" value="Finalizar Pagamento" />
			<p id="b-termos-credit" class="bcash-termos">
				Ao prosseguir o pagamento você concorda com o <a target="_blank" href="https://www.bcash.com.br/checkout/pay/contrato">Contrato de Gestão de Pagamentos</a>
			</p>
		</div>


		<div class="forma-pagamento">
			<h4>Transferência Bancária</h4>

			<ul id="tef_list">
				{foreach from=$tefs item=tef}
				<li>

					<label class="bandeira band-{$tef->id}" for="payment-method-{$tef->id}">
						<input id="payment-method-{$tef->id}" name="payment-method" class="noUniform" type="radio" value="{$tef->id}"/>
					 </label>

				</li>
				{/foreach}
			</ul>

			<div class="b-amount">
				R$ {$tefsAmount}
			</div>

			<input id="b-button-sucess-tef" class="b-button b-button-sucess" name="btn_submit" type="submit" value="Finalizar Pagamento" />
			<p id="b-termos-tef" class="bcash-termos">
				Ao prosseguir o pagamento você concorda com o <a target="_blank" href="https://www.bcash.com.br/checkout/pay/contrato">Contrato de Gestão de Pagamentos</a>
			</p>
		</div>

		<div class="forma-pagamento">
			<h4>Boleto Bancário</h4>

			<ul id="bankslip_list">
				{foreach from=$bankSlips item=bankSlip}
				<li>

					<label class="bandeira band-{$bankSlip->id}" for="payment-method-{$bankSlip->id}">
						<input id="payment-method-{$bankSlip->id}" name="payment-method" class="noUniform" type="radio" value="{$bankSlip->id}"/>
					 </label>

				</li>
				{/foreach}
			</ul>

			<div class="b-amount">
				R$ {$bankSlipsAmount}
			</div>

			<input id="b-button-sucess-bankslip" class="b-button b-button-sucess" name="btn_submit" type="submit" value="Finalizar Pagamento" />
			<p id="b-termos-bankslip" class="bcash-termos">
				Ao prosseguir o pagamento você concorda com o <a target="_blank" href="https://www.bcash.com.br/checkout/pay/contrato">Contrato de Gestão de Pagamentos</a>
			</p>
		</div>

	</form>
</div>