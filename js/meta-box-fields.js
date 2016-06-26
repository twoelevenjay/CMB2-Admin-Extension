jQuery( function( $ ) {

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
		 * reload UI
		 *
		 * @param {Object} event
		 * @param {Int} qty
		 */
		show_hide: function(field_type_select) {
            var field_type_val = field_type_select.val();
            var field_set = field_type_select.closest('.cmb-field-list');
            $('.cmb-row',field_set).hide();
            $('.no_hide, .cmb-row.'+field_type_val,field_set).show();
		},

		/**
		 * reload UI
		 *
		 * @param {Object} event
		 * @param {Int} qty
		 */
		show_hide_on_load: function() {
            var field_type_selects = $('.field_type_select');
            var that = this;
            field_type_selects.each(function(field_type_select){
                that.show_hide($(this));
            });

		},

		/**
		 * reload UI
		 *
		 * @param {Object} event
		 * @param {Int} qty
		 */
		field_type_change: function() {
            $('.cmb2-metabox').on('change', '.field_type_select', function(e){
                var field_type_select = $(e.target);
                this.show_hide(field_type_select);
            });
		},
	};

	cmb2_meta_box_field.init();

});
