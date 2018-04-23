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
			this.bindActionsToEvents();
		},

		/**
		 * Show / hide field rows based on field_type_select selected value.
		 *
		 * showHideMain
		 *
		 *
		 *
		 */
		showHideMain: function( fieldTypeSelect ) {
            var fieldTypeVal = $( fieldTypeSelect ).val();
            var fieldSet     = $( fieldTypeSelect ).closest( '.cmb-field-list' );
            $( '.cmb-row.cmb_hide_field', fieldSet ).hide();
            $( '.cmb-row.' + fieldTypeVal, fieldSet ).show();
		},

		/**
		 * Show / hide field rows based on field_type_select selected value.
		 *
		 * showHideMain
		 *
		 *
		 *
		 */
		showHideSide: function() {
            var isRepeatable = $( '#_cmb2_repeatable_group' ).is( ':checked' );
			var repeatableOptions = $( '.cmb2-id--cmb2-group-description, .cmb2-id--cmb2-entry-name, .cmb2-id--cmb2-get-post-meta-repeatable, .cmb2-id--cmb2-cmbf-repeatable' );
			var mainCodeExamples = $( '.repeatable .cmb-type-textarea-code' );
			repeatableOptions.hide();
			mainCodeExamples.show();
			if ( isRepeatable ) {
				repeatableOptions.show();
				mainCodeExamples.hide();
			}
		},

		/**
		 * Use the showHideMain() function on each field group's field rows on page load.
		 *
		 * showHideMainOnLoad
		 *
		 *
		 *
		 */
		showHideOnLoad: function() {
            var fieldTypeSelects = $( '.field_type_select' );
            fieldTypeSelects.each( function() {
                cmb2MetaBoxField.showHideMain( this );
            } );
			cmb2MetaBoxField.showHideSide();

		},

		/**
		 * Use the showHideMain() function on this field group's field rows on field_type_select change.
		 *
		 * fieldTypeChange
		 *
		 *
		 *
		 */
		fieldTypeChange: function() {
            $( '.cmb2-metabox' ).on( 'change', '.field_type_select', function( e ) {
                var fieldTypeSelect = e.target;
                cmb2MetaBoxField.showHideMain( fieldTypeSelect );
            } );
		},

		/**
		 * Use the showHideMain() function on this field group's field rows on field_type_select change.
		 *
		 * fieldTypeChange
		 *
		 *
		 *
		 */
		isRepeatableChanged: function() {
            $( '#_cmb2_repeatable_group' ).on( 'change', function() {
                cmb2MetaBoxField.showHideSide();
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
            $( '.cmb2-metabox' ).on( 'change keyup', '.field_name', function() {
                this.displayUsageFunctions();
            }.bind( this ) );
            $( '#title, #post_name' ).on( 'change keyup', function() {
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
			var getPostMetaRepeatable = $( '.get_post_meta_repeatable' );
			var cmbfRepeatable = $( '.cmbf_repeatable' );
			var fieldNameVal = fieldName.val().toLowerCase().replace( / /g, '_' );
			var postName = $( '#post_name' ).val();
			postName = postName ? postName : $( '#title' ).val().toLowerCase().replace( / /g, '_' );
			postName = postName ? postName : $( '#post_ID' ).val();
			getPostMeta.val( 'get_post_meta( get_the_ID(), \'_' + fieldNameVal + '\', true );' );
			cmbf.val( 'cmbf( get_the_ID(), \'_' + fieldNameVal + '\' );' );
			getPostMetaRepeatable.val( 'get_post_meta( get_the_ID(), \'_' + postName + '\', true );' );
			cmbfRepeatable.val( 'cmbf( get_the_ID(), \'_' + postName + '\' );' );
		},

		/**
		 * Run the show / hide functions after reaptabel groups have been manipulated.
		 *
		 * bindActionsToEvents
		 *
		 *
		 *
		 */
		bindActionsToEvents: function() {

			$( document ).on( 'click', '.cmb-add-group-row, .cmb-remove-group-row, .cmb-shift-rows', function() {
				cmb2MetaBoxField.showHideOnLoad();
			} );
			$( '#_cmb2_repeatable_group' ).on( 'change', function() {
				cmb2MetaBoxField.showHideSide();
			} );
		}
	};

	cmb2MetaBoxField.init();

} );
