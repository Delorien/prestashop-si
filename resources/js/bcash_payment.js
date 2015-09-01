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

	function showCardForm(radio) {
		if ($('#' + radio.id, '#credit_list').length == 1) {

			$('#card-data').show('slow');

			$('.card-installments').hide('fast');
			$('#card-installment-' + radio.value).show('fast');

			$('.b-button-sucess').hide('fast');
			$('#b-button-sucess-credit').show('fast');

		} else {
			$('#card-data').hide('slow');

			if ($('#' + radio.id, '#tef_list').length == 1) {
				$('.b-button-sucess').hide('fast');
				$('#b-button-sucess-tef').show('fast');
			}else {
				$('.b-button-sucess').hide('fast');
				$('#b-button-sucess-bankslip').show('fast');
			}
		}
	};

});
//document.ready

$(window).load(function() {
	if ($.isFunction($.uniform.restore)) {
		$.uniform.restore(".noUniform");
	}
}); 