<div class="bcash" id="bcash">
	
	<img src="https://devwww.bcash.com.br/webroot/img/banking/logo.png">
	
	<form class="b-form b-form-horizontal" id="adminform">
		
		<div class="b-form-group">
			<label>Título</label>
			<input name="titulo" type="text" value="Bcash" />
		</div>
		
		<div class="b-form-group">
			<label>Email</label>
			<input name="email" type="text" placeholder="email@loja.com.br" title="Email cadastrado no Bcash." />
		</div>
		
		<div class="b-form-group">
			<label>Consumer Key</label>
			<input name="consumer_key" type="text" title="Obtenha sua Consumer Key acessando sua conta em www.bcash.com.br e navegando até o menu: Ferramentas -> Gerenciamento de Apis" />
										
		</div>
		
		<div class="b-form-group">
			<label>Token</label>
			<input name="token" type="text" title="Obtenha seu Token acessando sua conta em www.bcash.com.br e navegando até o menu: Ferramentas -> Códigos de integração" />
		</div>
		
		<h4>Descontos</h4>
		<div class="b-form-group">
			<label>Percentual no boleto</label>
			<input name="desconto_boleto" type="text" placeholder="Ex.: 10" title="Percentual que será concedido em compras finalizadas com boleto." style="width: 60px;" />
			<span><strong>%</strong></span>
		</div>
		
		<div class="b-form-group">
			<label>Percentual em TEF</label>
			<input name="desconto_tef" type="text" placeholder="Ex.: 10" title="Percentual que será concedido em compras finalizadas através de TEF." style="width: 60px;" />
			<span><strong>%</strong></span>
		</div>
		
		<div class="b-form-group">
			<label>Percentual cartão de crédito a vista</label>
			<input name="desconto_credito" type="text" placeholder="Ex.: 10" title="Percentual que será concedido em compras finalizadas com cartão de crédito." style="width: 60px;" />
			<span><strong>%</strong></span>
		</div>
		
		<div class="b-form-group">
			<label for="check">
				<input name="sandbox" type="checkbox" name="check" id="check" title="Permite o módulo operar com o ambiente de testes (SandBox) do Bcash. Deve ser desabilitado após o período de desenvolvimento e integração." />
				Habilitar modo teste</label>
		</div>
		
		<input class="b-button b-button-primary" type="submit" value="Salvar" />
	</form>
</div>