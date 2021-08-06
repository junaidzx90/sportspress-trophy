const trophyApp = new Vue({
	el: '#trophy_table2',
	data: { },
	methods: {
		all_trophys_select: function (event) {
			if (event.target.checked) {
				jQuery('.singletrophy').each(function () {
					jQuery(this).prop('checked', true);
					jQuery(this).val(jQuery(this).attr('data'));
				});
			} else {
				jQuery('.singletrophy').each(function () {
					jQuery(this).removeAttr('checked');
					jQuery(this).val('');
				});
			}
		},
		single_trophy_select: function (event) {
			if (event.target.checked) {
				event.target.value = event.target.getAttribute('data');
			} else {
				event.target.value = '';
			}
		}
	},
});