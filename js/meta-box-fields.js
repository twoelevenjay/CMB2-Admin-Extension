jQuery( function( $ ) {

	/**
	 * JS meta_box field actions.
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
		 * Show / hide field rows based on field_type_select selected value.
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
		 * Use the show_hide() function on each field group's field rows on page load.
		 *
		 * show_hide_on_load
		 *
		 *
		 *
		 */
		show_hide_on_load: function() {
            var fieldTypeSelects = $( '.field_type_select' );
            fieldTypeSelects.each( function() {
                cmb2MetaBoxField.show_hide( this );
            } );

		},

		/**
		 * Use the show_hide() function on this field group's field rows on field_type_select change.
		 *
		 * field_type_change
		 *
		 *
		 *
		 */
		field_type_change: function() {
            $( '.cmb2-metabox' ).on( 'change', '.field_type_select', function( e ) {
                var fieldTypeSelect = e.target;
                cmb2MetaBoxField.show_hide( fieldTypeSelect );
            } );
		}
	};

	cmb2MetaBoxField.init();

} );
