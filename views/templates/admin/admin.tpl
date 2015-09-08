<div class="bcash" id="bcash">

	<img src="https://www.bcash.com.br/webroot/img/banking/logo.png">

	<form class="b-form b-form-horizontal" id="adminform" action="{$action_post|escape:'none'}" method="POST">

		<div class="b-form-group">
			<label>Título</label>
			<input name="titulo" type="text" value="{$titulo}" />
		</div>

		<div class="b-form-group">
			<label>Email</label>
			<input name="email" type="text" placeholder="email@loja.com.br" title="Email cadastrado no Bcash." value="{$email}" />
		</div>

		<div class="b-form-group">
			<label>Consumer Key</label>
			<input name="consumer_key" type="text" title="Obtenha sua Consumer Key acessando sua conta em www.bcash.com.br e navegando até o menu: Ferramentas -> Gerenciamento de Apis" value="{$consumer_key}" />

		</div>

		<div class="b-form-group">
			<label>Token</label>
			<input name="bcash_token" type="text" title="Obtenha seu Token acessando sua conta em www.bcash.com.br e navegando até o menu: Ferramentas -> Códigos de integração" value="{$bcash_token}"/>
		</div>

		<h4>Descontos</h4>
		<div class="b-form-group">
			<label>Percentual no boleto</label>
			<input name="desconto_boleto" type="text" placeholder="Ex.: 10" title="Percentual que será concedido em compras finalizadas com boleto." style="width: 60px;" value="{$desconto_boleto}" />
			<span><strong>%</strong></span>
		</div>

		<div class="b-form-group">
			<label>Percentual em TEF</label>
			<input name="desconto_tef" type="text" placeholder="Ex.: 10" title="Percentual que será concedido em compras finalizadas através de TEF." style="width: 60px;" value="{$desconto_tef}" />
			<span><strong>%</strong></span>
		</div>

		<div class="b-form-group">
			<label>Percentual cartão de crédito a vista</label>
			<input name="desconto_credito" type="text" placeholder="Ex.: 10" title="Percentual que será concedido em compras finalizadas com cartão de crédito." style="width: 60px;" value="{$desconto_credito}" />
			<span><strong>%</strong></span>
		</div>

		<h4>Consumidor</h4>
		<div class="b-form-group">
			<label>CPF</label>
			<select style="height: 35px" name="campo_cpf" id="campo_cpf">
				<option value="" selected="selected">Exibir campo no checkout para preenchimento</option>
				{foreach from=$campos_customer item=campo}
					{if $campo == $campo_cpf}
						<option value="{$campo}" selected="selected" >Usar campo <span>{$campo}</span> da tabela Customer como CPF</option>
					{else}
						<option value="{$campo}">Usar campo <span>{$campo}</span> da tabela Customer como CPF</option>
					{/if}
				{/foreach}
			</select>
		</div>

		<div class="b-form-group">
			<label>Telefone</label>
			<select style="height: 35px" name="campo_fone" id="campo_fone">
				<option value="" selected="selected">Exibir campo no checkout para preenchimento</option>
				{foreach from=$campos_fone item=campo_f}
					{if $campo_f == $campo_fone}
						<option value="{$campo_f}" selected="selected" >Usar campo <span>{$campo_f}</span> da tabela Customer como CPF</option>
					{else}
						<option value="{$campo_f}">Usar campo <span>{$campo_f}</span> da tabela Customer como CPF</option>
					{/if}
				{/foreach}
			</select>
		</div>

		<div class="b-form-group">
			<label for="sandbox">
				{if $sandbox == 1}
					<input name="sandbox" checked id="sandbox" type="checkbox" value="{$sandbox}" title="Permite o módulo operar com o ambiente de testes (SandBox) do Bcash. Deve ser desabilitado após o período de desenvolvimento e integração." />				
				{else}
    				<input name="sandbox" id="sandbox" type="checkbox" value="{$sandbox}" title="Permite o módulo operar com o ambiente de testes (SandBox) do Bcash. Deve ser desabilitado após o período de desenvolvimento e integração." />
				{/if}
				Habilitar modo teste</label>
		</div>

		<input class="b-button b-button-primary" name="btn_submit" type="submit" value="Salvar" />
	</form>
</div>