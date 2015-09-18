<div class="bcash" id="bcash">

	<div class='b-info-panel'>
		<span class="b-font-small">O número da sua transação no</span> 
		<span class="b-font-medium" style="margin-bottom: 20px;font-weight: bold;">Bcash</span>
		<span class="b-font-big" style="margin-bottom: 20px">{$bcash_transaction_id}</span>

		<div>
			<span class="b-font-small">
				Para acompanhar seu pedido <a href="https://www.bcash.com.br/site/Entrar">clique aqui</a>.
			</span>

			{if $bcash_payment_method->type == "ONLINE_TRANSFER"}
				<span>Um e-mail foi enviado para você com essas informações.</span>
			{/if}
		</div>

		<span class="b-font-small" style="margin-top: 20px;">O número do seu pedido é:</span>
		<span class="b-font-big" style="margin-bottom: 20px">{$store_order_id}</span>

	</div>
	<div class='b-info-panel'>
		<div class="payment-selected band-{$bcash_payment_method->id}"></div>

		<span class="b-font-medium" style="font-weight: bold;">Seu pedido foi concluído utilizando 

		{if $bcash_payment_method->type == "CARD"}
			Cartão de Crédito {$bcash_payment_method->title}
		</span>
		<span class="b-font-small">
			A transação encontra-se em análise e está sujeita à confirmação de dados cadastrais, 
			que poderá ser feita através de contato telefônico.
		</span>

		{elseif $bcash_payment_method->type == "BANKSLIP"}
			Boleto.
		</span>
		<span class="b-font-small">
			Agora é necessário efetuar o pagamento para concluir a transação. 
			O boleto para pagamento está aberto em outra janela do seu navegador.
		</span>
		<span class="b-font-small">
			Nosso sistema identificará junto ao banco o seu pagamento e assim que compensado 
			fará a aprovação desta transação.
		</span>

		Para mais informações acesse nossa <a href="https://devwww.bcash.com.br/site/Ajuda/" target="_blank">Central de atendimento</a>

		<a class="b-pay-button" href="{$bcash_paymentLink}" target="_blank">CLIQUE AQUI PARA ACESSAR O BOLETO</a>

		{else}
			transferência online.
		</span>
		<span class="b-font-small">
			Agora é necessário efetuar o pagamento para concluir a transação. 
			Acesse seu banco e realize a transferência online.
		</span>
		<span class="b-font-small">
			Nosso sistema identificará junto ao banco o seu pagamento e assim que compensado 
			fará a aprovação desta transação.
		</span>

		Para mais informações acesse nossa <a href="https://devwww.bcash.com.br/site/Ajuda/" target="_blank">Central de atendimento</a>

		<a class="b-pay-button" href="{$bcash_paymentLink}" target="_blank">CLIQUE AQUI PARA ACESSAR SEU BANCO</a>

		{/if}

	</div>


</div>