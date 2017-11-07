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
			this.field_name_change();
			this.display_usage_functions();
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
		},

		/**
		 * Update the usage code snippets when field_name changes.
		 *
		 * field_name_change
		 *
		 *
		 *
		 */
		field_name_change: function() {
            $( '.cmb2-metabox' ).on( 'change keyup', '.field_name', function( e ) {
                this.display_usage_functions();
            }.bind( this ) );
		},

		/**
		 * Convert field name to usage code snippet.
		 *
		 * display_usage_functions
		 *
		 *
		 *
		 */
		display_usage_functions: function() {
			var field_name = $( '.field_name' );
			var wrapper = field_name.closest( '.cmb-field-list' );
			var get_post_meta = $( '.get_post_meta', wrapper );
			var cmbf = $( '.cmbf', wrapper );
			get_post_meta.val( 'get_post_meta( get_the_ID(), \'_' + field_name.val().toLowerCase().replace( / /g, '_' ) + '\', true );');
			cmbf.val( 'cmbf( get_the_ID(), \'_' + field_name.val().toLowerCase().replace( / /g, '_' ) + '\' );');
		}
	};

	cmb2MetaBoxField.init();

} );
