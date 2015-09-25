<div class="bcash" id="bcash">
	<div class="b-panel">

		<div class="b-panel-heading">
			<img src="https://www.bcash.com.br/webroot/img/banking/logo.png">
			<span>Detalhes da transação no Bcash</span>
		</div>

		<table class="b-table">
		  <thead>
		    <tr>
		      <th>Transação</th>
		      <th>Forma de Pagamento</th>
		      <th>Valor</th>
		      <th>Taxa</th>
		      <th>A receber</th>
		      <th>Parcelamento</th>
		      <th>Status Transação</th>
		      <th>Data do registro</th>
		    </tr>
		  </thead>

		  {if isset($b_history)}
			  <tbody>
			  	{foreach from=$b_history item=b_row_der}
			    <tr>
			      <td>{$b_row_der['id_transacao']}		</td>
			      <td>{$b_row_der['pagamento_meio']}	</td>

					{if $b_row_der['valor_original']}
						<td>R$ {$b_row_der['valor_original']}	</td>
					{else}
						<td> - </td>
					{/if}

					{if $b_row_der['taxa']}
						<td>R$ {$b_row_der['taxa']}	</td>
					{else}
						<td> - </td>
					{/if}

					{if $b_row_der['valor_loja']}
						<td>R$ {$b_row_der['valor_loja']}	</td>
					{else}
						<td> - </td>
					{/if}

					{if $b_row_der['parcelas']}
						<td>{$b_row_der['parcelas']}	</td>
					{else}
						<td> - </td>
					{/if}

			      <td>{$b_row_der['status']}			</td>
			      <td>{$b_row_der['date_add']}			</td>
			    </tr>
				{/foreach}
			  </tbody>
		  {/if}
		</table>

		{if $b_isSuperAdmin}
		<button id="b_button_cancel" class="b-button b-button-danger">Cancelar Transação no Bcash</button>

		<div class="confirme">
			<span>O procedimento de cancelamento é irreversível, uma vez cancelada, 
				a transação não pode ser revertida.</span>
			<span class="atencao" style="margin-bottom: 5px;">Tem certeza que deseja cancelar a transação?</span>
			<a class="b-button b-button-danger">Cancelar</a>
			<button id="b_button_cancel_fechar" class="b-button b-button-sucess" style="margin-left: 5px;">Fechar</button>
		</div>
		{/if}

	</div>
</div>