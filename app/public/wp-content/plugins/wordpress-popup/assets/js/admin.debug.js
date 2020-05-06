( function( $ ) {
	'use strict';

	/**
	 * Defines the Hustle Object
	 *
	 * @type {{define, getModules, get, modules}}
	 */
	window.Hustle = ( function( $, doc, win ) {
		var _modules = {},
			_TemplateOptions = {
				evaluate: /<#([\s\S]+?)#>/g,
				interpolate: /\{\{\{([\s\S]+?)\}\}\}/g,
				escape: /\{\{([^\}]+?)\}\}(?!\})/g
			};

			let define = function( moduleName, module ) {
				var splits = moduleName.split( '.' );
				if ( splits.length ) { // if module_name has more than one object name, then add the module definition recursively
					let recursive = function( moduleName, modules ) {
						var arr = moduleName.split( '.' ),
							_moduleName = arr.splice( 0, 1 )[ 0 ],
							invoked;
						if ( ! _moduleName ) {
							return;
						}
						if ( ! arr.length ) {
							invoked = module.call( null, $, doc, win );
							modules[ _moduleName ] = _.isFunction( invoked ) ||
								'undefined' === typeof invoked ?
								invoked : _.extend( modules[ _moduleName ] || {}, invoked );
						} else {
							modules[ _moduleName ] = modules[ _moduleName ] || {};
						}
						if ( arr.length && _moduleName ) {
							recursive( arr.join( '.' ), modules[ _moduleName ]);
						}
					};
					recursive( moduleName, _modules );
				} else {
					let m = _modules[moduleName] || {};
					_modules[moduleName] = _.extend( m, module.call( null, $, doc, win ) );
				}
			},
			getModules = function() {
				return _modules;
			},
			get = function( moduleName ) {
				var module, recursive;
				if ( moduleName.split( '.' ).length ) { // recursively fetch the module
					module = false;
					recursive = function( moduleName, modules ) {
							var arr = moduleName.split( '.' ),
								_moduleName = arr.splice( 0, 1 )[ 0 ];
							module = modules[ _moduleName ];
							if ( arr.length ) {
								recursive( arr.join( '.' ), modules[ _moduleName ]);
							}
						};
					recursive( moduleName, _modules );
					return module;
				}
				return _modules[moduleName] || false;
			},
			Events = _.extend({}, Backbone.Events ),
			View = Backbone.View.extend({
				initialize: function() {
					if ( _.isFunction( this.initMix ) ) {
						this.initMix.apply( this, arguments );
					}
					if ( this.render ) {
						this.render = _.wrap( this.render, function( render ) {
							this.trigger( 'before_render' );
							render.call( this );
							Events.trigger( 'view.rendered', this );
							this.trigger( 'rendered' );
						});
					}
					if ( _.isFunction( this.init ) ) {
						this.init.apply( this, arguments );
					}
				}
			}),
			template = _.memoize( function( id ) {
				var compiled;
				return function( data ) {
					compiled = compiled || _.template( document.getElementById( id ).innerHTML, null, _TemplateOptions );
					return compiled( data ).replace( '/*<![CDATA[*/', '' ).replace( '/*]]>*/', '' );
				};
			}),
			createTemplate = _.memoize( function( str ) {
				var cache;
				return function( data ) {
					cache = cache || _.template( str, null, _TemplateOptions );
					return cache( data );
				};
			}),
			getTemplateOptions = function() {
				return $.extend(  true, {}, _TemplateOptions );
			},
			cookie = ( function() {

				// Get a cookie value.
				var get = function( name ) {
					var i, c, cookieName, value,
						ca = document.cookie.split( ';' ),
						caLength = ca.length;
					cookieName = name + '=';
					for ( i = 0; i < caLength; i += 1 ) {
						c = ca[i];
						while ( ' ' === c.charAt( 0 ) ) {
							c = c.substring( 1, c.length );
						}
						if ( 0 === c.indexOf( cookieName ) ) {
							let _val = c.substring( cookieName.length, c.length );
							return _val ? JSON.parse( _val ) : _val;
						}
					}
					return null;
				};

				// Saves the value into a cookie.
				var set = function( name, value, days ) {
					var date, expires;

					value = $.isArray( value ) || $.isPlainObject( value ) ? JSON.stringify( value ) : value;
					if ( ! isNaN( days ) ) {
						date = new Date();
						date.setTime( date.getTime() + ( days * 24 * 60 * 60 * 1000 ) );
						expires = '; expires=' + date.toGMTString();
					} else {
						expires = '';
					}
					document.cookie = name + '=' + value + expires + '; path=/';
				};
				return {
					set: set,
					get: get
				};
			}() ),
			consts = ( function() {
				return {
					ModuleShowCount: 'hustle_module_show_count-'
				};
			}() );

		return {
			define,
			getModules,
			get,
			Events,
			View,
			template,
			createTemplate,
			getTemplateOptions,
			cookie,
			consts
		};
	}( jQuery, document, window ) );

}( jQuery ) );

var  Optin = Optin || {};

Optin.View = {};
Optin.Models = {};
Optin.Events = {};

if ( 'undefined' !== typeof Backbone ) {
	_.extend( Optin.Events, Backbone.Events );
}

( function( $ ) {
	'use strict';
	Optin.NEVER_SEE_PREFIX = 'inc_optin_never_see_again-',
	Optin.COOKIE_PREFIX = 'inc_optin_long_hidden-';
	Optin.POPUP_COOKIE_PREFIX = 'inc_optin_popup_long_hidden-';
	Optin.SLIDE_IN_COOKIE_PREFIX = 'inc_optin_slide_in_long_hidden-';
	Optin.EMBEDDED_COOKIE_PREFIX = 'inc_optin_embedded_long_hidden-';

	Optin.globalMixin = function() {
		_.mixin({

			/**
			 * Logs to console
			 */
			log: function() {
				console.log( arguments );
			},

			/**
			 * Converts val to boolian
			 *
			 * @param val
			 * @returns {*}
			 */
			toBool: function( val ) {
				if ( _.isBoolean( val ) ) {
					return val;
				}
				if ( _.isString( val ) && -1 !== [ 'true', 'false', '1' ].indexOf( val.toLowerCase() ) ) {
					return 'true' === val.toLowerCase() || '1' === val.toLowerCase() ? true : false;
				}
				if ( _.isNumber( val ) ) {
					return ! ! val;
				}
				if ( _.isUndefined( val ) || _.isNull( val ) || _.isNaN( val ) ) {
					return false;
				}
				return val;
			},

			/**
			 * Checks if val is truthy
			 *
			 * @param val
			 * @returns {boolean}
			 */
			isTrue: function( val ) {
				if ( _.isUndefined( val ) || _.isNull( val ) || _.isNaN( val ) ) {
					return false;
				}
				if ( _.isNumber( val ) ) {
					return 0 !== val;
				}
				val = val.toString().toLowerCase();
				return -1 !== [ '1', 'true', 'on' ].indexOf( val );
			},
			isFalse: function( val ) {
				return ! _.isTrue( val );
			},
			controlBase: function( checked, current, attribute ) {
				attribute = _.isUndefined( attribute ) ? 'checked' : attribute;
				checked  = _.toBool( checked );
				current = _.isBoolean( checked ) ? _.isTrue( current ) : current;
				if ( _.isEqual( checked, current ) ) {
					return  attribute + '=' + attribute;
				}
				return '';
			},

			/**
			 * Returns checked=check if checked variable is equal to current state
			 *
			 *
			 * @param checked checked state
			 * @param current current state
			 * @returns {*}
			 */
			checked: function( checked, current ) {
				return _.controlBase( checked, current, 'checked' );
			},

			/**
			 * Adds selected attribute
			 *
			 * @param selected
			 * @param current
			 * @returns {*}
			 */
			selected: function( selected, current ) {
				return _.controlBase( selected, current, 'selected' );
			},

			/**
			 * Adds disabled attribute
			 *
			 * @param disabled
			 * @param current
			 * @returns {*}
			 */
			disabled: function( disabled, current ) {
				return _.controlBase( disabled, current, 'disabled' );
			},

			/**
			 * Returns css class based on the passed in condition
			 *
			 * @param conditon
			 * @param cls
			 * @param negating_cls
			 * @returns {*}
			 */
			class: function( conditon, cls, negatingCls ) {
				if ( _.isTrue( conditon ) ) {
					return cls;
				}
				return 'undefined' !== typeof negatingCls ? negatingCls : '';
			},

			/**
			 * Returns class attribute with relevant class name
			 *
			 * @param conditon
			 * @param cls
			 * @param negating_cls
			 * @returns {string}
			 */
			add_class: function( conditon, cls, negatingCls ) { // eslint-disable-line camelcase
				return 'class={class}'.replace( '{class}',  _.class( conditon, cls, negatingCls ) );
			},

			toUpperCase: function( str ) {
				return  _.isString( str ) ? str.toUpperCase() : '';
			}
		});

		if ( ! _.findKey ) {
			_.mixin({
				findKey: function( obj, predicate, context ) {
					predicate = cb( predicate, context );
					let keys = _.keys( obj ),
                        key;
					for ( let i = 0, length = keys.length; i < length; i++ ) {
						key = keys[i];
						if ( predicate( obj[ key ], key, obj ) ) {
							return key;
						}
					}
				}
			});
		}
	};

	Optin.globalMixin();

	/**
	 * Recursive toJSON
	 *
	 * @returns {*}
	 */
	Backbone.Model.prototype.toJSON = function() {
		var json = _.clone( this.attributes );
		var attr;
		for ( attr in json ) {
			if (
				( json[ attr ] instanceof Backbone.Model ) ||
				( Backbone.Collection && json[attr] instanceof Backbone.Collection )
			) {
				json[ attr ] = json[ attr ].toJSON();
			}
		}
		return json;
	};

	Optin.template = _.memoize( function( id ) {
		var compiled,

			options = {
				evaluate: /<#([\s\S]+?)#>/g,
				interpolate: /\{\{\{([\s\S]+?)\}\}\}/g,
				escape: /\{\{([^\}]+?)\}\}(?!\})/g
			};

		return function( data ) {
			compiled = compiled || _.template( $( '#' + id ).html(), null, options );
			return compiled( data ).replace( '/*<![CDATA[*/', '' ).replace( '/*]]>*/', '' );
		};
	});

	/**
	 * Compatibility with other plugin/theme e.g. upfront
	 *
	 */
	Optin.templateCompat = _.memoize( function( id ) {
		var compiled;

		return function( data ) {
			compiled = compiled || _.template( $( '#' + id ).html() );
			return compiled( data ).replace( '/*<![CDATA[*/', '' ).replace( '/*]]>*/', '' );
		};
	});

	Optin.cookie = Hustle.cookie;

	Optin.Mixins = {
		_mixins: {},
		_servicesMixins: {},
		_desingMixins: {},
		_displayMixins: {},
		add: function( id, obj ) {
			this._mixins[id] = obj;
		},
		getMixins: function() {
			return this._mixins;
		},
		addServicesMixin: function( id, obj ) {
			this._servicesMixins[id] = obj;
		},
		getServicesMixins: function() {
			return this._servicesMixins;
		}
	};


}( jQuery ) );

( function( $ ) {
	'use strict';

	Hustle.Events.on( 'view.rendered', function( view ) {

		if ( view instanceof Backbone.View ) {

			const accessibleHide = ( $elements ) => {
					$elements.hide();
					$elements.prop( 'tabindex', '-1' );
					$elements.prop( 'hidden', true );
				},
				accessibleShow = ( $elements ) => {
					$elements.show();
					$elements.prop( 'tabindex', '0' );
					$elements.removeProp( 'hidden' );
				};

			// Init select
			view.$( 'select:not([multiple])' ).each( function() {
				SUI.suiSelect( this );
			});

			// Init select2
			view.$( '.sui-select:not(.hustle-select-ajax)' ).SUIselect2({
				dropdownCssClass: 'sui-select-dropdown'
			});

			// Init accordion
			view.$( '.sui-accordion' ).each( function() {
				SUI.suiAccordion( this );
			});

			// Init tabs
			SUI.suiTabs();
			SUI.tabs();

			// Init float input
			SUI.floatInput();

			/**
			 * Hides and shows the content of the settings using sui-side-tabs.
			 * For us, non-designers: sui-side-tabs are the "buttons" that work as labels for radio inputs.
			 * They may have related content that should be shown or hidden depending on the selected option.
			 * @since 4.0
			 */
			view.$( '.sui-side-tabs' ).each( function() {

				const $inputs = $( this ).find( '.sui-tabs-menu .sui-tab-item input' ),

					handleTabs = () => {

						// This holds the dependency name of the selected input.
						// It's used to avoid hiding a container that should be shown
						// when two or more tabs share the same container.
						let shownDep = '';

						$.each( $inputs, function() {
							const $input = $( this ),
								$label = $input.parent( 'label' ),
								dependencyName = $input.data( 'tab-menu' ),
								$tabContent =  $( `.sui-tabs-content [data-tab-content="${ dependencyName }"]` ),
								$tabDependent =  $( `[data-tab-dependent="${ dependencyName }"]` );

							if ( $input[0].checked ) {
								$label.addClass( 'active' );
								if ( dependencyName ) {
									shownDep = dependencyName;

									$tabContent.addClass( 'active' );
									accessibleShow( $tabDependent );
								}

							} else {
								$label.removeClass( 'active' );
								if ( dependencyName !== shownDep ) {
									$tabContent.removeClass( 'active' );
									accessibleHide( $tabDependent );
								}
							}

						});
					};

				// Do it on load.
				handleTabs();

				// And do it on change.
				$inputs.on( 'change', () => handleTabs() );
			});

			/**
			 * Hides and shows the container dependent on toggles
			 * on view load and on change.
			 * Used in wizards and global settings page.
			 * @since 4.0.3
			 */
			view.$( '.sui-toggle.hustle-toggle-with-container' ).each( function() {
				const $this = $( this ),
					$checkbox = $this.find( 'input[type=checkbox]' ),
					$containersOn = $( `[data-toggle-content="${ $this.data( 'toggle-on' ) }"]` ),
					$containersOff = $( `[data-toggle-content="${ $this.data( 'toggle-off' ) }"]` ),
					doToggle = () => {
						if ( $checkbox[0].checked ) {
							Module.Utils.accessibleShow( $containersOn );
							Module.Utils.accessibleHide( $containersOff );
						} else {
							Module.Utils.accessibleShow( $containersOff );
							Module.Utils.accessibleHide( $containersOn );
						}
					};

				// Do it on load.
				doToggle();

				// And do it on change.
				$checkbox.on( 'change', () => doToggle() );
			});

			view.$( 'select.hustle-select-with-container' ).each( function() {

				const $this = $( this ),
					$depContainer = $( `[data-field-content="${ this.name }"]` ),
					valuesOn = $this.data( 'content-on' ).split( ',' ),
					doToggle = () => {
						if ( valuesOn.includes( $this.val() ) ) {
							Module.Utils.accessibleShow( $depContainer );
						} else {
							Module.Utils.accessibleHide( $depContainer );
						}
					};

				// Do it on load.
				doToggle();

				// And do it on change.
				$this.on( 'change', () => doToggle() );
			});
		}
	});

	$( document ).ready( function() {
		if ( $( '#hustle-email-day' ).length ) {
			$( '#hustle-email-day' ).datepicker({
				beforeShow: function( input, inst ) {
					$( '#ui-datepicker-div' ).addClass( 'sui-calendar' );
				},
				'dateFormat': 'MM dd, yy'
			});
		}

		if ( $( '#hustle-email-time' ).length ) {

			$( '#hustle-email-time' ).timepicker({
				timeFormat: 'h:mm p',
				interval: '1',
				minTime: '0',
				maxTime: '11:59pm',
				defaultTime: null,
				startTime: '00:00',
				dynamic: false,
				dropdown: true,
				scrollbar: true,
				change: function() {
					$( '#hustle-email-time' ).trigger( 'change' );
				}
			});
		}

		// Dismisses the notice that shows up when the user is a member but doesn't have Hustle Pro installed
		$( '#hustle-notice-pro-is-available .notice-dismiss' ).on( 'click', function( e ) {

			var data = {
				action: 'hustle_dismiss_admin_notice',
				dismissedNotice: 'hustle_pro_is_available'
			};

			$.post( ajaxurl, data, function( response ) {

				});
			});

		// Makes the 'copy' button work.
		$( '.hustle-copy-shortcode-button' ).on( 'click', function( e ) {
			e.preventDefault();

			let $button = $( e.target ),
				shortcode = $button.data( 'shortcode' ),
				$inputWrapper = $button.closest( '.sui-with-button-inside' );

				if ( 'undefined' !== typeof shortcode ) {

					// Actions in listing pages.
					let $temp = $( '<input />' );
					$( 'body' ).append( $temp );
					$temp.val( shortcode ).select();
					document.execCommand( 'copy' );
					$temp.remove();
					Module.Notification.open( 'success', optinVars.messages.commons.shortcode_copied );

				} else if ( $inputWrapper.length ) {

					// Copy shortcode in wizard pages.
					let $inputWithCopy = $inputWrapper.find( 'input[type="text"]' );
					$inputWithCopy.select();
					document.execCommand( 'copy' );
				}
		});

		$( '#hustle-tracking-migration-notice .hustle-notice-dismiss' ).on( 'click', function( e ) {
			e.preventDefault();

			$( '#hustle-dismiss-modal-button' ).on( 'click', function( e ) {
				e.preventDefault();

				$.post(
					ajaxurl,
					{
						action: 'hustle_dismiss_notification',
						name: $( e.currentTarget ).data( 'name' ),
						'_ajax_nonce': $( e.currentTarget ).data( 'nonce' )
					}
				)
				.always( () => location.reload() );
			});

			SUI.dialogs['hustle-dialog--migrate-dismiss-confirmation'].show();
		});

		$( '#hustle-dismiss-m2-notice' ).on( 'click', function( e ) {
			$.post(
				ajaxurl,
				{
					action: 'hustle_dismiss_m2_notification',
					'_ajax_nonce': $( this ).data( 'nonce' )
				}
			).always( () => {
				$( '#hustle-m2-notice' ).fadeOut( 'slow' );
			});
		});

		$( '#hustle-sendgrid-update-notice .notice-dismiss' ).on( 'click', function( e ) {
			e.preventDefault();

			const $container = $( e.currentTarget ).closest( '#hustle-sendgrid-update-notice' );

			$.post(
				ajaxurl,
				{
					action: 'hustle_dismiss_notification',
					name: $container.data( 'name' ),
					'_ajax_nonce': $container.data( 'nonce' )
				}
			);

		});

		$( '.hustle-notice .notice-dismiss, .hustle-notice .dismiss-notice' ).on( 'click', function( e ) {
			e.preventDefault();

			const $container = $( e.currentTarget ).closest( '.hustle-notice' );

			$.post(
				ajaxurl,
				{
					action: 'hustle_dismiss_notification',
					name: $container.data( 'name' ),
					_ajax_nonce: $container.data( 'nonce' ) // eslint-disable-line camelcase
				}
			)
			.always( () => location.reload() );
		});

		if ( $( '.sui-form-field input[type=number]' ).length ) {
			$( '.sui-form-field input[type=number]' ).on( 'keydown', function( e ) {
				if ( $( this )[0].hasAttribute( 'min' ) && 0 <= $( this ).attr( 'min' ) ) {
					let char = e.originalEvent.key.replace( /[^0-9^.^,]/, '' );
					if ( 0 === char.length && ! ( e.originalEvent.ctrlKey || e.originalEvent.metaKey ) ) {
						e.preventDefault();
					}
				}
			});
		}

		setTimeout( function() {
			if ( $( '.hustle-scroll-to' ).length ) {
				$( 'html, body' ).animate({
					scrollTop: $( '.hustle-scroll-to' ).offset().top
				}, 'slow' );
			}
		}, 100 );

		//table checkboxes
		$( '.hustle-check-all' ).on( 'click', function( e ) {
			let $this = $( e.target ),
				$list = $this.parents( '.sui-wrap' ).find( '.hustle-list' ),
				allChecked = $this.is( ':checked' );

			$list.find( '.hustle-listing-checkbox' ).prop( 'checked', allChecked );
			$this.parents( '.sui-wrap' ).find( '.hustle-check-all' ).prop( 'checked', allChecked );
			$( '.hustle-bulk-apply-button' ).prop( 'disabled', ! allChecked );
		});

		$( '.hustle-list .hustle-listing-checkbox' ).on( 'click', function( e ) {
			let $this = $( e.target ),
				$list = $this.parents( '.sui-wrap' ).find( '.hustle-list' ),
				allChecked = $this.is( ':checked' ) && ! $list.find( '.hustle-listing-checkbox:not(:checked)' ).length,
				count = $list.find( '.hustle-listing-checkbox:checked' ).length,
				disabled = 0 === count;

			$( '.hustle-check-all' ).prop( 'checked', allChecked );
			$( '.hustle-bulk-apply-button' ).prop( 'disabled', disabled );

			return;
		});

		$( '.hustle-bulk-apply-button' ).on( 'click', function( e ) {
			let $this = $( e.target ),
				value = $( 'select option:selected', $this.closest( '.hui-bulk-actions' ) ).val(),
				elements = $( '.hustle-list .hustle-listing-checkbox:checked' );

			if ( 0 === elements.length || 'undefined' === value ) {
				return false;
			}
			let ids = [];
			$.each( elements, function() {
				ids.push( $( this ).val() );
			});

			if ( 'delete-all' === value ) {
				let data = {
					ids: ids.join( ',' ),
					nonce: $this.siblings( 'input[name="hustle_nonce"]' ).val(),
					title: $this.data( 'title' ),
					description: $this.data( 'description' ),
					action: value
				};

				Module.deleteModal.open( data );
				return false;
			}
		});

	});

} ( jQuery ) );

Hustle.define( 'Modals.Migration', function( $ ) {

	'use strict';

	const migrationModalView = Backbone.View.extend({

		el: '#hustle-dialog--migrate',

		data: {},

		events: {
			'click #hustle-migrate-start': 'migrateStart',
			'click #hustle-create-new-module': 'createModule',
			'click .sui-box-selector': 'enableContinue',
			'click .hustle-dialog-migrate-skip': 'dismissModal',
			'click .sui-dialog-overlay': 'dismissModal'
		},

		initialize() {
			if ( ! this.$el.length ) {
				return;
			}

			let currentSlide = '',
				focusOnOpen = '';

			if ( 0 === this.$el.data( 'isFirst' ) ) {
				currentSlide = '#hustle-dialog--migrate-slide-2';
				focusOnOpen = 'hustle-migrate-start';

			} else {
				currentSlide = '#hustle-dialog--migrate-slide-1';
				focusOnOpen = 'hustle-migrate-get-started';

			}

			this.$( currentSlide ).addClass( 'sui-active sui-loaded' );

			setTimeout( () => SUI.openModal( 'hustle-dialog--migrate', focusOnOpen, $( '.sui-wrap' )[0], false ), 100 );

			this.$progressBar = this.$el.find( '.sui-progress .sui-progress-bar span' );
			this.$progressText = this.$el.find( '.sui-progress .sui-progress-text span' );
			this.$partialRows = this.$el.find( '#hustle-partial-rows' );
		},

		migrateStart( e ) {

			const me = this;

			const button      = $( e.target );
			const $container = this.$el,
				$dialog      = $container.find( '#hustle-dialog--migrate-slide-2' ),
				description  = $dialog.find( '#migrateDialog2Description' );

			// On load button
			button.addClass( 'sui-button-onload' );

			// Remove skip migration link
			$dialog.find( '.hustle-dialog-migrate-skip' ).remove();

			description.text( description.data( 'migrate-text' ) );

			Module.Utils.accessibleHide( $dialog.find( 'div[data-migrate-start]' ) );
			Module.Utils.accessibleHide( $dialog.find( 'div[data-migrate-failed]' ) );
			Module.Utils.accessibleShow( $dialog.find( 'div[data-migrate-progress]' ) );

			me.migrateTracking( e );

			button.removeClass( 'sui-button-onload' );

			e.preventDefault();

		},

		migrateComplete() {

			const slide       = this.$( '#hustle-dialog--migrate-slide-2' ),
				self = this;
			const title       = slide.find( '#migrateDialog2Title' );
			const description = slide.find( '#migrateDialog2Description' );

			this.$el.find( 'sui-button-onload' ).removeClass( 'sui-button-onload' );

			title.text( title.data( 'done-text' ) );
			description.text( description.data( 'done-text' ) );

			Module.Utils.accessibleHide( slide.find( 'div[data-migrate-progress]' ) );
			Module.Utils.accessibleShow( slide.find( 'div[data-migrate-done]' ) );

			this.$el.closest( '.sui-modal' ).on( 'click', ( e ) => self.closeDialog( e ) );

		},

		migrateFailed() {

			const slide = this.$el.find( '#hustle-dialog--migrate-slide-2' ),
				description = slide.find( '#dialogDescription' );

			description.text( '' );

			Module.Utils.accessibleHide( slide.find( 'div[data-migrate-start]' ) );
			Module.Utils.accessibleShow( slide.find( 'div[data-migrate-failed]' ) );
			Module.Utils.accessibleHide( slide.find( 'div[data-migrate-progress]' ) );
		},

		updateProgress( migratedRows, rowsPercentage, totalRows ) {

			if ( 'undefined' === typeof this.totalRows ) {
				this.totalRows = totalRows;
				this.$el.find( '#hustle-total-rows' ).text( totalRows );
			}

			this.$partialRows.text( migratedRows );

			const width = rowsPercentage + '%';
			this.$progressBar.css( 'width', width );

			if ( 100 >= rowsPercentage ) {
				this.$progressText.text( rowsPercentage + '%' );
			}
		},

		migrateTracking( e ) {
			e.preventDefault();

			let self = this,
				$button = $( e.currentTarget ),
				nonce = $button.data( 'nonce' ),
				data = {
					action: 'hustle_migrate_tracking',
					'_ajax_nonce': nonce
				};

			$.ajax({
				type: 'POST',
				url: ajaxurl,
				dataType: 'json',
				data,
				success: function( res ) {
					if ( res.success ) {

						const migratedRows = res.data.migrated_rows,
							migratedPercentage = res.data.migrated_percentage,
							totalRows = res.data.total_entries || '0';

						if ( 'done' !== res.data.current_meta ) {

							self.updateProgress( migratedRows, migratedPercentage, totalRows );
							self.migrateTracking( e );

						} else {
							self.updateProgress( migratedRows, migratedPercentage, totalRows );

							// Set a small delay so the users can see the progress update in front before moving
							// forward and they don't think some rows were not migrated.
							setTimeout( () => self.migrateComplete(), 500 );
						}


					} else {
						self.migrateFailed();
					}
				},
				error: function( res ) {
					self.migrateFailed();
				}
			});
			return false;
		},

		createModule( e ) {

			const button = $( e.target ),
				$selection = this.$el.find( '.sui-box-selector input:checked' );


			if ( $selection.length ) {

				this.dismissModal();

				button.addClass( 'sui-button-onload' );

				const moduleType = $selection.val(),
					page = 'undefined' !== typeof optinVars.module_page[ moduleType ] ? optinVars.module_page[ moduleType ] : optinVars.module_page.popup;

				window.location = `?page=${page}&create-module=true`;

			} else {

				// Show an error message or something?
			}

			e.preventDefault();
		},

		closeDialog( e ) {

			SUI.closeModal();

			e.preventDefault();
			e.stopPropagation();

		},

		enableContinue() {
			this.$el.find( '#hustle-create-new-module' ).prop( 'disabled', false );
		},

		dismissModal( e ) {

			if ( e ) {
				e.preventDefault();
			}

			$.post(
				ajaxurl,
				{
					action: 'hustle_dismiss_notification',
					name: 'migrate_modal',
					'_ajax_nonce': this.$el.data( 'nonce' )
				}
			);
		}

	});

	new migrationModalView();
});

Hustle.define( 'Modals.ReviewConditions', function( $ ) {

	'use strict';

	const ReviewConditionsModalView = Backbone.View.extend({

		el: '#hustle-dialog--review_conditions',

		initialize() {
			if ( ! this.$el.length ) {
				return;
			}
			setTimeout( this.show, 100, this );
		},

		show( reviewConditions ) {
			if ( 'undefined' === typeof SUI || 'undefined' === typeof SUI.dialogs ) {
				setTimeout( reviewConditions.show, 100, reviewConditions );
				return;
			}
			if ( 'undefined' !== typeof SUI.dialogs[ reviewConditions.$el.prop( 'id' ) ]) {
				SUI.dialogs[ reviewConditions.$el.prop( 'id' ) ].show();
			}
		}

	});

	new ReviewConditionsModalView();

});

Hustle.define( 'Upgrade_Modal', function( $ ) {
	'use strict';
	return Backbone.View.extend({
		el: '#wph-upgrade-modal',
		opts: {},
		events: {
			'click .wpmudev-i_close': 'close'
		},
		initialize: function( options ) {
			this.opts = _.extend({}, this.opts, options );
		},
		close: function( e ) {
			e.preventDefault();
			e.stopPropagation();
			this.$el.removeClass( 'wpmudev-modal-active' );
		}
	});
});

Hustle.define( 'Modals.Welcome', function( $ ) {

	'use strict';

	const welcomeModalView = Backbone.View.extend({

		el: '#hustle-dialog--welcome',

		events: {
			'click #hustle-new-create-module': 'createModule',
			'click .sui-box-selector': 'enableContinue',
			'click #getStarted': 'dismissModal',
			'click .sui-onboard-skip': 'dismissModal',
			'click .sui-dialog-close': 'dismissModal'
		},

		initialize() {
			if ( ! this.$el.length ) {
				return;
			}
			setTimeout( this.show, 100, this );
		},

		show( welcome ) {
			if ( 'undefined' === typeof SUI ) {
				setTimeout( welcome.show, 100, welcome );
				return;
			}
			if ( 'undefined' === typeof SUI.dialogs ) {
				setTimeout( welcome.show, 100, welcome );
				return;
			}
			if ( 'undefined' !== typeof SUI.dialogs[ welcome.$el.prop( 'id' ) ]) {
				SUI.dialogs[ welcome.$el.prop( 'id' ) ].show();
			}
		},

		createModule( e ) {

			const button = $( e.target ),
				$selection = this.$el.find( '.sui-box-selector input:checked' );


			if ( $selection.length ) {

				button.addClass( 'sui-button-onload' );

				const moduleType = $selection.val(),
					page = 'undefined' !== typeof optinVars.module_page[ moduleType ] ? optinVars.module_page[ moduleType ] : optinVars.module_page.popup;

				window.location = `?page=${page}&create-module=true`;

			}

			e.preventDefault();

		},

		enableContinue() {
			this.$el.find( '#hustle-new-create-module' ).prop( 'disabled', false );
		},

		dismissModal( e ) {

			if ( e ) {
				e.preventDefault();
			}

			$.post(
				ajaxurl,
				{
					action: 'hustle_dismiss_notification',
					name: 'welcome_modal',
					'_ajax_nonce': this.$el.data( 'nonce' )
				}
			);
		}

	});

	new welcomeModalView();

});

Hustle.define( 'Featured_Image_Holder', function( $ ) {
	'use strict';

	return Backbone.View.extend({

		mediaFrame: false,
		el: '#wph-wizard-choose_image',
		options: {
			attribute: 'feature_image',
			multiple: false
		},

		initialize: function( options ) {

			this.options.title = optinVars.messages.media_uploader.select_or_upload;
			this.options.button_text = optinVars.messages.media_uploader.use_this_image; // eslint-disable-line camelcase

			this.options = _.extend({}, this.options, options );

			if ( ! this.model || ! this.options.attribute ) {
				throw new Error( 'Undefined model or attribute' );
			}
			this.targetDiv = options.targetDiv;
			$( document ).on( 'click', '.wpmudev-feature-image-browse', $.proxy( this.open, this ) );
			$( document ).on( 'click', '#wpmudev-feature-image-clear', $.proxy( this.clear, this ) );
			this.render();
		},

		render: function() {
			this.defineMediaFrame();
			return this;
		},

		// If no featured image is set, show the upload button. Display the selected image otherwise.
		showImagePreviewOrButton: function() {
			var featureImage = this.model.get( 'feature_image' );
			if ( '' === featureImage || 'undefined' === typeof featureImage ) {
				this.$el.removeClass( 'sui-has_file' );
			} else {
				this.$el.addClass( 'sui-has_file' );
			}
		},

		defineMediaFrame: function() {
			var self = this;
			this.mediaFrame = wp.media({
				title: self.options.title,
				button: {
					text: self.options.button_text
				},
				multiple: self.options.multiple
			}).on( 'select', function() {
				var media = self.mediaFrame.state().get( 'selection' ).first().toJSON();
                var featureImageSrc, featureImageThumbnail;
				if ( media && media.url ) {
					featureImageSrc = media.url;
					featureImageThumbnail = '';
					self.model.set( 'feature_image', featureImageSrc );
					if ( media.sizes && media.sizes.thumbnail && media.sizes.thumbnail.url ) {
						featureImageThumbnail = media.sizes.thumbnail.url;
					}
					self.$el.find( '.sui-upload-file span' ).text( featureImageSrc ).change();
					self.$el.find( '.sui-image-preview' ).css( 'background-image', 'url( ' + featureImageThumbnail + ' )' );

					self.showImagePreviewOrButton();
				}
			});
		},

		open: function( e ) {
			e.preventDefault();
			this.mediaFrame.open();
		},

		clear: function( e ) {
			e.preventDefault();
			this.model.set( 'feature_image', '' );
			this.$el.find( '.sui-upload-file span' ).text( '' ).change();
			this.$el.find( '.sui-image-preview' ).css( 'background-image', 'url()' );

			//this.model.set( 'feature_image', '', {silent: true} );
			this.showImagePreviewOrButton();
		}
	});

});

Hustle.define( 'Modals.Edit_Field', function( $ ) {

	'use strict';

	return Backbone.View.extend({

		el: '#hustle-dialog--edit-field',

		events: {
			'click .sui-dialog-overlay': 'closeModal',
			'click .hustle-discard-changes': 'closeModal',
			'change input[name="time_format"]': 'changeTimeFormat',
			'click #hustle-apply-changes': 'applyChanges',
			'blur input[name="name"]': 'trimName',
			'change input': 'fieldUpdated',
			'click input[type="radio"]': 'fieldUpdated',
			'change select': 'fieldUpdated',
			'change input[name="version"]': 'handleCaptchaSave'
		},

		initialize( options ) {
			this.field = options.field;
			this.changed = {};

			// Same as this.field, but with the values for the field's view. Won't be stored.
			this.fieldData = options.fieldData;
			this.model = options.model;
			this.render();
		},

		render() {
			this.renderHeader();
			this.renderLabels();
			this.renderSettings();
			this.renderStyling();
			this.handleCaptchaSave();

			//select the first tab
			this.$( '.hustle-data-pane' ).first().trigger( 'click' );

			// Make the search box work within the modal.
			this.$( '.sui-select' ).SUIselect2({
				dropdownParent: $( '#hustle-dialog--edit-field .sui-box' ),
				dropdownCssClass: 'sui-select-dropdown'
			});
		},

		renderHeader() {
			this.$( '.sui-box-header .sui-tag' ).text( this.field.type );
		},

		renderLabels() {
			if ( -1 !== $.inArray( this.field.type, [ 'recaptcha', 'gdpr', 'submit' ]) ) {
				this.$( '#hustle-data-tab--labels' ).removeClass( 'hustle-data-pane' ).addClass( 'sui-hidden' );
				this.$( '#hustle-data-pane--labels' ).addClass( 'sui-hidden' );
				return;
			} else {
				this.$( '#hustle-data-tab--labels' ).removeClass( 'sui-hidden' ).addClass( 'hustle-data-pane' );

				this.$( '#hustle-data-pane--labels' ).removeClass( 'sui-hidden' );
			}

			// Check if a specific template for this field exists.
			let templateId = 'hustle-' + this.field.type + '-field-labels-tpl';

			// If a specific template doesn't exist, use the common template.
			if ( ! $( '#' + templateId ).length ) {
				templateId = 'hustle-common-field-labels-tpl';
			}

			const template = Optin.template( templateId );
			this.$( '#hustle-data-pane--labels' ).html( template( this.fieldData ) );
			Hustle.Events.trigger( 'view.rendered', this );

		},

		renderSettings() {

			if ( 'hidden' === this.field.type ) {
				this.$( '#hustle-data-tab--settings' ).removeClass( 'hustle-data-pane' ).addClass( 'sui-hidden' );
				this.$( '#hustle-data-pane--settings' ).addClass( 'sui-hidden' );

				Module.Utils.accessibleHide( this.$( '[data-tabs]' ) );
				return;
			} else {
				Module.Utils.accessibleShow( this.$( '[data-tabs]' ) );
			}

			this.$( '#hustle-data-tab--settings' ).removeClass( 'sui-hidden' ).addClass( 'hustle-data-pane' );
			this.$( '#hustle-data-pane--settings' ).removeClass( 'sui-hidden' );

			// Check if a specific template for this field exists.
			let templateId = 'hustle-' + this.field.type + '-field-settings-tpl';

			// If a specific template doesn't exist, use the common template.
			if ( ! $( '#' + templateId ).length ) {
				templateId = 'hustle-common-field-settings-tpl';
			}

			const template = Optin.template( templateId );
			this.$( '#hustle-data-pane--settings' ).html( template( this.fieldData ) );
			Hustle.Events.trigger( 'view.rendered', this );

			if ( 'gdpr' === this.field.type ) {

				// These only allow inline elements.
				const editorSettings = {
					tinymce: {
						wpautop: false,
						toolbar1: 'bold,italic,strikethrough,link',
						valid_elements: 'a[href|target=_blank],strong/b,i,u,s,em,del', // eslint-disable-line camelcase
						forced_root_block: '' // eslint-disable-line camelcase
					},
					quicktags: { buttons: 'strong,em,del,link' }
				};

				wp.editor.remove( 'gdpr_message' );
				wp.editor.initialize( 'gdpr_message', editorSettings );

			} else if ( 'recaptcha' === this.field.type ) {

				const editorSettings = {
					tinymce: { toolbar: [ 'bold italic link alignleft aligncenter alignright' ] },
					quicktags: true
				};
				wp.editor.remove( 'v3_recaptcha_badge_replacement' );
				wp.editor.initialize( 'v3_recaptcha_badge_replacement', editorSettings );

				wp.editor.remove( 'v2_invisible_badge_replacement' );
				wp.editor.initialize( 'v2_invisible_badge_replacement', editorSettings );
			}
		},

		renderStyling() {

			if ( 'hidden' === this.field.type ) {
				this.$( '#hustle-data-tab--styling' ).removeClass( 'hustle-data-pane' ).addClass( 'sui-hidden' );
				this.$( '#hustle-data-pane--styling' ).addClass( 'sui-hidden' );

				return;
			}

			this.$( '#hustle-data-tab--styling' ).removeClass( 'sui-hidden' ).addClass( 'hustle-data-pane' );
			this.$( '#hustle-data-pane--styling' ).removeClass( 'sui-hidden' );

			// Check if a specific template for this field exists.
			let templateId = 'hustle-' + this.field.type + '-field-styling-tpl';

			// If a specific template doesn't exist, use the common template.
			if ( ! $( '#' + templateId ).length ) {
				templateId = 'hustle-common-field-styling-tpl';
			}
			let template = Optin.template( templateId );
			this.$( '#hustle-data-pane--styling' ).html( template( this.fieldData ) );
		},

		fieldUpdated( e ) {
			let $this = $( e.target ),
				dataName = $this.attr( 'name' ),
				dataValue = $this.is( ':checkbox' ) ? $this.is( ':checked' ) : $this.val();

			this.changed[ dataName ] = dataValue;
		},

		closeModal() {
			this.undelegateEvents();
			this.stopListening();

			// Hide dialog
			SUI.dialogs[ 'hustle-dialog--edit-field' ].hide();
		},

		changeTimeFormat( e ) {
			let $this = $( e.target ),
				dataValue = $this.val();
			if ( '12' === dataValue ) {
				$( '#hustle-date-format' ).closest( '.sui-form-field' ).show();
				$( 'input[name="time_hours"]' ).prop( 'min', 1 ).prop( 'max', 12 );
			} else {
				$( '#hustle-date-format' ).closest( '.sui-form-field' ).hide();
				$( 'input[name="time_hours"]' ).prop( 'min', 0 ).prop( 'max', 23 );
			}
		},

		handleCaptchaSave( e ) {
			if ( 'recaptcha' !== this.field.type ) {
				return;
			}
			let avaiableCaptcha = $( '#available_recaptchas' ).val();
			if ( avaiableCaptcha ) {
				avaiableCaptcha = avaiableCaptcha.split( ',' );
				let version = $( 'input[name="version"]:checked' ).val();

				if ( -1 === _.indexOf( avaiableCaptcha, version ) ) {
					$( '#hustle-dialog--edit-field' ).find( '#hustle-apply-changes' ).attr( 'disabled', 'disabled' );
				} else {
					$( '#hustle-dialog--edit-field' ).find( '#hustle-apply-changes' ).attr( 'disabled', false );
				}
			} else {
				$( '#hustle-dialog--edit-field' ).find( '#hustle-apply-changes' ).attr( 'disabled', 'disabled' );
			}
		},

		/**
		 * Trim and replace spaces in field name.
		 * @since 4.0
		 * @param event e
		 */
		trimName( e ) {
			let $input = this.$( e.target ),
				newVal;

			newVal = $.trim( $input.val() ).replace( / /g, '_' );

			$input.val( newVal );
		},

		/**
		 * Add the saved settings to the model.
		 * @since 4.0
		 * @param event e
		 */
		applyChanges( e ) {

			// TODO: do validation
			// TODO: keep consistency with how stuff is saved in visibility conditions
			let self = this,
				$button = this.$( e.target ),
				formFields = Object.assign({}, this.model.get( 'form_elements' ) );

			// if gdpr message
			if ( 'gdpr' === this.field.type && 'undefined' !== typeof tinyMCE ) {

				// gdpr_message editor
				let gdprMessageEditor = tinyMCE.get( 'gdpr_message' ),
					$gdprMessageTextarea = this.$( 'textarea#gdpr_message' ),
					gdprMessage = ( 'true' === $gdprMessageTextarea.attr( 'aria-hidden' ) ) ? gdprMessageEditor.getContent() : $gdprMessageTextarea.val();

				formFields.gdpr.gdpr_message = gdprMessage; // eslint-disable-line camelcase
				this.model.set( 'form_elements', formFields );
				this.model.userHasChange();

			} else if ( 'recaptcha' === this.field.type && 'undefined' !== typeof tinyMCE ) {

				// v3 recaptcha badge editor.
				let v3messageEditor = tinyMCE.get( 'v3_recaptcha_badge_replacement' ),
					$v3messageTextarea = this.$( 'textarea#v3_recaptcha_badge_replacement' ),
					v3message = ( 'true' === $v3messageTextarea.attr( 'aria-hidden' ) ) ? v3messageEditor.getContent() : $v3messageTextarea.val();

				formFields.recaptcha.v3_recaptcha_badge_replacement = v3message; // eslint-disable-line camelcase

				// v2 invisible badge editor.
				let v2messageEditor = tinyMCE.get( 'v2_invisible_badge_replacement' ),
				$v2messageTextarea = this.$( 'textarea#v2_invisible_badge_replacement' ),
				v2message = ( 'true' === $v2messageTextarea.attr( 'aria-hidden' ) ) ? v2messageEditor.getContent() : $v2messageTextarea.val();

				formFields.recaptcha.v2_invisible_badge_replacement = v2message; // eslint-disable-line camelcase

				this.model.set( 'form_elements', formFields );
				this.model.userHasChange();

			}

			// If there were changes.
			if ( Object.keys( this.changed ).length ) {

				let oldField = _.extend({}, this.field );
				_.extend( this.field, this.changed );

				// Don't allow to override Email field created by default
				// and prevent field's names from being empty.
				if (
					( ( 'name' in this.changed ) && 'email' !== oldField.name && 'email' === this.field.name ) ||
					( 'name' in this.changed && ! this.field.name.trim().length )
				) {
					this.field.name = oldField.name;
					delete this.changed.name;
				}

				// "Name" is the unique identifier. If it changed, return and let the callback handle it.
				if ( ! ( 'name' in this.changed ) && 'email' !== oldField.name ) {

					// Update this field.
					formFields[ this.field.name ] = this.field;
					this.model.set( 'form_elements', formFields );
					this.model.userHasChange();

				} else if ( 'email' === oldField.name ) {
					this.field.name = 'email';
					delete this.changed.name;
				}

				this.trigger( 'field:updated', this.field, this.changed, oldField );
			}
			$button.addClass( 'sui-button-onload' );
			setTimeout( function() {
				self.closeModal();
				$button.removeClass( 'sui-button-onload' );
			}, 300 );
		}
	});
});

Hustle.define( 'Modals.Optin_Fields', function( $ ) {
	'use strict';
	return Backbone.View.extend({

		el: '#hustle-dialog--optin-fields',

		events: {
			'click .sui-box-selector input': 'selectFields',
			'click .sui-dialog-overlay': 'closeModal',
			'click .hustle-cancel-insert-fields': 'closeModal',
			'click #hustle-insert-fields': 'insertFields'
		},

		initialize() {
			this.selectedFields = [];
		},

		selectFields( e ) {
			var $input = this.$( e.target ),
				value = $input.val(),
				$selectorLabel  = this.$el.find( 'label[for="' + $input.attr( 'id' ) + '"]' )
				;
			$selectorLabel.toggleClass( 'selected' );
			if ( $input.prop( 'checked' ) ) {
				this.selectedFields.push( value );
			} else {
				this.selectedFields = _.without( this.selectedFields, value );
			}
		},

		insertFields( e ) {
			var self = this,
				$button   = this.$( e.target )
				;
			$button.addClass( 'sui-button-onload' );
			this.trigger( 'fields:added', this.selectedFields );
			setTimeout( function() {
				$button.removeClass( 'sui-button-onload' );
				self.closeModal();
			}, 500 );
		},

		closeModal: function() {
			this.undelegateEvents();
			this.stopListening();
			let $selector = this.$el.find( '.sui-box-selector:not(.hustle-skip)' ),
				$input    = $selector.find( 'input' );

			// Hide dialog
			SUI.dialogs[ 'hustle-dialog--optin-fields' ].hide();

			// Uncheck options
			$selector.removeClass( 'selected' );
			$input.prop( 'checked', false );
			$input[0].checked = false;
		}

	});
});

Hustle.define( 'Modals.Visibility_Conditions', function( $ ) {
	'use strict';

	return Backbone.View.extend({

		el: '#hustle-dialog--visibility-options',

		selectedConditions: [],

		opts: {
			groupId: 0,
			conditions: []
		},

		events: {
			'click .sui-box-selector input': 'selectConditions',
			'click .hustle-cancel-conditions': 'cancelConditions',
			'click .sui-dialog-overlay': 'cancelConditions',
			'click #hustle-add-conditions': 'addConditions'
		},

		initialize: function( options ) {
			this.opts = _.extend({}, this.opts, options );
			this.selectedConditions = this.opts.conditions;

			this.$( '.hustle-visibility-condition-option' ).prop( 'checked', false ).prop( 'disabled', false );

			for ( let conditionId of this.selectedConditions ) {
				this.$( '#hustle-condition--' + conditionId ).prop( 'checked', true ).prop( 'disabled', true );
			}

		},

		selectConditions: function( e ) {

			let $input = this.$( e.target ),
				$selectorLabel  = this.$el.find( 'label[for="' + $input.attr( 'id' ) + '"]' ),
				value = $input.val()
				;

			$selectorLabel.toggleClass( 'selected' );

			if ( $input.prop( 'checked' ) ) {
				this.selectedConditions.push( value );
			} else {
				this.selectedConditions = _.without( this.selectedConditions, value );
			}
		},

		cancelConditions: function() {

			// Hide dialog
			SUI.dialogs[ 'hustle-dialog--visibility-options' ].hide();

		},

		addConditions: function( e ) {
			let me = this,
				$button   = this.$( e.target );
			$button.addClass( 'sui-button-onload' );

			this.trigger( 'conditions:added', { groupId: $button.data( 'group_id' ), conditions: this.selectedConditions });
			setTimeout( function() {

				// Hide dialog
				SUI.dialogs[ 'hustle-dialog--visibility-options' ].hide();
				$button.removeClass( 'sui-button-onload' );
				me.undelegateEvents();
			}, 500 );
		}

	});
});

( function( $ ) {
	'use strict';

	Optin.listingBase = Hustle.View.extend({

		el: '.sui-wrap',

		logShown: false,

		moduleType: '',

		singleModuleActionNonce: '',

		_events: {

			// Modals.
			'click .hustle-create-module': 'openCreateModal',
			'click .hustle-delete-module-button': 'openDeleteModal',
			'click .hustle-module-tracking-reset-button': 'openResetTrackingModal',
			'click .hustle-manage-tracking-button': 'openManageTrackingModal',
			'click .hustle-import-module-button': 'openImportModal',
			'click .hustle-upgrade-modal-button': 'openUpgradeModal',

			// Modules' actions.
			'click .hustle-single-module-button-action': 'handleSingleModuleAction',
			'click .hustle-preview-module-button': 'openPreview',

			// Bulk actions.
			'click form.sui-bulk-actions .hustle-bulk-apply-button': 'bulkActionCheck',
			'click #hustle-dialog--delete .hustle-delete': 'bulkActionSend',
			'click #hustle-bulk-action-reset-tracking-confirmation .hustle-delete': 'bulkActionSend',

			// Utilities.
			'click .sui-accordion-item-action .hustle-onload-icon-action': 'addLoadingIconToActionsButton'
		},

		initialize( opts ) {

			this.events = $.extend( true, {}, this.events, this._events );
			this.delegateEvents();

			this.moduleType = opts.moduleType;

			this.singleModuleActionNonce = optinVars.single_module_action_nonce;

			let newModuleModal = Hustle.get( 'Modals.New_Module' ),
				importModal = Hustle.get( 'Modals.ImportModule' );

			new newModuleModal({ moduleType: this.moduleType });
			this.ImportModal = new importModal();

			// Why this doesn't work when added in events
			$( '.sui-accordion-item-header' ).on( 'click', $.proxy( this.openTrackingChart, this ) );

			// Open the tracking chart when the class is present. Used when coming from 'view tracking' in Dashboard.
			if ( $( '.hustle-display-chart' ).length ) {
				this.openTrackingChart( $( '.hustle-display-chart' ) );
			}

			this.doActionsBasedOnUrl();
		},

		doActionsBasedOnUrl() {

			// Display the "Create module" dialog.
			if ( 'true' === Module.Utils.getUrlParam( 'create-module' ) ) {
				setTimeout( () => {
					$( '.hustle-create-module' ).trigger( 'click' );
				}, 100 );
			}

			// Display "Upgrade modal".
			if ( 'true' === Module.Utils.getUrlParam( 'requires-pro' ) ) {
				const self = this;
				setTimeout( () => self.openUpgradeModal(), 100 );
			}

			// Display notice based on URL parameters.
			if ( Module.Utils.getUrlParam( 'show-notice' ) ) {
				const status = 'success' === Module.Utils.getUrlParam( 'show-notice' ) ? 'success' : 'error',
					notice = Module.Utils.getUrlParam( 'notice' ),
					message = ( notice && 'undefined' !== optinVars.messages.commons[ notice ]) ? optinVars.messages.commons[ notice ] : Module.Utils.getUrlParam( 'notice-message' );

				if ( 'undefined' !== typeof message && message.length ) {
					Module.Notification.open( status, message );
				}
			}
		},

		handleSingleModuleAction( e ) {
			this.addLoadingIcon( e );
			Module.handleActions.initAction( e, 'listing', this );
		},

		/**
		 * initAction succcess callback for "toggle-status".
		 * @since 4.0.4
		 */
		actionToggleStatus( $this, data ) {

			const enabled = data.was_module_enabled;

			let item = $this.closest( '.sui-accordion-item' ),
				tag  = item.find( '.sui-accordion-item-title span.sui-tag' );

			if ( ! enabled ) {
				tag.text( tag.data( 'publish' ) );
				tag.addClass( 'sui-tag-blue' );
				tag.attr( 'data-status', 'published' );

			} else {
				tag.text( tag.data( 'draft' ) );
				tag.removeClass( 'sui-tag-blue' );
				tag.attr( 'data-status', 'draft' );
			}

			$this.find( 'span' ).toggleClass( 'sui-hidden' );

			// Update tracking data
			if ( item.hasClass( 'sui-accordion-item--open' ) ) {
				item.find( '.sui-accordion-open-indicator' ).trigger( 'click' ).trigger( 'click' );
			}
		},

		actionDisplayError( $this, data ) {

			const message = data.message,
				$dialog = $this.closest( '.sui-modal' ),
				$errorContainer = $dialog.find( '.sui-notice-error' ),
				$error = $errorContainer.find( 'p' );

			$error.html( message );
			Module.Utils.accessibleShow( $errorContainer, false );
		},

		openPreview( e ) {
			let $this = $( e.currentTarget ),
				id = $this.data( 'id' ),
				type = $this.data( 'type' );

			Module.preview.open( id, type );
		},

		openTrackingChart( e ) {

			let flexHeader = '';

			if ( e.target ) {

				if ( $( e.target ).closest( '.sui-accordion-item-action' ).length ) {
					return true;
				}

				e.preventDefault();
				e.stopPropagation();

				flexHeader = $( e.currentTarget );
			} else {
				flexHeader = e;
			}

			let self = this,
				flexItem   = flexHeader.parent(),
				flexChart  = flexItem.find( '.sui-chartjs-animated' )
				;

			if ( flexItem.hasClass( 'sui-accordion-item--disabled' ) ) {
				flexItem.removeClass( 'sui-accordion-item--open' );
			} else {
				if ( flexItem.hasClass( 'sui-accordion-item--open' ) ) {
					flexItem.removeClass( 'sui-accordion-item--open' );
				} else {
					flexItem.addClass( 'sui-accordion-item--open' );
				}
			}

			flexItem.find( '.sui-accordion-item-data' ).addClass( 'sui-onload' );
			flexChart.removeClass( 'sui-chartjs-loaded' );

			if ( flexItem.hasClass( 'sui-accordion-item--open' ) ) {
				let id = flexHeader.data( 'id' ),
					nonce = flexHeader.data( 'nonce' ),
					data = {
						id: id,
						'_ajax_nonce': nonce,
						action: 'hustle_tracking_data'
					};
				$.ajax({
					url: ajaxurl,
					type: 'POST',
					data: data,
					success: function( resp ) {
						if ( resp.success && resp.data ) {

							flexItem.find( '.sui-accordion-item-body' ).html( resp.data.html );

							Module.trackingChart.init( flexItem, resp.data.charts_data );

							flexChart  = flexItem.find( '.sui-chartjs-animated' );

							// Init tabs
							SUI.suiTabs();
						}
						flexItem.find( '.sui-accordion-item-data' ).removeClass( 'sui-onload' );
						flexChart.addClass( 'sui-chartjs-loaded' );
					},
					error: function( resp ) {
						flexItem.find( '.sui-accordion-item-data' ).removeClass( 'sui-onload' );
						flexChart.addClass( 'sui-chartjs-loaded' );
					}
				});

			}

		},

		getChecked: function( type ) {
			let query = '.sui-wrap .sui-accordion-item-title input[type=checkbox]';
			if ( 'checked' === type ) {
				query += ':checked';
			}
			return $( query );
		},

		bulkActionCheck: function( e ) {
			let $this = $( e.target ),
				value = $this.closest( '.hustle-bulk-actions-container' ).find( 'select[name="hustle_action"] option:selected' ).val(), //$( 'select option:selected', $this.closest( '.sui-box' ) ).val(),
				elements = this.getChecked( 'checked' );

			if ( 0 === elements.length || 'undefined' === value ) {
				return false;
			}

			if ( 'delete' === value ) {
				const data = {
					actionClass: 'hustle-delete',
					action: 'delete',
					title: $this.data( 'delete-title' ),
					description: $this.data( 'delete-description' )
				};
				Module.deleteModal.open( data );
				return false;

			} else if ( 'reset-tracking' === value ) {
				const data = {
					actionClass: 'hustle-delete',
					action: 'reset-tracking',
					title: $this.data( 'reset-title' ),
					description: $this.data( 'reset-description' )
				};

				Module.deleteModal.open( data );

				return false;
			}

			this.bulkActionSend( e, value );
		},

		bulkActionSend: function( e, action ) {
			e.preventDefault();

			this.addLoadingIcon( e );
			let button = $( '.sui-bulk-actions .hustle-bulk-apply-button' ),
				value = action ? action : $( e.target ).data( 'hustle-action' ),
				elements = this.getChecked( 'checked' );

			if ( 0 === elements.length ) {
				return false;
			}
			let ids = [];
			$.each( elements, function() {
				ids.push( $( this ).val() );
			});

			let data = {
				ids: ids,
				hustle: value,
				type: button.data( 'type' ),
				'_ajax_nonce': button.data( 'nonce' ),
				action: 'hustle_listing_bulk'
			};
			$.ajax({
				url: ajaxurl,
				type: 'POST',
				data: data,
				success: function( resp ) {
					if ( resp.success ) {
						location.reload();
					} else {
						SUI.dialogs['hustle-dialog--delete'].hide();

						//show error notice
					}
				}
			});
		},

		addLoadingIcon( e ) {
			const $button = $( e.currentTarget );
			if ( $button.hasClass( 'sui-button' ) ) {
				$button.addClass( 'sui-button-onload' );
			}
		},

		addLoadingIconToActionsButton( e ) {
			const $actionButton = $( e.currentTarget ),
				$mainButton = $actionButton.closest( '.sui-accordion-item-action' ).find( '.sui-dropdown-anchor' );

			$mainButton.addClass( 'sui-button-onload' );
		},

		// ===================================
		// Modals
		// ===================================

		openCreateModal( e ) {

			let page = '_page_hustle_sshare_listing';

			if ( false === $( e.currentTarget ).data( 'enabled' ) ) {
				this.openUpgradeModal();

			} else {

				if ( page !== pagenow.substr( pagenow.length - page.length ) ) {
					SUI.openModal(
						'hustle-new-module--type',
						'hustle-create-new-module',
						'hustle-new-module--type-close',
						false
					);
				} else {
					SUI.openModal(
						'hustle-new-module--create',
						'hustle-create-new-module',
						'hustle-module-name',
						false
					);
				}

				// SUI.dialogs['hustle-dialog--add-new-module'].show();
			}
		},

		openUpgradeModal( e ) {

			if ( e ) {
				e.preventDefault();
				e.stopPropagation();
			}

			$( '.sui-button-onload' ).removeClass( 'sui-button-onload' );

			if ( ! $( '#hustle-dialog--upgrade-to-pro' ).length ) {
				return;
			}

			SUI.dialogs['hustle-dialog--upgrade-to-pro'].show();

			return;
		},

		openDeleteModal( e ) {
			e.preventDefault();

			let $this = $( e.currentTarget ),
				data = {
					id: $this.data( 'id' ),
					nonce: $this.data( 'nonce' ),
					action: 'delete',
					title: $this.data( 'title' ),
					description: $this.data( 'description' ),
					actionClass: 'hustle-single-module-button-action'
				};

			Module.deleteModal.open( data );
		},

		openImportModal( e ) {

			const $this = $( e.currentTarget );

			if ( false === $this.data( 'enabled' ) ) {
				this.openUpgradeModal();

			} else {

				this.ImportModal.open( e );
			}
		},

		/**
		 * The "are you sure?" modal from before resetting the tracking data of modules.
		 * @since 4.0
		 */
		openResetTrackingModal( e ) {
			e.preventDefault();

			const $this = $( e.target ),
				data = {
					id: $this.data( 'module-id' ),
					nonce: this.singleModuleActionNonce,
					action: 'reset-tracking',
					title: $this.data( 'title' ),
					description: $this.data( 'description' ),
					actionClass: 'hustle-single-module-button-action'
				};

			Module.deleteModal.open( data );
		},

		openManageTrackingModal( e ) {
			const template = Optin.template( 'hustle-manage-tracking-form-tpl' ),
				$modal = $( '#hustle-dialog--manage-tracking' ),
				$button = $( e.currentTarget ),
				moduleId = $button.data( 'module-id' ),
				data = {

					//moduleID: $button.data( 'module-id' ),
					enabledTrackings: $button.data( 'tracking-types' ).split( ',' )
				};

			$modal.find( '#hustle-manage-tracking-form-container' ).html( template( data ) );
			$modal.find( '#hustle-button-toggle-tracking-types' ).data( 'module-id', moduleId );
			SUI.dialogs[ 'hustle-dialog--manage-tracking' ].show();
		}

	});
}( jQuery ) );

Hustle.define( 'Modals.New_Module', function( $ ) {

	'use strict';

	return Backbone.View.extend({
		el: '#hustle-new-module--dialog',
		data: {},
		events: {
			'click #hustle-select-mode': 'modeSelected',
			'keypress #hustle-new-module--type': 'maybeModeSelected',
			'click #hustle-create-module': 'createModule',
			'keypress #hustle-new-module--create': 'maybeCreateModule',
			'click #hustle-new-module--create-back': 'goToModeStep',
			'change input[name="mode"]': 'modeChanged',
			'keydown input[name="name"]': 'nameChanged'
		},

		initialize( args ) {
			_.extend( this.data, args );
		},

		modeChanged( e ) {
			var $this = $( e.target ),
				value = $this.val();
			this.data.mode = value;
			this.$el.find( '#hustle-select-mode' ).prop( 'disabled', false );
		},

		nameChanged( e ) {
			setTimeout( () => {
				this.$( '.sui-error-message' ).hide();
				let $this = $( e.target ),
					value = $this.val();
				this.data.name = value;
				if ( 0 === value.trim().length ) {
					this.$( '#hustle-create-module' ).prop( 'disabled', true );
					this.$( '#error-empty-name' ).closest( '.sui-form-field' ).addClass( 'sui-form-field-error' );
					this.$( '#error-empty-name' ).show();
				} else {
					this.$( '#hustle-create-module' ).prop( 'disabled', false );
					this.$( '#error-empty-name' ).closest( '.sui-form-field' ).removeClass( 'sui-form-field-error' );
					this.$( '#error-empty-name' ).hide();
				}
			}, 300 );
		},

		modeSelected( e ) {

			const newModalId        = 'hustle-new-module--create',
				newFocusAfterClosed = 'hustle-create-new-module',
				newFocusFirst       = 'hustle-module-name',
				hasOverlayMask      = false
				;

			this.$el.find( 'input[name="mode"]:checked' ).trigger( 'change' );

			if ( 0 === Object.keys( this.data ).length ) {
				return;
			}

			SUI.replaceModal( newModalId, newFocusAfterClosed, newFocusFirst, hasOverlayMask );

			e.preventDefault();

		},

		maybeCreateModule( e ) {

			if ( 13 === e.which ) { // the enter key code
				e.preventDefault();
				this.$( '#hustle-create-module' ).click();
			}
		},

		maybeModeSelected( e ) {

			if ( 13 === e.which ) { // the enter key code
				e.preventDefault();
				this.$( '#hustle-select-mode' ).click();
			}
		},

		goToModeStep( e ) {

			const newModalId        = 'hustle-new-module--type',
				newFocusAfterClosed = 'hustle-create-new-module',
				newFocusFirst       = 'hustle-new-module--type-close',
				hasOverlayMask      = false
				;

			SUI.replaceModal( newModalId, newFocusAfterClosed, newFocusFirst, hasOverlayMask );

			e.preventDefault();

		},

		createModule( e ) {
			let $step = $( e.target ).closest( '#hustle-new-module--create' ),
				$errorSavingMessage = $step.find( '#error-saving-settings' ),
				$button = $step.find( '#hustle-create-module' ),
				nonce = $step.data( 'nonce' );

			if (
				( 'undefined' === typeof this.data.mode && 'social_sharing' !== this.data.moduleType ) ||
				'undefined' === typeof this.data.name || 0 === this.data.name.length
			) {
				$errorSavingMessage.show();
				$button.removeClass( 'sui-button-onload' );
				return;
			}

			$errorSavingMessage.hide();
			$button.addClass( 'sui-button-onload' );

			$.ajax({
				url: ajaxurl,
				type: 'POST',
				data: {
					data: {
						'module_name': this.data.name,
						'module_mode': this.data.mode,
						'module_type': this.data.moduleType
					},
					action: 'hustle_create_new_module',
					'_ajax_nonce': nonce
				}

			}).done( function( res ) {

				// Go to the wizard of this type of module on success, or listing page is limits were reached.
				if ( res && res.data && res.data.redirect_url ) {
					window.location.replace( res.data.redirect_url );
				} else {
					$errorSavingMessage.show();
					$button.removeClass( 'sui-button-onload' );
				}
			}).fail( function() {
				$errorSavingMessage.show();
				$button.removeClass( 'sui-button-onload' );
			});
		}

	});
});

Hustle.define( 'Modals.ImportModule', function( $ ) {
	'use strict';

	return Backbone.View.extend({
		el: '#hustle-dialog--import',

		events: {
			'change #hustle-import-file-input': 'selectUploadFile',
			'click .sui-upload-file': 'changeFile',
			'click .sui-upload-file button': 'resetUploadFile',
			'click .hustle-import-check-all-checkbox': 'checkAll',
			'change .hustle-module-meta-checkbox': 'uncheckAllOption'
		},

		initialize() {},

		open( e ) {

			const $this = $( e.currentTarget ),
				moduleId = $this.data( 'module-id' ),
				template = Optin.template( 'hustle-import-modal-options-tpl' ),
				$importDialog = $( '#hustle-dialog--import' ),
				$submitButton = $importDialog.find( '#hustle-import-module-submit-button' ),
				isNew = 'undefined' === typeof moduleId,
				templateData = {
					isNew,
					isOptin: 'optin' === $this.data( 'module-mode' ) // Always "false" when importing into a new module.
				};

			$importDialog.find( '#hustle-import-modal-options' ).html( template( templateData ) );

			if ( isNew ) {
				$submitButton.removeAttr( 'data-module-id' );

				// Bind the tabs again with their SUI actions.
				// Only the modal for importing a new module has tabs.
				SUI.tabs();

				$importDialog.find( '.sui-tab-item' ).on( 'click', function() {

					const $this = $( this ),
						$radio = $( '#' + $this.data( 'label-for' ) );

					$radio.click();
				});

			} else {
				$submitButton.attr( 'data-module-id', moduleId );
			}

			SUI.openModal( 'hustle-dialog--import', e.currentTarget, 'hustle-import-file-input', true );
		},

		selectUploadFile( e ) {

			e.preventDefault();

			let $this = $( e.target ),
				value = $this.val().replace( /C:\\fakepath\\/i, '' );

			//hide previous error
			Module.Utils.accessibleHide( $( '#hustle-dialog--import .sui-notice-error' ), false );

			if ( value ) {
				$( '.sui-upload-file span:first' ).text( value );
				$( '.sui-upload' ).addClass( 'sui-has_file' );
				$( '#hustle-import-module-submit-button' ).prop( 'disabled', false );
			} else {
				$( '.sui-upload' ).removeClass( 'sui-has_file' );
				$( '.sui-upload-file span:first' ).text( '' );
				$( '#hustle-import-module-submit-button' ).prop( 'disabled', true );
			}
		},

		resetUploadFile( e ) {
			e.stopPropagation();
			$( '#hustle-import-file-input' ).val( '' ).trigger( 'change' );
		},

		changeFile( e ) {
			$( '#hustle-import-file-input' ).trigger( 'click' );
		},

		checkAll( e ) {
			const $this = $( e.currentTarget ),
				value = $this.is( ':checked' ),
				$container = $this.closest( '.hui-inputs-list' ),
				$checkboxes = $container.find( 'input.hustle-module-meta-checkbox:not(.hustle-import-check-all-checkbox)' );

			$checkboxes.prop( 'checked', value );
		},

		uncheckAllOption( e ) {
			const $this = $( e.currentTarget ),
				$container = $this.closest( '.hui-inputs-list' ),
				$allCheckbox = $container.find( '.hustle-import-check-all-checkbox' ),
				isAllChecked = $allCheckbox.is( ':checked' );

			if ( ! isAllChecked ) {
				return;
			}

			$allCheckbox.prop( 'checked', false );
		}

	});
});

Hustle.define( 'Mixins.Model_Updater', function( $, doc, win ) {
	'use strict';
	return {

		initMix: function() {
			this.events = _.extend({}, this.events, this._events );
			this.delegateEvents();
		},

		_events: {
			'change textarea': '_updateText',
			'change input[type="text"]': '_updateText',
			'change input[type="url"]': '_updateText',
			'change input[type="hidden"]': '_updateText',
			'change input[type="number"]': '_updateText',
			'change input[type="checkbox"]': '_updateCheckbox',
			'change input[type=radio]': '_updateRadios',
			'change select': '_updateSelect'
		},

		_updateText: function( e ) {
			var $this = $( e.target ),
				attr = $this.data( 'attribute' ),
				model = this[ $this.data( 'model' ) || 'model' ],
				opts = _.isTrue( $this.data( 'silent' ) ) ? { silent: true } : {};
			if ( model && attr ) {
				e.stopPropagation();
				model.set.call( model, attr, e.target.value, opts );
			}
		},

		_updateCheckbox: function( e ) {
			var $this = $( e.target ),
				attr = $this.data( 'attribute' ),
				value = $this.val(),
				model = this[$this.data( 'model' ) || 'model'],
				opts = _.isTrue( $this.data( 'silent' ) ) ? { silent: true } : {};
			if ( model && attr ) {
				e.stopPropagation();

				// If the checkboxes values should behave as an array, instead of as an on/off toggle.
				if ( 'on' !== value ) {
					let current = model.get.call( model, attr );
					if ( $this.is( ':checked' ) ) {
						current.push( value );
					} else {
						current = _.without( current, value );
					}
					model.set.call( model, attr, current, opts );
				} else {
					model.set.call( model, attr, $this.is( ':checked' ) ? 1 : 0, opts );
				}
			}
		},

		_updateRadios: function( e ) {
			var $this = $( e.target ),
				attribute = $this.data( 'attribute' ),
				model = this[$this.data( 'model' ) || 'model'],
				opts = _.isTrue( $this.data( 'silent' ) ) ? {silent: true} : {};
			if ( model && attribute ) {
				e.stopPropagation();
				model.set.call( model, attribute, e.target.value, opts );
			}
		},

		_updateSelect: function( e ) {
			var $this = $( e.target ),
				attr = $this.data( 'attribute' ),
				model = this[$this.data( 'model' ) || 'model'],
				opts = _.isTrue( $this.data( 'silent' ) ) ? {silent: true} : {};
			if ( model && attr ) {
				e.stopPropagation();
				model.set.call( model, attr, $this.val(), opts );
			}
		}
	};
});

Hustle.define( 'Mixins.Module_Settings', function( $, doc, win ) {

	'use strict';

	return _.extend({}, Hustle.get( 'Mixins.Model_Updater' ), {

		el: '#hustle-wizard-behaviour',

		events: {},

		init( opts ) {

			const Model = opts.BaseModel.extend({
				defaults: {},
				initialize: function( data ) {
					_.extend( this, data );

					const Triggers = Hustle.get( 'Models.Trigger' );

					if ( ! ( this.get( 'triggers' ) instanceof Backbone.Model ) ) {
						this.set( 'triggers', new Triggers( this.triggers ), { silent: true });
					}
				}
			});

			this.model = new Model( optinVars.current.settings || {});
			this.moduleType = optinVars.current.data.module_type;

			this.listenTo( this.model, 'change', this.viewChanged );
			if ( 'embedded' !== this.moduleType ) {
				this.listenTo( this.model.get( 'triggers' ), 'change', this.viewChanged );
			}

			// Called just to trigger the "view.rendered" action.
			this.render();

			return this;
		},

		render() {},

		viewChanged: function( model ) {

			var changed = model.changed;

			if ( 'on_scroll' in changed ) {
				let $scrolledContentDiv = this.$( '#hustle-on-scroll--scrolled-toggle-wrapper' ),
					$selectorContentDiv = this.$( '#hustle-on-scroll--selector-toggle-wrapper' );

				if ( $scrolledContentDiv.length || $selectorContentDiv.length ) {
					if ( 'scrolled' === changed.on_scroll ) {
						$scrolledContentDiv.removeClass( 'sui-hidden' );
						$selectorContentDiv.addClass( 'sui-hidden' );
					} else {
						$selectorContentDiv.removeClass( 'sui-hidden' );
						$scrolledContentDiv.addClass( 'sui-hidden' );
					}
				}
			}

			if ( 'on_submit' in changed ) {
				let $toggleDiv = this.$( '#hustle-on-submit-delay-wrapper' );
				if ( $toggleDiv.length ) {
					if ( 'nothing' !== changed.on_submit ) {
						$toggleDiv.removeClass( 'sui-hidden' );
					} else {
						$toggleDiv.addClass( 'sui-hidden' );
					}
				}

			}

		}

	});
});

Hustle.define( 'Mixins.Module_Content', function( $, doc, win ) {

	'use strict';

	return _.extend({}, Hustle.get( 'Mixins.Model_Updater' ), {

		el: '#hustle-wizard-content',

		events: {},

		init( opts ) {
			this.model = new opts.BaseModel( optinVars.current.content || {});
			this.moduleType  = optinVars.current.data.module_type;

			this.listenTo( this.model, 'change', this.modelUpdated );

			this.render();
		},

		render() {

			this.renderFeaturedImage();

			if ( 'true' ===  Module.Utils.getUrlParam( 'new' ) ) {
				Module.Notification.open( 'success', optinVars.messages.commons.module_created.replace( /{type_name}/g, optinVars.module_name[ this.moduleType ]), 10000 );
			}
		},

		renderFeaturedImage() {

			if ( ! this.$( '#wph-wizard-choose_image' ).length ) {
				return;
			}

			const MediaHolder = Hustle.get( 'Featured_Image_Holder' );
			this.mediaHolder = new MediaHolder({
				model: this.model,
				attribute: 'feature_image',
				moduleType: this.moduleType
			});
		},

		modelUpdated( model ) {
			let changed = model.changed;

			// Update module_name from the model when changed.
			if ( 'module_name' in changed ) {
				this.model.set( 'module_name', changed.module_name, { silent: true });
			}
			if ( 'feature_image' in changed ) {

				// Uploading a featured image makes the "Featured Image settings" show up in the "Appearance" tab.
				Hustle.Events.trigger( 'modules.view.feature_image_updated', changed );
			}
		}
	});
});

Hustle.define( 'Mixins.Module_Design', function( $, doc, win ) {

	'use strict';

	return _.extend({}, Hustle.get( 'Mixins.Model_Updater' ), {

		el: '#hustle-wizard-appearance',

		cssEditor: false,

		events: {
			'click .hustle-css-stylable': 'insertSelector',
			'click .hustle-reset-color-palette': 'resetPickers'
		},

		init( opts ) {

			this.model = new opts.BaseModel( optinVars.current.design || {});

			this.listenTo( this.model, 'change', this.viewChanged );

			// Update the Appearance tab view when "Feature image" is changed in the Content tab.
			Hustle.Events.off( 'modules.view.feature_image_updated' ).on( 'modules.view.feature_image_updated', $.proxy( this.ViewChangedContentTab, this ) );

			this.render();
		},

		render() {

			this.createPickers();
			this.addCreatePalettesLink();

			this.createEditor();
			this.cssChange();
		},

		// ============================================================
		// Color Pickers
		createPickers: function() {

			var self = this,
				$suiPickerInputs = this.$( '.sui-colorpicker-input' );

			$suiPickerInputs.wpColorPicker({

				change: function( event, ui ) {
					var $this = $( this );

					// Prevent the model from being marked as changed on load.
					if ( $this.val() !== ui.color.toCSS() ) {
						$this.val( ui.color.toCSS() ).trigger( 'change' );
					}
				},
				palettes: [
					'#333333',
					'#FFFFFF',
					'#17A8E3',
					'#E1F6FF',
					'#666666',
					'#AAAAAA',
					'#E6E6E6'
				]
			});

			if ( $suiPickerInputs.hasClass( 'wp-color-picker' ) ) {

				$suiPickerInputs.each( function() {

					var $suiPickerInput = $( this ),
						$suiPicker      = $suiPickerInput.closest( '.sui-colorpicker-wrap' ),
						$suiPickerColor = $suiPicker.find( '.sui-colorpicker-value span[role=button]' ),
						$suiPickerValue = $suiPicker.find( '.sui-colorpicker-value' ),
						$suiPickerClear = $suiPickerValue.find( 'button' ),
						$suiPickerType  = 'hex'
						;

					var $wpPicker       = $suiPickerInput.closest( '.wp-picker-container' ),
						$wpPickerButton = $wpPicker.find( '.wp-color-result' ),
						$wpPickerAlpha  = $wpPickerButton.find( '.color-alpha' ),
						$wpPickerClear  = $wpPicker.find( '.wp-picker-clear' )
						;

					// Check if alpha exists
					if ( true === $suiPickerInput.data( 'alpha' ) ) {

						$suiPickerType = 'rgba';

						// Listen to color change
						$suiPickerInput.bind( 'change', function() {

							// Change color preview
							$suiPickerColor.find( 'span' ).css({
								'background-color': $wpPickerAlpha.css( 'background' )
							});

							// Change color value
							$suiPickerValue.find( 'input' ).val( $suiPickerInput.val() );

						});

					} else {

						// Listen to color change
						$suiPickerInput.bind( 'change', function() {

							// Change color preview
							$suiPickerColor.find( 'span' ).css({
								'background-color': $wpPickerButton.css( 'background-color' )
							});

							// Change color value
							$suiPickerValue.find( 'input' ).val( $suiPickerInput.val() );

						});
					}

					// Add picker type class
					$suiPicker.find( '.sui-colorpicker' ).addClass( 'sui-colorpicker-' + $suiPickerType );

					// Open iris picker
					$suiPicker.find( '.sui-button, span[role=button]' ).on( 'click', function( e ) {

						$wpPickerButton.click();

						e.preventDefault();
						e.stopPropagation();

					});

					// Clear color value
					$suiPickerClear.on( 'click', function( e ) {

						let inputName = $suiPickerInput.data( 'attribute' ),
							selectedStyle = self.model.get( 'color_palette' ),
							resetValue = optinVars.palettes[ selectedStyle ][ inputName ];

						$wpPickerClear.click();
						$suiPickerValue.find( 'input' ).val( resetValue );
						$suiPickerInput.val( resetValue ).trigger( 'change' );
						$suiPickerColor.find( 'span' ).css({
							'background-color': resetValue
						});

						e.preventDefault();
						e.stopPropagation();

					});
				});
			}
		},

		updatePickers: function( selectedStyle ) {

			let self = this;

			if ( 'undefined' !== typeof optinVars.palettes[ selectedStyle ]) {

				let colors = optinVars.palettes[ selectedStyle ];

				// update color palettes
				_.each( colors, function( color, key ) {
					self.$( 'input[data-attribute="' + key + '"]' ).val( color ).trigger( 'change' );
				});
			}

			// TODO: else, display an error message.
		},

		resetPickers: function( e ) {
			let $el = $( e.target );
			$el.addClass( 'sui-button-onload' ).prop( 'disabled', true );

			let style = $( 'select[data-attribute="color_palette"]' ).val();
			this.updatePickers( style );

			setTimeout( function() {
				$el.removeClass( 'sui-button-onload' ).prop( 'disabled', false );
			}, 500 );
		},

		/**
		 * Add the "Create custom palette button" to the existing palettes dropdown.
		 * @since 4.0.3
		 */
		addCreatePalettesLink() {

			const $link = this.$( '#hustle-create-palette-link' ),
				$selectPaletteContainer = this.$( '.select-container.hui-select-palette .list-results' ),
				$selectButton = $selectPaletteContainer.find( '.hui-button' );

			if ( ! $selectButton.length ) {
				$selectPaletteContainer.append( $link );
			}

		},

		// ============================================================
		// CSS Editor
		createEditor: function() {

			this.cssEditor = ace.edit( 'hustle_custom_css' );

			this.cssEditor.getSession().setMode( 'ace/mode/css' );
			this.cssEditor.$blockScrolling = Infinity;
			this.cssEditor.setTheme( 'ace/theme/sui' );
			this.cssEditor.getSession().setUseWrapMode( true );
			this.cssEditor.getSession().setUseWorker( false );
			this.cssEditor.setShowPrintMargin( false );
			this.cssEditor.renderer.setShowGutter( true );
			this.cssEditor.setHighlightActiveLine( true );

		},

		updateCustomCss: function() {

			if ( this.cssEditor ) {
				this.model.set( 'custom_css', this.cssEditor.getValue() );
			}
		},

		cssChange: function() {
			var self = this;
			this.cssEditor.getSession().on( 'change', function() {
				self.model.userHasChange();
			});
		},

		insertSelector: function( e ) {

			var $el = $( e.target ),
				stylable = $el.data( 'stylable' ) + '{}';

			this.cssEditor.navigateFileEnd();
			this.cssEditor.insert( stylable );
			this.cssEditor.navigateLeft( 1 );
			this.cssEditor.focus();

			e.preventDefault();

		},

		// ============================================================
		// Adjust the view when model is updated
		viewChanged: function( model ) {

			let changed = model.changed;

			// Show or hide the positions available for each form layout.
			if ( 'form_layout' in changed ) {

				let $divSection  = this.$( '#hustle-feature-image-position-option' ),
					$targetAbove = this.$( '#hustle-feature-image-above-label' ),
					$targetBelow = this.$( '#hustle-feature-image-below-label' )
					;

				if ( $targetAbove.length || $targetBelow.length ) {

					if ( 'one' === changed.form_layout ) {
						$targetAbove.removeClass( 'sui-hidden' );
						$targetBelow.removeClass( 'sui-hidden' );

					} else {
						let $imgPosition = model.get( 'feature_image_position' );

						if ( 'left' !== $imgPosition && 'right' !== $imgPosition ) {
							$divSection.find( 'input' ).prop( 'checked', false );
							$divSection.find( '#hustle-feature-image-left' ).prop( 'checked', true );
							this.model.set( 'feature_image_position', 'left' );
							$divSection.find( '.sui-tab-item' ).removeClass( 'active' );
							$divSection.find( '#hustle-feature-image-left-label' ).addClass( 'active' );
						}

						$targetAbove.addClass( 'sui-hidden' );
						$targetBelow.addClass( 'sui-hidden' );

					}
				}
			}

			// Styles
			if ( 'color_palette' in changed ) {
				this.updatePickers( changed.color_palette );
			}

			if ( 'feature_image_horizontal' in changed ) {

				let $target = this.$( '#hustle-image-custom-position-horizontal' );

				if ( $target.length ) {

					if ( 'custom' !== changed.feature_image_horizontal ) {
						$target.prop( 'disabled', true );
					} else {
						$target.prop( 'disabled', false );
					}
				}
			} else if ( 'feature_image_vertical' in changed ) {

				let $target = this.$( '#hustle-image-custom-position-vertical' );

				if ( $target.length ) {

					if ( 'custom' !== changed.feature_image_vertical ) {
						$target.prop( 'disabled', true );
					} else {
						$target.prop( 'disabled', false );
					}
				}
			}
		},

		// Handle the changes on the Appearance tab due to Content tab changes
		ViewChangedContentTab( changed ) {

			if ( 'feature_image' in changed ) {

				let $divPlaceholder = this.$( '#hustle-appearance-feature-image-placeholder' ),
					$divSettings = this.$( '#hustle-appearance-feature-image-settings' )
					;

				if ( $divPlaceholder.length && $divSettings.length ) {

					if ( changed.feature_image ) {

						// Hide feature image settings.
						$divSettings.show();

						// Hide disabled message
						$divPlaceholder.hide();

					} else {

						// Hide feature image settings.
						$divSettings.hide();

						// Show disabled message.
						$divPlaceholder.show();

					}
				}
			}
		}
	});
});

Hustle.define( 'Mixins.Module_Display', function( $, doc, win ) {

	'use strict';

	return _.extend({}, Hustle.get( 'Mixins.Model_Updater' ), {

		el: '#hustle-wizard-display',

		events: {},

		init( opts ) {

			this.model = new opts.BaseModel( optinVars.current.display || {});
			this.moduleType  = optinVars.current.data.module_type;

			this.listenTo( this.model, 'change', this.viewChanged );

			// Called just to trigger the "view.rendered" action.
			this.render();
		},

		render() {},

		viewChanged( model ) {}

	});
});

Hustle.define( 'Mixins.Module_Emails', function( $, doc, win ) {

	'use strict';

	return _.extend({}, Hustle.get( 'Mixins.Model_Updater' ), {

		el: '#hustle-wizard-emails',

		events: {
			'click .hustle-optin-field--add': 'addFields',
			'click .hustle-optin-field--edit': 'editField',
			'click .sui-builder-field': 'maybeEditField',
			'click .hustle-optin-field--delete': 'deleteFieldOnClick',
			'click ul.list-results li': 'setFieldOption',
			'click .hustle-optin-field--copy': 'duplicateField'
		},

		init( opts ) {
			this.model = new opts.BaseModel( optinVars.current.emails || {});
			this.listenTo( this.model, 'change', this.viewChanged );

			this.render();
		},

		render() {
			let self = this,
				formElements = this.model.get( 'form_elements' );

			// Add the already stored form fields to the panel.
			for ( let fieldId in formElements ) {
				let field = formElements[ fieldId ];

				// Assign the defaults for the field, in case there's anything missing.
				formElements[ fieldId ] = _.extend({}, this.getFieldDefaults( field.type ), field );

				// Submit is already at the bottom of the panel. We don't want to add it again.
				if ( 'submit' === fieldId ) {
					continue;
				}
				self.addFieldToPanel( formElements[ fieldId ]);
			}

			// update form_elements for having default properties if they were lost for some reason
			this.model.set( 'form_elements', formElements, { silent: true });

			// Initiate the sortable functionality to sort form fields' order.
			let sortableContainer = this.$( '#hustle-form-fields-container' ).sortable({
				axis: 'y',
				containment: '.sui-box-builder'
			});

			sortableContainer.on( 'sortupdate', $.proxy( self.fieldsOrderChanged, self, sortableContainer ) );

			this.updateDynamicValueFields();

			return this;
		},

		//reset all field selects
		resetDynamicValueFieldsPlaceholders() {

			this.$( 'select.hustle-field-options' ).html( '' );

			if ( this.$( '.hustle-fields-placeholders-options' ).length ) {
				this.$( '.hustle-fields-placeholders-options' ).html( '' );
			}
		},

		//update all field selects
		updateDynamicValueFields() {
			let formElements = this.model.get( 'form_elements' );

			this.resetDynamicValueFieldsPlaceholders();

			for ( let fieldId in formElements ) {

				if ( 'submit' === fieldId || 'recaptcha' === fieldId || 'gdpr' === fieldId ) {
					continue;
				}

				this.addFieldToDynamicValueFields( formElements[ fieldId ]);
				this.$( 'select.hustle-field-options' ).trigger( 'sui:change' );

			}

			//set info notice for empty dynamic fields select
			this.$( 'div.select-list-container .list-results:empty' ).each( function() {
				let fieldType = $( this ).closest( '.select-container' ).find( 'select.hustle-field-options' ).data( 'type' );
				$( this ).html( '<li style="cursor: default; pointer-events: none;">' + optinVars.messages.form_fields.errors.no_fileds_info.replace( '{field_type}', fieldType ) + '</li>' );
			});

		},

		/**
		 * Assign the new field order to the model. Triggered when the fields are sorted.
		 * @since 4.0
		 * @param jQuery sortable object
		 */
		fieldsOrderChanged( sortable ) {

			let formElements = this.model.get( 'form_elements' ),
				newOrder = sortable.sortable( 'toArray', { attribute: 'data-field-id' }),
				orderedFields = {};

			for ( let id of newOrder ) {
				orderedFields[ id ] = formElements[ id ] ;
			}

			orderedFields = _.extend({}, orderedFields, formElements );

			this.model.set( 'form_elements', orderedFields );

		},

		/**
		 * Handle the changes in the view when the model is updated.
		 * @since 4.0
		 * @param emails_model model
		 */
		viewChanged( model ) {
			var changed = model.changed;

			// Show or hide the content dependent of auto_close_success_message.
			if ( 'auto_close_success_message' in changed ) {
				let $targetDiv = this.$( '#section-auto-close-success-message .sui-row' );

				if ( $targetDiv.length ) {
					if ( '1' === changed.auto_close_success_message ) {
						$targetDiv.removeClass( 'sui-hidden' );
					} else {
						$targetDiv.addClass( 'sui-hidden' );
					}
				}

			}

			if ( 'form_elements' in changed ) {
				this.updateDynamicValueFields();
			}

		},

		/**
		 * Open the "Add new fields" modal.
		 * @since 4.0
		 */
		addFields() {

			let OptinFieldsModalView = Hustle.get( 'Modals.Optin_Fields' ),
				newFieldModal = new OptinFieldsModalView();

			// Create the fields and append them to panel.
			newFieldModal.on( 'fields:added', $.proxy( this.addNewFields, this ) );

			// Show dialog
			SUI.dialogs['hustle-dialog--optin-fields'].show();

		},

		maybeEditField( e ) {
			let $ct = $( e.target );

			if ( ! $ct.closest( '.sui-dropdown' ).length ) {
				this.editField( e );
			}

		},

		/**
		 * Open the "edit field" modal.
		 * @since 4.0
		 * @param event e
		 */
		editField( e ) {

			let $button = $( e.target ),
				fieldId = $button.closest( '.sui-builder-field' ).data( 'field-id' ),
				existingFields = this.model.get( 'form_elements' ),
				field = existingFields[ fieldId ],
				fieldData = Object.assign({}, this.getFieldViewDefaults( field.type ), field ),
				EditFieldModalView = Hustle.get( 'Modals.Edit_Field' ),
				editModalView = new EditFieldModalView({
					field,
					fieldData,
					model: this.model
				});

			editModalView.on( 'field:updated', $.proxy( this.formFieldUpdated, this ) );

			// Show dialog
			SUI.dialogs['hustle-dialog--edit-field'].show();

		},

		/**
		 * Update the appearance of the form field row of the field that was updated.
		 * @since 4.0
		 * @param object updatedField Object with the properties of the updated field.
		 */
		formFieldUpdated( updatedField, changed, oldField ) {

			if ( ! Object.keys( changed ).length ) {
				return;
			}

			// Name is the unique identifier.
			// If it changed, update the existing fields removing the old one and creating a new one.
			if ( 'name' in changed ) {
				this.addNewFields( updatedField.type, updatedField, oldField.name );
				this.deleteField( oldField.name );
				return;
			}

			let $fieldRow = this.$( '#hustle-optin-field--' + updatedField.name );

			if ( 'required' in changed ) {

				let $requiredTag = $fieldRow.find( '.sui-error' ),
					isRequired = updatedField.required;

				// Show the "required" asterisk to this field's row.
				if ( _.isTrue( isRequired ) ) {
					$requiredTag.show();

				} else if (  _.isFalse( isRequired ) ) {

					// Hide the "required" asterisk to this field's row.
					$requiredTag.hide();
				}

			}

			if ( 'label' in changed ) {

				this.updateDynamicValueFields();

				let $labelWrapper = $fieldRow.find( '.hustle-field-label-text' );
				$labelWrapper.text( updatedField.label );
			}

		},

		deleteFieldOnClick( e ) {

			let $button = $( e.target ),
				fieldName = $button.closest( '.sui-builder-field' ).data( 'field-id' );

			this.deleteField( fieldName );
		},

		setFieldOption( e ) {
			let $li = $( e.target ),
				val = $li.find( 'span:eq(1)' ).text(),
				$input = $li.closest( '.sui-insert-variables' ).find( 'input[type="text"]' );

			$input.val( val ).trigger( 'change' );
		},

		deleteField( fieldName ) {

			let $fieldRow = this.$( '#hustle-optin-field--' + fieldName ),
				formElements = Object.assign({}, this.model.get( 'form_elements' ) );

			delete formElements[ fieldName ];

			this.model.set( 'form_elements', formElements );

			if ( -1 !== jQuery.inArray( fieldName, [ 'gdpr', 'recaptcha' ]) ) {
				$fieldRow.addClass( 'sui-hidden' );
				$( '#hustle-optin-insert-field--' + fieldName ).prop( 'disabled', false ).prop( 'checked', false );
			} else {
				$fieldRow.remove();
			}
		},

		duplicateField( e ) {

			let $button = $( e.target ),
				fieldId = $button.closest( '.sui-builder-field' ).data( 'field-id' ),
				formElements = Object.assign({}, this.model.get( 'form_elements' ) ),
				duplicatedField = Object.assign({}, formElements[ fieldId ]);

			// Remove 'name' because it should be an unique identifier. Will be added in 'add_new_fields'.
			delete duplicatedField.name;

			// Make the field deletable because it can't be deleted otherwise, and you'll have it stuck forevah.
			duplicatedField.can_delete = true; // eslint-disable-line camelcase

			this.addNewFields( duplicatedField.type, duplicatedField );
		},

		/**
		 * Used to add new fields.
		 * When using form_fields, make sure only 1 type of each field is added.
		 * In other words, use field.type as an unique identifier.
		 * @since 4.0
		 * @param array|string form_fields
		 * @param object form_fields_data
		 */
		addNewFields( formFields, formFieldsData, after = null ) {
			let self = this,
				existingFields = Object.assign({}, this.model.get( 'form_elements' ) );
			if ( Array.isArray( formFields ) ) {
				for ( let field of formFields ) {
					let fieldData = self.getFieldDefaults( field );
					if ( formFieldsData && field in formFieldsData ) {
						_.extend( fieldData, formFieldsData[ field ]);
					}
					self.addFieldToPanel( fieldData );
					existingFields[ fieldData.name ] = fieldData;
				}
			} else {
				let fieldData = self.getFieldDefaults( formFields );
				if ( formFieldsData ) {
					_.extend( fieldData, formFieldsData );
				}
				self.addFieldToPanel( fieldData, after );
				if ( null === after ) {
					existingFields[ fieldData.name ] = fieldData;
				} else {
					let reorderExistingFields = [];
					jQuery.each( existingFields, function( index, data ) {
						reorderExistingFields[ index ] = data;
						if ( index === after ) {
							reorderExistingFields[ fieldData.name ] = fieldData;
						}
					});
					existingFields = reorderExistingFields;
				}
			}
			this.model.set( 'form_elements', existingFields );
		},

		/**
		 * Add a field to the fields with dynamic values for the automated emails.
		 * The field object must have all its core prop assigned. The views prop are assigned here.
		 * @since 4.0
		 * @param object field
		 */
		addFieldToDynamicValueFields( field ) {
			let option = $( '<option/>', {
				value: field.name,
				'data-content': '{' + field.name + '}'
			}).text( field.label ),
				listOption = `<li><button value="{${field.name}}">${field.label}</button></li>`;

			this.$( 'select.hustle-field-options:not([data-type]), select.hustle-field-options[data-type="' + field.type + '"]' ).append( option );

			if ( this.$( '.hustle-fields-placeholders-options' ).length ) {
				this.$( '.hustle-fields-placeholders-options' ).append( listOption );
			}
		},

		/**
		 * Add a field to the fields pannel.
		 * The field object must have all its core prop assigned. The views prop are assigned here.
		 * @since 4.0
		 * @param object field
		 */
		addFieldToPanel( field, after = null ) {
			let template = Optin.template( 'hustle-form-field-row-tpl' ),
				$fieldsContainer = this.$( '#hustle-form-fields-container' );
			field = _.extend({}, this.getFieldViewDefaults( field.type ), field );
			if ( -1 !== jQuery.inArray( field.type, [ 'gdpr', 'recaptcha' ]) ) {
				this.$( '#hustle-optin-field--' + field.type ).removeClass( 'sui-hidden' );
				$( '#hustle-optin-insert-field--' + field.type ).prop( 'checked', true ).prop( 'disabled', true );
			} else {
				if ( null === after ) {
					$fieldsContainer.append( template( field ) );
				} else {
					let $el = this.$( '#hustle-optin-field--' + after );
					if ( 0 < $el.length ) {
						$el.after( template( field ) );
					} else {
						$fieldsContainer.append( template( field ) );
					}
				}
			}
		},

		getNewFieldId( fieldName ) {
			let existingFields = Object.assign({}, this.model.get( 'form_elements' ) ),
				fieldId = fieldName;
			while ( fieldId in existingFields && -1 === jQuery.inArray( fieldId, [ 'gdpr', 'recaptcha', 'submit' ]) ) {
				fieldId = fieldName + '-' + Math.floor( Math.random() * 99 );
			}
			return fieldId;
		},

		/**
		 * Retrieve the default settings for each field type.
		 * These are going to be stored.
		 * @since 4.0
		 * @param string field_type. The field type.
		 */
		getFieldDefaults( fieldType ) {
			let fieldId = this.getNewFieldId( fieldType ),
				defaults = {
					label: optinVars.messages.form_fields.label[fieldType + '_label'],
					required: 'false',
					'css_classes': '',
					type: fieldType,
					name: fieldId,
					'required_error_message': optinVars.messages.required_error_message.replace( '{field}', fieldType ),
					'validation_message': optinVars.messages.validation_message.replace( '{field}', fieldType ),
					placeholder: ''
				};

				switch ( fieldType ) {
					case 'timepicker':
						defaults.time_format = '12'; // eslint-disable-line camelcase
						defaults.time_hours = '9'; // eslint-disable-line camelcase
						defaults.time_minutes = '30'; // eslint-disable-line camelcase
						defaults.time_period = 'am'; // eslint-disable-line camelcase
						defaults.validation_message = optinVars.messages.time_validation_message; // eslint-disable-line camelcase
						defaults.required_error_message = optinVars.messages.is_required.replace( '{field}', defaults.label ); // eslint-disable-line camelcase
						defaults.validate = 'false';
						break;
					case 'datepicker':
						defaults.date_format = 'mm/dd/yy'; // eslint-disable-line camelcase
						defaults.validation_message = optinVars.messages.date_validation_message; // eslint-disable-line camelcase
						defaults.required_error_message = optinVars.messages.is_required.replace( '{field}', defaults.label ); // eslint-disable-line camelcase
						defaults.validate = 'false';
						break;
					case 'recaptcha':
						defaults.threshold = '0.5'; // eslint-disable-line camelcase
						defaults.version = 'v2_checkbox'; // eslint-disable-line camelcase
						defaults.recaptcha_type = 'compact'; // eslint-disable-line camelcase
						defaults.recaptcha_theme = 'light'; // eslint-disable-line camelcase
						defaults.v2_invisible_theme = 'light'; // eslint-disable-line camelcase
						defaults.recaptcha_language = 'automatic'; // eslint-disable-line camelcase
						defaults.v2_invisible_show_badge = '1'; // eslint-disable-line camelcase
						defaults.v2_invisible_badge_replacement = optinVars.messages.form_fields.recaptcha_badge_replacement; // eslint-disable-line camelcase
						defaults.v3_recaptcha_show_badge = '1'; // eslint-disable-line camelcase
						defaults.v3_recaptcha_badge_replacement = optinVars.messages.form_fields.recaptcha_badge_replacement; // eslint-disable-line camelcase
						defaults.validation_message = optinVars.messages.recaptcha_validation_message; // eslint-disable-line camelcase
						defaults.error_message = optinVars.messages.form_fields.recaptcha_error_message; // eslint-disable-line camelcase
						break;
					case 'gdpr':
						defaults.gdpr_message = optinVars.messages.form_fields.gdpr_message; // eslint-disable-line camelcase
						defaults.required = 'true';
						defaults.required_error_message = optinVars.messages.gdpr_required_error_message; // eslint-disable-line camelcase
						break;
					case 'email':
						defaults.validate = 'true';
						break;
					case 'url':
						defaults.required_error_message = optinVars.messages.url_required_error_message; // eslint-disable-line camelcase
						defaults.validate = 'true';
						break;
					case 'phone':
						defaults.validate = 'false';
						break;
					case 'hidden':
						defaults.default_value = ''; // eslint-disable-line camelcase
						defaults.custom_value = ''; // eslint-disable-line camelcase
						break;
					case 'number':
					case 'text':
						defaults.required_error_message = optinVars.messages.cant_empty; // eslint-disable-line camelcase
						break;
				}

			return defaults;

		},

		/**
		 * Retrieve the defaults for each field type's setting view.
		 * These settings are intended to display the proper content of each field
		 * in the wizard settings. These won't be stored.
		 * @since 4.0
		 * @param string field_type. The field type.
		 */
		getFieldViewDefaults( fieldType ) {

			let defaults = {
				required: 'false',
				validated: 'false',
				'placeholder_placeholder': optinVars.messages.form_fields.label.placeholder,
				'label_placeholder': '',
				'name_placeholder': '',
				icon: 'send',
				'css_classes': '',
				type: fieldType,
				name: fieldType,
				placeholder: optinVars.messages.form_fields.label[fieldType + '_placeholder'],
				'can_delete': true,
				fieldId: this.getNewFieldId( fieldType )
			};

			switch ( fieldType ) {
				case 'email':
					defaults.icon = 'mail';
					break;
				case 'name':
					defaults.icon = 'profile-male';
					break;
				case 'phone':
					defaults.icon = 'phone';
					break;
				case 'address':
					defaults.icon = 'pin';
					break;
				case 'url':
					defaults.icon = 'web-globe-world';
					break;
				case 'text':
					defaults.icon = 'style-type';
					break;
				case 'number':
					defaults.icon = 'element-number';
					break;
				case 'timepicker':
					defaults.icon = 'clock';
					break;
				case 'datepicker':
					defaults.icon = 'calendar';
					break;
				case 'recaptcha':
					defaults.icon = 'recaptcha';
					break;
				case 'gdpr':
					defaults.icon = 'gdpr';
					break;
				case 'hidden':
					defaults.icon = 'eye-hide';
					break;

			}

			return defaults;

		}
	});
});

Hustle.define( 'Module.IntegrationsView', function( $, doc, win ) {
	'use strict';

	const integrationsView = Hustle.View.extend( _.extend({}, Hustle.get( 'Mixins.Model_Updater' ), {

		el: '#hustle-box-section-integrations',

		events: {
			'click .connect-integration': 'connectIntegration',
			'keypress .connect-integration': 'preventEnterKeyFromDoingThings'
		},

		init( opts ) {
			this.model = new opts.BaseModel( optinVars.current.integrations_settings || {});
			this.moduleId = optinVars.current.data.module_id;
			this.listenTo( Hustle.Events, 'hustle:providers:reload', this.renderProvidersTables );
			this.render();
		},

		render() {
			let $notConnectedWrapper = this.$el.find( '#hustle-not-connected-providers-section' ),
				$connectedWrapper = this.$el.find( '#hustle-connected-providers-section' );

			if ( 0 < $notConnectedWrapper.length && 0 < $connectedWrapper.length ) {
				this.renderProvidersTables();
			}

		},

		renderProvidersTables() {

			var self = this,
				data = {}
			;

			// Add preloader
			this.$el.find( '.hustle-integrations-display' )
				.html(
					'<div class="sui-notice sui-notice-sm sui-notice-loading">' +
						'<p>' + optinVars.fetching_list + '</p>' +
					'</div>'
				);

			data.action      = 'hustle_provider_get_form_providers';
			data._ajax_nonce = optinVars.providers_action_nonce; // eslint-disable-line camelcase
			data.data = {
				moduleId: this.moduleId
			};

			const ajax = $.post({
				url: ajaxurl,
				type: 'post',
				data: data
			})
			.done( function( result ) {
				if ( result && result.success ) {
					const $activeIntegrationsInput = self.$el.find( '#hustle-integrations-active-integrations' ),
						$activeIntegrationsCount = self.$el.find( '#hustle-integrations-active-count' );

					self.$el.find( '#hustle-not-connected-providers-section' ).html( result.data.not_connected );
					self.$el.find( '#hustle-connected-providers-section' ).html( result.data.connected );

					// Prevent marking the model as changed on load.
					if ( $activeIntegrationsInput.val() !== result.data.list_connected ) {
						$activeIntegrationsInput.val( result.data.list_connected ).trigger( 'change' );
					}

					// Prevent marking the model as changed on load.
					if ( $activeIntegrationsCount.val() !== String( result.data.list_connected_total ) ) {
						$activeIntegrationsCount.val( result.data.list_connected_total ).trigger( 'change' );
					}
				}
			});

			// Remove preloader
			ajax.always( function() {
				self.$el.find( '.sui-box-body' ).removeClass( 'sui-block-content-center' );
				self.$el.find( '.sui-notice-loading' ).remove();
			});
		},

		// Prevent the enter key from opening integrations modals and breaking the page.
		preventEnterKeyFromDoingThings( e ) {
			if ( 13 === e.which ) { // the enter key code
				e.preventDefault();
				return;
			}
		},

		connectIntegration( e ) {
			Module.integrationsModal.open( e );
		}

	}) );

	return integrationsView;
});

Hustle.define( 'Mixins.Module_Visibility', function( $, doc, win ) {

	'use strict';

	return _.extend({}, Hustle.get( 'Mixins.Model_Updater' ), {

		el: '#hustle-conditions-group',

		events: {

			'click .hustle-add-new-visibility-group': 'addNewGroup',
			'click .hustle-choose-conditions': 'openConditionsModal',
			'click .hustle-remove-visibility-group': 'removeGroup',
			'change .visibility-group-filter-type': 'updateAttribute',

			'change .visibility-group-show-hide': 'updateAttribute',
			'change .visibility-group-apply-on': 'updateGroupApplyOn'
		},

		init( opts ) {

			const Model = opts.BaseModel.extend({
					defaults: { conditions: '' },
					initialize: function( data ) {

						_.extend( this, data );

						if ( ! ( this.get( 'conditions' ) instanceof Backbone.Model ) ) {

							/**
							 * Make sure conditions is not an array
							 */
							if ( _.isEmpty( this.get( 'conditions' ) ) && _.isArray( this.get( 'conditions' ) )  ) {
								this.conditions = {};
							}

							let hModel = Hustle.get( 'Model' );
							this.set( 'conditions', new hModel( this.conditions ), { silent: true });
						}
					}
				});

			this.model = new Model( optinVars.current.visibility || {});

			this.moduleType = optinVars.current.data.module_type;
			this.activeConditions = {};
			this.render();
			$( '#hustle-general-conditions' ).on( 'click',  $.proxy( this.switchConditions, this ) );
			$( '#hustle-wc-conditions' ).on( 'click',  $.proxy( this.switchConditions, this ) );
            this.groupId = '';
		},

		render() {

			let self = this,
				groups = this.model.get( 'conditions' ).toJSON();

			if ( ! $.isEmptyObject( groups ) ) {

				for ( let groupId in groups ) {

					let group = this.model.get( 'conditions.' + groupId );

					if ( ! ( group instanceof Backbone.Model ) ) {

						// Make sure it's not an array
						if ( _.isEmpty( group ) && _.isArray( group )  ) {
							group = {};
						}

						group = this.getConditionsGroupModel( group );

						self.model.set( 'conditions.' + groupId, group, { silent: true });
					}

					this.addGroupToPanel( group, 'render' );

				}

				this.maybeToggleGroupsBin();

			} else {
				this.addNewGroup();
			}

		},

		afterRender() {
			this.bindRemoveConditions();
		},

		bindRemoveConditions() {

			// Remove condition
			$( '#hustle-conditions-group .hustle-remove-visibility-condition' ).off( 'click' ).on( 'click', $.proxy( this.removeCondition, this ) );

		},

		openConditionsModal( e ) {

			let self = this,
				$this = $( e.currentTarget ),
				groupId = $this.data( 'group-id' ),
				savedConditions = this.model.get( 'conditions.' + groupId ),
				groupConditions = 'undefined' !== typeof savedConditions ? Object.keys( savedConditions.toJSON() ) : [],
				VisibilityModalView = Hustle.get( 'Modals.Visibility_Conditions' ),
				visibilityModal = new VisibilityModalView({
					groupId: groupId,
					conditions: groupConditions
				});

			visibilityModal.on( 'conditions:added', $.proxy( self.addNewConditions, self ) );

			this.groupId = groupId;

			// Show dialog

			if ( 'done' !== $( 'html' ).data( 'show-was-bind' ) ) {
				SUI.dialogs['hustle-dialog--visibility-options'].on( 'show', function( dialogEl ) {
					$( '#hustle-add-conditions' ).data( 'group_id', self.groupId );
				});
				$( 'html' ).data( 'show-was-bind', 'done' );
			}
			SUI.dialogs['hustle-dialog--visibility-options'].show();

		},

		addNewConditions( args ) {

			let self = this,
				groupId = args.groupId,
				conditions = args.conditions,
				group = this.model.get( 'conditions.' + groupId );

			$.each( conditions, ( i, id ) => {
				if ( group.get( id ) ) {

					// If this condition is already set for this group, abort. Prevent duplicated conditions in a group.
					return true;
				}

				self.addConditionToPanel( id, {}, groupId, group, 'new' );
			});

			this.bindRemoveConditions();

			Hustle.Events.trigger( 'view.rendered', this );

		},

		addGroupToPanel( group, source ) {

			// Render this group container.
			let groupId = group.get( 'group_id' ),
				targetContainer = $( '#hustle-visibility-conditions-box' ),
				_template = Optin.template( 'hustle-visibility-group-box-tpl' ),

				html = _template( _.extend({}, {
					groupId,
					apply_on_floating: group.get( 'apply_on_floating' ), // eslint-disable-line camelcase
					apply_on_inline: group.get( 'apply_on_inline' ), // eslint-disable-line camelcase
					apply_on_widget: group.get( 'apply_on_widget' ), // eslint-disable-line camelcase
					apply_on_shortcode: group.get( 'apply_on_shortcode' ), // eslint-disable-line camelcase
					show_or_hide_conditions: group.get( 'show_or_hide_conditions' ), // eslint-disable-line camelcase
					filter_type: group.get( 'filter_type' ) // eslint-disable-line camelcase
				}) );

			$( html ).insertBefore( targetContainer.find( '.hustle-add-new-visibility-group' ) );

			this.activeConditions[ groupId ] = {};

			// Render each of this group's conditions.
			let self = this,
				conditions = group.toJSON();

			$.each( conditions, function( id, condition ) {

				if ( 'object' !== typeof condition ) {

					// If this property is not an actual condition, like "group_id", or "filter_type",
					// continue. Check the next property as this isn't the condition we want to render.
					return true;
				}

				self.addConditionToPanel( id, condition, groupId, group, source );

			});
		},

		addConditionToPanel( id, condition, groupId, group, source ) {

			if ( 'undefined' === typeof Optin.View.Conditions[ id ]) {
				return;
			}

			let $conditionsContainer = this.$( '#hustle-visibility-group-' + groupId + ' .sui-box-builder-body' ),
				thisCondition =  new Optin.View.Conditions[ id ]({
					type: this.moduleType,
					model: group,
					groupId: groupId,
					source
				});

			if ( ! thisCondition ) {
				return;
			}

			// If there aren't other conditions rendered within the group, empty it for adding new conditions.
			if ( ! $conditionsContainer.find( '.sui-builder-field' ).length ) {
				$conditionsContainer.find( '.sui-box-builder-message-block' ).hide();
				$conditionsContainer.find( '.sui-button-dashed' ).show();
			}

			if ( $.isEmptyObject( condition ) ) {
				group.set( id, thisCondition.getConfigs() );
			} else {
				group.set( id, condition );
			}
			this.activeConditions[ groupId ][ id ] = thisCondition;

			$( thisCondition.$el ).appendTo( $conditionsContainer.find( '.sui-builder-fields' ) );

			return thisCondition;
		},

		addNewGroup() {

			let group = this.getConditionsGroupModel(),
				targetContainer = $( '#hustle-conditions-group' ),
				groupId = group.get( 'group_id' );

			this.model.set( 'conditions.' + groupId, group );

			this.addGroupToPanel( group, 'new' );

			this.maybeToggleGroupsBin();

			Hustle.Events.trigger( 'view.rendered', this );
		},

		switchConditions( e ) {
			e.preventDefault();

			let $this = $( e.currentTarget ),
				currentId = $this.prop( 'id' );

			if ( 'hustle-wc-conditions' === currentId ) {
				$( '#hustle-dialog--visibility-options .general_condition' ).hide();
				$( '#hustle-dialog--visibility-options .wc_condition' ).show();
			} else {
				$( '#hustle-dialog--visibility-options .wc_condition' ).hide();
				$( '#hustle-dialog--visibility-options .general_condition' ).show();
			}
		},

		removeGroup( e ) {

			let groupId = $( e.currentTarget ).data( 'group-id' ),
				$groupContainer = this.$( '#hustle-visibility-group-' + groupId );

			// Remove the group from the model.
			delete this.activeConditions[ groupId ];
			this.model.get( 'conditions' ).unset( groupId );

			// Remove the group container from the page.
			$groupContainer.remove();

			// If the last group was removed, add a new group so the page is not empty.
			if ( ! Object.keys( this.activeConditions ).length ) {
				this.addNewGroup();
			}

			this.maybeToggleGroupsBin();
		},

		removeCondition( e ) {

			let $this = $( e.currentTarget ),
				conditionId =  $this.data( 'condition-id' ),
				groupId = $this.data( 'group-id' ),
				$conditionsContainer = this.$( '#hustle-visibility-group-' + groupId + ' .sui-box-builder-body' ),
				thisCondition = this.activeConditions[ groupId ][ conditionId ];

			thisCondition.remove();

			delete this.activeConditions[ groupId ][ conditionId ];

			this.model.get( 'conditions.' + groupId ).unset( conditionId );

			if ( ! $conditionsContainer.find( '.sui-builder-field' ).length ) {
				$conditionsContainer.find( '.sui-box-builder-message-block' ).show();
			}

			this.bindRemoveConditions();
		},

		updateAttribute( e ) {

			e.stopPropagation();

			let $this = $( e.target ),
				groupId = $this.data( 'group-id' ),
				attribute = $this.data( 'group-attribute' ),
				value = $this.val(),
				group = this.model.get( 'conditions.' + groupId );

			group.set( attribute, value );

		},

		updateGroupApplyOn( e ) {

			e.stopPropagation();

			let $this = $( e.target ),
				groupId = $this.data( 'group-id' ),
				attribute = $this.data( 'property' ),
				value = $this.is( ':checked' ),
				group = this.model.get( 'conditions.' + groupId );

			if ( 'embedded' === this.moduleType && -1 !== $.inArray( attribute, [ 'apply_on_inline', 'apply_on_widget', 'apply_on_shortcode' ]) ||
				'social_sharing' === this.moduleType && -1 !== $.inArray( attribute, [ 'apply_on_floating', 'apply_on_inline', 'apply_on_widget', 'apply_on_shortcode' ])
			) {
				group.set( attribute, value );
			}

		},

		getConditionsGroupModel( group ) {

			if ( ! group ) {

				let groupId = ( new Date().getTime() ).toString( 16 );

				if ( 'undefined' !== typeof this.model.get( 'conditions.' + groupId ) ) {

					// TODO: create another group_id while the group id exists.
				}

				group = {
					group_id: groupId, // eslint-disable-line camelcase
					show_or_hide_conditions: 'show', // eslint-disable-line camelcase
					filter_type: 'all' // eslint-disable-line camelcase
				};

				if ( 'embedded' === this.moduleType ) {
					group.apply_on_inline = true; // eslint-disable-line camelcase
					group.apply_on_widget = true; // eslint-disable-line camelcase
					group.apply_on_shortcode = false; // eslint-disable-line camelcase
				} else if ( 'social_sharing' === this.moduleType ) {
					group.apply_on_floating = true; // eslint-disable-line camelcase
					group.apply_on_inline = true; // eslint-disable-line camelcase
					group.apply_on_widget = true; // eslint-disable-line camelcase
					group.apply_on_shortcode = false; // eslint-disable-line camelcase
				}

			} else if ( 'embedded' === this.moduleType && ( ! group.apply_on_inline || ! group.apply_on_widget  || ! group.apply_on_shortcode ) ) {

				if ( ! group.apply_on_inline ) {
					group.apply_on_inline = true; // eslint-disable-line camelcase
				}
				if ( ! group.apply_on_widget ) {
					group.apply_on_widget = true; // eslint-disable-line camelcase
				}
				if ( ! group.apply_on_shortcode ) {
					group.apply_on_shortcode = false; // eslint-disable-line camelcase
				}

			} else if ( 'social_sharing' === this.moduleType && ( ! group.apply_on_floating || ! group.apply_on_inline  || ! group.apply_on_widget || ! group.apply_on_shortcode ) ) {

				if ( ! group.apply_on_floating ) {
					group.apply_on_floating = true; // eslint-disable-line camelcase
				}
				if ( ! group.apply_on_inline ) {
					group.apply_on_inline = true; // eslint-disable-line camelcase
				}
				if ( ! group.apply_on_widget ) {
					group.apply_on_widget = true; // eslint-disable-line camelcase
				}
				if ( ! group.apply_on_shortcode ) {
					group.apply_on_shortcode = false; // eslint-disable-line camelcase
				}

			}

			let hModel = Hustle.get( 'Model' ),
				groupModel = new hModel( group );

			return groupModel;
		},

		/**
		 * Prevent the last standing group from being removable
		 * Enable again the "bin" icons to remove if there's more than 1 group.
		 *
		 * @since 4.1.0
		 */
		maybeToggleGroupsBin() {

			const groups = this.model.get( 'conditions' ),
				$groupsBin = $( '#hustle-conditions-group .sui-box-builder-header .hustle-remove-visibility-group' );

			if ( 1 === Object.keys( groups.toJSON() ).length ) {
				Module.Utils.accessibleHide( $groupsBin );

			} else {
				Module.Utils.accessibleShow( $groupsBin );
			}
		}

	});
});

Hustle.define( 'Mixins.Wizard_View', function( $, doc, win ) {

	'use strict';

	return {

		moduleType: '',

		el: '.sui-wrap',

		events: {
			'click .sui-sidenav .sui-vertical-tab a': 'sidenav',
			'change select.sui-mobile-nav': 'sidenavMobile',
			'click a.hustle-go-to-tab': 'sidenav',
			'click a.notify-error-tab': 'sidenav',
			'click .hustle-action-save': 'saveChanges',
			'click .wpmudev-button-navigation': 'doButtonNavigation',
			'change #hustle-module-name': 'updateModuleName',
			'click #hustle-preview-module': 'previewModule',
			'blur input.sui-form-control': 'removeErrorMessage'
		},

		// ============================================================
		// Initialize Wizard
		init( opts ) {

			this.setTabsViews( opts );

			Hustle.Events.off( 'modules.view.switch_status', $.proxy( this.switchStatusTo, this ) );
			Hustle.Events.on( 'modules.view.switch_status', $.proxy( this.switchStatusTo, this ) );

			$( win ).off( 'popstate', $.proxy( this.updateTabOnPopstate, this ) );
			$( win ).on( 'popstate', $.proxy( this.updateTabOnPopstate, this ) );

			$( document ).off( 'tinymce-editor-init', $.proxy( this.tinymceReady, this ) );
			$( document ).on( 'tinymce-editor-init', $.proxy( this.tinymceReady, this ) );

			if ( 'undefined' !== typeof this._events ) {
				this.events = $.extend( true, {}, this.events, this._events );
				this.delegateEvents();
			}

			this.renderTabs();

			return this;

		},

		/**
		 * Assign the tabs views to the object.
		 * Overridden by social share.
		 * @param object opts
		 */
		setTabsViews( opts ) {

			this.contentView    = opts.contentView;
			this.emailsView     = opts.emailsView;
			this.designView     = opts.designView;
			this.integrationsView = opts.integrationsView;
			this.visibilityView = opts.visibilityView;
			this.settingsView   = opts.settingsView;
			this.moduleType = this.model.get( 'module_type' );

			if ( 'embedded' === this.moduleType ) {
				this.displayView  = opts.displayView;
			}
		},

		// ============================================================
		// Render content

		/**
		 * Render the tabs.
		 * Overridden by social share.
		 */
		renderTabs() {

			// Content view
			this.contentView.delegateEvents();

			// Emails view
			this.emailsView.delegateEvents();

			// Integrations view
			this.integrationsView.delegateEvents();

			// Appearance view
			this.designView.delegateEvents();

			// Display Options View
			if ( 'embedded' === this.moduleType ) {
				this.displayView.delegateEvents();
			}

			// Visibility view
			this.visibilityView.delegateEvents();
			this.visibilityView.afterRender();

			// Behavior view
			this.settingsView.delegateEvents();
		},

		// ============================================================
		// Side Navigation
		sidenav( e ) {
			e.preventDefault();

			let tabName = $( e.target ).data( 'tab' );

			if ( tabName ) {
				this.goToTab( tabName, true );
			}
		},

		sidenavMobile( e ) {
			const tabName = $( e.currentTarget ).val();

			if ( tabName ) {
				this.goToTab( tabName, true );
			}
		},

		goToTab( tabName, updateHistory ) {

			let $tab 	 = this.$el.find( 'a[data-tab="' + tabName + '"]' ),
				$sidenav = $tab.closest( '.sui-vertical-tabs' ),
				$tabs    = $sidenav.find( '.sui-vertical-tab a' ),
				$content = this.$el.find( '.sui-box[data-tab]' ),
				$current = this.$el.find( '.sui-box[data-tab="' + tabName + '"]' );

			if ( updateHistory ) {

				// The module id must be defined at this point.
				// If it's not, the user should be redirected to the listing page to properly create a module before reaching this.
				let state = { tabName },
				moduleId = this.model.get( 'module_id' );

				history.pushState( state, 'Hustle ' + this.moduleType + ' wizard', 'admin.php?page=' + optinVars.current.wizard_page + '&id=' + moduleId + '&section=' + tabName  );
			}

			$tabs.removeClass( 'current' );
			$content.hide();

			$tab.addClass( 'current' );
			$current.show();

			$( '.sui-wrap-hustle' )[0].scrollIntoView();
		},

		// Keep the sync of the shown tab and the URL when going "back" with the browser.
		updateTabOnPopstate( e ) {
			var state = e.originalEvent.state;

			if ( state ) {
				this.goToTab( state.tabName );
			}
		},

		// Go to he "next" and "previous" tab when using the buttons at the bottom of the wizard.
		doButtonNavigation( e ) {
			e.preventDefault();
			let $button = $( e.target ),
				direction = 'prev' === $button.data( 'direction' ) ? 'prev' : 'next',
				nextTabName = this.getNextOrPrevTabName( direction );

			this.goToTab( nextTabName, true );

		},

		// Get the name of the previous or next tab.
		getNextOrPrevTabName( direction ) {
			var current = $( '#hustle-module-wizard-view .sui-sidenav ul li a.current' ),
				tab = current.data( 'tab' );

			if ( 'prev' === direction ) {
				tab = current.parent().prev().find( 'a' ).data( 'tab' );
			} else {
				tab = current.parent().next().find( 'a' ).data( 'tab' );
			}

			return tab;
		},

		// ============================================================
		// TinyMCE

		// Mark the wizard as "unsaved" when the tinymce editors had a change.
		tinymceReady( e, editor ) {
			const self = this;
			editor.on( 'change', () => {
				if ( ! Module.hasChanges ) {
					self.contentView.model.userHasChange();
				}
			});
			$( 'textarea#' + editor.id ).on( 'change', () => {
				if ( ! Module.hasChanges ) {
					self.contentView.model.userHasChange();
				}
			});
		},

		setContentFromTinymce( keepSilent = false ) {

			if ( 'undefined' !== typeof tinyMCE ) {

				// main_content editor
				let mainContentEditor = tinyMCE.get( 'main_content' ),
					$mainContentTextarea = this.$( 'textarea#main_content' ),
					mainContent = ( 'true' === $mainContentTextarea.attr( 'aria-hidden' ) ) ? mainContentEditor.getContent() : $mainContentTextarea.val();

				this.contentView.model.set( 'main_content', mainContent, {
					silent: keepSilent
				});

				// success_message editor
				let successMessageEditor = tinyMCE.get( 'success_message' ),
					$successMessageTextarea = this.$( 'textarea#success_message' ),
					successMessage = ( 'true' === $successMessageTextarea.attr( 'aria-hidden' ) ) ? successMessageEditor.getContent() : $successMessageTextarea.val();

				this.emailsView.model.set( 'success_message', successMessage, {
					silent: keepSilent
				});

				// email_body editor
				let emailBodyEditor = tinyMCE.get( 'email_body' ),
					$emailBodyTextarea = this.$( 'textarea#email_body' ),
					emailBody = ( 'true' === $successMessageTextarea.attr( 'aria-hidden' ) ) ? emailBodyEditor.getContent() : $emailBodyTextarea.val();

				this.emailsView.model.set( 'email_body', emailBody, {
					silent: keepSilent
				});

			}
		},

		// ============================================================
		// Sanitize Data
		sanitizeData() {

			// Call to action
			var ctaUrl = this.contentView.model.get( 'cta_url' );

			if ( 0 !== ctaUrl.indexOf( 'mailto:' ) && 0 !== ctaUrl.indexOf( 'tel:' ) ) {
				if ( ! /^(f|ht)tps?:\/\//i.test( ctaUrl ) ) {
					ctaUrl = 'https://' + ctaUrl;
					this.contentView.model.set( 'cta_url', ctaUrl, { silent: true });
				}
			}
		},

		validate() {

			// Custom CSS
			this.designView.updateCustomCss();
			this.setContentFromTinymce( true );
			this.sanitizeData();

			// Preparig the data
			let me       = this,
				$this    = this.$el.find( '#hustle-module-wizard-view' ),
				id       = ( ! $this.data( 'id' ) ) ? '-1' : $this.data( 'id' ),
				nonce    = $this.data( 'nonce' ),
				module   = this.model.toJSON(),
				data 	 = {
					action: 'hustle_validate_module',
					'_ajax_nonce': nonce,
					id,
					module
				};

			_.extend( data, this.getDataToSave() );

			// ajax save here
			return $.ajax({
				url: ajaxurl,
				type: 'POST',
				data: data,
				dataType: 'json',
				success: function( result ) {

					if ( true === result.success ) {

						// TODO: handle errors. Such as when nonces expire when you leave the window opend for long.

						// The changes were already saved.
						Module.hasChanges = false;

						// Change the "Pending changes" label to "Saved".
						me.switchStatusTo( 'saved' );
					} else {
						let errors = result.data,
							errorMessage = '';

						if ( 'undefined' !== typeof errors.data.icon_error ) {
							_.each( errors.data.icon_error, function( error ) {
								$( '#hustle-platform-' + error ).find( '.sui-error-message' ).show();
								$( '#hustle-platform-' + error + ' .hustle-social-url-field' ).addClass( 'sui-form-field-error' );
								$( '#hustle-platform-' + error ).not( '.sui-accordion-item--open' ).find( '.sui-accordion-open-indicator' ).click();
							});

							errorMessage = '<a href="#" data-tab="services" class="notify-error-tab"> Services </a>';
						}

						if ( 'undefined' !== typeof errors.data.selector_error ) {
							_.each( errors.data.selector_error, function( error ) {
								$( 'input[name="' + error + '_css_selector"]' ).siblings( '.sui-error-message' ).show();

								$( 'input[name="' + error + '_css_selector"]' ).parent( '.sui-form-field' ).addClass( 'sui-form-field-error' );
							});

							if ( ! _.isEmpty( errorMessage ) ) {
								errorMessage = errorMessage + ' and ';
							}

							errorMessage = errorMessage + '<a href="#" data-tab="display" class="notify-error-tab"> Display Options </a>';
						}

						errorMessage =  optinVars.messages.sshare_module_error.replace( '{page}', errorMessage );

						Module.Notification.open( 'error', errorMessage, 1000000000 );
					}
				}
			});
		},

		// ============================================================
		// Save changes
		save() {

			this.setContentFromTinymce( true );
			this.sanitizeData();

			// Preparig the data
			let me       = this,
				$this    = this.$el.find( '#hustle-module-wizard-view' ),
				id       = ( ! $this.data( 'id' ) ) ? '-1' : $this.data( 'id' ),
				nonce    = $this.data( 'nonce' ),
				module   = this.model.toJSON();

			let data = {
					action: 'hustle_save_module',
					'_ajax_nonce': nonce,
					id,
					module
				};

			_.extend( data, this.getDataToSave() );

			// ajax save here
			return $.ajax({
				url: ajaxurl,
				type: 'POST',
				data: data,
				dataType: 'json',
				success: function( result ) {

					if ( true === result.success ) {

						// TODO: handle errors. Such as when nonces expire when you leave the window opend for long.

						// The changes were already saved.
						Module.hasChanges = false;

						// Change the "Pending changes" label to "Saved".
						me.switchStatusTo( 'saved' );
					} else {
						let errors = result.data,
							errorMessage = '';

						if ( 'undefined' !== typeof errors.data.icon_error ) {
							_.each( errors.data.icon_error, function( error ) {
								$( '#hustle-platform-' + error ).find( '.sui-error-message' ).show();
								$( '#hustle-platform-' + error + ' .hustle-social-url-field' ).addClass( 'sui-form-field-error' );
								$( '#hustle-platform-' + error ).not( '.sui-accordion-item--open' ).find( '.sui-accordion-open-indicator' ).click();
							});

							errorMessage = '<a href="#" data-tab="services" class="notify-error-tab"> Services </a>';
						}

						if ( 'undefined' !== typeof errors.data.selector_error ) {
							_.each( errors.data.selector_error, function( error ) {
								$( 'input[name="' + error + '_css_selector"]' ).siblings( '.sui-error-message' ).show();

								$( 'input[name="' + error + '_css_selector"]' ).parent( '.sui-form-field' ).addClass( 'sui-form-field-error' );
							});

							if ( ! _.isEmpty( errorMessage ) ) {
								errorMessage = errorMessage + ' and ';
							}

							errorMessage = errorMessage + '<a href="#" data-tab="display" class="notify-error-tab"> Display Options </a>';
						}

						errorMessage =  optinVars.messages.sshare_module_error.replace( '{page}', errorMessage );

						Module.Notification.open( 'error', errorMessage, 10000 );
					}
				}
			});
		},

		getDataToSave() {

			const data = {
				content: this.contentView.model.toJSON(),
				emails: this.emailsView.model.toJSON(),
				design: this.designView.model.toJSON(),
				integrations_settings: this.integrationsView.model.toJSON(), // eslint-disable-line camelcase
				visibility: this.visibilityView.model.toJSON(),
				settings: this.settingsView.model.toJSON()
			};

			if ( 'embedded' === this.moduleType ) {
				data.display = this.displayView.model.toJSON();
			}

			return data;

		},

		saveChanges( e ) {

			let me             = this,
				currentActive = this.model.get( 'active' ),
				setActiveTo  = 'undefined' !== typeof $( e.currentTarget ).data( 'active' ) ? String( $( e.currentTarget ).data( 'active' ) ) : false,
				updateActive  = false,
				validation    = false
				;

			if ( false !== setActiveTo ) {
				if ( '0' === setActiveTo ) {
					me.disableButtonsOnSave( 'draft' );
				} else {
					me.disableButtonsOnSave( 'publish' );
				}
			}

			const validate = this.validate();
			validate.done( function( resp ) {

				if ( resp.success ) {
					if ( false !== setActiveTo && resp.success ) {
						validation = true;

						if ( '0' !== setActiveTo  && setActiveTo !== currentActive ) {
							me.publishingFlow( 'loading' );
						}
						if ( setActiveTo !== currentActive ) {
							updateActive = true;
							me.model.set( 'active', setActiveTo, {
								silent: true
							});
						}
					}

					const save = me.save();
					if ( save && validation ) {
						save.done( function( resp ) {

							if ( 'string' === typeof resp  ) {
								resp = JSON.parse( resp );
							}

							if ( resp.success ) {

								if ( updateActive ) {
									me.updateViewOnActiveChange();
								}
							}

							if ( '0' !== setActiveTo && setActiveTo !== currentActive ) {

								if ( resp.success ) {

									if ( updateActive ) {

										setTimeout( function() {
											me.publishingFlow( 'ready' );
										}, 500 );
									}
								}
							}
						}).always( function() {
							me.enableSaveButtons();
						});

					} else {

						// If saving did not work, remove loading icon.
						me.enableSaveButtons();

					}
				} else {

					// Change the "Pending changes" label to "Saved".
					me.switchStatusTo( 'unsaved' );

					// If saving did not work, remove loading icon.
					me.enableSaveButtons();
				}
			});

			e.preventDefault();

		},

		// ============================================================
		// Update the view elements

		/**
		 * Update this module's name if the new value is not empty.
		 * @param event e
		 */
		updateModuleName( e ) {

			let $input = $( e.target ),
				moduleName = $input.val();

			if ( moduleName.length ) {
				this.$( '#hustle-module-name-wrapper' ).removeClass( 'sui-form-field-error' );
				this.$( '#hustle-module-name-error' ).hide();
				this.model.set( 'module_name', moduleName );
			} else {
				this.$( '#hustle-module-name-wrapper' ).addClass( 'sui-form-field-error' );
				this.$( '#hustle-module-name-error' ).show();
			}
		},

		// Disable the save buttons.
		disableButtonsOnSave( type ) {

			if ( 'draft' === type ) {
				this.$( '#hustle-draft-button' ).addClass( 'sui-button-onload' );

			} else if ( 'publish' === type ) {
				this.$( '.hustle-publish-button' ).addClass( 'sui-button-onload' );
			}

			this.$( '.hustle-action-save' ).prop( 'disabled', true );
			this.$( '.wpmudev-button-navigation' ).prop( 'disabled', true );
		},

		// Enable the save buttons.
		enableSaveButtons() {
			this.$( '.sui-button-onload' ).removeClass( 'sui-button-onload' );
			this.$( '.hustle-action-save' ).prop( 'disabled', false );
			this.$( '.wpmudev-button-navigation' ).prop( 'disabled', false );
		},

		// Change the 'saved'/'unsaved' label.
		switchStatusTo( switchTo ) {

			if ( 'saved' === switchTo ) {
				this.$el.find( '#hustle-unsaved-changes-status' ).addClass( 'sui-hidden' );
				this.$el.find( '#hustle-saved-changes-status' ).removeClass( 'sui-hidden' );
			} else {
				this.$el.find( '#hustle-unsaved-changes-status' ).removeClass( 'sui-hidden' );
				this.$el.find( '#hustle-saved-changes-status' ).addClass( 'sui-hidden' );
			}
		},

		// Change the 'Draft'/'Published' module status label, and update the save buttons for each case.
		updateViewOnActiveChange() {

			var active = this.model.get( 'active' ),
				newStatus = '1' === active ? optinVars.messages.commons.published : optinVars.messages.commons.draft, // eslint-disable-line camelcase
				draftButtonText = '1' === active ? optinVars.messages.commons.unpublish : optinVars.messages.commons.save_draft, // eslint-disable-line camelcase
				publishButtonText = '1' === active ? optinVars.messages.commons.save_changes : optinVars.messages.commons.publish; // eslint-disable-line camelcase

			// Update the module status tag. The one that says if the module is Published or a Draft.
			this.$el.find( '.sui-status-module .sui-tag' ).text( newStatus );

			// Update the text within the Draft button.
			this.$el.find( '#hustle-draft-button .button-text' ).text( draftButtonText );

			// Update the text within the Publish button.
			this.$el.find( '.hustle-publish-button .button-text' ).text( publishButtonText );
		},

		// Publishing flow dialog.
		publishingFlow( flowStatus ) {

			const getDialog = $( '#hustle-dialog--publish-flow' );
			const getContent = getDialog.find( '.sui-dialog-content > .sui-box' );
			const getIcon = getDialog.find( '#dialogIcon' );
			const getTitle = getDialog.find( '#dialogTitle' );
			const getDesc = getDialog.find( '#dialogDescription' );
			const getClose = getDialog.find( '.sui-dialog-close' );
			const getMask = getDialog.find( '.sui-dialog-overlay' );

			function resetPublishReady() {

				getIcon.removeClass( 'sui-icon-' + getContent.data( 'loading-icon' ) );
				getIcon.addClass( 'sui-icon-' + getContent.data( 'ready-icon' ) );

				if ( 'loader' === getContent.attr( 'data-loading-icon' ) ) {
					getIcon.removeClass( 'sui-loading' );
				}

				getTitle.text( getContent.data( 'ready-title' ) );
				getDesc.text( getContent.data( 'ready-desc' ) );

				getClose.show();

			}

			function resetPublishLoading() {

				getIcon.removeClass( 'sui-icon-' + getContent.data( 'ready-icon' ) );
				getIcon.addClass( 'sui-icon-' + getContent.data( 'loading-icon' ) );

				if ( 'loader' === getContent.attr( 'data-loading-icon' ) ) {
					getIcon.addClass( 'sui-loading' );
				}

				getTitle.text( getContent.data( 'loading-title' ) );
				getDesc.text( getContent.data( 'loading-desc' ) );

				getClose.hide();
			}

			function closeDialog() {

				SUI.dialogs['hustle-dialog--publish-flow'].hide();

				setTimeout( function() {
					resetPublishLoading();
				}, 500 );
			}

			if ( 'loading' === flowStatus ) {
				resetPublishLoading();
				SUI.dialogs['hustle-dialog--publish-flow'].show();
			}

			if ( 'ready' === flowStatus ) {

				resetPublishReady();

				// Focus ready title
				// This will help screen readers know when module has been published
				getTitle.focus();

				// Close dialog when clicking on mask
				getMask.on( 'click', function() {
					closeDialog();
				});

				// Close dialog when clicking on close button
				getClose.on( 'click', function() {
					closeDialog();
				});

			}
		},

		//remove error message
		removeErrorMessage( e ) {
			if ( e.target.value ) {
				let parent = $( e.target ).parent( '.sui-form-field' );
				parent.removeClass( 'sui-form-field-error' );
				parent.find( '.sui-error-message' ).hide();
			}
		},

		// ============================================================
		// Previewing

		previewModule( e ) {

			e.preventDefault();

			this.setContentFromTinymce( true );
			this.sanitizeData();

			let $button = $( e.currentTarget ),
				id = this.model.get( 'module_id' ),
				type = this.model.get( 'module_type' ),
				previewData = _.extend({}, this.model.toJSON(), this.getDataToSave() );

			$button.addClass( 'sui-button-onload' );

			Module.preview.open( id, type, previewData );
		}
	};

});

( function( $ ) {

	'use strict';

	var ConditionBase;

	Optin.View.Conditions = Optin.View.Conditions || {};

	ConditionBase = Hustle.View.extend({

		conditionId: '',

		className: 'sui-builder-field sui-accordion-item sui-accordion-item--open',

		_template: Optin.template( 'hustle-visibility-rule-tpl' ),

		template: false,

		_defaults: {
			typeName: '',
			conditionName: ''
		},

		_events: {
			'change input': 'changeInput',
			'change textarea': 'changeInput',
			'change select': 'changeInput'
		},

		init: function( opts ) {

			this.undelegateEvents();
			this.$el.removeData().unbind();

			this.type = opts.type;
			this.groupId = opts.groupId;
			this.filter_type = opts.filter_type; // eslint-disable-line camelcase
			this.id = this.conditionId;

			this.template =  ( 'undefined' !== typeof this.cpt ) ? Optin.template( 'hustle-visibility-rule-tpl--post_type' ) : Optin.template( 'hustle-visibility-rule-tpl--' + this.conditionId );

			/**
			 * Defines typeName and conditionName based on type and id so that it can be used in the template later on
			 *
			 * @type {Object}
			 * @private
			 */
			this._defaults = {
				typeName: optinVars.messages.settings[ this.type ] ? optinVars.messages.settings[ this.type ] : this.type,
				conditionName: optinVars.messages.conditions[ this.conditionId ] ? optinVars.messages.conditions[ this.conditionId ] : this.conditionId,
				groupId: this.groupId,
				id: this.conditionId,
				source: opts.source
			};

			this.data = this.getData();

			this.render();
			this.events = $.extend( true, {}, this.events, this._events );
			this.delegateEvents();
			if ( this.onInit && _.isFunction( this.onInit ) ) {
				this.onInit.apply( this, arguments );
			}
			return this;
		},

		getData: function() {
			return _.extend({}, this._defaults, this.defaults(), this.model.get( this.conditionId ), { type: this.type });
		},

		getTitle: function() {
			return this.title.replace( '{type_name}', this.data.typeName );
		},

		getBody: function() {
			return 'function' === typeof this.body ? this.body.apply( this, arguments ) : this.body.replace( '{type_name}', this.data.typeName );
		},

		getHeader: function() {
			return this.header;
		},

		countLines: function( value ) {

			// trim trailing return char if exists
			let text = value.replace( /\s+$/g, '' );
			let split = text.split( '\n' );
			return split.length;
		},

		render: function() {

			this.setProperties();

			let html = this._template( _.extend({}, {
					title: this.getTitle(),
					body: this.getBody(),
					header: this.getHeader()
				},
				this._defaults,
				{ type: this.type }
			) );

			this.$el.html( '' );
			this.$el.html( html );

			$( '.wph-conditions--box .wph-conditions--item:not(:last-child)' )
				.removeClass( 'wph-conditions--open' )
				.addClass( 'wph-conditions--closed' );
			$( '.wph-conditions--box .wph-conditions--item:not(:last-child) section' ).hide();

			if ( this.rendered && 'function' === typeof this.rendered ) {
				this.rendered.apply( this, arguments );
			};
			return this;
		},

		/**
		 * Updates attribute value into the condition hash
		 *
		 * @param attribute
		 * @param val
		 */
		updateAttribute: function( attribute, val ) {
			this.data = this.model.get( this.conditionId );
			this.data[ attribute ] = val;
			this.model.set( this.conditionId, this.data );

			// TODO: instead of triggering manually, clone the retrieved object so
			// backbone recognizes the change.
			this.model.trigger( 'change' );

		},
		getAttribute: function( attribute ) {
			var data = this.model.get( this.conditionId  );
			return data && data[ attribute ] ? data[ attribute ] : false;
		},
		refreshLabel: function() {
			var html =  this.getHeader();
			this.$el.find( '.wph-condition--preview' ).html( '' );
			this.$el.find( '.sui-accordion-item-header .sui-tag' ).html( html );
		},

		/**
		 * Triggered on input change
		 *
		 * @param e
		 * @returns {*}
		 */
		changeInput: function( e ) {

			//stop handler in /assets/js/admin/mixins/model-updater.js

			var updated,
				el = e.target,
				attribute = el.getAttribute( 'data-attribute' ),
				$el = $( el ),
				val = $el.is( '.sui-select' ) ? $el.val() : e.target.value;

			e.stopImmediatePropagation();

			if ( $el.is( ':checkbox' ) ) {
				val = $el.is( ':checked' );
			}

			// skip for input search
			if ( $el.is( '.select2-search__field' ) ) {
				return false;
			}

			updated = this.updateAttribute( attribute, val );

			this.refreshLabel();
			return updated;
		},

		/**
		 * Returns configs of condition
		 *
		 * @returns bool true
		 */
		getConfigs: function() {
			return this.defaults() || true;
		}
	});

	let reenableScroll = function( e ) {

		/**
		 * reenable scrolling for the container
		 * select2 disables scrolling after select so we reenable it
		 */
		$( '.wph-conditions--items' ).data( 'select2ScrollPosition', {});
	},
	ToggleButtonTogglerMixin = {
		events: {
			'change input[type="radio"]': 'setCurrentLi'
		},
		setCurrentLi: function( e ) {
			var $this = $( e.target ),
				$li = $this.closest( 'li' );

			$li.siblings().removeClass( 'current' );
			$li.toggleClass( 'current',  $this.is( ':checked' ) );
		}
	};

	/**
	 * Posts
	 */
	Optin.View.Conditions.posts = ConditionBase.extend( _.extend({}, ToggleButtonTogglerMixin, {
		conditionId: 'posts',
		setProperties() {
			this.title = optinVars.messages.conditions.posts;
		},
		defaults: function() {
			return {
				'filter_type': 'except', // except | only
				posts: []
			};
		},
		onInit: function() {

			//this.listenTo( this.model, 'change', this.render );
		},
		getHeader: function() {
			if ( this.getAttribute( 'posts' ).length ) {
				return ( 'only' === this.getAttribute( 'filter_type' ) ? optinVars.messages.condition_labels.only_these : optinVars.messages.condition_labels.except_these ).replace( '{number}',  this.getAttribute( 'posts' ).length );
			} else {
				return 'only' === this.getAttribute( 'filter_type' ) ? optinVars.messages.condition_labels.none : optinVars.messages.condition_labels.all;
			}
		},
		body: function() {
			return this.template( this.getData() );
		},
		rendered: function() {
			this.$( '.hustle-select-ajax' ).SUIselect2({
				tags: 'true',
				width: '100%',
				dropdownCssClass: 'sui-select-dropdown',
				ajax: {
					url: ajaxurl,
					delay: 250, // wait 250 milliseconds before triggering the request
					dataType: 'json',
					type: 'POST',
					data: function( params ) {
						var query = {
							action: 'get_new_condition_ids',
							search: params.term,
							postType: 'post'
						};

						return query;
					},
					processResults: function( data ) {
						return {
							results: data.data
						};
					},
					cache: true
				},
				createTag: function() {
					return false;
				}
			})
			.on( 'select2:selecting', reenableScroll )
			.on( 'select2:unselecting', reenableScroll );

		}
	}) );

	/**
	 * Pages
	 */
	Optin.View.Conditions.pages = ConditionBase.extend( _.extend({}, ToggleButtonTogglerMixin, {
		conditionId: 'pages',
		setProperties() {
			this.title = optinVars.messages.conditions.pages;
		},
		defaults: function() {
			return {
				'filter_type': 'except', // except | only
				pages: []
			};
		},
		onInit: function() {

			//this.listenTo( this.model, 'change', this.render );
		},
		getHeader: function() {
			if ( this.getAttribute( 'pages' ).length ) {
				return ( 'only' === this.getAttribute( 'filter_type' ) ? optinVars.messages.condition_labels.only_these : optinVars.messages.condition_labels.except_these ).replace( '{number}',  this.getAttribute( 'pages' ).length );
			} else {
				return ( 'only' === this.getAttribute( 'filter_type' ) ) ? optinVars.messages.condition_labels.none : optinVars.messages.condition_labels.all;
			}
		},
		body: function() {
			return this.template( this.getData() );
		},
		rendered: function() {
			this.$( '.hustle-select-ajax' ).SUIselect2({
					tags: 'true',
					width: '100%',
					dropdownCssClass: 'sui-select-dropdown',
					ajax: {
						url: ajaxurl,
						delay: 250, // wait 250 milliseconds before triggering the request
						dataType: 'json',
						type: 'POST',
						data: function( params ) {
							var query = {
								action: 'get_new_condition_ids',
								search: params.term,
								postType: 'page'
							};

							return query;
						},
						processResults: function( data ) {
							return {
								results: data.data
							};
						},
						cache: true
					},
					createTag: function() {
						return false;
					}
				})
			.on( 'select2:selecting', reenableScroll )
			.on( 'select2:unselecting', reenableScroll );

		}
	}) );

	/**
	 * Custom Post Types
	 */
	if ( optinVars.post_types ) {
		_.each( optinVars.post_types, function( cptDetails, cpt ) {
			Optin.View.Conditions[ cptDetails.name ] = ConditionBase.extend( _.extend({}, ToggleButtonTogglerMixin, {
				conditionId: cptDetails.name,
				cpt: true,

				setProperties() {
					this.title = cptDetails.label;
				},
				defaults: function() {
					return {
						'filter_type': 'except', // except | only
						'selected_cpts': [],
						postType: cpt,
						postTypeLabel: cptDetails.label
					};
				},
				getHeader: function() {
					if ( this.getAttribute( 'selected_cpts' ).length ) {
						return ( 'only' === this.getAttribute( 'filter_type' ) ? optinVars.messages.condition_labels.only_these : optinVars.messages.condition_labels.except_these ).replace( '{number}',  this.getAttribute( 'selected_cpts' ).length  );
					} else {
						return 'only' === this.getAttribute( 'filter_type' ) ? optinVars.messages.condition_labels.none : optinVars.messages.condition_labels.all;
					}
				},
				body: function() {
					return this.template( this.getData() );
				},
				rendered: function() {
					this.$( '.hustle-select-ajax' ).SUIselect2({
						tags: 'true',
						width: '100%',
						dropdownCssClass: 'sui-select-dropdown',
						ajax: {
							url: ajaxurl,
							delay: 250, // wait 250 milliseconds before triggering the request
							dataType: 'json',
							type: 'POST',
							data: function( params ) {
								var query = {
									action: 'get_new_condition_ids',
									search: params.term,
									postType: cpt
								};

								return query;
							},
							processResults: function( data ) {
								return {
									results: data.data
								};
							},
							cache: true
						},
						createTag: function() {
							return false;
						}
					})
					.on( 'select2:selecting', reenableScroll )
					.on( 'select2:unselecting', reenableScroll );
				}
			}) );
		});
	}

	/**
	 * Categories
	 */
	Optin.View.Conditions.categories = ConditionBase.extend( _.extend({}, ToggleButtonTogglerMixin, {
		conditionId: 'categories',
		setProperties() {
			this.title = optinVars.messages.conditions.categories;
		},
		defaults: function() {
			return {
				'filter_type': 'except', // except | only
				categories: []
			};
		},
		onInit: function() {

			//this.listenTo( this.model, 'change', this.render );
		},
		getHeader: function() {
			if ( this.getAttribute( 'categories' ).length ) {
				return ( 'only' === this.getAttribute( 'filter_type' ) ? optinVars.messages.condition_labels.only_these : optinVars.messages.condition_labels.except_these ).replace( '{number}',  this.getAttribute( 'categories' ).length );
			} else {
				return 'only' === this.getAttribute( 'filter_type' ) ? optinVars.messages.condition_labels.none : optinVars.messages.condition_labels.all;
			}
		},
		body: function() {
			return this.template( this.getData() );
		},
		rendered: function() {
			this.$( '.hustle-select-ajax' ).SUIselect2({
					tags: 'true',
					width: '100%',
					dropdownCssClass: 'sui-select-dropdown',
					ajax: {
						url: ajaxurl,
						delay: 250, // wait 250 milliseconds before triggering the request
						dataType: 'json',
						type: 'POST',
						data: function( params ) {
							var query = {
								action: 'get_new_condition_ids',
								search: params.term,
								postType: 'category'
							};

							return query;
						},
						processResults: function( data ) {
							return {
								results: data.data
							};
						},
						cache: true
					},
					createTag: function() {
						return false;
					}
			})
			.on( 'select2:selecting', reenableScroll )
			.on( 'select2:unselecting', reenableScroll );
		}
	}) );

	/**
	 * Tags
	 */
	Optin.View.Conditions.tags = ConditionBase.extend( _.extend({}, ToggleButtonTogglerMixin, {
		conditionId: 'tags',
		setProperties() {
			this.title = optinVars.messages.conditions.tags;
		},
		defaults: function() {
			return {
				'filter_type': 'except', // except | only
				tags: []
			};
		},
		onInit: function() {

			//this.listenTo( this.model, 'change', this.render );
		},
		getHeader: function() {
			if ( this.getAttribute( 'tags' ).length ) {
				return ( 'only' === this.getAttribute( 'filter_type' ) ? optinVars.messages.condition_labels.only_these : optinVars.messages.condition_labels.except_these ).replace( '{number}',  this.getAttribute( 'tags' ).length );
			} else {
				return 'only' === this.getAttribute( 'filter_type' ) ? optinVars.messages.condition_labels.none : optinVars.messages.condition_labels.all;
			}
		},
		body: function() {
			return this.template( this.getData() );
		},
		rendered: function() {
			this.$( '.hustle-select-ajax' ).SUIselect2({
					width: '100%',
					tags: 'true',
					dropdownCssClass: 'sui-select-dropdown',
					ajax: {
						url: ajaxurl,
						delay: 250, // wait 250 milliseconds before triggering the request
						dataType: 'json',
						type: 'POST',
						data: function( params ) {
							var query = {
								action: 'get_new_condition_ids',
								search: params.term,
								postType: 'tag'
							};

							return query;
						},
						processResults: function( data ) {
							return {
								results: data.data
							};
						},
						cache: true
					},
					createTag: function() {
						return false;
					}
			})
			.on( 'select2:selecting', reenableScroll )
			.on( 'select2:unselecting', reenableScroll );
		}
	}) );

	/**
	 * Visitor logged in / not logged in
	 */
	Optin.View.Conditions.visitor_logged_in_status = ConditionBase.extend({ // eslint-disable-line camelcase
		conditionId: 'visitor_logged_in_status',
		setProperties() {
			this.title = optinVars.messages.conditions.visitor_logged_in;
		},
		defaults: function() {
			return {
				'show_to': 'logged_in'
			};
		},
		getHeader: function() {
			if ( this.getAttribute( 'show_to' ).length && 'logged_out' === this.getAttribute( 'show_to' ) ) {
				return optinVars.messages.condition_labels.logged_out;
			} else {
				return optinVars.messages.condition_labels.logged_in;
			}
		},
		body: function() {
			return this.template( this.getData() );
		}
	});

	/**
	 * Amount of times the module has been shown to the same visitor
	 */
	Optin.View.Conditions.shown_less_than = ConditionBase.extend({ // eslint-disable-line camelcase
		conditionId: 'shown_less_than',
		setProperties() {
			this.title = optinVars.messages.conditions.shown_less_than;
		},
		defaults: function() {
			return {
				'less_or_more': 'less_than',
				'less_than': ''
			};
		},
		getHeader: function() {
			if ( 0 < this.getAttribute( 'less_than' ) ) {
				if ( 'less_than' === this.getAttribute( 'less_or_more' ) ) {
					return ( optinVars.messages.condition_labels.number_views ).replace( '{number}',  this.getAttribute( 'less_than' ) );
				} else {
					return ( optinVars.messages.condition_labels.number_views_more ).replace( '{number}',  this.getAttribute( 'less_than' ) );
				}
			} else {
				return optinVars.messages.condition_labels.any;
			}
		},
		body: function() {
			return this.template( this.getData() );
		}
	});

	/**
	 * Visitor is on mobile / desktop
	 */
	Optin.View.Conditions.visitor_device = ConditionBase.extend({ // eslint-disable-line camelcase
		conditionId: 'visitor_device',
		setProperties() {
			this.title = optinVars.messages.conditions.only_on_mobile;
		},
		defaults: function() {
			return {
				'filter_type': 'mobile' // mobile | not_mobile
			};
		},
		getHeader: function() {
			if ( 'not_mobile' === this.getAttribute( 'filter_type' ) ) {
				return optinVars.messages.condition_labels.desktop_only;
			} else {
				return optinVars.messages.condition_labels.mobile_only;
			}
		},
		body: function() {
			return this.template( this.getData() );
		}
	});

	/**
	 * From referrer
	 */
	Optin.View.Conditions.from_referrer = ConditionBase.extend({ // eslint-disable-line camelcase
		conditionId: 'from_referrer',
		disable: [ 'from_referrer' ],
		setProperties() {
			this.title = optinVars.messages.conditions.from_specific_ref;
		},
		defaults: function() {
			return {
				'filter_type': 'true', // true | false
				refs: ''
			};
		},
		getHeader: function() {
			let length = 0;
			if ( this.getAttribute( 'refs' ).length ) {
				length = this.countLines( this.getAttribute( 'refs' ) );
			}
			if ( length ) {
				return ( 'false' === this.getAttribute( 'filter_type' ) ? optinVars.messages.condition_labels.except_these : optinVars.messages.condition_labels.only_these ).replace( '{number}', length );
			} else {
				return 'false' === this.getAttribute( 'filter_type' ) ? optinVars.messages.condition_labels.any : optinVars.messages.condition_labels.none;
			}
		},
		body: function() {
			return this.template( this.getData() );
		}
	});

	/**
	 * Source of arrival
	 */
	Optin.View.Conditions.source_of_arrival = ConditionBase.extend({ // eslint-disable-line camelcase
		conditionId: 'source_of_arrival',
		setProperties() {
			this.title = optinVars.messages.conditions.from_search_engine;
		},
		defaults: function() {
			return {
				'source_direct': 'false', // true | false
				'source_external': 'false', // true | false
				'source_internal': 'false', // true | false
				'source_not_search': 'false', // true | false
				'source_search': 'false' // true | false
			};
		},
		getHeader: function() {
			let conditions = 0,
				direct = _.isTrue( this.getAttribute( 'source_direct' ) ) && ++conditions,
				external = _.isTrue( this.getAttribute( 'source_external' ) ) && ++conditions,
				internal = _.isTrue( this.getAttribute( 'source_internal' ) ) && ++conditions,
				search = _.isTrue( this.getAttribute( 'source_search' ) ) && ++conditions,
				notSearch = _.isTrue( this.getAttribute( 'source_not_search' ) ) && ++conditions	;

			if ( search && notSearch || direct && internal && external ) {
				return optinVars.messages.condition_labels.any;
			} else if ( conditions ) {
				return ( optinVars.messages.condition_labels.any_conditions ).replace( '{number}', conditions );
			} else {
				return optinVars.messages.condition_labels.any;
			}
		},
		body: function() {
			return this.template( this.getData() );
		}
	});

	/**
	 * On/not on specific url
	 */
	Optin.View.Conditions.on_url = ConditionBase.extend({ // eslint-disable-line camelcase
		conditionId: 'on_url',
		setProperties() {
			this.title = optinVars.messages.conditions.on_specific_url;
		},
		defaults: function() {
			return {
				'filter_type': 'except', // except | only
				urls: ''
			};
		},
		getHeader: function() {
			let length = 0;
			if ( this.getAttribute( 'urls' ).length ) {
				length = this.countLines( this.getAttribute( 'urls' ) );
			}
			if ( length ) {
				return ( 'only' === this.getAttribute( 'filter_type' ) ? optinVars.messages.condition_labels.only_these : optinVars.messages.condition_labels.except_these ).replace( '{number}', length );
			} else {
				return 'only' === this.getAttribute( 'filter_type' ) ? optinVars.messages.condition_labels.none : optinVars.messages.condition_labels.all;
			}
		},
		body: function() {
			return this.template( this.getData() );
		}
	});

	/**
	 * On/not on specific browser
	 */
	Optin.View.Conditions.on_browser = ConditionBase.extend({ // eslint-disable-line camelcase
		conditionId: 'on_browser',
		setProperties() {
			this.title = optinVars.messages.conditions.on_specific_browser;
		},
		defaults: function() {
			return {
				browsers: '',
				'filter_type': 'except' // except | only
			};
		},
		getHeader: function() {
			if ( this.getAttribute( 'browsers' ).length ) {
				return ( 'only' === this.getAttribute( 'filter_type' ) ? optinVars.messages.condition_labels.only_these : optinVars.messages.condition_labels.except_these ).replace( '{number}', this.getAttribute( 'browsers' ).length );
			} else {
				return 'only' === this.getAttribute( 'filter_type' ) ? optinVars.messages.condition_labels.none : optinVars.messages.condition_labels.all;
			}
		},
		body: function() {
			return this.template( this.getData() );
		},
		rendered: function() {
			this.$( '.sui-select' )
				.val( this.getAttribute( 'browsers' ) )
				.SUIselect2()
				.on( 'select2:selecting', reenableScroll )
				.on( 'select2:unselecting', reenableScroll );
		}
	});

	/**
	 * Visitor commented or not
	 */
	Optin.View.Conditions.visitor_commented = ConditionBase.extend({ // eslint-disable-line camelcase
		conditionId: 'visitor_commented',
		setProperties() {
			this.title = optinVars.messages.conditions.visitor_has_never_commented;
		},
		defaults: function() {
			return {
				'filter_type': 'true' // true | false
			};
		},
		getHeader: function() {
			return 'false' === this.getAttribute( 'filter_type' ) ? optinVars.messages.condition_labels.false : optinVars.messages.condition_labels.true;
		},
		body: function() {
			return this.template( this.getData() );
		}
	});

	/**
	 * User has role
	 */
	Optin.View.Conditions.user_roles = ConditionBase.extend({ // eslint-disable-line camelcase
		conditionId: 'user_roles',
		setProperties() {
			this.title = optinVars.messages.conditions.on_specific_roles;
		},
		defaults: function() {
			return {
				roles: '',
				'filter_type': 'except' // except | only
			};
		},
		getHeader: function() {
			if ( this.getAttribute( 'roles' ).length ) {
				return ( 'only' === this.getAttribute( 'filter_type' ) ? optinVars.messages.condition_labels.only_these : optinVars.messages.condition_labels.except_these ).replace( '{number}', this.getAttribute( 'roles' ).length );
			} else {
				return 'only' === this.getAttribute( 'filter_type' ) ? optinVars.messages.condition_labels.none : optinVars.messages.condition_labels.all;
			}
		},
		body: function() {
			return this.template( this.getData() );
		},
		rendered: function() {
			this.$( '.sui-select' )
				.val( this.getAttribute( 'roles' ) )
				.SUIselect2()
				.on( 'select2:selecting', reenableScroll )
				.on( 'select2:unselecting', reenableScroll );
		}
	});

	/**
	 * Page templates
	 */
	Optin.View.Conditions.page_templates = ConditionBase.extend({ // eslint-disable-line camelcase
		conditionId: 'page_templates',
		setProperties() {
			this.title = optinVars.messages.conditions.on_specific_templates;
		},
		defaults: function() {
			return {
				templates: '',
				'filter_type': 'except' // except | only
			};
		},
		getHeader: function() {
			if ( this.getAttribute( 'templates' ).length ) {
				return ( 'only' === this.getAttribute( 'filter_type' ) ? optinVars.messages.condition_labels.only_these : optinVars.messages.condition_labels.except_these ).replace( '{number}', this.getAttribute( 'templates' ).length );
			} else {
				return 'only' === this.getAttribute( 'filter_type' ) ? optinVars.messages.condition_labels.none : optinVars.messages.condition_labels.all;
			}
		},
		body: function() {
			return this.template( this.getData() );
		},
		rendered: function() {
			this.$( '.sui-select' )
				.val( this.getAttribute( 'templates' ) )
				.SUIselect2()
				.on( 'select2:selecting', reenableScroll )
				.on( 'select2:unselecting', reenableScroll );
		}
	});

	/**
	 * Show modules based on user registration time
	 */
	Optin.View.Conditions.user_registration = ConditionBase.extend({ // eslint-disable-line camelcase
		conditionId: 'user_registration',
		setProperties() {
			this.title = optinVars.messages.conditions.user_registration;
		},
		defaults: function() {
			return {
				'from_date': 0,
				'to_date': 0
			};
		},
		getHeader: function() {
			let from, upTo;

			from = 0 < this.getAttribute( 'from_date' ) ?
				( optinVars.messages.condition_labels.reg_date ).replace( '{number}',  this.getAttribute( 'from_date' ) ) :
				optinVars.messages.condition_labels.immediately;

			upTo = 0 < this.getAttribute( 'to_date' ) ?
				( optinVars.messages.condition_labels.reg_date ).replace( '{number}',  this.getAttribute( 'to_date' ) ) :
				optinVars.messages.condition_labels.forever;

			return from + ' - ' + upTo;
		},
		body: function() {
			return this.template( this.getData() );
		}
	});

	/**
	 * Visitor country
	 */
	Optin.View.Conditions.visitor_country = ConditionBase.extend({ // eslint-disable-line camelcase
		conditionId: 'visitor_country',
		setProperties() {
			this.title = optinVars.messages.conditions.not_in_a_country;
		},
		defaults: function() {
			return {
				countries: '',
				'filter_type': 'except' // only | except
			};
		},
		getHeader: function() {
			if ( this.getAttribute( 'countries' ).length ) {
				return ( 'only' === this.getAttribute( 'filter_type' ) ? optinVars.messages.condition_labels.only_these : optinVars.messages.condition_labels.except_these ).replace( '{number}',  this.getAttribute( 'countries' ).length );
			} else {
				return 'only' === this.getAttribute( 'filter_type' ) ? optinVars.messages.condition_labels.none : optinVars.messages.condition_labels.all;
			}
		},
		body: function() {
			return this.template( this.getData() );
		},
		rendered: function() {
			this.$( '.sui-select' )
				.val( this.getAttribute( 'countries' ) )
				.SUIselect2()
				.on( 'select2:selecting', reenableScroll )
				.on( 'select2:unselecting', reenableScroll );
		}
	});

	/**
	 * Static Pages
	 */
	Optin.View.Conditions.wp_conditions = ConditionBase.extend({ // eslint-disable-line camelcase
		conditionId: 'wp_conditions',
		setProperties() {
			this.title = optinVars.messages.conditions.wp_conditions;
		},
		defaults: function() {
			return {
				'wp_conditions': '',
				'filter_type': 'except' // except | only
			};
		},
		getHeader: function() {
			if ( this.getAttribute( 'wp_conditions' ).length ) {
				return ( 'only' === this.getAttribute( 'filter_type' ) ? optinVars.messages.condition_labels.only_these : optinVars.messages.condition_labels.except_these ).replace( '{number}', this.getAttribute( 'wp_conditions' ).length );
			} else {
				return 'only' === this.getAttribute( 'filter_type' ) ? optinVars.messages.condition_labels.none : optinVars.messages.condition_labels.all;
			}
		},
		body: function() {
			return this.template( this.getData() );
		},
		rendered: function() {
			this.$( '.sui-select' )
				.val( this.getAttribute( 'wp_conditions' ) )
				.SUIselect2()
				.on( 'select2:selecting', reenableScroll )
				.on( 'select2:unselecting', reenableScroll );
		}
	});

	/**
	 * Archive Pages
	 */
	Optin.View.Conditions.archive_pages = ConditionBase.extend({ // eslint-disable-line camelcase
		conditionId: 'archive_pages',
		setProperties() {
			this.title = optinVars.messages.conditions.archive_pages;
		},
		defaults: function() {
			return {
				'archive_pages': '',
				'filter_type': 'except' // except | only
			};
		},
		getHeader: function() {
			if ( this.getAttribute( 'archive_pages' ).length ) {
				return ( 'only' === this.getAttribute( 'filter_type' ) ? optinVars.messages.condition_labels.only_these : optinVars.messages.condition_labels.except_these ).replace( '{number}', this.getAttribute( 'archive_pages' ).length );
			} else {
				return 'only' === this.getAttribute( 'filter_type' ) ? optinVars.messages.condition_labels.none : optinVars.messages.condition_labels.all;
			}
		},
		body: function() {
			return this.template( this.getData() );
		},
		rendered: function() {
			this.$( '.sui-select' )
				.val( this.getAttribute( 'archive_pages' ) )
				.SUIselect2()
				.on( 'select2:selecting', reenableScroll )
				.on( 'select2:unselecting', reenableScroll );
		}
	});


/**********************************************************************************************************************************************************/
/*********************************** WooCommerce Conditions ***********************************************************************************************/
/**********************************************************************************************************************************************************/

	/**
	 * All WooCommerce Pages
	 */
	Optin.View.Conditions.wc_pages = ConditionBase.extend( _.extend({}, ToggleButtonTogglerMixin, { // eslint-disable-line camelcase
		conditionId: 'wc_pages',
		setProperties() {
			this.title = optinVars.messages.conditions.wc_pages;
		},
		defaults: function() {
			return {
				'filter_type': 'all' // all | none
			};
		},
		getHeader: function() {
			if ( 'none' === this.getAttribute( 'filter_type' ) ) {
				return optinVars.messages.condition_labels.none;
			} else {
				return optinVars.messages.condition_labels.all;
			}
		},
		body: function() {
			return this.template( this.getData() );
		}
	}) );

	/**
	 * WooCommerce Categories
	 */
	Optin.View.Conditions.wc_categories = ConditionBase.extend( _.extend({}, ToggleButtonTogglerMixin, { // eslint-disable-line camelcase
		conditionId: 'wc_categories',
		setProperties() {
			this.title = optinVars.messages.conditions.wc_categories;
		},
		defaults: function() {
			return {
				'filter_type': 'except', // except | only
				wc_categories: [] // eslint-disable-line camelcase
			};
		},
		onInit: function() {
		},
		getHeader: function() {
			if ( this.getAttribute( 'wc_categories' ).length ) {
				return ( 'only' === this.getAttribute( 'filter_type' ) ? optinVars.messages.condition_labels.only_these : optinVars.messages.condition_labels.except_these ).replace( '{number}',  this.getAttribute( 'wc_categories' ).length );
			} else {
				return 'only' === this.getAttribute( 'filter_type' ) ? optinVars.messages.condition_labels.none : optinVars.messages.condition_labels.all;
			}
		},
		body: function() {
			return this.template( this.getData() );
		},
		rendered: function() {
			this.$( '.hustle-select-ajax' ).SUIselect2({
					tags: 'true',
					width: '100%',
					dropdownCssClass: 'sui-select-dropdown',
					ajax: {
						url: ajaxurl,
						delay: 250, // wait 250 milliseconds before triggering the request
						dataType: 'json',
						type: 'POST',
						data: function( params ) {
							var query = {
								action: 'get_new_condition_ids',
								search: params.term,
								postType: 'wc_category'
							};

							return query;
						},
						processResults: function( data ) {
							return {
								results: data.data
							};
						},
						cache: true
					},
					createTag: function() {
						return false;
					}
			})
			.on( 'select2:selecting', reenableScroll )
			.on( 'select2:unselecting', reenableScroll );
		}
	}) );

	/**
	 * WooCommerce Tags
	 */
	Optin.View.Conditions.wc_tags = ConditionBase.extend( _.extend({}, ToggleButtonTogglerMixin, { // eslint-disable-line camelcase
		conditionId: 'wc_tags',
		setProperties() {
			this.title = optinVars.messages.conditions.wc_tags;
		},
		defaults: function() {
			return {
				'filter_type': 'except', // except | only
				wc_tags: [] // eslint-disable-line camelcase
			};
		},
		onInit: function() {
		},
		getHeader: function() {
			if ( this.getAttribute( 'wc_tags' ).length ) {
				return ( 'only' === this.getAttribute( 'filter_type' ) ? optinVars.messages.condition_labels.only_these : optinVars.messages.condition_labels.except_these ).replace( '{number}',  this.getAttribute( 'wc_tags' ).length );
			} else {
				return 'only' === this.getAttribute( 'filter_type' ) ? optinVars.messages.condition_labels.none : optinVars.messages.condition_labels.all;
			}
		},
		body: function() {
			return this.template( this.getData() );
		},
		rendered: function() {
			this.$( '.hustle-select-ajax' ).SUIselect2({
					tags: 'true',
					width: '100%',
					dropdownCssClass: 'sui-select-dropdown',
					ajax: {
						url: ajaxurl,
						delay: 250, // wait 250 milliseconds before triggering the request
						dataType: 'json',
						type: 'POST',
						data: function( params ) {
							var query = {
								action: 'get_new_condition_ids',
								search: params.term,
								postType: 'wc_tag'
							};

							return query;
						},
						processResults: function( data ) {
							return {
								results: data.data
							};
						},
						cache: true
					},
					createTag: function() {
						return false;
					}
			})
			.on( 'select2:selecting', reenableScroll )
			.on( 'select2:unselecting', reenableScroll );
		}
	}) );

	/**
	 * WooCommerce Archive Pages
	 */
	Optin.View.Conditions.wc_archive_pages = ConditionBase.extend({ // eslint-disable-line camelcase
		conditionId: 'wc_archive_pages',
		setProperties() {
			this.title = optinVars.messages.conditions.wc_archive_pages;
		},
		defaults: function() {
			return {
				'wc_archive_pages': '',
				'filter_type': 'except' // except | only
			};
		},
		getHeader: function() {
			if ( this.getAttribute( 'wc_archive_pages' ).length ) {
				return ( 'only' === this.getAttribute( 'filter_type' ) ? optinVars.messages.condition_labels.only_these : optinVars.messages.condition_labels.except_these ).replace( '{number}', this.getAttribute( 'wc_archive_pages' ).length );
			} else {
				return 'only' === this.getAttribute( 'filter_type' ) ? optinVars.messages.condition_labels.none : optinVars.messages.condition_labels.all;
			}
		},
		body: function() {
			return this.template( this.getData() );
		},
		rendered: function() {
			this.$( '.sui-select' )
				.val( this.getAttribute( 'wc_archive_pages' ) )
				.SUIselect2()
				.on( 'select2:selecting', reenableScroll )
				.on( 'select2:unselecting', reenableScroll );
		}
	});

	/**
	 * WooCommerce Static Pages
	 */
	Optin.View.Conditions.wc_static_pages = ConditionBase.extend({ // eslint-disable-line camelcase
		conditionId: 'wc_static_pages',
		setProperties() {
			this.title = optinVars.messages.conditions.wc_static_pages;
		},
		defaults: function() {
			return {
				'wc_static_pages': '',
				'filter_type': 'except' // except | only
			};
		},
		getHeader: function() {
			if ( this.getAttribute( 'wc_static_pages' ).length ) {
				return ( 'only' === this.getAttribute( 'filter_type' ) ? optinVars.messages.condition_labels.only_these : optinVars.messages.condition_labels.except_these ).replace( '{number}', this.getAttribute( 'wc_static_pages' ).length );
			} else {
				return 'only' === this.getAttribute( 'filter_type' ) ? optinVars.messages.condition_labels.none : optinVars.messages.condition_labels.all;
			}
		},
		body: function() {
			return this.template( this.getData() );
		},
		rendered: function() {
			this.$( '.sui-select' )
				.val( this.getAttribute( 'wc_static_pages' ) )
				.SUIselect2()
				.on( 'select2:selecting', reenableScroll )
				.on( 'select2:unselecting', reenableScroll );
		}
	});

	$( document ).trigger( 'hustleAddViewConditions', [ ConditionBase ]);

}( jQuery ) );

Hustle.define( 'Settings.Palettes', function( $ ) {
	'use strict';

	return Backbone.View.extend({
		el: '#palettes-box',

		events: {
			'click .hustle-create-palette': 'openCreatePaletteModal',
			'click .hustle-delete-button': 'openDeletePaletteModal',
			'click .hustle-button-delete': 'delettePalette'
		},

		initialize() {
			const PaletteModal = Hustle.get( 'Settings.Palettes_Modal' );
			this.paletteModal = new PaletteModal();
		},

		openCreatePaletteModal( e ) {
			this.paletteModal.open( e );
		},

		openDeletePaletteModal( e ) {
			e.preventDefault();

			let $this = $( e.currentTarget ),
				data = {
					id: $this.data( 'id' ),
					title: $this.data( 'title' ),
					description: $this.data( 'description' ),
					action: 'delete',
					nonce: $this.data( 'nonce' ),
					actionClass: 'hustle-button-delete'
				};

			Module.deleteModal.open( data );

			// This element is outside the view and only added after opening the modal.
			$( '.hustle-button-delete' ).on( 'click', $.proxy( this.delettePalette, this ) );
		},

		/**
		 * Handle the color palettes 'delete' action.
		 * @since 4.0.3
		 * @param {Object} e
		 */
		delettePalette( e ) {
			e.preventDefault();

			const $this = $( e.currentTarget ),
				relatedFormId = $this.data( 'form-id' ),
				actionData = $this.data(),
				$form = $( '#' + relatedFormId ),
				data = new FormData( $form[0]);

			// TODO: remove when "hustle_action" field name is changed to "hustleAction"
			$.each( actionData, ( name, value ) => data.append( name, value ) );

			data.append( '_ajax_nonce', optinVars.settings_palettes_action_nonce );
			data.append( 'action', 'hustle_handle_palette_actions' );

			$.ajax({
				url: ajaxurl,
				type: 'POST',
				data,
				contentType: false,
				processData: false
			})
			.done( res => {

				if ( res.data.url ) {
					location.replace( res.data.url );

				} else if ( res.data.notification ) {
					Module.Notification.open( res.data.notification.status, res.data.notification.message, res.data.notification.delay );
				}

				// Don't remove the 'loading' icon when redirecting/reloading.
				if ( ! res.data.url ) {
					$( '.sui-button-onload' ).removeClass( 'sui-button-onload' );
				}
			})
			.error( () => {
				Module.Notification.open( 'error', optinVars.messages.commons.generic_ajax_error );
				$( '.sui-button-onload' ).removeClass( 'sui-button-onload' );
			});
		}


	});
});

Hustle.define( 'Settings.Data_Settings', function( $ ) {
	'use strict';
	return Backbone.View.extend({
		el: '#data-box',

		events: {
			'click #hustle-dialog-open--reset-data-settings': 'dataDialog'
		},

		// ============================================================
		// DIALOG: Reset Settings
		// Open dialog
		dataDialog: function( e ) {

			var $button = this.$( e.target ),
				$dialog = $( '#hustle-dialog--reset-data-settings' ),
				$title  = $dialog.find( '#dialogTitle' ),
				$info   = $dialog.find( '#dialogDescription' )
				;

			$title.text( $button.data( 'dialog-title' ) );
			$info.text( $button.data( 'dialog-info' ) );

			SUI.dialogs['hustle-dialog--reset-data-settings'].show();

			e.preventDefault();

			$( '#hustle-reset-settings' ).on( 'click', $.proxy( this.settingsReset ) );
		},

		// Confirm and close
		settingsReset: function( e ) {
			var $this    = $( e.currentTarget ),
				$dialog  = $this.closest( '.sui-dialog' ),
				$buttons = $dialog.find( 'button, .sui-button' );

			$buttons.prop( 'disabled', true );
			$this.addClass( 'sui-button-onload' );
			$.ajax({
				url: ajaxurl,
				type: 'POST',
				data: {
					action: 'hustle_reset_settings',
					_ajax_nonce: $this.data( 'nonce' ) // eslint-disable-line camelcase
				},
				success: function() {
					$( '#' + $this.data( 'notice' ) ).show();
					SUI.dialogs[ $dialog.attr( 'id' ) ].hide();
					$this.removeClass( 'sui-button-onload' );
					$buttons.prop( 'disabled', false );
					Module.Notification.open( 'success', optinVars.messages.settings_was_reset );
					window.setTimeout( () => location.reload( true ), 2000 );
				},
				error: function() {
					SUI.dialogs[ $dialog.attr( 'id' ) ].hide();
					$this.removeClass( 'sui-button-onload' );
					$buttons.prop( 'disabled', false );
					Module.Notification.open( 'error', optinVars.messages.something_went_wrong );
				}
			});
		}
	});

});

Hustle.define( 'Settings.Palettes_Modal', function( $ ) {

	'use strict';

	return Backbone.View.extend({

		el: '#hustle-dialog--edit-palette',

		events: {
			'click .hustle-button-action': 'handleAction',
			'click .hustle-cancel-palette': 'closeCreatePaletteModal',
			'change #hustle-palette-module-type': 'updateModulesOptions'
		},

		initialize() {},

		open( e ) {

			const slug = $( e.currentTarget ).data( 'slug' );

			if ( 'undefined' !== typeof slug ) {

				// When editing a palette.
				this.handleAction( e );
			} else {

				// When creating a new palette.

				// Update the modules' options when opening.
				this.$( '#hustle-palette-module-type' ).trigger( 'change' );

				SUI.openModal( 'hustle-dialog--edit-palette', e.currentTarget, 'hustle-palette-name', false );
			}
		},

		/**
		 * Handle the color palettes 'save' action.
		 * @since 4.0.3
		 * @param {Object} e
		 */
		handleAction( e ) {
			e.preventDefault();

			const self = this,
				$this = $( e.currentTarget ),
				relatedFormId = $this.data( 'form-id' ),
				actionData = $this.data();

			$this.addClass( 'sui-button-onload' );
			Module.Utils.accessibleHide( this.$( '.sui-error-message' ) );

			let data = new FormData(),
				errors = false ;


			// Grab the form's data if the action has a related form.
			if ( 'undefined' !== typeof relatedFormId ) {
				const $form = $( '#' + relatedFormId );

				if ( $form.length ) {
					data = new FormData( $form[0]);
					$form.find( '.hustle-required-field' ).each( ( i, el ) => {
						const $field = $( el );

							if ( ! $field.val().trim().length ) {
								const errorMessage = $field.data( 'error-message' ),
									$errorMessage = $field.siblings( '.sui-error-message' );

								$errorMessage.html( errorMessage );
								Module.Utils.accessibleShow( $errorMessage );
								errors = true;
							}
					});
				}
			}

			// Don't do the request if there are missing required fields.
			if ( errors ) {
				$( '.sui-button-onload' ).removeClass( 'sui-button-onload' );
				return;
			}

			$.each( actionData, ( name, value ) => data.append( name, value ) );

			data.append( '_ajax_nonce', optinVars.settings_palettes_action_nonce );
			data.append( 'action', 'hustle_handle_palette_actions' );

			$.ajax({
				url: ajaxurl,
				type: 'POST',
				data,
				contentType: false,
				processData: false
			})
			.done( res => {

				// If there's a defined callback, call it.
				if ( res.data.callback && 'function' === typeof self[ res.data.callback ]) {

					// This calls the "action{ hustle action }" functions from this view.
					// For example: actionToggleStatus();
					self[ res.data.callback ]( res.data, res.success, e );

				} else if ( res.data.url ) {
					location.replace( res.data.url );

				} else if ( res.data.notification ) {

					Module.Notification.open( res.data.notification.status, res.data.notification.message, res.data.notification.delay );
				}

				// Don't remove the 'loading' icon when redirecting/reloading.
				if ( ! res.data.url ) {
					$( '.sui-button-onload' ).removeClass( 'sui-button-onload' );
				}
			})
			.error( res => {
				$( '.sui-button-onload' ).removeClass( 'sui-button-onload' );
			});
		},

		actionOpenEditPalette( data, success, e ) {

			this.actionGoToSecondStep( data );
			SUI.openModal( 'hustle-dialog--edit-palette', e.currentTarget, 'hustle-palette-name', false );

			if ( data.palette_data.name ) {
				$( '#hustle-dialog--edit-palette' ).find( '#hustle-palette-name' ).val( data.palette_data.name );
			}
		},

		actionGoToSecondStep( data ) {

			const stepOne     = this.$( '#hustle-edit-palette-first-step' ),
				stepTwo     = this.$( '#hustle-edit-palette-second-step' ),
				btnAction   = this.$( '.hustle-button-action' ),
				paletteData = data.palette_data,
				template    = Optin.template( 'hustle-dialog--edit-palette-tpl' );

			// Hide first step
			Module.Utils.accessibleHide( stepOne, true );

			// Print and show second step
			stepTwo.html( template( paletteData ) );
			this.initiateSecondStepElements();

			Module.Utils.accessibleShow( stepTwo, true );
			stepTwo.focus();

			// Set new step
			btnAction.data( 'step', 3 );
			btnAction.addClass( 'sui-button-blue' );
			Module.Utils.accessibleHide( btnAction.find( '#hustle-step-button-text' ) );
			Module.Utils.accessibleShow( btnAction.find( '#hustle-finish-button-text' ) );

		},

		initiateSecondStepElements() {

			// Accordions.
			this.$( '.sui-accordion' ).each( function() {
				SUI.suiAccordion( this );
			});

			// Init tabs
			SUI.suiTabs();
			SUI.tabs();

			// Color pickers.
			this.createPickers();
		},

		closeCreatePaletteModal() {

			const self    = this,
				stepOne   = this.$( '#hustle-edit-palette-first-step' ),
				stepTwo   = this.$( '#hustle-edit-palette-second-step' ),
				btnAction = this.$( '.hustle-button-action' );

			// Hide modal
			SUI.closeModal();

			setTimeout( function() {

				// Hide error messages
				Module.Utils.accessibleHide( self.$( '.sui-error-message' ) );

				// Hide second step
				Module.Utils.accessibleHide( stepTwo, true );
				stepTwo.html( '' );

				// Show first step
				Module.Utils.accessibleShow( stepOne, true );

				// Reset action button
				btnAction.removeClass( 'sui-button-blue' );
				btnAction.data( 'step', 2 );
				Module.Utils.accessibleShow( btnAction.find( '#hustle-step-button-text' ) );
				Module.Utils.accessibleHide( btnAction.find( '#hustle-finish-button-text' ) );

			}, 500 );

		},

		// ============================================================
		// Color Pickers

		// TODO: Copied from wizards. Re-use instead of copy-pasting
		createPickers: function() {

			var self = this,
				$suiPickerInputs = this.$( '.sui-colorpicker-input' );

			$suiPickerInputs.wpColorPicker({

				change: function( event, ui ) {
					var $this = $( this );

					// Prevent the model from being marked as changed on load.
					if ( $this.val() !== ui.color.toCSS() ) {
						$this.val( ui.color.toCSS() ).trigger( 'change' );
					}
				},
				palettes: [
					'#333333',
					'#FFFFFF',
					'#17A8E3',
					'#E1F6FF',
					'#666666',
					'#AAAAAA',
					'#E6E6E6'
				]
			});

			if ( $suiPickerInputs.hasClass( 'wp-color-picker' ) ) {

				$suiPickerInputs.each( function() {

					var $suiPickerInput = $( this ),
						$suiPicker      = $suiPickerInput.closest( '.sui-colorpicker-wrap' ),
						$suiPickerColor = $suiPicker.find( '.sui-colorpicker-value span[role=button]' ),
						$suiPickerValue = $suiPicker.find( '.sui-colorpicker-value' ),
						$suiPickerClear = $suiPickerValue.find( 'button' ),
						$suiPickerType  = 'hex'
						;

					var $wpPicker       = $suiPickerInput.closest( '.wp-picker-container' ),
						$wpPickerButton = $wpPicker.find( '.wp-color-result' ),
						$wpPickerAlpha  = $wpPickerButton.find( '.color-alpha' ),
						$wpPickerClear  = $wpPicker.find( '.wp-picker-clear' )
						;

					// Check if alpha exists
					if ( true === $suiPickerInput.data( 'alpha' ) ) {

						$suiPickerType = 'rgba';

						// Listen to color change
						$suiPickerInput.bind( 'change', function() {

							// Change color preview
							$suiPickerColor.find( 'span' ).css({
								'background-color': $wpPickerAlpha.css( 'background' )
							});

							// Change color value
							$suiPickerValue.find( 'input' ).val( $suiPickerInput.val() );

						});

					} else {

						// Listen to color change
						$suiPickerInput.bind( 'change', function() {

							// Change color preview
							$suiPickerColor.find( 'span' ).css({
								'background-color': $wpPickerButton.css( 'background-color' )
							});

							// Change color value
							$suiPickerValue.find( 'input' ).val( $suiPickerInput.val() );

						});
					}

					// Add picker type class
					$suiPicker.find( '.sui-colorpicker' ).addClass( 'sui-colorpicker-' + $suiPickerType );

					// Open iris picker
					$suiPicker.find( '.sui-button, span[role=button]' ).on( 'click', function( e ) {

						$wpPickerButton.click();

						e.preventDefault();
						e.stopPropagation();

					});

					// Clear color value
					$suiPickerClear.on( 'click', function( e ) {

						let inputName = $suiPickerInput.data( 'attribute' ),
							selectedStyle = $( '#hustle-palette-module-fallback' ).val(),
							resetValue = optinVars.palettes[ selectedStyle ][ inputName ];

						$wpPickerClear.click();
						$suiPickerValue.find( 'input' ).val( resetValue );
						$suiPickerInput.val( resetValue ).trigger( 'change' );
						$suiPickerColor.find( 'span' ).css({
							'background-color': resetValue
						});

						e.preventDefault();
						e.stopPropagation();

					});
				});
			}
		},

		updateModulesOptions( e ) {

			const $this = $( e.currentTarget ),
				moduleType = $this.val(),
				$modulesOptionsSelect = this.$( '#hustle-palette-module-name' );

			let html = '';

			$.each( optinVars.current[ moduleType ], ( id, name ) => {
				html += `<option value="${ id }">${ name }</option>`;
			});

			$modulesOptionsSelect.html( html );

			this.$( '.sui-select:not(.hustle-select-ajax)' ).SUIselect2({
				dropdownCssClass: 'sui-select-dropdown'
			});
		}

	});
});

Hustle.define( 'Settings.Permissions_View', function( $ ) {
	'use strict';

	return Backbone.View.extend({

		el: '#permissions-box',

		initialize: function() {
			$( function() {

				//Delete the remove ability for Administrator option in select2
				function blockingAdminRemove() {
					$( '.select2-selection__rendered li:first-child .select2-selection__choice__remove' ).off( 'click' ).text( '' ).on( 'click', function( e ) {
						e.stopImmediatePropagation();
						e.preventDefault();
					});
				}
				$( 'select' ).on( 'change.select2', function( e ) {
					blockingAdminRemove();
				});
				blockingAdminRemove();
			});
		}
	});
});

Hustle.define( 'Settings.Privacy_Settings', function( $ ) {
	'use strict';
	return Backbone.View.extend({
		el: '#privacy-box',

		events: {
			'click #hustle-dialog-open--delete-ips': 'openDeleteIpsDialog'
		},

		initialize: function() {
			$( '#hustle-delete-ips-submit' ).on( 'click', this.handleIpDeletion );
		},

		// ============================================================
		// DIALOG: Delete All IPs
		// Open dialog
		openDeleteIpsDialog( e ) {
			SUI.dialogs['hustle-dialog--delete-ips'].show();
			e.preventDefault();
		},

		handleIpDeletion( e ) {
			e.preventDefault();

			const $this = $( e.currentTarget ),
				$dialog  = $this.closest( '.sui-dialog' ),
				$form = $( '#' + $this.data( 'formId' ) ),
				data = new FormData( $form[0]);

			data.append( 'action', 'hustle_remove_ips' );
			data.append( '_ajax_nonce', $this.data( 'nonce' ) );

			$this.addClass( 'sui-button-onload' );

			$.ajax({
				url: ajaxurl,
				type: 'POST',
				data,
				contentType: false,
				processData: false,
				success: function( res ) {

					Module.Notification.open( 'success', res.data.message );
					SUI.dialogs[ $dialog.attr( 'id' ) ].hide();
					$( '.sui-button-onload' ).removeClass( 'sui-button-onload' );
				},
				error: function() {
					SUI.dialogs[ $dialog.attr( 'id' ) ].hide();
					$( '.sui-button-onload' ).removeClass( 'sui-button-onload' );
					Module.Notification.open( 'error', optinVars.messages.something_went_wrong );
				}
			});
		}

	});

});

Hustle.define( 'Settings.reCaptcha_Settings', function( $ ) {
	'use strict';
	return Backbone.View.extend({
		el: '#recaptcha-box',
		data: {},

		initialize() {
			this.maybeRenderRecaptchas();
		},

		maybeRenderRecaptchas() {

			const self = this,
				versions = [ 'v2_checkbox', 'v2_invisible', 'v3_recaptcha' ];

			let scriptAdded = false;

			for ( let version of versions ) {

				const $previewContainer = this.$( `#hustle-modal-recaptcha-${ version }-0` ),
					sitekey = this.$( `input[name="${ version }_site_key"]` ).val().trim(),
					secretkey = this.$( `input[name="${ version }_secret_key"]` ).val().trim();

				if ( sitekey && secretkey ) {

					$previewContainer.data( 'sitekey', sitekey );

					if ( ! scriptAdded ) {

						$.ajax({
							url: ajaxurl,
							type: 'POST',
							data: {
								action: 'hustle_load_recaptcha_preview'
							}
						}).done( result => {
							if ( result.success ) {
								scriptAdded = true;
								self.$( '#hustle-recaptcha-script-container' ).html( result.data );
								setTimeout( () => HUI.maybeRenderRecaptcha( $previewContainer.closest( '.sui-form-field' ) ), 1000 );
							}
						});

					} else {
						HUI.maybeRenderRecaptcha( $previewContainer.closest( '.sui-form-field' ) );
					}

					this.$( `.hustle-recaptcha-${ version }-preview-notice` ).hide();
					$previewContainer.show();

				} else {
					this.$( `.hustle-recaptcha-${ version }-preview-notice` ).show();
					$previewContainer.hide();
				}
			}
		}
	});

});

Hustle.define( 'Settings.Top_Metrics_View', function( $, doc, win ) {
	'use strict';

	return Backbone.View.extend({
		el: '#top-metrics-box',

		events: {
			'click .sui-checkbox input': 'maybeDisableInputs'
		},

		initialize: function() {
			this.maybeDisableInputs();
		},

		maybeDisableInputs: function() {
			var $allchecked = this.$el.find( 'input:checked' ),
				$unchecked  = this.$el.find( 'input:not(:checked)' ),
				$button  	= this.$el.find( 'button[type="submit"]' ),
				$buttonTip  = $button.parent(),
				$design     = $unchecked.next( 'span' );
			if ( 3 <= $allchecked.length ) {
				$unchecked.prop( 'disabled', true );
				$design.addClass( 'sui-tooltip' );
				$design.css( 'opacity', '1' );
				$button.prop( 'disabled', false );
				$buttonTip.removeClass( 'sui-tooltip' );
			} else {
				$button.prop( 'disabled', true );
				$unchecked.prop( 'disabled', false );
				$design.removeClass( 'sui-tooltip' );
				$design.css( 'opacity', '' );
				$buttonTip.addClass( 'sui-tooltip' );
			}
		}
	});
});

( function( $, doc ) {
	'use strict';

	$( document ).on( 'click', '.wpoi-listing-wrap header.can-open .toggle, .wpoi-listing-wrap header.can-open .toggle-label', function( e ) {
		e.stopPropagation();
	});


	$( '.accordion header .optin-delete-optin, .accordion header .edit-optin, .wpoi-optin-details tr .button-edit' ).hide().css({
		transition: 'none'
	});

	$( document ).on({
		mouseenter: function() {
			var $this = $( this );
			$this.find( '.optin-delete-optin, .edit-optin' ).stop().fadeIn( 'fast' );
		},
		mouseleave: function() {
			var $this = $( this );
			$this.find( '.toggle-checkbox' ).removeProp( 'disabled' );
			$this.find( '.edit-optin' ).removeProp( 'disabled' );
			$this.removeClass( 'disabled' );
			$this.find( '.optin-delete-optin, .edit-optin, .delete-optin-confirmation' ).stop().fadeOut( 'fast' );
		}
	}, '.accordion header' );

	$( document ).on({
		mouseenter: function() {
			var $this = $( this );
			$this.find( '.button-edit' ).stop().fadeIn( 'fast' );
		},
		mouseleave: function() {
			var $this = $( this );
			$this.find( '.button-edit' ).stop().fadeOut( 'fast' );
		}
	}, '.wpoi-optin-details tr' );

	$( document ).on( 'click', '.wpoi-tabs-menu a', function( event ) {
		var tab = $( this ).attr( 'tab' );
		event.preventDefault();
		Optin.router.navigate( tab, true );
	});

	$( document ).on( 'click', '.edit-optin', function( event ) {
		event.stopPropagation();
		event.preventDefault();
		window.location.href = $( this ).attr( 'href' );
	});

	$( document ).on( 'click', '.wpoi-type-edit-button', function( event ) {
		var optinId = $( this ).data( 'id' );
		var optinType = $( this ).data( 'type' );
		event.preventDefault();
		window.location.href = 'admin.php?page=inc_optin&optin=' + optinId + '#display/' + optinType;
	});

	/**
	 * Make 'for' attribute work on tags that don't support 'for' by default
	 *
	 */
	$( document ).on( 'click', '*[for]', function( e ) {
		var $this = $( this ),
			_for = $this.attr( 'for' ),
			$for = $( '#' + _for );

		if ( $this.is( 'label' ) || ! $for.length ) {
			return;
		}

		$for.trigger( 'change' );
		$for.trigger( 'click' );
	});

	$( '#wpoi-complete-message' ).fadeIn();

	$( document ).on( 'click', '#wpoi-complete-message .next-button button', function( e ) {
		$( '#wpoi-complete-message' ).fadeOut();
	});

	$( document ).on( 'click', '.wpoi-listing-page .wpoi-listing-wrap header.can-open', function( e ) {
		$( this ).find( '.open' ).trigger( 'click' );
	});

	/**
	 * On click of arrow of any optin in the listing page
	 *
	 */
	$( document ).on( 'click', '.wpoi-listing-page .wpoi-listing-wrap .can-open .open', function( e ) {
		var $this = $( this ),
			$panel = $this.closest( '.wpoi-listing-wrap' ),
			$section = $panel.find( 'section' ),
			$others = $( '.wpoi-listing-wrap' ).not( $panel ),
			$otherSections = $( '.wpoi-listing-wrap section' ).not( $section );
		e.stopPropagation();

		$otherSections.slideUp( 300, function() {
			$otherSections.removeClass( 'open' );
		});
		$others.find( '.dev-icon' ).removeClass( 'dev-icon-caret_up' ).addClass( 'dev-icon-caret_down' );

		$section.slideToggle( 300, function() {
			$panel.toggleClass( 'open' );
			$panel.find( '.dev-icon' ).toggleClass( 'dev-icon-caret_up dev-icon-caret_down' );
		});

	});

	Optin.decorateNumberInputs = function( elem ) {
		var $items =  elem && elem.$el ? elem.$el.find( '.wph-input--number input' ) : $( '.wph-input--number input' ),
			tpl = Hustle.createTemplate( '<div class="wph-nbr--nav"><div class="wph-nbr--button wph-nbr--up {{disabled}}">+</div><div class="wph-nbr--button wph-nbr--down {{disabled}}">-</div></div>' )
		;
		$items.each( function() {
			var $this = $( this ),
				disabledClass = $this.is( ':disabled' ) ? 'disabled' : '';

			// Add + and - buttons only if it's not already added
			if ( ! $this.siblings( '.wph-nbr--nav' ).length ) {
				$this.after( tpl({ disabled: disabledClass }) );
			}

		});

	};

	Hustle.Events.on( 'view.rendered', Optin.decorateNumberInputs );

	// Listen to number input + and - click events
	( function() {
		$( document ).on( 'click', '.wph-nbr--up:not(.disabled)', function( e ) {
			var $this = $( this ),
				$wrap = $this.closest( '.wph-input--number' ),
				$input = $wrap.find( 'input' ),
				oldValue = parseFloat( $input.val() ),
				min = $input.attr( 'min' ),
				max = $input.attr( 'max' ),
				newVal;

			if ( oldValue >= max ) {
				newVal = oldValue;
			} else {
				newVal = oldValue + 1;
			}

			if ( newVal !== oldValue ) {
				$input.val( newVal ).trigger( 'change' );
			}
		});

		$( document ).on( 'click', '.wph-nbr--down:not(.disabled)', function( e ) {
			var $this = $( this ),
				$wrap = $this.closest( '.wph-input--number' ),
				$input = $wrap.find( 'input' ),
				oldValue = parseFloat( $input.val() ),
				min = $input.attr( 'min' ),
				max = $input.attr( 'max' ),
				newVal;


			if ( oldValue <= min ) {
				newVal = oldValue;
			} else {
				newVal = oldValue - 1;
			}

			if ( newVal !== oldValue ) {
				$input.val( newVal ).trigger( 'change' );
			}
		});
	}() );

	// Sticky eye icon
	( function() {
		function stickyRelocate() {
			var windowTop = $( window ).scrollTop();
			var divTop = $( '.wph-sticky--anchor' );

			if ( ! divTop.length ) {
				return;
			}

			divTop = divTop.offset().top;
			if ( windowTop > divTop ) {
				$( '.wph-preview--eye' ).addClass( 'wph-sticky--element' );
				$( '.wph-sticky--anchor' ).height( $( '.wph-preview--eye' ).outerHeight() );
			} else {
				$( '.wph-preview--eye' ).removeClass( 'wph-sticky--element' );
				$( '.wph-sticky--anchor' ).height( 0 );
			}
		}
		$( function() {
			$( window ).scroll( stickyRelocate );
			stickyRelocate();
		});
	}() );

}( jQuery, document ) );

Hustle.define( 'Integration_Modal_Handler', function( $ ) {
	'use strict';

	return Backbone.View.extend({

		events: {
			'click .hustle-provider-connect': 'connectAddOn',
			'click .hustle-provider-disconnect': 'disconnectAddOn',
			'click .hustle-provider-next': 'submitNextStep',
			'click .hustle-provider-back': 'goPrevStep',
			'click .hustle-refresh-email-lists': 'refreshLists',
			'click .hustle-provider-form-disconnect': 'disconnectAddOnForm',
			'click .hustle-provider-clear-radio-options': 'clearRadioOptions',
			'keypress .sui-dialog-content': 'preventEnterKeyFromDoingThings',
			'change select#group': 'showInterests'
		},

		preventEnterKeyFromDoingThings( e ) {
			if ( 13 === e.which ) { // the enter key code
				e.preventDefault();

				if ( this.$( '.hustle-provider-connect' ).length ) {
					this.$( '.hustle-provider-connect' ).trigger( 'click' );

				} else if ( this.$( '.hustle-provider-next' ).length ) {
					this.$( '.hustle-provider-next' ).trigger( 'click' );
				}
			}
		},

		initialize: function( options ) {

			this.slug      = options.slug;
			this.nonce     = options.nonce;
			this.action    = options.action;
			// eslint-disable-next-line camelcase
			this.moduleId = options.moduleId;
			// eslint-disable-next-line camelcase
			this.multi_id  = options.multiId;
			this.globalMultiId = options.globalMultiId;
			this.step = 0;
			// eslint-disable-next-line camelcase
			this.next_step = false;
			// eslint-disable-next-line camelcase
			this.prev_step = false;

			return this.render();
		},

		render: function() {

			const data = {};

			data.action = this.action;
			// eslint-disable-next-line camelcase
			data._ajax_nonce = this.nonce;
			data.data = {};
			data.data.slug = this.slug;
			data.data.step = this.step;
			// eslint-disable-next-line camelcase
			data.data.current_step = this.step;
			if ( this.moduleId ) {
				// eslint-disable-next-line camelcase
				data.data.module_id = this.moduleId;
			}
			if ( this.multi_id ) {
				// eslint-disable-next-line camelcase
				data.data.multi_id = this.multi_id;
			}
			if ( this.globalMultiId ) {
				// eslint-disable-next-line camelcase
				data.data.global_multi_id = this.globalMultiId;
			}

			this.request( data, false, true );
		},

		applyLoader: function( $element ) {
			$element.find( '.sui-button:not(.disable-loader)' ).addClass( 'sui-button-onload' );
		},

		resetLoader: function( $element ) {
			$element.find( '.sui-button' ).removeClass( 'sui-button-onload' );
		},

		request: function( data, close, loader ) {

			let self = this;

			if ( loader ) {
				this.$el
					.find( '.sui-box-body' )
					.addClass( 'sui-block-content-center' )
					.html(

						// TODO: translate "loading content".
						'<p class="sui-loading-dialog" aria-label="Loading content"><i class="sui-icon-loader sui-loading" aria-hidden="true"></i></p>'
					);
				this.$el.find( '.sui-box-footer' ).html( '' );
				this.$el.find( '.integration-header' ).html( '' );
			}

			this.applyLoader( this.$el );

			this.ajax = $
			.post({
				url: ajaxurl,
				type: 'post',
				data: data
			})
			.done( function( result ) {
				if ( result && result.success ) {

					// Render popup body
					self.renderBody( result );

					// Render popup footer
					self.renderFooter( result );

					// Shorten result data
					const resultData = result.data.data;

					self.onRender( resultData );

					self.resetLoader( self.$el );

					// Handle close modal
					if ( close || ( ! _.isUndefined( resultData.is_close ) && resultData.is_close ) ) {
						self.close( self );
					}

					// Add closing event
					self.$el.find( '.hustle-provider-close' ).on( 'click', function() {
						self.close( self );
					});

					// Handle notifications
					if (
						! _.isUndefined( resultData.notification ) &&
						! _.isUndefined( resultData.notification.type ) &&
						! _.isUndefined( resultData.notification.text )
					) {
						const custom = Module.Notification;
						custom.open(
							resultData.notification.type,
							resultData.notification.text
						);
					}

					// Show Mailchimp interests is Group is already choosen
					if ( 'mailchimp' === self.slug ) {
						let group = self.$el.find( '#group' );
						if ( group.length ) {
							group.trigger( 'change' );
						}
					}
				}

			});

			// Remove the preloader
			this.ajax.always( function() {
				self.$el.find( '.sui-box-body' ).removeClass( 'sui-block-content-center' );
				self.$el.find( '.sui-loading-dialog' ).remove();
			});
		},

		renderBody: function( result ) {

			this.$el.find( '.sui-box-body' ).html( result.data.data.html );

			// append header to integration-header
			let integrationHeader = this.$el.find( '.sui-box-body .integration-header' ).remove();

			if ( 0 < integrationHeader.length ) {
				this.$el.find( '.integration-header' ).html( integrationHeader.html() );
			}

			// Hide empty content
			if ( ! $.trim( this.$el.find( '.sui-box-body' ).html() ).length ) {
				this.$el.find( '.sui-box-body' ).addClass( 'sui-hidden' );
				this.$el.find( '.sui-box-footer' ).css( 'padding-top', '' );

			} else {

				const children = this.$el.find( '.sui-box-body' ).children();
				let hideBody = true;

				$.each( children, ( i, child ) => {

					if ( ! $( child ).is( ':hidden' ) ) {
						hideBody = false;
					}
				});

				// Hide the content only when all children are hidden.
				if ( hideBody ) {
					this.$el.find( '.sui-box-body' ).addClass( 'sui-hidden' );
					this.$el.find( '.sui-box-footer' ).css( 'padding-top', '' );

				} else {

					// Load SUI select
					this.$el.find( '.sui-box-body select' ).each( function() {
						SUI.suiSelect( this );
					});

					// FIX: Prevent extra spacing.
					if ( this.$el.find( '.sui-box-body .sui-notice' ).next().is( 'input[type="hidden"]' ) ) {
						this.$el.find( '.sui-box-body .sui-notice' ).css({
							'margin-bottom': '0'
						});
					}
				}

			}
		},

		renderFooter: function( result ) {

			var self = this,
				buttons = result.data.data.buttons,
				body = self.$el.find( '.sui-box-body' ),
				footer = self.$el.find( '.sui-box-footer' )
				;

			// Clear footer from previous buttons
			self.$el.find( '.sui-box-footer' )
				.removeClass( 'sui-hidden' )
				.removeClass( 'sui-hidden-important' )
				.removeClass( 'sui-box-footer-center' )
				.removeClass( 'sui-box-footer-right' )
				.html( '' )
				;

			// Append buttons
			_.each( buttons, function( button ) {

				self.$el.find( '.sui-box-footer' )
					.append( button.markup )
					;
			});

			if ( 0 === footer.find( '.sui-button' ).length ) {
				footer.addClass( 'sui-hidden-important' );
			} else {

				if ( body.find( '.hustle-installation-error' ).length ) {
					footer.addClass( 'sui-hidden-important' );
				}

				// FIX: Align buttons to center.
				if ( footer.find( '.sui-button' ).hasClass( 'sui-button-center' ) ) {
					footer.addClass( 'sui-box-footer-center' );

				// FIX: Align buttons to right.
				} else if ( footer.find( '.sui-button' ).hasClass( 'sui-button-right' ) ) {

					if ( ! footer.find( '.sui-button' ).hasClass( 'sui-button-left' ) ) {
						footer.addClass( 'sui-box-footer-right' );
					}
				}
			}
		},

		onRender: function( result ) {
			var self = this;

			this.delegateEvents();

			// Update current step
			if ( ! _.isUndefined( result.opt_in_provider_current_step ) ) {
				this.step = +result.opt_in_provider_current_step;
			}

			// Update has next step
			if ( ! _.isUndefined( result.opt_in_provider_has_next_step ) ) {
				// eslint-disable-next-line camelcase
				this.next_step = result.opt_in_provider_has_next_step;
			}

			// Update has prev step
			if ( ! _.isUndefined( result.opt_in_provider_has_prev_step ) ) {
				// eslint-disable-next-line camelcase
				this.prev_step = result.opt_in_provider_has_prev_step;
			}

			self.$el.find( 'select' ).each( function() {
				SUI.suiSelect( this );
			});

			self.$el.find( '.sui-select' ).SUIselect2({
				dropdownCssClass: 'sui-select-dropdown'
			});
		},

		refreshLists: function( e ) {
			e.preventDefault();
			e.stopPropagation();

			let $this = $( e.currentTarget ),
				id = this.moduleId,
				slug = this.slug,
				type = $( '#form_id' ).length ? 'forms' : 'lists',
				nonce = this.nonce;

			$this.addClass( 'sui-button-onload' );

			$.ajax({
				url: ajaxurl,
				type: 'POST',
				data: {
					action: 'hustle_refresh_email_lists',
					id: id,
					slug: slug,
					type: type,
					_ajax_nonce: nonce // eslint-disable-line camelcase
				}
			})
			.done( function( result ) {
				if ( result.success ) {
					if ( 'undefined' !== typeof result.data.select ) {
						let select = $this.siblings( 'select' );
						select.next().remove();
						select.remove();
						$this.before( result.data.select );
						$this.siblings( '.sui-select' ).SUIselect2({
							dropdownCssClass: 'sui-select-dropdown'
						});
					}
				}
			})
			.error( function( res ) {

				// TODO: handle errors
				console.log( res );
			})
			.always( function() {
				$this.removeClass( 'sui-button-onload' );
			});

		},

		submitNextStep: function( e ) {
			let data = {},
				form = this.$el.find( 'form' ),
				params = {
					slug: this.slug,
					step: this.getStep(),
					// eslint-disable-next-line camelcase
					current_step: this.step
				},
				formData = form.serialize();

			if ( this.moduleId ) {
				// eslint-disable-next-line camelcase
				params.module_id = this.moduleId;
			}

			formData = formData + '&' + $.param( params );
			data.action = this.action;
			// eslint-disable-next-line camelcase
			data._ajax_nonce = this.nonce;
			data.data = formData;

			this.request( data, false, false );

		},

		goPrevStep: function( e ) {
			let data     = {},
				params   = {
					'slug': this.slug,
					'step': this.getPrevStep(),
					'current_step': this.step
				}
			;

			if ( this.moduleId ) {
				// eslint-disable-next-line camelcase
				params.module_id = this.moduleId;
			}
			if ( this.multi_id ) {
				// eslint-disable-next-line camelcase
				params.multi_id = this.multi_id;
			}

			data.action = this.action;
			// eslint-disable-next-line camelcase
			data._ajax_nonce = this.nonce;
			data.data = params;

			this.request( data, false, false );
		},

		getStep: function() {
			if ( this.next_step ) {
				return this.step + 1;
			}

			return this.step;
		},

		getPrevStep: function() {
			if ( this.prev_step ) {
				return this.step - 1;
			}

			return this.step;
		},

		connectAddOn: function() {
			const data = {},
				form = this.$el.find( 'form' ),
				params = {
					slug: this.slug,
					step: this.getStep(),
					// eslint-disable-next-line camelcase
					current_step: this.step
				};

			let formData = form.serialize();

			if ( this.moduleId ) {
				// eslint-disable-next-line camelcase
				params.module_id = this.moduleId;
			}
			if ( this.multi_id ) {
				// eslint-disable-next-line camelcase
				params.multi_id = this.multi_id;
			}

			formData = formData + '&' + $.param( params );
			data.action = this.action;
			// eslint-disable-next-line camelcase
			data._ajax_nonce = this.nonce;
			data.data = formData;

			this.request( data, false, false );
		},

		disconnectAddOn: function( e ) {
			var self  = this,
				img   = this.$el.find( '.sui-dialog-image img' ).attr( 'src' ),
				title = this.$el.find( '#dialogTitle2' ).html();
			const data = {},
			isActiveData = {};

			var modules = {},
			warningFlag = $( 'hustle-dialog--remove-active-warning' ).val();

			data.action = 'hustle_provider_deactivate';
			// eslint-disable-next-line camelcase
			data._ajax_nonce = this.nonce;
			data.data = {};
			data.data.slug = this.slug;
			data.data.img  = img;
			data.data.title = title;


			if ( this.globalMultiId ) {
				// eslint-disable-next-line camelcase
				data.data.global_multi_id = this.globalMultiId;
			}

			isActiveData.action = 'hustle_provider_is_on_module';
			// eslint-disable-next-line camelcase
			isActiveData._ajax_nonce = this.nonce;
			isActiveData.data = {};
			isActiveData.data.slug = this.slug;
			isActiveData.data.globalMultiId = this.globalMultiId;

			this.$el.find( '.sui-button:not(.disable-loader)' ).addClass( 'sui-button-onload' );

			$.ajax({
				url: ajaxurl,
				type: 'POST',
				data: isActiveData,
				success: function( resp ) {
					if ( true === resp.success ) {
						modules = resp;
					}
				},
				complete: function() {
					if ( true === modules.success ) {
						Module.integrationsActiveRemove.open( modules.data, data, self );
					} else {
						self.request( data, true, false );
					}
				}
			});

		},

		disconnectAddOnForm: function( e ) {
			var self = this;

			const data = {};

			let active 		 	= $( '#hustle-integrations-active-count' ).val(),
			activeIntegration 	= $( '#hustle-integrations-active-integrations' ).val();
			data.action 		= 'hustle_provider_form_deactivate';

			// eslint-disable-next-line camelcase
			data._ajax_nonce = this.nonce;
			data.data = {};
			data.data.slug = this.slug;

			// eslint-disable-next-line camelcase
			data.data.module_id = this.moduleId;

			if ( this.multi_id ) {
				// eslint-disable-next-line camelcase
				data.data.multi_id = this.multi_id;
			}

			if ( 1 == active && activeIntegration === this.slug && 'local_list' !== this.slug ) {
				Module.integrationsAllRemove.open( data, self );
			} else if ( 1 == active && 'local_list' === this.slug ) {
				Module.Notification.open( 'error', optinVars.messages.integraiton_required );
			} else {
				this.request( data, true, false );
			}
		},

		close: function( self ) {

			// Kill AJAX hearbeat
			self.ajax.abort();

			// Remove the view
			self.remove();

			// Reset body scrollbar
			$( 'body' ).css( 'overflow', 'auto' );

			// Refrest add-on list
			Hustle.Events.trigger( 'hustle:providers:reload' );
		},

		clearRadioOptions: function() {
			this.$( 'input[type=radio]', this.$el ).removeAttr( 'checked' );
		},

		//show interests for mailchimp
		showInterests: function( e ) {
			let self = this,
				$this = $( e.currentTarget ),
				nonce = $this.data( 'nonce' ),
				group = $this.val(),
				data = {},
				form = self.$el.find( 'form' ),
				params = {
					slug: self.slug,
					group: group,
					'module_id': self.moduleId
				},
				formData = form.serialize();

			formData = formData + '&' + $.param( params );
			data.action = 'hustle_mailchimp_get_group_interests';
			// eslint-disable-next-line camelcase
			data._ajax_nonce = nonce;
			data.data = formData;

			self.applyLoader( self.$el );

			$.ajax({
				url: ajaxurl,
				type: 'POST',
				data: data
			})
			.done( function( result ) {
				if ( result.success ) {
					form.find( '.sui-form-field' ).slice( 1 ).remove();
					form.find( '.sui-form-field:first-child' ).after( result.data );

					self.$el.find( '.sui-select' ).SUIselect2({
						dropdownCssClass: 'sui-select-dropdown'
					});
				}
			})
			.error( function( res ) {

				// TODO: handle errors
				console.log( res );
			})
			.always( function() {
				self.resetLoader( self.$el );
			});
		}

	});

});

var Module = window.Module || {};

Hustle.define( 'Model', function( $ ) {
	'use strict';

	return Backbone.Model.extend({

		initialize: function() {
			this.on( 'change', this.userHasChange, this );
			Backbone.Model.prototype.initialize.apply( this, arguments );
		},

		userHasChange: function() {

			Module.hasChanges = true;

			// Add the "unsaved" status tag to the module screen.
			Hustle.Events.trigger( 'modules.view.switch_status', 'unsaved' );
		}
	});
});

Hustle.define( 'Models.M', function() {
	'use strict';
	return Hustle.get( 'Model' ).extend({
			toJSON: function() {
				var json = _.clone( this.attributes );
                var attr;
				for ( attr in json ) {
					if ( ( json[ attr ] instanceof Backbone.Model ) || ( json[ attr ] instanceof Backbone.Collection ) ) {
						json[ attr ] = json[ attr ].toJSON();
					}
				}
				return json;
			},
			set: function( key, val, options ) {
                var parent, child, parentModel;

				if ( 'string' === typeof key && -1 !== key.indexOf( '.' ) ) {
					parent = key.split( '.' )[ 0 ];
					child = key.split( '.' )[ 1 ];
					parentModel = this.get( parent );

					if ( parentModel && parentModel instanceof Backbone.Model ) {
						parentModel.set( child, val, options );
						this.trigger( 'change:' + key, key, val, options );
						this.trigger( 'change:' + parent, key, val, options );
					}

				} else {
					Backbone.Model.prototype.set.call( this, key, val, options );
				}
			},
			get: function( key ) {
                var parent, child;
				if ( 'string' === typeof key && -1 !== key.indexOf( '.' ) ) {
					parent = key.split( '.' )[ 0 ];
					child = key.split( '.' )[ 1 ];
					return this.get( parent ).get( child );
				} else {
					return Backbone.Model.prototype.get.call( this, key );
				}
			}
		});
});

Hustle.define( 'Models.Trigger', function() {
	'use strict';
	return  Hustle.get( 'Model' ).extend({
		defaults: {
			trigger: 'time', // time | scroll | click | exit_intent | adblock
			'on_time_delay': 0,
			'on_time_unit': 'seconds',
			'on_scroll': 'scrolled', // scrolled | selector
			'on_scroll_page_percent': '20',
			'on_scroll_css_selector': '',
			'enable_on_click_element': '1',
			'on_click_element': '',
			'enable_on_click_shortcode': '1',
			'on_exit_intent': '1',
			'on_exit_intent_per_session': '1',
			'on_exit_intent_delayed': '0',
			'on_exit_intent_delayed_time': 5,
			'on_exit_intent_delayed_unit': 'seconds',
			'on_adblock': '0'
		}
	});
});

Module.Model  = Hustle.get( 'Models.M' ).extend({
	defaults: {
		'module_name': '',
		moduleType: 'popup',
		active: '0'
	}
});

( function( $ ) {

	'use strict';

	var Module = window.Module || {};

	/**
	 * Render a notification at the top of the page.
	 * Used in the global settings page when saving, for example.
	 * @since 4.0
	 */
	Module.Notification = {

		initialize: function() {

			if ( ! $( '#hustle-notification' ).length ) {

				$( '<div role="alert" id="hustle-notification" class="sui-notice-top sui-notice-' + this.type + ' sui-can-dismiss">' +
					'<div class="sui-notice-content">' +
						'<p>' + this.text + '</p>' +
					'</div>' +
					'<span class="sui-notice-dismiss" aria-hidden="true">' +
						'<a role="button" href="#" aria-label="' + optinVars.messages.commons.dismiss + '" class="sui-icon-check"></a>' +
					'</span>' +
				'</div>' )
				.removeAttr( 'hidden' )
				.appendTo( $( 'main.sui-wrap' ) )
				.slideDown()
				;

				/**
				 * !!! TO IMPROVE:
				 *
				 * Uncomment code below and replace MODULE_ID with
				 * imported module ID to focus it.
				 *
				 * We also need to run this on window load.
				 */
				// $( '.sui-accordion-item-header[data-id="' + MODULE_ID + '"]' ).closest( '.sui-accordion-item' ).focus();

			} else {
				$( '#hustle-notification' ).remove();
				this.initialize();
			}
		},

		open: function( type, text, closeTime ) {

			var self = this;

			if ( _.isUndefined( closeTime ) ) {
				closeTime = 4000;
			}

			if ( 'undefined' !== typeof ( self.closeTimeout ) ) {
				window.clearTimeout( self.closeTimeout );
				delete self.closeTimeout;
				self.close();
			}

			this.type = type || 'notice';
			this.text = text;

			this.initialize();

			const $popup = $( '#hustle-notification' );

			$popup.removeClass( 'sui-hidden' );
			$popup.removeProp( 'hidden' );

			$( '.sui-notice-dismiss a' ).click( function( e ) {
				e.preventDefault();

				self.close();

				return false;
			});

			if ( closeTime ) {

				this.closeTimeout = setTimeout( function() {
					self.close();
				}, closeTime );
			}
		},

		close: function() {

			var $popup = $( '#hustle-notification' );

			$popup.addClass( 'sui-hidden' );
			$popup.prop( 'hidden', true );
			$popup.stop().slideUp( 'slow' );
		}
	};

	/**
	 * Render the modal used for editing the itnegrations' settings.
	 * @since 4.0
	 */
	Module.integrationsModal = {

		$popup: {},

		_deferred: {},

		open( e ) {

			var self = this;
			var $target = $( e.target );

			// Remove popup
			$( '#hustle-integration-popup' ).remove();

			if ( ! $target.hasClass( 'connect-integration' ) ) {
				$target = $target.closest( '.connect-integration' );
			}

			let closeClick = () => {
				self.close();
				return false;
			};

			let nonce = $target.data( 'nonce' ),
				slug = $target.data( 'slug' ),
				title =  $target.data( 'title' ),
				image = $target.data( 'image' ),
				action = $target.data( 'action' ),
				moduleId = $target.data( 'module_id' ),
				multiId = $target.data( 'multi_id' ),
				globalMultiId = $target.data( 'global_multi_id' )
				;

			let tpl = Optin.template( 'hustle-integration-dialog-tpl' );

			$( 'main.sui-wrap' ).append( tpl({
				image: image,
				title: title
			}) );

			this.$popup = $( '#hustle-integration-dialog' );

			let settingsView = Hustle.get( 'Integration_Modal_Handler' ),
				view = new settingsView({
				slug: slug,
				nonce: nonce,
				action: action,
				moduleId: moduleId,
				multiId: multiId,
				globalMultiId,
				el: this.$popup
			});

			view.on( 'modal:closed', () => self.close() );

			this.$popup.find( '.hustle-popup-action' ).remove();

			// Add closing event
			this.$popup.find( '.sui-dialog-close' ).on( 'click', closeClick );
			this.$popup.find( '.sui-dialog-overlay' ).on( 'click', closeClick );
			this.$popup.on( 'click', '.hustle-popup-cancel', closeClick );
			this.$popup.find( '.sui-dialog-overlay' ).on( 'click', function() {
				$( this ).parent( '#hustle-integration-dialog' ).find( '.sui-dialog-close' ).trigger( 'click' );
			});

			// Open
			this.$popup.find( '.sui-dialog-overlay' ).removeClass( 'sui-fade-out' ).addClass( 'sui-fade-in' );
			this.$popup.find( '.sui-dialog-content' ).removeClass( 'sui-bounce-out' ).addClass( 'sui-bounce-in' );

			this.$popup.removeAttr( 'aria-hidden' );

			// hide body scrollbar
			$( 'body' ).css( 'overflow', 'hidden' );

			this._deferred = new $.Deferred();

			// Make sui-tabs changeable
			this.$popup.on( 'click', '.sui-tab-item', function( e ) {
				let $this = $( e.currentTarget ),
					$items = $this.closest( '.sui-side-tabs' ).find( '.sui-tab-item' );

				$items.removeClass( 'active' );
				$this.addClass( 'active' );
			});

			return this._deferred.promise();

		},

		close( result ) {

			var $popup = $( '#hustle-integration-popup' );

			$popup.find( '.sui-dialog-overlay' ).removeClass( 'sui-fade-in' ).addClass( 'sui-fade-out' );
			$popup.find( '.sui-dialog-content' ).removeClass( 'sui-bounce-in' ).addClass( 'sui-bounce-out' );

			// reset body scrollbar
			$( 'body' ).css( 'overflow', 'auto' );

			setTimeout( function() {
				$popup.attr( 'aria-hidden', 'true' );
			}, 300 );

			this._deferred.resolve( this.$popup, result );
		}
	};

	/**
	 * Render the modal used when removing the only left integration.
	 * @since 4.0.1
	 */
	Module.integrationsAllRemove = {

		$popup: {},

		_deferred: {},

		/**
		 * @since 4.0.2
		 * @param ModuleID
		 */
		open( data, referrer ) {

			var self = this;

			let dialogId = $( '#hustle-dialog--final-delete' );

			let closeClick = () => {
				self.close();
				return false;
			};

			let insertLocal = ( data ) => {
				self.insertLocalList( data );
				return false;
			};

			let deleteInt = ( data, referrer ) => {
				self.deleteIntegration( data, referrer );
				return false;
			};

			// Add closing event
			dialogId.find( '.sui-dialog-close' ).on( 'click', closeClick );
			dialogId.find( '.sui-dialog-overlay' ).on( 'click', closeClick );
			dialogId.find( '#hustle-delete-final-button-cancel' ).on( 'click', closeClick );

			$( '#hustle-delete-final-button' ).off( 'click' ).on( 'click', function( e ) {
				$( '#hustle-delete-final-button' ).addClass( 'sui-button-onload' );
				deleteInt( data, referrer );
				insertLocal( data );
				closeClick();
			});

			$( '#hustle-integration-dialog' ).addClass( 'sui-fade-out' ).hide();
			$( '#hustle-delete-final-button' ).removeAttr( 'disabled' );

			SUI.dialogs[ 'hustle-dialog--final-delete' ].show();
		},

		close() {

			var $popup = $( '#hustle-dialog--final-delete' );

			$popup.find( '.sui-dialog-overlay' ).removeClass( 'sui-fade-in' ).addClass( 'sui-fade-out' );
			$popup.find( '.sui-dialog-content' ).removeClass( 'sui-bounce-in' ).addClass( 'sui-bounce-out' );
			$( '#hustle-delete-final-button' ).removeClass( 'sui-button-onload' );
			$( '#hustle-integration-dialog' ).remove();

			// reset body scrollbar
			$( 'body' ).css( 'overflow', 'auto' );
			$( '#hustle-delete-final-button' ).attr( 'disabled' );

			setTimeout( function() {
				$popup.attr( 'aria-hidden', 'true' );
			}, 300 );

			SUI.dialogs[ 'hustle-dialog--final-delete' ].hide();
		},

		confirmDelete( data, referrer ) {
			this.deleteIntegration( data, referrer );
			this.insertLocal( data );
			this.close();
		},
		deleteIntegration( data, referrer ) {
			referrer.request( data, true, false );
		},

		insertLocalList( data ) {
			let ajaxData = {
				id: data.data.module_id,
				'_ajax_nonce': data._ajax_nonce,
				action: 'hustle_provider_insert_local_list'
			};
			$.ajax({
				url: ajaxurl,
				type: 'POST',
				data: ajaxData,
				success: function( resp ) {
					if ( resp.success ) {
						Hustle.Events.trigger( 'hustle:providers:reload' );
					} else {
						if ( 'undefined' === typeof SUI.dialogs[ 'hustle-dialog--final-delete' ]) {
							Module.Notification.open( 'error', optinVars.messages.something_went_wrong );
							return;
						}
						SUI.dialogs[ 'hustle-dialog--final-delete' ].hide();
					}
				},
				error: function() {
					Module.Notification.open( 'error', optinVars.messages.something_went_wrong );
					SUI.dialogs[ 'hustle-dialog--final-delete' ].hide();
				}
			});
		}
	};

	/**
	 * Render the modal used when removing the only left integration.
	 * @since 4.0.1
	 */
	Module.integrationsActiveRemove = {

		$popup: {},

		_deferred: {},

		/**
		 * @since 4.0.2
		 * @param ModuleID
		 */
		open( data, disconnect, referrer ) {

			var self = this;

			let dialogId = $( '#hustle-dialog--remove-active' );

			let closeClick = () => {
				self.close();
				return false;
			};

			let goBack = () => {
				self.back( referrer );
				return false;
			};

			let removeIntegration = ( data, referrer, modules ) => {
				self.removeIntegration( data, referrer, modules );
				closeClick();
			};

			let tpl 	= Optin.template( 'hustle-modules-active-integration-tpl' ),
				tplImg  = Optin.template( 'hustle-modules-active-integration-img-tpl' ),
				tplHead = Optin.template( 'hustle-modules-active-integration-header-tpl' ),
				tplDesc = Optin.template( 'hustle-modules-active-integration-desc-tpl' );

			//remove previous html
			$( '#hustle-dialog--remove-active tbody' ).html( '' );
			$( '#hustle-dialog--remove-active .sui-dialog-image' ).html( '' );
			$( '#hustle-dialog--remove-active #sui-box-modal-header' ).html( '' );
			$( '#hustle-dialog--remove-active #sui-box-modal-content' ).html( '' );

			$( '#hustle-dialog--remove-active .sui-dialog-image' ).append( tplImg({
				image: disconnect.data.img,
				title: disconnect.data.slug
			}) );

			$( '#hustle-dialog--remove-active #sui-box-modal-header' ).append( tplHead({
				title: disconnect.data.title.replace( /Connect|Configure/gi, ' ' )
			}) );

			$( '#hustle-dialog--remove-active #sui-box-modal-content' ).append( tplDesc({
				title: disconnect.data.title.replace( /Connect|Configure/gi, ' ' )
			}) );

			$.each( data, function( id, meta ) {

				$( '#hustle-dialog--remove-active tbody' ).append( tpl({
					name: meta.name,
					type: meta.type,
					editUrl: meta.edit_url
				}) );
			});

			// Add closing event
			dialogId.find( '.sui-dialog-close' ).on( 'click', closeClick );
			dialogId.find( '.sui-dialog-overlay' ).on( 'click', closeClick );
			dialogId.find( '#hustle-remove-active-button-cancel' ).on( 'click', closeClick );
			dialogId.find( '.hustle-remove-active-integration-back' ).on( 'click', function() {
				goBack();
			});

			$( '#hustle-remove-active-button' ).off( 'click' ).on( 'click', function( event ) {
				$( this ).addClass( 'sui-button-onload' );
				removeIntegration( disconnect, referrer, data );
			});

			$( '#hustle-integration-dialog' ).addClass( 'sui-fade-out' ).hide();

			SUI.dialogs[ 'hustle-dialog--remove-active' ].show();
		},

		close() {

			var $popup = $( '#hustle-dialog--remove-active' );

			$popup.find( '.sui-dialog-overlay' ).removeClass( 'sui-fade-in' ).addClass( 'sui-fade-out' );
			$popup.find( '.sui-dialog-content' ).removeClass( 'sui-bounce-in' ).addClass( 'sui-bounce-out' );
			$( '#hustle-delete-final-button' ).removeClass( 'sui-button-onload' );
			$( '#hustle-integration-dialog' ).remove();

			// reset body scrollbar
			$( 'body' ).css( 'overflow', 'auto' );

			setTimeout( function() {
				$popup.attr( 'aria-hidden', 'true' );
			}, 300 );

			SUI.dialogs[ 'hustle-dialog--remove-active' ].hide();
		},
		back( slug ) {
			var self = this;
			self.close();

			//integrations that doesn't support global multi id.
			if ( 'hubspot' === slug.slug || 'constantcontact' === slug.slug ) {
				$( 'button[data-slug="' + slug.slug + '"]' ).trigger( 'click' );
			} else {
				$( 'button[data-global_multi_id="' + slug.globalMultiId + '"]' ).trigger( 'click' );
			}
		},

		removeIntegration( data, referrer, modules ) {
			var self = this;
			$.each( modules, function( id, meta ) {
				if ( data.data.slug === meta.active.active_integrations ) {
					self.insertLocalList( data, id );
				}
			});

			referrer.request( data, true, false );
			$( '#hustle-remove-active-button' ).removeClass( 'sui-button-onload' );
		},

		insertLocalList( data, id ) {
			let ajaxData = {
				id: id,
				'_ajax_nonce': data._ajax_nonce,
				action: 'hustle_provider_insert_local_list'
			};
			$.ajax({
				url: ajaxurl,
				type: 'POST',
				data: ajaxData,
				success: function( resp ) {
					if ( false === resp.success ) {
						Module.Notification.open( 'error', optinVars.messages.something_went_wrong );
						return;
					}
				},
				error: function() {
					Module.Notification.open( 'error', optinVars.messages.something_went_wrong );
				}
			});
		}
	};

	/**
	 * The provider migration model
	 * @since 4.0.3
	 */
	Module.ProviderMigration = {

		$popup: {},

		_deferred: {},

		/**
		 * @since 4.0.3
		 * @param object slug of provider.
		 */
		open( slug, id = null ) {

			let	dialogId = $( '#hustle-dialog-migrate--' + slug ),
				self = this,
				closeClick = () => {
					self.close( dialogId, slug );
					return false;
				},
				reauthMultiID = () => {
					var form = dialogId.find( 'form' ),
					data 	 = {},
					params 	 = {
						slug: slug,
						// eslint-disable-next-line camelcase
						global_multi_id: id
					},
					formData = form.serialize();
					$( '#integration-migrate' ).addClass( 'sui-button-onload' );

					// eslint-disable-next-line camelcase
					data._ajax_nonce = $( '#integration-migrate' ).data( 'nonce' );
					data.action 	 = 'hustle_provider_migrate_aweber';
					formData 		 = formData + '&' + $.param( params );
					data.data 		 = formData;
					self.reauth( dialogId, data, id, slug );
				};

			dialogId.find( '.sui-dialog-close' ).on( 'click', closeClick );
			dialogId.find( '.sui-dialog-overlay' ).on( 'click', closeClick );
			dialogId.find( '#integration-migrate' ).on( 'click', reauthMultiID );

			if ( id ) {
				$( '#integration-migrate' ).attr( 'data-id', id );
			}

			setTimeout( () =>  SUI.dialogs[ 'hustle-dialog-migrate--' + slug ].show(), 300 );

		},
		close( dialogId, slug ) {

			dialogId.find( '.sui-dialog-overlay' ).removeClass( 'sui-fade-in' ).addClass( 'sui-fade-out' );
			dialogId.find( '.sui-dialog-content' ).removeClass( 'sui-bounce-in' ).addClass( 'sui-bounce-out' );

			// reset body scrollbar
			$( 'body' ).css( 'overflow', 'auto' );

			setTimeout( () =>  dialogId.attr( 'aria-hidden', 'true' ), 300 );

			SUI.dialogs[ 'hustle-dialog-migrate--' + slug ].hide();
		},
		reauth( dialogId, data, id, slug ) {
			var self = this,
			notice = $( '.hustle_migration_notice__' + slug + '[data-id="' + id + '"]' );

			this.ajax = $
			.post({
				url: ajaxurl,
				type: 'post',
				data: data
			})
			.done( function( result ) {
				if ( result && result.success ) {
					self.close( dialogId, slug );
					notice.hide();

					Module.Notification.open( 'success', optinVars.messages.aweber_migration_success, 100000 );
				} else {
					$( dialogId ).find( '#integration-migrate' ).removeClass( 'sui-button-onload' );
					$( dialogId ).find( '.sui-error-message' ).removeClass( 'sui-hidden' );
					$( dialogId ).find( '.sui-form-field' ).addClass( 'sui-form-field-error' );
				}
			});
		}

	};

	/**
	 * The "are you sure?" modal from when deleting modules or entries.
	 * @since 4.0
	 */
	Module.deleteModal = {

		/**
		 * @since 4.0
		 * @param object data - must contain 'title', 'description', 'nonce', 'action', and 'id' that's being deleted.
		 */
		open( data ) {
			let dialogId = 'hustle-dialog--delete',
				template = Optin.template( 'hustle-dialog--delete-tpl' ),
				content = template( data );

			// Add the templated content to the modal.
			$( '#' + dialogId + ' #hustle-delete-dialog-content' ).html( content );

			// Add the title to the modal.
			$( '#' + dialogId + ' #hustle-dialog-title' ).html( data.title );

			if ( 'undefined' === typeof SUI.dialogs[ dialogId ]) {
				Module.Notification.open( 'error', optinVars.messages.something_went_wrong );
				return false;
			}

			$( '#' + dialogId + ' .hustle-delete-confirm' ).on( 'click', function( e ) {
				let $button = $( e.currentTarget );
				$button.addClass( 'sui-button-onload' );
			});

			SUI.dialogs[ dialogId ].create();
			SUI.dialogs[ dialogId ].show();
		}
	};

	/**
	 * Open the module's preview.
	 * Shows the module if it's slide-in or pop-up.
	 * Open a modal containing the module if it's embedded or social sharing. This should be already rendered in the page.
	 * @since 4.0
	 */
	Module.preview = {

		open( id, type, previewData = false ) {
			const me = this,
				isInline = ( 'embedded' === type || 'social_sharing' === type );

			$.ajax({
				url: ajaxurl,
				type: 'POST',
				data: {
					action: 'hustle_preview_module',
					id,
					previewData
				}
			})
			.then( function( res ) {

				if ( res.success ) {

					let $previewContainer = '';

					// Fill a regular div if they're not inline modules.
					if ( ! isInline ) {
						$previewContainer = $( '#module-preview-container' );

						// If it doesn't exist already, add it.
						if ( ! $previewContainer.length ) {
							$( 'main.sui-wrap' ).append( '<div id="module-preview-container"></div>' );
							$previewContainer = $( '#module-preview-container' );
						}

					} else { // Use the preview modal for inline modules.
						$previewContainer = $( '#hustle-dialog--preview .sui-box-body' );

					}

					$previewContainer.html( res.data.html );
					const $module = $previewContainer.find( '.hustle-ui' );

					// Load select2 if this module has select fields.
					if ( $module.find( '.hustle-select2' ).length ) {
						HUI.select2();
					}

					// If there's a timepicker.
					if ( $module.find( '.hustle-time' ).length ) {
						HUI.timepicker( '.hustle-time' );
					}

					// If there's a datepicker.
					if ( $module.find( '.hustle-date' ).length ) {
						const { days_and_months: strings } = optinVars.messages;
						HUI.datepicker( '.hustle-date', strings.days_full, strings.days_short, strings.days_min, strings.months_full, strings.months_short );
					}

					HUI.nonSharingSimulation( $module );
					HUI.inputFilled();

					if ( res.data.style ) {
						$previewContainer.append( res.data.style );
					}

					if ( res.data.script ) {
						$previewContainer.append( res.data.script );
					}

					setTimeout( () => HUI.maybeRenderRecaptcha( $module ), 1000 );

				}

				return {
					id,
					data: res.data.module
				};
			},
			function( res ) {

				// TODO: handle errors
				console.log( res );
			})
			.then( function({ id, data }) {

				// If no ID, abort.
				if ( ! id ) {
					return;
				}

				// Display the preview modal for inline modules.
				if ( isInline ) {
					SUI.dialogs['hustle-dialog--preview'].show();

				}

				// Display the module.
				me.showModule( id, data );

			})
			.always( function() {
				$( '.sui-button-onload' ).removeClass( 'sui-button-onload' );
			});
		},

		showModule( id, data ) {

			const el = '.hustle_module_id_' + id;

			if ( 'popup' === data.module_type ) {
				const autohideDelay = '0' === String( $( el ).data( 'close-delay' ) ) ? false : $( el ).data( 'close-delay' );
				HUI.popupLoad( el, autohideDelay );

			} else if ( 'slidein' === data.module_type ) {
				const autohideDelay = '0' === String( $( el ).data( 'close-delay' ) ) ? false : $( el ).data( 'close-delay' );
				HUI.slideinLayouts( el );
				HUI.slideinLoad( el, autohideDelay );

				$( window ).on( 'resize', function() {
					HUI.slideinLayouts( el );
				});

			} else {
				HUI.inlineResize( el );
				HUI.inlineLoad( el );
			}

		}
	};

	/**
	 * Renders the module's charts in the listing pages.
	 * It also handles the view when the 'conversions type' select changes.
	 * @since 4.0.4
	 */
	Module.trackingChart = {

		chartsData: {},
		theCharts: {},

		init( $container, chartsData ) {

			$container.find( 'select.hustle-conversion-type' ).each( ( i, el ) => {
				SUI.suiSelect( el );
				$( el ).on( 'change.select2', ( e ) => this.conversionTypeChanged( e, $container ) );
			});

			this.chartsData = chartsData;
			Object.values( chartsData ).forEach( chart => this.updateChart( chart ) );
		},

		conversionTypeChanged( e, $container ) {
			const $select = $( e.currentTarget ),
				conversionType = $select.val(),
				moduleSubType = $select.data( 'moduleType' ),
				subTypeChart = this.chartsData[ moduleSubType ],
				$conversionsCount = $container.find( `.hustle-tracking-${ moduleSubType }-conversions-count` ),
				$conversionsRate = $container.find( `.hustle-tracking-${ moduleSubType }-conversions-rate` );

			// Update the number for the conversions count and conversion rate at the top of the chart.
			$conversionsCount.text( subTypeChart[ conversionType ].conversions_count );
			$conversionsRate.text( subTypeChart[ conversionType ].conversion_rate + '%' );

			this.updateChart( subTypeChart, conversionType, false );
		},

		updateChart( chart, conversionType = 'all', render = true ) {

			let views = chart.views,
				submissions = chart[ conversionType ].conversions,

			datasets = [
				{
					label: 'Submissions',
					data: submissions,
					backgroundColor: [
						'#E1F6FF'
					],
					borderColor: [
						'#17A8E3'
					],
					borderWidth: 2,
					pointRadius: 0,
					pointHitRadius: 20,
					pointHoverRadius: 5,
					pointHoverBorderColor: '#17A8E3',
					pointHoverBackgroundColor: '#17A8E3'
				},
				{
					label: 'Views',
					data: views,
					backgroundColor: [
						'#F8F8F8'
					],
					borderColor: [
						'#DDDDDD'
					],
					borderWidth: 2,
					pointRadius: 0,
					pointHitRadius: 20,
					pointHoverRadius: 5,
					pointHoverBorderColor: '#DDDDDD',
					pointHoverBackgroundColor: '#DDDDDD'
				}
			];

			// The chart was already created. Update it.
			if ( 'undefined' !== typeof this.theCharts[ chart.id ]) {

				// The container has been re-rendered, so render the chart again.
				if ( render ) {
					this.theCharts[ chart.id ].destroy();
					this.createNewChart( chart, datasets );

				} else {

					// Just update the chart otherwise.
					this.theCharts[ chart.id ].data.datasets = datasets;
					this.theCharts[ chart.id ].update();
				}

			} else {
				this.createNewChart( chart, datasets );
			}
		},

		createNewChart( chart, datasets ) {
			let yAxesHeight = ( Math.max( ...chart.views ) + 2 );
			const chartContainer = document.getElementById( chart.id );

			if ( Math.max( ...chart.views ) < Math.max( ...chart.conversions ) ) {
				yAxesHeight = ( Math.max( ...chart.conversions ) + 2 );
			}

			if ( ! chartContainer ) {
				return;
			}

			const days = chart.days,
				chartData = {
					labels: days,
					datasets
				};

			let chartOptions = {
				maintainAspectRatio: false,
				legend: {
					display: false
				},
				scales: {
					xAxes: [
						{
							display: false,
							gridLines: {
								color: 'rgba(0, 0, 0, 0)'
							}
						}
					],
					yAxes: [
						{
							display: false,
							gridLines: {
								color: 'rgba(0, 0, 0, 0)'
							},
							ticks: {
								beginAtZero: false,
								min: 0,
								max: yAxesHeight,
								stepSize: 1
							}
						}
					]
				},
				elements: {
					line: {
						tension: 0
					},
					point: {
						radius: 0.5
					}
				},
				tooltips: {
					custom: function( tooltip ) {

						if ( ! tooltip ) {
							return;
						}

						// Disable displaying the color box
						tooltip.displayColors = false;
					},
					callbacks: {
						title: function( tooltipItem, data ) {
							if ( 0 === tooltipItem[0].datasetIndex ) {
								return optinVars.labels.submissions.replace( '%d', tooltipItem[0].yLabel );// + ' Submissions';
							} else if ( 1 === tooltipItem[0].datasetIndex ) {
								return optinVars.labels.views.replace( '%d', tooltipItem[0].yLabel ); //+ ' Views';
							}
						},
						label: function( tooltipItem, data ) {
							return tooltipItem.xLabel;
						},

						// Set label text color
						labelTextColor: function( tooltipItem, chart ) {
							return '#AAAAAA';
						}
					}
				}
			};

			this.theCharts[ chart.id ] = new Chart( chartContainer, {
				type: 'line',
				fill: 'start',
				data: chartData,
				options: chartOptions
			});
		}
	};

	/**
	 * Key var to listen user changes before triggering
	 * navigate away message.
	 **/
	Module.hasChanges = false;

	// Unused
	/*Module.user_change = function() {
		Module.hasChanges = true;
	};*/

	window.onbeforeunload = function() {

		if ( Module.hasChanges ) {
			return optinVars.messages.dont_navigate_away;
		}
	};

	$( '.highlight_input_text' ).focus( function() {
		$( this ).select();
	});

}( jQuery ) );

( function( $ ) {
	'use strict';

	var Module = window.Module || {};

	Module.Utils = {

		/*
		 * Return URL param value
		 */
		getUrlParam: function( param ) {
			var urlParams = optinVars.urlParams;
			if ( 'undefined' !== typeof urlParams[ param ]) {
				return urlParams[ param ];
			}

			return false;
		},

		accessibleHide( $elements, isFocusable = false, extraToUpdate = false ) {
			$elements.hide();
			$elements.attr( 'aria-hidden', true );
			$elements.prop( 'hidden', true );
			if ( isFocusable ) {
				$elements.prop( 'tabindex', '-1' );
			}
			if ( extraToUpdate ) {
				if ( 'undefined' !== typeof extraToUpdate.name ) {
					if ( 'undefined' !== typeof extraToUpdate.value ) {
						$elements.attr( extraToUpdate.name, extraToUpdate.value );
					} else {
						$elements.removeAttr( extraToUpdate.name );
					}
				}
			}
		},

		accessibleShow( $elements, isFocusable = false, extraToUpdate = false ) {
			$elements.show();
			$elements.removeAttr( 'aria-hidden' );
			$elements.removeClass( 'sui-hidden' );
			$elements.removeProp( 'hidden' );
			if ( isFocusable ) {
				$elements.attr( 'tabindex', '0' );
			}
			if ( extraToUpdate ) {
				if ( 'undefined' !== typeof extraToUpdate.name ) {
					if ( 'undefined' !== typeof extraToUpdate.value ) {
						$elements.attr( extraToUpdate.name, extraToUpdate.value );
					} else {
						$elements.removeAttr( extraToUpdate.name );
					}
				}
			}
		}

	};

	/**
	 * One callback to rule them all.
	 * Receives the events from single module actions.
	 * Call another callback or does an action (eg. a redirect) according to the ajax request response.
	 * Used in module listing pages and dashboard.
	 * @since 4.0.3
	 */
	Module.handleActions = {

		context: '',

		/**
		 * Function to initiate the action.
		 * @since 4.0.3
		 * @param {Object} e
		 * @param {String} context Where it's called from. dashboard|listing
		 */
		initAction( e, context, referrer ) {

			e.preventDefault();

			this.context = context;

			const self = this,
				$this = $( e.currentTarget ),
				relatedFormId = $this.data( 'form-id' ),
				actionData = $this.data();

			let data = new FormData();

			// Grab the form's data if the action has a related form.
			if ( 'undefined' !== typeof relatedFormId ) {
				const $form = $( '#' + relatedFormId );

				if ( $form.length ) {
					data = new FormData( $form[0]);
				}
			}

			$.each( actionData, ( name, value ) => data.append( name, value ) );

			data.append( 'context', this.context );
			data.append( '_ajax_nonce', optinVars.single_module_action_nonce );
			data.append( 'action', 'hustle_module_handle_single_action' );

			$.ajax({
				url: ajaxurl,
				type: 'POST',
				data: data,
				contentType: false,
				processData: false
			})
			.done( res => {

				// If there's a defined callback, call it.
				if ( res.data.callback && 'function' === typeof self[ res.data.callback ]) {

					// This calls the "action{ hustle action }" functions from this view.
					// For example: actionToggleStatus();
					self[ res.data.callback ]( $this, res.data, res.success );

				} else if ( res.data.callback && 'function' === typeof referrer[ res.data.callback ]) {
					referrer[ res.data.callback ]( $this, res.data, res.success );

				} else if ( res.data.url ) {
					location.replace( res.data.url );

				} else if ( res.data.notification ) {

					Module.Notification.open( res.data.notification.status, res.data.notification.message, res.data.notification.delay );
				}

				// Don't remove the 'loading' icon when redirecting/reloading.
				if ( ! res.data.url ) {
					$( '.sui-button-onload' ).removeClass( 'sui-button-onload' );
				}
			})
			.error( res => {
				$( '.sui-button-onload' ).removeClass( 'sui-button-onload' );
			});
		},

		/**
		 * initAction succcess callback for "toggle-tracking".
		 * @since 4.0.3
		 */
		actionToggleTracking( $this, data ) {

			if ( ! data.is_embed_or_sshare ) {

				const enabled = data.was_enabled ? 1 : 0,
					item = $this.parents( '.sui-accordion-item' );

				$this.data( 'enabled', 1 - enabled );
				$this.find( 'span' ).toggleClass( 'sui-hidden' );

				// update tracking data
				if ( item.hasClass( 'sui-accordion-item--open' ) ) {
					item.find( '.sui-accordion-open-indicator' ).trigger( 'click' ).trigger( 'click' );
				}

			} else {

				let $button = $( '.hustle-manage-tracking-button[data-module-id="' + $this.data( 'module-id' ) + '"]' ),
					item = $button.parents( '.sui-accordion-item' );

				SUI.dialogs[ 'hustle-dialog--manage-tracking' ].hide();

				$button.data( 'tracking-types', data.enabled_types );

				// update tracking data
				if ( item.hasClass( 'sui-accordion-item--open' ) ) {
					item.find( '.sui-accordion-open-indicator' ).trigger( 'click' ).trigger( 'click' );
				}
			}

			Module.Notification.open( 'success', data.message, 10000 );
		}

	};

}( jQuery ) );

Hustle.define( 'SShare.Content_View', function( $, doc, win ) {

	'use strict';

	return Hustle.View.extend(

		_.extend({}, Hustle.get( 'Mixins.Module_Content' ), {

			el: '#hustle-wizard-content',

			activePlatforms: [],

			events: {

				'change select.hustle-select-field-variables': 'addPlaceholderToField',
				'click ul.wpmudev-tabs-menu li label': 'toggleCheckbox',

				// Open Add Platforms popup
				'click .hustle-choose-platforms': 'openPlatformsModal'
			},
			render() {
				const me = this,
					data = this.model.toJSON();

				if ( 'undefined' !== typeof data.social_icons && data.social_icons ) {
					for ( let platform in data.social_icons ) {
						me.addPlatformToPanel( platform, data.social_icons[ platform ]);
					}
				}

				// Initiate the sortable functionality to sort form platforms' order.
				let sortableContainer = this.$( '#hustle-social-services' ).sortable({
					axis: 'y',
					containment: '.sui-box-builder'
				});

				sortableContainer.on( 'sortupdate', $.proxy( me.platformsOrderChanged, me, sortableContainer ) );

				//add all platforms to Add Platforms popup
				for ( let platform in optinVars.social_platforms ) {
					me.addPlatformToDialog( platform );
				}

				this.bindRemoveService();

				if ( 'true' ===  Module.Utils.getUrlParam( 'new' )  ) {
					Module.Notification.open( 'success', optinVars.messages.commons.module_created.replace( /{type_name}/g, optinVars.module_name[ this.moduleType ]), 10000 );
				}
			},

			bindRemoveService() {

				// Delete Social Service
				$( '#hustle-wizard-content .hustle-remove-social-service' ).off( 'click' ).on( 'click', $.proxy( this.removeService, this ) );
			},

			openPlatformsModal( e ) {

				let self = this,
					savedPlatforms = this.model.get( 'social_icons' ),
					platforms = 'undefined' !== typeof savedPlatforms ? Object.keys( savedPlatforms ) : [],
					PlatformsModalView = Hustle.get( 'Modals.Services_Platforms' ),
					platformsModal = new PlatformsModalView( platforms );

				platformsModal.on( 'platforms:added', $.proxy( self.addNewPlatforms, self ) );

				// Show dialog
				SUI.dialogs['hustle-dialog--add-platforms'].show();
			},

			addNewPlatforms( platforms ) {

				if ( ! this.model.get( 'social_icons' ) ) {
					this.model.set( 'social_icons', {});
				}

				let self = this,
					savedPlatforms = _.extend({}, this.model.get( 'social_icons' ) );

				$.each( platforms, ( i, platform ) => {
					if ( savedPlatforms && platform in savedPlatforms ) {

						//If this platform is already set, abort. Prevent duplicated platforms.
						return true;
					}
					self.addPlatformToPanel( platform, {});
					let data = this.getPlatformDefaults( platform );
					savedPlatforms[ platform ] = data;
				});

				this.bindRemoveService();

				this.model.set( 'social_icons', savedPlatforms );

				Hustle.Events.trigger( 'view.rendered', this );

			},

			addPlatformToPanel( platform, data ) {

				let template = Optin.template( 'hustle-platform-row-tpl' ),
					$platformsContainer = this.$( '#hustle-social-services' );

				data = _.extend({}, this.getPlatformViewDefaults( platform ), data );

				this.activePlatforms.push( platform );

				$platformsContainer.append( template( data ) );

			},

			addPlatformToDialog( platform ) {

				let template = Optin.template( 'hustle-add-platform-li-tpl' ),
					$container = $( '#hustle_add_platforms_container' ),
					data = this.getPlatformViewDefaults( platform );
				$container.append( template( data ) );
			},

			getPlatformDefaults( platform ) {
				let label = platform in optinVars.social_platforms ? optinVars.social_platforms[ platform ] : platform,
					defaults = {
						platform: platform,
						label,
						type: 'click',
						counter: '0',
						link: ''
					};

				if ( 'email' === platform ) {
					defaults.title = '{post_title}';
					defaults.message = optinVars.social_platforms_data.email_message_default;
				}

				return defaults;
			},

			getPlatformViewDefaults( platform ) {

				let data = this.model.toJSON(),
					counterEnabled = 'undefined' === typeof data.counter_enabled ? 'true' : data.counter_enabled,
					changedStyles = { 'fivehundredpx': '500px' },
					hasEndpoint = -1 !== optinVars.social_platforms_with_endpoints.indexOf( platform ),
					hasCounter = -1 !== optinVars.social_platforms_with_api.indexOf( platform );

				let platformStyle = platform in changedStyles ? changedStyles[ platform ] : platform,

					viewDefaults = _.extend({}, this.getPlatformDefaults( platform ), {
						'platform_style': platformStyle,
						'counter_enabled': counterEnabled,
						hasEndpoint,
						hasCounter
					});

				return viewDefaults;
			},

			/**
			 * Assign the new platfom order to the model. Triggered when the platforms are sorted.
			 * @since 4.0
			 * @param jQuery sortable object
			 */
			platformsOrderChanged( sortable ) {
				let platforms = this.model.get( 'social_icons' ),
					newOrder = sortable.sortable( 'toArray', { attribute: 'data-platform' }),
					orderedPlatforms = {};

				for ( let id of newOrder ) {
					orderedPlatforms[ id ] = platforms[ id ] ;
				}

				this.model.set( 'social_icons', orderedPlatforms );

				this.model.trigger( 'change', this.model );

			},

			removeService( e ) {

				let $this = $( e.currentTarget ),
					platform =  $this.data( 'platform' ),
					socialIcons = this.model.get( 'social_icons' ),
					$platformContainer = this.$( '#hustle-platform-' + platform );

				// Remove the platform container from the page.
				$platformContainer.remove();

				this.activePlatforms = _.without( this.activePlatforms, platform );

				delete socialIcons[ platform ];

				this.model.trigger( 'change', this.model );

				e.stopPropagation();
			},

			modelUpdated( e ) {
				var changed = e.changed,
					socialIcons,
					key = 'undefined' !== typeof Object.keys( changed )[0] ? Object.keys( changed )[0] : '';

				// for service_type
				if ( 'service_type' in changed ) {
					this.serviceTypeUpdated( changed.service_type );
				}

				// for click_counter
				if ( 'click_counter' in changed ) {
					this.clickCounterUpdated( changed.click_counter );
				} else if ( -1 !== key.indexOf( '_counter' ) ) {
					let platform = key.slice( 0, -8 );
					socialIcons = this.model.get( 'social_icons' );
					if ( platform in socialIcons ) {
						socialIcons[ platform ].counter = parseInt( changed[ key ]);
					}
					this.model.unset( key, {silent: true});
				}

				if ( -1 !== key.indexOf( '_link' ) ) {
					let platform = key.slice( 0, -5 );
					socialIcons = this.model.get( 'social_icons' );
					if ( platform in socialIcons ) {
						socialIcons[ platform ].link = changed[ key ];
					}
					this.model.unset( key, {silent: true});
				}

				if ( -1 !== key.indexOf( '_type' ) ) {
					let platform = key.slice( 0, -5 );
					socialIcons = this.model.get( 'social_icons' );
					if ( platform in socialIcons ) {
						socialIcons[ platform ].type = 'native' === changed[ key ] ? 'native' : 'click';
					}
					this.model.unset( key, {silent: true});
				}

				if ( 'email_title' in changed ) {
					let platform = 'email';
					socialIcons = this.model.get( 'social_icons' );
					if ( platform in socialIcons ) {
						socialIcons[ platform ].title = changed[ key ];
					}
					this.model.unset( key, {silent: true});
				}

				if ( 'email_message' in changed ) {
					let platform = 'email';
					socialIcons = this.model.get( 'social_icons' );
					if ( platform in socialIcons ) {
						socialIcons[ platform ].message = changed[ key ];
					}
					this.model.unset( key, {silent: true});
				}

			},

			serviceTypeUpdated: function( val ) {
				var $counterOptions = this.$( '#wpmudev-sshare-counter-options' ),
					$nativeOptions = $( '.wph-wizard-services-icons-native' ),
					$customOptions = $( '.wph-wizard-services-icons-custom' );

				if ( 'native' === val ) {
					$counterOptions.removeClass( 'wpmudev-hidden' );
					$customOptions.addClass( 'wpmudev-hidden' );
					$nativeOptions.removeClass( 'wpmudev-hidden' );
				} else {
					$counterOptions.addClass( 'wpmudev-hidden' );
					$nativeOptions.addClass( 'wpmudev-hidden' );
					$customOptions.removeClass( 'wpmudev-hidden' );
				}
			},

			clickCounterUpdated: function( val ) {

				var $counterNotice = $( '#wpmudev-sshare-counter-options .hustle-twitter-notice' );
				if ( 'native' === val ) {
					$counterNotice.removeClass( 'wpmudev-hidden' );
				} else {
					if ( ! $counterNotice.hasClass( 'wpmudev-hidden' ) ) {
						$counterNotice.addClass( 'wpmudev-hidden' );
					}
				}
				$( '#wph-wizard-services-icons-native .wpmudev-social-item' ).each( function() {
					var $checkbox = $( this ).find( '.toggle-checkbox' ),
						isChecked = $checkbox.is( ':checked' ),
						$inputCounter = $( this ).find( 'input.wpmudev-input_number' );

					if ( 'none' !== val && isChecked ) {
						$inputCounter.removeClass( 'wpmudev-hidden' );
					} else {
						if ( ! $inputCounter.hasClass( 'wpmudev-hidden' ) ) {
							$inputCounter.addClass( 'wpmudev-hidden' );
						}
					}
				});

				$( '#wph-wizard-services-icons-native #wpmudev-counter-title>strong' ).removeClass( 'wpmudev-hidden' );
				if ( 'none' === val ) {
					$( '#wph-wizard-services-icons-native #wpmudev-counter-title>strong:first-child' ).addClass( 'wpmudev-hidden' );
				} else {
					$( '#wph-wizard-services-icons-native #wpmudev-counter-title>strong:nth-child(2)' ).addClass( 'wpmudev-hidden' );
				}
			},

			toggleCheckbox: function( e ) {
				var $this = this.$( e.target ),
					$li = $this.closest( 'li' ),
					$input = $li.find( 'input' ),
					prop = $input.data( 'attribute' );

				e.preventDefault();
				e.stopPropagation();

				if ( $li.hasClass( 'current' ) ) {
					return;
				}

				$li.addClass( 'current' );
				$li.siblings().removeClass( 'current' );
				this.model.set( prop, $input.val() );

			},

			setSocialIcons: function() {
				var services = this.model.toJSON();
				services = this.getSocialIconsData( services );
				this.model.set( 'social_icons', services.social_icons, { silent: true });
			},

			getSocialIconsData: function( services ) {

				var $socialContainers = $( '#wph-wizard-services-icons-' + services['service_type'] + ' .wpmudev-social-item' ),
					socialIcons = {};

				$socialContainers.each( function() {
					var $sc = $( this ),
						$toggleInput = $sc.find( 'input.toggle-checkbox' ),
						icon = $toggleInput.data( 'id' ),
						$counter = $sc.find( 'input.wpmudev-input_number' ),
						$link = $sc.find( 'input.wpmudev-input_text' );

						// check if counter have negative values
						if ( $counter.length ) {
							let counterVal = parseInt( $counter.val() );
							if ( 0 > counterVal ) {
								$counter.val( 0 );
							}
						}

						if ( $toggleInput.is( ':checked' ) ) {
							socialIcons[icon] = {
								'enabled': true,
								'counter': ( $counter.length ) ? $counter.val() : '0',
								'link': ( $link.length ) ? $link.val() : ''
							};
						}

				});

				if ( $socialContainers.length ) {
					services['social_icons'] = socialIcons;
				}

				return services;
			},

			addPlaceholderToField( e ) {

				const $select = $( e.currentTarget ),
					selectedPlaceholder = $select.val(),
					targetInputName = $select.data( 'field' ),
					$input = $( `[name="${ targetInputName }"]` ),
					val = $input.val() + selectedPlaceholder;

				$input.val( val ).trigger( 'change' );
			}
		}
	) );

});

Hustle.define( 'SShare.Design_View', function( $, doc, win ) {
	'use strict';
	return Hustle.View.extend(

		_.extend({}, Hustle.get( 'Mixins.Model_Updater' ), Hustle.get( 'Mixins.Module_Design' ), {

			//beforeRender() {

			//	// Update the Appearance tab view when the display types are changed in the Display tab.
			//	Hustle.Events.off( 'modules.view.displayTypeUpdated' ).on( 'modules.view.displayTypeUpdated', $.proxy( this.viewChangedDisplayTab, this ) );
			//},

			render: function() {

				//if ( this.targetContainer.length ) {
					this.createPickers();

				//}

				Hustle.Events.off( 'modules.view.displayTypeUpdated' ).on( 'modules.view.displayTypeUpdated', $.proxy( this.viewChangedDisplayTab, this ) );

				// Trigger preview when this tab is shown.
				$( 'a[data-tab="appearance"]' ).on( 'click', $.proxy( this.updatePreview, this ) );
				$( '.sui-box[data-tab="display"] .sui-button[data-direction="next"' ).on( 'click', $.proxy( this.updatePreview, this ) );
				$( '.sui-box[data-tab="visibility"] .sui-button[data-direction="prev"' ).on( 'click', $.proxy( this.updatePreview, this ) );

				this.updatePreview();
			},

			updatePreview: function() {
				$( '#hui-preview-social-shares-floating' ).trigger( 'hustle_update_prewiev' );
			},

			// Adjust the view when model is updated
			viewChanged: function( model ) {

				let changed = model.changed;

				if ( 'flat' === model.get( 'icon_style' ) ) {
					$( '#hustle-floating-icons-custom-background' ).addClass( 'sui-hidden' );
					$( '#hustle-widget-icons-custom-background' ).addClass( 'sui-hidden' );
				} else {
					$( '#hustle-floating-icons-custom-background' ).removeClass( 'sui-hidden' );
					$( '#hustle-widget-icons-custom-background' ).removeClass( 'sui-hidden' );
				}

				if ( 'outline' === model.get( 'icon_style' ) ) {

					// Replace "icon background" text with "icon border"
					$( '#hustle-floating-icons-custom-background .sui-label' ).text( 'Icon border' );
					$( '#hustle-widget-icons-custom-background .sui-label' ).text( 'Icon border' );

					// Hide counter border color
					$( '#hustle-floating-counter-border' ).addClass( 'sui-hidden' );
					$( '#hustle-widget-counter-border' ).addClass( 'sui-hidden' );
				} else {

					// Replace "icon border" text with "icon background"
					$( '#hustle-floating-icons-custom-background .sui-label' ).text( 'Icon background' );
					$( '#hustle-widget-icons-custom-background .sui-label' ).text( 'Icon background' );

					// Show counter border color
					$( '#hustle-floating-counter-border' ).removeClass( 'sui-hidden' );
					$( '#hustle-widget-counter-border' ).removeClass( 'sui-hidden' );
				}

				this.updatePreview();

			},

			viewChangedDisplayTab( model ) {

				const inline = model.get( 'inline_enabled' ),
					widget = model.get( 'widget_enabled' ),
					shortcode = model.get( 'shortcode_enabled' ),
					floatDesktop = model.get( 'float_desktop_enabled' ),
					floatMobile = model.get( 'float_mobile_enabled' ),
					isWidgetEnabled = ( _.intersection([ 1, '1', 'true' ], [ inline, widget, shortcode ]) ).length,
					isFloatingEnabled = ( _.intersection([ 1, '1', 'true' ], [ floatMobile, floatDesktop ]) ).length;

				// TODO: we should be using this.$( '...' ) here instead.
				if ( isFloatingEnabled ) {
					$( '#hustle-appearance-floating-icons-row' ).show();
					$( '#hustle-appearance-floating-icons-placeholder' ).hide();

				} else {
					$( '#hustle-appearance-floating-icons-row' ).hide();
					$( '#hustle-appearance-floating-icons-placeholder' ).show();
				}

				if ( isWidgetEnabled ) {
					$( '#hustle-appearance-widget-icons-row' ).show();
					$( '#hustle-appearance-widget-icons-placeholder' ).hide();
				} else {
					$( '#hustle-appearance-widget-icons-row' ).hide();
					$( '#hustle-appearance-widget-icons-placeholder' ).show();
				}

				if ( ! isWidgetEnabled && ! isFloatingEnabled ) {
					$( '#hustle-appearance-icons-style' ).hide();
					$( '#hustle-appearance-empty-message' ).show();
					$( '#hustle-appearance-floating-icons-placeholder' ).hide();
					$( '#hustle-appearance-widget-icons-placeholder' ).hide();
				} else {
					$( '#hustle-appearance-icons-style' ).show();
					$( '#hustle-appearance-empty-message' ).hide();
				}
			}

		})
	);
});

Hustle.define( 'SShare.Display_View', function( $ ) {
	'use strict';

	return Hustle.View.extend(
		_.extend({}, Hustle.get( 'Mixins.Module_Display' ), {

			viewChanged( changed ) {

				if ( ( _.intersection([ 'float_desktop_enabled', 'float_mobile_enabled', 'inline_enabled', 'widget_enabled', 'shortcode_enabled' ], Object.keys( changed ) ) ).length ) {

					// Show/hide some settings in the Appearance tab.
					Hustle.Events.trigger( 'modules.view.displayTypeUpdated', this.model );

				} else if ( 'float_desktop_position' in changed ) {

					if ( 'right' === changed.float_desktop_position ) {
						this.$( '#hustle-float_desktop-left-offset-label' ).addClass( 'sui-hidden' );
						this.$( '#hustle-float_desktop-right-offset-label' ).removeClass( 'sui-hidden' );
						this.$( '#hustle-float_desktop-offset-x-wrapper' ).removeClass( 'sui-hidden' );

					} else if ( 'left' === changed.float_desktop_position ) {
						this.$( '#hustle-float_desktop-left-offset-label' ).removeClass( 'sui-hidden' );
						this.$( '#hustle-float_desktop-right-offset-label' ).addClass( 'sui-hidden' );
						this.$( '#hustle-float_desktop-offset-x-wrapper' ).removeClass( 'sui-hidden' );

					} else {
						this.$( '#hustle-float_desktop-offset-x-wrapper' ).addClass( 'sui-hidden' );
					}

				} else if ( 'float_desktop_position_y' in changed ) {

					if ( 'bottom' === changed.float_desktop_position_y ) {
						this.$( '#hustle-float_desktop-top-offset-label' ).addClass( 'sui-hidden' );
						this.$( '#hustle-float_desktop-bottom-offset-label' ).removeClass( 'sui-hidden' );

					} else {
						this.$( '#hustle-float_desktop-top-offset-label' ).removeClass( 'sui-hidden' );
						this.$( '#hustle-float_desktop-bottom-offset-label' ).addClass( 'sui-hidden' );
					}

				} else if ( 'float_mobile_position' in changed ) {

					if ( 'right' === changed.float_mobile_position ) {
						this.$( '#hustle-float_mobile-left-offset-label' ).addClass( 'sui-hidden' );
						this.$( '#hustle-float_mobile-right-offset-label' ).removeClass( 'sui-hidden' );
						this.$( '#hustle-float_mobile-offset-x-wrapper' ).removeClass( 'sui-hidden' );

					} else if ( 'left' === changed.float_mobile_position ) {
						this.$( '#hustle-float_mobile-left-offset-label' ).removeClass( 'sui-hidden' );
						this.$( '#hustle-float_mobile-right-offset-label' ).addClass( 'sui-hidden' );
						this.$( '#hustle-float_mobile-offset-x-wrapper' ).removeClass( 'sui-hidden' );

					} else {
						this.$( '#hustle-float_mobile-offset-x-wrapper' ).addClass( 'sui-hidden' );
					}

				} else if ( 'float_mobile_position_y' in changed ) {

					if ( 'bottom' === changed.float_mobile_position_y ) {
						this.$( '#hustle-float_mobile-top-offset-label' ).addClass( 'sui-hidden' );
						this.$( '#hustle-float_mobile-bottom-offset-label' ).removeClass( 'sui-hidden' );

					} else {
						this.$( '#hustle-float_mobile-top-offset-label' ).removeClass( 'sui-hidden' );
						this.$( '#hustle-float_mobile-bottom-offset-label' ).addClass( 'sui-hidden' );
					}

				}
			}
		})
	);
});

Hustle.define( 'Modals.Services_Platforms', function( $ ) {
	'use strict';

	return Backbone.View.extend({

		el: '#hustle-dialog--add-platforms',

		selectedPlatforms: [],

		events: {
			'click .sui-box-selector input': 'selectPlatforms',
			'click .hustle-cancel-platforms': 'cancelPlatforms',
			'click .sui-dialog-overlay': 'cancelPlatforms',

			//Add platforms
			'click #hustle-add-platforms': 'addPlatforms'
		},

		initialize: function( platforms ) {
			this.selectedPlatforms = platforms;

			this.$( '.hustle-add-platforms-option' ).prop( 'checked', false ).prop( 'disabled', false );

			for ( let platform of this.selectedPlatforms ) {
				this.$( '#hustle-social--' + platform ).prop( 'checked', true ).prop( 'disabled', true );
			}
		},

		selectPlatforms: function( e ) {

			let $input = this.$( e.target ),
				$selectorLabel  = this.$el.find( 'label[for="' + $input.attr( 'id' ) + '"]' ),
				value = $input.val()
				;

			$selectorLabel.toggleClass( 'selected' );

			if ( $input.prop( 'checked' ) ) {
				this.selectedPlatforms.push( value );
			} else {
				this.selectedPlatforms = _.without( this.selectedPlatforms, value );
			}
		},

		checkPlatforms: function() {
			for ( let platform of this.selectedPlatforms ) {
				if ( ! this.$( '#hustle-social--' + platform ).prop( 'checked' ) ) {
					this.selectedPlatforms = _.without( this.selectedPlatforms, platform );
				}
			}
		},

		cancelPlatforms: function() {

			// Hide dialog
			SUI.dialogs[ 'hustle-dialog--add-platforms' ].hide();

		},

		addPlatforms: function( e ) {
			let $button   = this.$( e.target );
			$button.addClass( 'sui-button-onload' );
			this.checkPlatforms();
			this.trigger( 'platforms:added', this.selectedPlatforms );
			setTimeout( function() {

				// Hide dialog
				SUI.dialogs[ 'hustle-dialog--add-platforms' ].hide();
				$button.removeClass( 'sui-button-onload' );
			}, 500 );
		}

	});
});

Hustle.define( 'SShare.View', function( $ ) {

	'use strict';
	return Hustle.View.extend(
		_.extend({}, Hustle.get( 'Mixins.Wizard_View' ), {

			_events: {
				'hustle_update_prewiev #hui-preview-social-shares-floating': 'updatePreview'
			},

			updatePreview( e ) {
				var previewData = _.extend({}, this.model.toJSON(), this.getDataToSave() );

				$.ajax({
					type: 'POST',
					url: ajaxurl,
					dataType: 'json',
					data: {
						action: 'hustle_preview_module',
						id: this.model.get( 'module_id' ),
						previewData: previewData
					},
					success: function( res ) {
						if ( res.success ) {
							const $floatingContainer = $( '#hui-preview-social-shares-floating' ),
								$widgetContainer = $( '#hui-preview-social-shares-widget' );
							$floatingContainer.html( res.data.floatingHtml );
							$widgetContainer.html( res.data.widgetHtml );

							if ( res.data.style ) {
								$floatingContainer.append( res.data.style );
							}

							$( '.hustle-share-icon' ).on( 'click', ( e ) => e.preventDefault() );
						}
					}
				});
			},

			/**
			 * Overriding.
			 * @param object opts
			 */
			setTabsViews( opts ) {
				this.contentView = opts.contentView;
				this.displayView = opts.displayView;
				this.designView = opts.designView;
				this.visibilityView = opts.visibilityView;
			},

			/**
			 * Overriding.
			 */
			renderTabs() {

				// Services
				this.contentView.delegateEvents();

				// Appearance view
				this.designView.delegateEvents();

				// Display Options View
				this.displayView.delegateEvents();

				// Visibility view.
				this.visibilityView.delegateEvents();
				this.visibilityView.afterRender();
			},

			/**
			 * Overriding.
			 */
			sanitizeData() {},

			/**
			 * Overriding.
			 */
			getDataToSave() {
				return {
					content: this.contentView.model.toJSON(),
					display: this.displayView.model.toJSON(),
					design: this.designView.model.toJSON(),
					visibility: this.visibilityView.model.toJSON()
				};
			}
		})
	);
});

( function() {

	'use strict';

	/**
	 * Listing Page
	 */
	( function() {

		let page = '_page_hustle_popup_listing';
		if ( page !== pagenow.substr( pagenow.length - page.length ) ) {
			return;
		}

		new Optin.listingBase({ moduleType: optinVars.current.module_type });

	}() );

	/**
	 * Edit or New page
	 */
	( function() {

		let page = '_page_hustle_popup';
		if ( page !== pagenow.substr( pagenow.length - page.length ) ) {
			return;
		}

		let View             = Hustle.View.extend( Hustle.get( 'Mixins.Wizard_View' ) ),
			ViewContent		 = Hustle.View.extend( Hustle.get( 'Mixins.Module_Content' ) ),
			ViewEmails       = Hustle.View.extend( Hustle.get( 'Mixins.Module_Emails' ) ),
			ViewDesign       = Hustle.View.extend( Hustle.get( 'Mixins.Module_Design' ) ),
			ViewVisibility   = Hustle.View.extend( Hustle.get( 'Mixins.Module_Visibility' ) ),
			ViewSettings     = Hustle.View.extend( Hustle.get( 'Mixins.Module_Settings' ) ),
			ViewIntegrations = Hustle.get( 'Module.IntegrationsView' ),

			ModelView           = Module.Model,
			BaseModel = Hustle.get( 'Models.M' );

		return new View({
			model: new ModelView( optinVars.current.data || {}),
			contentView: new ViewContent({ BaseModel }),
			emailsView: new ViewEmails({ BaseModel }),
			designView: new ViewDesign({ BaseModel }),
			integrationsView: new ViewIntegrations({ BaseModel }),
			visibilityView: new ViewVisibility({ BaseModel }),
			settingsView: new ViewSettings({ BaseModel })
		});

	}() );

}() );

( function() {

	'use strict';

	/**
	 * Listing Page
	 */
	( function() {

		let page = '_page_hustle_slidein_listing';
		if ( page !== pagenow.substr( pagenow.length - page.length ) ) {
			return;
		}

		new Optin.listingBase({ moduleType: optinVars.current.module_type });

	}() );

	/**
	 * Edit or New page
	 */
	( function() {

		let page = '_page_hustle_slidein';
		if ( page !== pagenow.substr( pagenow.length - page.length ) ) {
			return;
		}

		let View             = Hustle.View.extend( Hustle.get( 'Mixins.Wizard_View' ) ),
			ViewContent      = Hustle.View.extend( Hustle.get( 'Mixins.Module_Content' ) ),
			ViewEmails       = Hustle.View.extend( Hustle.get( 'Mixins.Module_Emails' ) ),
			ViewDesign       = Hustle.View.extend( Hustle.get( 'Mixins.Module_Design' ) ),
			ViewVisibility   = Hustle.View.extend( Hustle.get( 'Mixins.Module_Visibility' ) ),
			ViewSettings    = Hustle.View.extend( Hustle.get( 'Mixins.Module_Settings' ) ),
			ViewIntegrations = Hustle.get( 'Module.IntegrationsView' ),

			ModelView = Module.Model,
			BaseModel = Hustle.get( 'Models.M' );

		return new View({
			model: new ModelView( optinVars.current.data || {}),
			contentView: new ViewContent({ BaseModel }),
			emailsView: new ViewEmails({ BaseModel }),
			designView: new ViewDesign({ BaseModel }),
			integrationsView: new ViewIntegrations({ BaseModel }),
			visibilityView: new ViewVisibility({ BaseModel }),
			settingsView: new ViewSettings({ BaseModel })
		});

	}() );
}() );

( function() {

	'use strict';

	// Listings Page
	( function() {
		let page = '_page_hustle_embedded_listing';
		if ( page !== pagenow.substr( pagenow.length - page.length ) ) {
			return;
		}

		new Optin.listingBase({ moduleType: optinVars.current.module_type });

	}() );

	// Wizard Page
	( function() {

		let page = '_page_hustle_embedded';
		if ( page !== pagenow.substr( pagenow.length - page.length ) ) {
			return;
		}

		let view				= Hustle.View.extend( Hustle.get( 'Mixins.Wizard_View' ) ),
			ViewContent			= Hustle.View.extend( Hustle.get( 'Mixins.Module_Content' ) ),
			ViewEmails 			= Hustle.View.extend( Hustle.get( 'Mixins.Module_Emails' ) ),
			ViewDesign			= Hustle.View.extend( Hustle.get( 'Mixins.Module_Design' ) ),
			ViewDisplay 		= Hustle.View.extend( Hustle.get( 'Mixins.Module_Display' ) ),
			ViewVisibility		= Hustle.View.extend( Hustle.get( 'Mixins.Module_Visibility' ) ),
			ViewSettings		= Hustle.View.extend( Hustle.get( 'Mixins.Module_Settings' ) ),
			ViewIntegrations 	= Hustle.get( 'Module.IntegrationsView' ),

			viewModel = Module.Model,
			BaseModel = Hustle.get( 'Models.M' );

		return new view({
			model: new viewModel( optinVars.current.data || {}),
			contentView: new ViewContent({ BaseModel }),
			emailsView: new ViewEmails({ BaseModel }),
			designView: new ViewDesign({ BaseModel }),
			integrationsView: new ViewIntegrations({ BaseModel }),
			displayView: new ViewDisplay({ BaseModel }),
			visibilityView: new ViewVisibility({ BaseModel }),
			settingsView: new ViewSettings({ BaseModel })
		});

	}() );

}() );

( function() {

	'use strict';

	/**
	 * Listing Page.
	 */
	( function() {

		let page = '_page_hustle_sshare_listing';
		if ( page !== pagenow.substr( pagenow.length - page.length ) ) {
			return;
		}

		new Optin.listingBase({ moduleType: optinVars.current.module_type });

	}() );


	/**
	 * Wizard page.
	 */
	( function() {

		let page = '_page_hustle_sshare';
		if ( page !== pagenow.substr( pagenow.length - page.length ) ) {
			return;
		}

		const view = Hustle.get( 'SShare.View' ),
			ViewContent = Hustle.get( 'SShare.Content_View' ),
			ViewDisplay = Hustle.get( 'SShare.Display_View' ),
			ViewDesign = Hustle.get( 'SShare.Design_View' ),
			ViewVisibility = Hustle.View.extend( Hustle.get( 'Mixins.Module_Visibility' ) ),

			viewModel = Module.Model,
			BaseModel = Hustle.get( 'Models.M' );

		return new view({
			model: new viewModel( optinVars.current.data || {}),
			contentView: new ViewContent({ BaseModel }),
			displayView: new ViewDisplay({ BaseModel }),
			designView: new ViewDesign({ BaseModel }),
			visibilityView: new ViewVisibility({ BaseModel })
		});
	}() );
}() );


Hustle.define( 'Dashboard.View', function( $, doc, win ) {
	'use strict';

	if ( 'toplevel_page_hustle' !== pagenow ) { // eslint-disable-line camelcase
		return;
	}

	const dashboardView = Backbone.View.extend({

		el: '.sui-wrap',

		events: {
			'click .hustle-preview-module-button': 'openPreview',
			'click .hustle-delete-module-button': 'openDeleteModal',
			'click .hustle-free-version-create': 'showUpgradeModal',
			'click .sui-dropdown .hustle-onload-icon-action': 'addLoadingIconToActionsButton',

			// Modules' actions.
			'click .hustle-single-module-button-action': 'handleSingleModuleAction'
		},

		initialize( opts ) {

			if ( $( '#hustle-dialog--welcome' ).length ) {
				this.openWelcomeDialog();
			}

			if ( $( '#hustle-dialog--migrate' ).length ) {
				this.openMigrateDialog();
			}

			this.doActionsBasedOnUrl();
		},

		doActionsBasedOnUrl() {

			// Display notice based on URL parameters.
			if ( Module.Utils.getUrlParam( 'show-notice' ) ) {
				const status = 'success' === Module.Utils.getUrlParam( 'show-notice' ) ? 'success' : 'error',
					notice = Module.Utils.getUrlParam( 'notice' ),
					message = ( notice && 'undefined' !== optinVars.messages.commons[ notice ]) ? optinVars.messages.commons[ notice ] : Module.Utils.getUrlParam( 'notice-message' );

				if ( 'undefined' !== typeof message && message.length ) {
					Module.Notification.open( status, message );
				}
			}
		},

		openPreview( e ) {
			let $this = $( e.currentTarget ),
				id = $this.data( 'id' ),
				type = $this.data( 'type' );

			Module.preview.open( id, type );
		},

		showUpgradeModal( e ) {
			if ( 'undefined' !== typeof e ) {
				e.preventDefault();
			}

			let $upgradeModal = $( '#wph-upgrade-modal' );
			$upgradeModal.addClass( 'wpmudev-modal-active' );
		},

		/**
		 * @since 4.0
		 */
		openDeleteModal( e ) {
			e.preventDefault();
			let $this = $( e.currentTarget ),
				data = {
					id: $this.data( 'id' ),
					nonce: $this.data( 'nonce' ),
					action: 'delete',
					title: $this.data( 'title' ),
					description: $this.data( 'description' )
				};

			Module.deleteModal.open( data );
		},

		addLoadingIconToActionsButton( e ) {
			const $actionButton = $( e.currentTarget ),
				$mainButton = $actionButton.closest( '.sui-dropdown' ).find( '.sui-dropdown-anchor' );

			$mainButton.addClass( 'sui-button-onload' );
		},

		openWelcomeDialog() {
			Hustle.get( 'Modals.Welcome' );
		},

		openMigrateDialog() {
			Hustle.get( 'Modals.Migration' );
		},

		handleSingleModuleAction( e ) {
			Module.handleActions.initAction( e, 'dashboard', this );
		},

		/**
		 * initAction succcess callback for "toggle-status".
		 * @since 4.0.4
		 */
		actionToggleStatus( $this, data ) {

			const enabled = data.was_module_enabled;

			$this.find( 'span' ).toggleClass( 'sui-hidden' );

			let tooltip = $this.parents( 'td.hui-status' ).find( 'span.sui-tooltip' );
			tooltip.removeClass( 'sui-draft sui-published' );

			if ( enabled ) {
				tooltip.addClass( 'sui-draft' ).attr( 'data-tooltip', optinVars.messages.commons.draft ); // eslint-disable-line camelcase
			} else {
				tooltip.addClass( 'sui-published' ).attr( 'data-tooltip', optinVars.messages.commons.published ); // eslint-disable-line camelcase
			}

		}

	});

	new dashboardView();
});

Hustle.define( 'Integrations.View', function( $, doc, win ) {
	'use strict';

	let page = '_page_hustle_integrations';
	if ( page !== pagenow.substr( pagenow.length - page.length ) ) {
		return;
	}

	const integrationsView = Backbone.View.extend({

		el: '.sui-wrap',

		events: {
			'click .connect-integration': 'connectIntegration',
			'keypress .connect-integration': 'preventEnterKeyFromDoingThings'
		},

		initialize() {

			this.stopListening( Hustle.Events, 'hustle:providers:reload', this.renderProvidersTables );
			this.listenTo( Hustle.Events, 'hustle:providers:reload', this.renderProvidersTables );

			this.render();
		},

		render() {
			var $notConnectedWrapper = this.$el.find( '#hustle-not-connected-providers-section' ),
				$connectedWrapper = this.$el.find( '#hustle-connected-providers-section' );

			if ( 0 < $notConnectedWrapper.length && 0 < $connectedWrapper.length ) {
				this.renderProvidersTables();
			}

			if ( optinVars.integration_redirect ) {
				this.handleIntegrationRedirect();
			}
		},

		renderProvidersTables() {

			var self = this,
				data = {}
			;

			this.$el.find( '.hustle-integrations-display' ).html(
				'<div class="sui-notice sui-notice-sm sui-notice-loading">' +
					'<p>' + optinVars.fetching_list + '</p>' +
				'</div>'
			);

			data.action      = 'hustle_provider_get_providers';
			data._ajax_nonce = optinVars.providers_action_nonce; // eslint-disable-line camelcase
			data.data = {};

			const ajax = $.post({
				url: ajaxurl,
				type: 'post',
				data: data
			})
			.done( function( result ) {
				if ( result && result.success ) {
					self.$el.find( '#hustle-not-connected-providers-section' ).html( result.data.not_connected );
					self.$el.find( '#hustle-connected-providers-section' ).html( result.data.connected );
				}
			});

			//remove the preloader
			ajax.always( function() {
				self.$el.find( '.sui-notice-loading' ).remove();
			});
		},

		// Prevent the enter key from opening integrations modals and breaking the page.
		preventEnterKeyFromDoingThings( e ) {
			if ( 13 === e.which ) { // the enter key code
				e.preventDefault();
				return;
			}
		},

		connectIntegration( e ) {
			Module.integrationsModal.open( e );
		},

		handleIntegrationRedirect() {

			const data 		= optinVars.integration_redirect;
			const migrate 	= optinVars.integrations_migrate;
			window.history.pushState({}, document.title, optinVars.integrations_url );
			if ( 'notification' === data.action ) {

				const status = 'success' === data.status ? 'success' : 'error',
					delay = data.delay ? data.delay : 10000;

				Module.Notification.open( status, data.message, delay );

			}

			if ( migrate.hasOwnProperty( 'provider_modal' ) && 'constantcontact' === migrate.provider_modal ) {
				Module.ProviderMigration.open( migrate.provider_modal );
			}

			if ( migrate.hasOwnProperty( 'provider_modal' ) && 'aweber' === migrate.provider_modal ) {
				Module.ProviderMigration.open( migrate.provider_modal, migrate.integration_id );
			}

			if ( migrate.hasOwnProperty( 'migration_notificaiton' ) ) {
				const status = 'success' === migrate.migration_notificaiton.status ? 'success' : 'error',
					delay  =  migrate.migration_notificaiton.delay ?  migrate.migration_notificaiton.delay : 10000;
				Module.Notification.open( status,  migrate.migration_notificaiton.message, delay );
			}
		}

	});

	new integrationsView();
});

Hustle.define( 'Entries.View', function( $ ) {
	'use strict';

	let page = '_page_hustle_entries';
	if ( page !== pagenow.substr( pagenow.length - page.length ) ) {
		return;
	}

	const entriesView = Backbone.View.extend({

		el: '.sui-wrap',

		events: {
			'click .sui-pagination-wrap .hustle-open-inline-filter': 'openFilterInline',
			'click .sui-pagination-wrap .hustle-open-dialog-filter': 'openFilterModal',
			'click #hustle-dialog--filter-entries .hustle-dialog-close': 'closeFilterModal',
			'click .hustle-delete-entry-button': 'openDeleteModal',
			'click .sui-active-filter-remove': 'removeFilter',
			'click .hustle-entries-clear-filter': 'clearFilter'
		},

		initialize( opts ) {

			var entriesDatePickerRange = {},
				entriesAlert = $( '.hui-entries-alert' );

			if ( 'undefined' !== typeof window.hustle_entries_datepicker_ranges ) {
				entriesDatePickerRange = window.hustle_entries_datepicker_ranges;
			}

			$( 'input.hustle-entries-filter-date' ).daterangepicker({
				autoUpdateInput: false,
				autoApply: true,
				alwaysShowCalendars: true,
				ranges: entriesDatePickerRange,
				locale: optinVars.daterangepicker
			});

			$( 'input.hustle-entries-filter-date' ).on( 'apply.daterangepicker', function( ev, picker ) {
				$( this ).val( picker.startDate.format( 'MM/DD/YYYY' ) + ' - ' + picker.endDate.format( 'MM/DD/YYYY' ) );
			});

			if ( entriesAlert.length ) {

				// Assign correct colspan.
				entriesAlert.attr( 'colspan', entriesAlert.closest( '.sui-table' ).find( '> thead tr th' ).length );

				// Show message.
				entriesAlert.find( 'i' ).hide();
				entriesAlert.find( 'span' ).removeClass( 'sui-screen-reader-text' );
			}
		},

		openFilterInline( e ) {

			var $this    = this.$( e.target ),
				$wrapper = $this.closest( '.sui-pagination-wrap' ),
				$button  = $wrapper.find( '.sui-button-icon' ),
				$filters = $this.closest( '.hui-actions-bar' ).next( '.sui-pagination-filter' )
				;

			$button.toggleClass( 'sui-active' );
			$filters.toggleClass( 'sui-open' );

			e.preventDefault();
			e.stopPropagation();

		},

		openFilterModal( e ) {

			// Show dialog
			// SUI.dialogs['hustle-dialog--filter-entries'].show();

			// Change animation on the show event
			SUI.dialogs['hustle-dialog--filter-entries'].show().on( 'show', function( dialogEl, event ) {
				var content = dialogEl.getElementsByClassName( 'sui-dialog-content' );
				content[0].className = 'sui-dialog-content sui-fade-in';
			});

			e.preventDefault();

		},

		closeFilterModal( e ) {

			// Hide dialog
			SUI.dialogs['hustle-dialog--filter-entries'].hide();

			// Change animation on the hide event
			SUI.dialogs['hustle-dialog--filter-entries'].on( 'hide', function( dialogEl, event ) {
				var content = dialogEl.getElementsByClassName( 'sui-dialog-content' );
				content[0].className = 'sui-dialog-content sui-fade-out';
			});

			e.preventDefault();

		},

		removeFilter( e ) {
			let $this    = this.$( e.target ),
				possibleFilters = [ 'order_by', 'search_email', 'date_range' ],
				currentFilter = $this.data( 'filter' ),
				re = new RegExp( '&' + currentFilter + '=[^&]*', 'i' );

			if ( -1 !== possibleFilters.indexOf( currentFilter ) ) {
				location.href = location.href.replace( re, '' );
			}
		},

		openDeleteModal( e ) {
			e.preventDefault();

			let $this = $( e.target ),
				data = {
					id: $this.data( 'id' ),
					nonce: $this.data( 'nonce' ),
					action: 'delete',
					title: $this.data( 'title' ),
					description: $this.data( 'description' ),
					actionClass: ''
				};

			Module.deleteModal.open( data );
		},

		clearFilter( e ) {

			e.preventDefault();

			this.$( 'input[name=search_email]' ).val( '' );
			this.$( 'input[name=date_range]' ).val( '' );
		}

	});

	new entriesView();
});

Hustle.define( 'ProviderNotice.View', function( $, doc, win ) {
	'use strict';

	const providerNotice = Backbone.View.extend({

		el: '.hustle-provider-notice',
		cookieKey: '',
		events: {
			'click .dismiss-provider-migration-notice': 'HideProviderNotice'
		},

		initialize() {
			this.cookieKey = 'provider_migration_notice_';

			if ( $( '.hustle-provider-notice' ).length ) {
				this.showProviderNotice();
			}
		},

		HideProviderNotice( e ) {
			Optin.cookie.set( this.cookieKey + $( e.currentTarget ).data( 'name' ), 1, 7 );
			location.reload();
		},

		showProviderNotice() {
			let provider = $( '.hustle-provider-notice' ).data( 'name' ),
			notice = Optin.cookie.get( this.cookieKey + provider );
			if ( 1 !== notice ) {
				$( '.hustle_migration_notice__' + provider ).show();
			}
		}

	});

	new providerNotice();
});

Hustle.define( 'Settings.View', function( $, doc, win ) {

	'use strict';

	if ( 'hustle_page_hustle_settings' !== pagenow ) {
		return;
	}

	const viewSettings = Backbone.View.extend({

		el: '.sui-wrap',

		events: {
			'click .sui-sidenav .sui-vertical-tab a': 'sidenav',
			'change select.sui-mobile-nav': 'sidenavMobile',
			'click .sui-pagination-wrap > button': 'pagination',
			'click #hustle-dialog-open--reset-settings': 'resetDialog',
			'click .hustle-load-on-click': 'addLoadingState',

			// Save settings.
			'click .hustle-settings-save': 'handleSave'
		},

		initialize: function( opts ) {

			let me = this,

				recaptchaView = Hustle.get( 'Settings.reCaptcha_Settings' ),
				topMetricsView = Hustle.get( 'Settings.Top_Metrics_View' ),
				privacySettings = Hustle.get( 'Settings.Privacy_Settings' ),
				permissionsView = Hustle.get( 'Settings.Permissions_View' ),
				dataSettings = Hustle.get( 'Settings.Data_Settings' ),
				palettesView = Hustle.get( 'Settings.Palettes' );

				this.recaptchaView = new recaptchaView();
				new topMetricsView();
				new privacySettings();
				new permissionsView();
				new dataSettings();
				new palettesView();

			$( win ).off( 'popstate', $.proxy( me.tabUpdate, me ) );
			$( win ).on( 'popstate', $.proxy( me.tabUpdate, me ) );

			Hustle.Events.trigger( 'view.rendered', this );

			this.doActionsBasedOnUrl();
		},

		doActionsBasedOnUrl() {

			// Do stuff based on URL parameters.
			if ( Module.Utils.getUrlParam( 'show-notice' ) ) {

				// Display notices.
				const status = 'success' === Module.Utils.getUrlParam( 'show-notice' ) ? 'success' : 'error',
					notice = Module.Utils.getUrlParam( 'notice' ),
					message = ( notice && 'undefined' !== optinVars.messages[ notice ]) ? optinVars.messages[ notice ] : Module.Utils.getUrlParam( 'notice-message' );

				if ( 'undefined' !== typeof message && message.length ) {
					Module.Notification.open( status, message );
				}

			} else if ( Module.Utils.getUrlParam( '404-downgrade-modal' ) ) {

				// Display the downgrade to 4.0.4 modal.
				if ( this.$( '#hustle-dialog--404-downgrade' ).length ) {
					SUI.openModal( 'hustle-dialog--404-downgrade', 'hustle-popup-number' );
				}
			}
		},

		sidenav: function( e ) {

			var tabName = $( e.target ).data( 'tab' );

			if ( tabName ) {
				this.tabJump( tabName, true );
			}

			e.preventDefault();
		},

		sidenavMobile( e ) {
			const tabName = $( e.currentTarget ).val();

			if ( tabName ) {
				this.tabJump( tabName, true );
			}
		},

		tabUpdate: function( e ) {

			var state = e.originalEvent.state;

			if ( state ) {
				this.tabJump( state.tabSelected );
			}
		},

		tabJump: function( tabName, updateHistory ) {

			var $tab 	 = this.$el.find( 'a[data-tab="' + tabName + '"]' ),
				$sidenav = $tab.closest( '.sui-vertical-tabs' ),
				$tabs    = $sidenav.find( '.sui-vertical-tab' ),
				$content = this.$el.find( '.sui-box[data-tab]' ),
				$current = this.$el.find( '.sui-box[data-tab="' + tabName + '"]' );

			if ( updateHistory ) {
				history.pushState(
					{ tabSelected: tabName },
					'Hustle Settings',
					'admin.php?page=hustle_settings&section=' + tabName
				);
			}

			$tabs.removeClass( 'current' );
			$content.hide();

			$tab.parent().addClass( 'current' );
			$current.show();
		},

		pagination: function( e ) {

			var $this    = this.$( e.target ),
				$wrapper = $this.closest( '.sui-pagination-wrap' ),
				$button  = $wrapper.find( '.sui-button-icon' ),
				$filters = $wrapper.next( '.sui-pagination-filter' )
				;

			$button.toggleClass( 'sui-active' );
			$filters.toggleClass( 'sui-open' );

			e.preventDefault();
			e.stopPropagation();

		},

		// ============================================================
		// Handle saving actions
		handleSave( e ) {
			e.preventDefault();

			const self = this,
				$this = $( e.currentTarget ),
				relatedFormId = $this.data( 'form-id' ),
				actionData = $this.data();

			let data = new FormData();
			tinyMCE.triggerSave();

			// Grab the form's data if the action has a related form.
			if ( 'undefined' !== typeof relatedFormId ) {
				const $form = $( '#' + relatedFormId );

				if ( $form.length ) {
					data = new FormData( $form[0]);

					// Add unchecked checkboxes.
					$.each( $form.find( 'input[type=checkbox]' ), function() {
						const $this = $( this );
						if ( ! $this.is( ':checked' ) ) {
							data.append( $this.attr( 'name' ), '0' );
						}
					});
				}

			}

			$.each( actionData, ( name, value ) => data.append( name, value ) );

			data.append( '_ajax_nonce', optinVars.current.save_settings_nonce );
			data.append( 'action', 'hustle_save_settings' );

			// Handle the button behavior.
			$this.addClass( 'sui-button-onload' );
			$this.prop( 'disabled', true );

			$.ajax({
				url: ajaxurl,
				type: 'POST',
				data: data,
				contentType: false,
				processData: false
			})
			.done( res => {

				// If the response returned actionable data.
				if ( res.data ) {

					// If there's a defined callback, call it.
					if ( res.data.callback && 'undefined' !== self[ res.data.callback ]) {

						// This calls the "action{ hustle action }" functions from this view.
						// For example: actionToggleStatus();
						self[ res.data.callback ]( $this, res.data, res.success );
					}

					if ( res.data.url ) {
						if ( true === res.data.url ) {
							location.reload();
						} else {
							location.replace( res.data.url );
						}

					} else if ( res.data.notification ) {

						Module.Notification.open( res.data.notification.status, res.data.notification.message, res.data.notification.delay );
					}

					// Don't remove the 'loading' icon when redirecting/reloading.
					if ( ! res.data.url ) {
						$( '.sui-button-onload' ).removeClass( 'sui-button-onload' );
						$this.prop( 'disabled', false );
					}

				} else {

					// Use default actions otherwise.
					if ( res.success ) {
						Module.Notification.open( 'success', optinVars.messages.settings_saved );
					} else {
						Module.Notification.open( 'error', optinVars.messages.something_went_wrong );
					}

					$( '.sui-button-onload' ).removeClass( 'sui-button-onload' );
					$this.prop( 'disabled', false );
				}
			})
			.error( res => {
				$( '.sui-button-onload' ).removeClass( 'sui-button-onload' );
				$this.prop( 'disabled', false );
				Module.Notification.open( 'error', optinVars.messages.something_went_wrong );
			});
		},

		/**
		 * Callback action for when saving reCaptchas.
		 * @since 4.1.0
		 */
		actionSaveRecaptcha() {
			this.recaptchaView.maybeRenderRecaptchas();
		},

		// ============================================================
		// DIALOG
		// Open dialog
		resetDialog: function( e ) {

			var $button = this.$( e.target ),
				$dialog = $( '#hustle-dialog--reset-settings' ),
				$title  = $dialog.find( '#dialogTitle' ),
				$info   = $dialog.find( '#dialogDescription' );

			$title.text( $button.data( 'dialog-title' ) );
			$info.text( $button.data( 'dialog-info' ) );

			SUI.dialogs['hustle-dialog--reset-settings'].show();

			e.preventDefault();

		},

		addLoadingState( e ) {

			const $button = $( e.currentTarget );
			$button.addClass( 'sui-button-onload' );
		}
	});

	new viewSettings();

});
