jQuery(function($) {

	/**
	 * Variations actions
	 */
	var cmb2_meta_box_field = {

		/**
		 * Initialize meta box fields behavior
		 */
		init: function() {
            this.show_hide_on_load();
            this.field_type_change();
		},

		/**
		 * show_hide
		 *
		 *
		 *
		 */
		show_hide: function(field_type_select) {
            var field_type_val = field_type_select.val();
            var field_set = field_type_select.closest('.cmb-field-list');
            $('.cmb-row.cmb_hide_field',field_set).hide();
            $('.cmb-row.'+field_type_val,field_set).show();
		},

		/**
		 * show_hide_on_load
		 *
		 *
		 *
		 */
		show_hide_on_load: function() {
            var field_type_selects = $('.field_type_select');
            field_type_selects.each(function(field_type_select){
                cmb2_meta_box_field.show_hide($(this));
            });

		},

		/**
		 * field_type_change
		 *
		 *
		 *
		 */
		field_type_change: function() {
            $('.cmb2-metabox').on('change', '.field_type_select', function(e){
                var field_type_select = $(e.target);
                cmb2_meta_box_field.show_hide(field_type_select);
            });
		},
	};

	cmb2_meta_box_field.init();

});
