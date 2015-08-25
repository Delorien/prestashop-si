$(document).ready(function() {

	jQuery.validator.addMethod("tokenDifereConsumerKey", function(value, element) {
		return $("input[name=consumer_key]").val() != $("input[name=token]").val();
	}, "Consumer key e Token não podem ser iguais.");

	jQuery.validator.addMethod("isPercentValid", function(value, element) {
		var regexp = /^(\d{1,2})$|(^(\d{1,2})(\.|\,)\d{1}$)|^$/;
		return regexp.test(value);

	}, "Formato inválido. (Ex.:10.0)");

	$("#adminform").validate({
		ignoreTitle: true,
		rules : {
			email : {
				required : true,
				email : true
			},
			consumer_key : {
				required : true,
				tokenDifereConsumerKey : true
			},
			token : {
				required : true,
				tokenDifereConsumerKey : true
			},
			desconto_boleto : {
				isPercentValid : true
			},
			desconto_tef : {
				isPercentValid : true
			},
			desconto_credito : {
				isPercentValid : true
			}
		},
		messages : {
			email : {
				required : "Campo email é obrigatório",
				email : "Informe um email válido."
			},
			consumer_key : {
				required : "Campo consumer key é obrigatório",
			},
			token : {
				required : "Campo token é obrigatório",
			}
		}
	});
	
	
	function mvalor(v){
	    v=v.replace(/\D/g,"");//Remove tudo o que não é dígito
	    v=v.replace(/^(\d{1})(\d{1})(\d{1})(\d)$/,"$1$2$3");//coloca a virgula antes dos 2 últimos dígitos
		v=v.replace(/^(\d{1,2})(\d{1})$/,"$1.$2");//coloca a virgula antes dos 2 últimos dígitos
	    return v;
	}
	$('input[name=desconto_boleto]').keypress(function(){
		setTimeout(
			function(){
				$('input[name=desconto_boleto]').val(mvalor($('input[name=desconto_boleto]').val()));
			},1);
	});
	$('input[name=desconto_tef]').keypress(function(){
		setTimeout(
			function(){
				$('input[name=desconto_tef]').val(mvalor($('input[name=desconto_tef]').val()));
			},1);
	});
	$('input[name=desconto_credito]').keypress(function(){
		setTimeout(
			function(){
				$('input[name=desconto_credito]').val(mvalor($('input[name=desconto_credito]').val()));
			},1);
	});
	
	
});
