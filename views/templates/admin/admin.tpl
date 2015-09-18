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
			<label class="label_campo_cpf" for="campo_cpf_def">
				{if $campo_cpf eq 'exibir'}
					<input type="radio" id="campo_cpf_def" name="campo_cpf" checked value="exibir"> <span>Exibir campo no checkout para preenchimento</span>
				{else}
					<input type="radio" id="campo_cpf_def" name="campo_cpf" value="exibir"> <span>Exibir campo no checkout para preenchimento</span>
				{/if}
			</label>
			<label class="label_campo_cpf" for="campo_cpf_specified">
				{if $campo_cpf == 'specified'}
					<input type="radio" id="campo_cpf_specified" name="campo_cpf" checked value="specified"> <span>Especificar campo do CPF</span>
					<label>(tabela: {$table_cpf}, campo: {$campo_cpf_select}, id_customer: {$where_cpf_select})</label>
				{else}
					<input type="radio" id="campo_cpf_specified" name="campo_cpf" value="specified"> <span>Especificar campo do CPF</span>
				{/if}

			</label>

			<div class="b-form-group" id="cpf-spec" style="display: none">

				<div class="table-search">
					<label>Listar campos da tabela: </label>
					<input type="text" id="tableAjax" name="tableAjax" style="width: 160px;" />
					<input type="button" id="tableAjaxButton" value="Buscar" />
				</div>

				<div class="select-column" style="display: none">
					<label>Campo com CPF:
						<select style="height: 35px" name="campo_cpf_select" id="campo_cpf_select" >
						</select>
					</label>
					<label>
						Campo com id_customer:
						<select style="height: 35px" name="where_cpf_select" id="where_cpf_select" >
						</select>
					</label>
				</div>
			</div>
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

<script>
	$("#tableAjaxButton").click(function($table)
	{
		$.ajax({
		  type: 'POST',
		  url: '{$ajax_dir}',
		  data: 'table=' + $("#tableAjax").val(),
		  dataType: 'json',
		  success: function(json) {

			$('#campo_cpf_select').find('option').remove()

			$(json).each(function(index, element) {
				$("#campo_cpf_select").append('<option value=' + element + '>' + element + '</option>');
			});

			$('#where_cpf_select').find('option').remove()

			$(json).each(function(index, element) {
				$("#where_cpf_select").append('<option value=' + element + '>' + element + '</option>');
			});

			$(".select-column").show('slow');
		  },
		  error: function (xhr, ajaxOptions, thrownError) {
		  		$(".select-column").hide('slow');
				alert('Não foi possível carregar os campos da tabela ' + $("#tableAjax").val() + '. Por favor verifieque o nome da tabela.')
	      }
		});
	});
</script>
