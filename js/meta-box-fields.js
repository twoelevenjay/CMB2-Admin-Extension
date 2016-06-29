jQuery( function( $ ) {

	/**
	 * Variations actions.
	 */
	var cmb2MetaBoxField = {

		/**
		 * Initialize meta box fields behavior.
		 */
		init: function() {
            this.show_hide_on_load();
            this.field_type_change();
		},

		/**
		 * This function needs documentation.
		 *
		 * show_hide
		 *
		 *
		 *
		 */
		show_hide: function( fieldTypeSelect ) {
            var fieldTypeVal = $( fieldTypeSelect ).val();
            var fieldSet     = $( fieldTypeSelect ).closest( '.cmb-field-list' );
            $( '.cmb-row.cmb_hide_field', fieldSet ).hide();
            $( '.cmb-row.' + fieldTypeVal, fieldSet ).show();
		},

		/**
		 * This function needs documentation.
		 *
		 * show_hide_on_load
		 *
		 *
		 *
		 */
		show_hide_on_load: function() {
            var fieldTypeSelects = $( '.field_type_select' );
            fieldTypeSelects.each( function( fieldTypeSelect ) {
                cmb2MetaBoxField.show_hide( fieldTypeSelect );
            } );

		},

		/**
		 * This function needs documentation.
		 *
		 * field_type_change
		 *
		 *
		 *
		 */
		field_type_change: function() {
            $( '.cmb2-metabox' ).on( 'change', '.field_type_select', function( e ) {
                var fieldTypeSelect = $( e.target );
                cmb2MetaBoxField.show_hide( fieldTypeSelect );
            } );
		}
	};

	cmb2MetaBoxField.init();

} );
