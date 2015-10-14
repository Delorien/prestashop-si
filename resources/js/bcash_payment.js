$(document).ready(function() {

	$('#bcash .bandeira input[type=radio]').each(function() {

		var radio = $('input[type=radio][id=' + this.id + ']');

		radio.change(function() {

			resetAllLabels();

			radio.parent().addClass("checked");

			showCardForm(this);
		});
	});

	function resetAllLabels() {
		$('.bandeira').each(function() {
			$(this).removeClass('checked');

		});
	};

	function resetAllFields() {
		$('#card-data').find('input:text').val('');
		$('#card-data').find('input:radio').attr('checked', false);
		$('#validade_ano_cartao').prop('selectedIndex',0);
		$('#validade_mes_cartao').prop('selectedIndex',0);

		var validator = $( "#b-form-checkout" ).validate();
		validator.resetForm();
	};

	function showCardForm(radio) {
		if ($('#' + radio.id, '#credit_list').length == 1) {

			$('#card-data').show('slow');

			$('.card-installments').hide('fast');
			$('#card-installment-' + radio.value).show('fast');

			$('.b-button-sucess').fadeOut();
			$('#b-button-sucess-credit').fadeIn();

			$('.bcash-termos').hide('fast');
			$('#b-termos-credit').show('fast');

			if (radio.id == 'payment-method-45' ) {
				$('.codigo-seguranca').hide('fast');
			} else if (radio.id == 'payment-method-37') {
				$('#card-security-code').attr('maxlength', '4');
				$('.codigo-seguranca').show('fast');
			} else {
				$('#card-security-code').attr('maxlength', '3');
				$('.codigo-seguranca').show('fast');
			}

		} else {
			$('#card-data').hide('slow');

			resetAllFields();

			if ($('#' + radio.id, '#tef_list').length == 1) {

				$('.b-button-sucess').fadeOut();
				$('#b-button-sucess-tef').fadeIn();

				$('.bcash-termos').hide('fast');
				$('#b-termos-tef').show('fast');
			}else {
				$('.b-button-sucess').fadeOut();
				$('#b-button-sucess-bankslip').fadeIn();

				$('.bcash-termos').hide('fast');
				$('#b-termos-bankslip').show('fast');
			}
		}
	};


	var validaTecla=function(evt){
		var ev=evt.keyCode;
		if(ev==8||ev==9||ev==13||ev==16||ev==35||ev==36||ev==37||ev==38||ev==39||ev==40||ev==46){
			return true;
		}
	};


	$('#card-number').keydown(function(evt){
		if ((evt.keyCode>47&&evt.keyCode<58&&evt.shiftKey===false)||(evt.keyCode>95&&evt.keyCode<106)){
			return true;
		}else{
			if(validaTecla(evt)){
				return true;
			}else{
				evt.preventDefault();
			}
		}
	});

	$('#card-security-code').keydown(function(evt){
		if ((evt.keyCode>47&&evt.keyCode<58&&evt.shiftKey===false)||(evt.keyCode>95&&evt.keyCode<106)){
			return true;
		}else{
			if(validaTecla(evt)){
				return true;
			}else{
				evt.preventDefault();
			}
		}
	});

	$('#card-owner-name').keydown(function(evt){
		if(evt.keyCode===0||(evt.keyCode>64&&evt.keyCode<91)){
			return true;
		}else{
			if(validaTecla(evt)||evt.keyCode==32){
				return true;
			}else{
				if(evt.keyCode==59||evt.keyCode==219||evt.keyCode==222||evt.keyCode==186){
					return true;
				}else{
					evt.preventDefault();
				}
			}
		}
	});
});
// document.ready

$(window).load(function() {
	if ($.isFunction($.uniform.restore)) {
		$.uniform.restore(".noUniform");
	}
}); 