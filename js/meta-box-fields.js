jQuery( function( $ ) {

	/**
	 * JS meta_box field actions.
	 */
	var cmb2MetaBoxField = {

		/**
		 * Initialize meta box fields behavior.
		 */
		init: function() {
            this.showHideOnLoad();
			this.fieldTypeChange();
			this.fieldNameChange();
			this.displayUsageFunctions();
		},

		/**
		 * Show / hide field rows based on field_type_select selected value.
		 *
		 * showHide
		 *
		 *
		 *
		 */
		showHide: function( fieldTypeSelect ) {
            var fieldTypeVal = $( fieldTypeSelect ).val();
            var fieldSet     = $( fieldTypeSelect ).closest( '.cmb-field-list' );
            $( '.cmb-row.cmb_hide_field', fieldSet ).hide();
            $( '.cmb-row.' + fieldTypeVal, fieldSet ).show();
		},

		/**
		 * Use the showHide() function on each field group's field rows on page load.
		 *
		 * showHideOnLoad
		 *
		 *
		 *
		 */
		showHideOnLoad: function() {
            var fieldTypeSelects = $( '.field_type_select' );
            fieldTypeSelects.each( function() {
                cmb2MetaBoxField.showHide( this );
            } );

		},

		/**
		 * Use the showHide() function on this field group's field rows on field_type_select change.
		 *
		 * fieldTypeChange
		 *
		 *
		 *
		 */
		fieldTypeChange: function() {
            $( '.cmb2-metabox' ).on( 'change', '.field_type_select', function( e ) {
                var fieldTypeSelect = e.target;
                cmb2MetaBoxField.showHide( fieldTypeSelect );
            } );
		},

		/**
		 * Update the usage code snippets when field_name changes.
		 *
		 * fieldNameChange
		 *
		 *
		 *
		 */
		fieldNameChange: function() {
            $( '.cmb2-metabox' ).on( 'change keyup', '.field_name', function( e ) {
                this.displayUsageFunctions();
            }.bind( this ) );
		},

		/**
		 * Convert field name to usage code snippet.
		 *
		 * displayUsageFunctions
		 *
		 *
		 *
		 */
		displayUsageFunctions: function() {
			var fieldName = $( '.field_name' );
			var wrapper = fieldName.closest( '.cmb-field-list' );
			var getPostMeta = $( '.get_post_meta', wrapper );
			var cmbf = $( '.cmbf', wrapper );
			var fieldNameVal = fieldName.val().toLowerCase().replace( / /g, '_' );
			getPostMeta.val( 'get_post_meta( get_the_ID(), \'_' + fieldNameVal + '\', true );' );
			cmbf.val( 'cmbf( get_the_ID(), \'_' + fieldNameVal + '\' );' );
		}
	};

	cmb2MetaBoxField.init();

} );
