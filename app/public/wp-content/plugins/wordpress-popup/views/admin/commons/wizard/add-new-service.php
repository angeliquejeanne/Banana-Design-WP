<div id="wph-add-new-service-modal" class="wpmudev-modal">

    <div class="wpmudev-modal-mask" aria-hidden="true"></div>

    <div class="wpmudev-box-modal">

    </div>

</div>

<script id="wpmudev-hustle-modal-add-new-service-tpl" type="text/template">

    <div class="wpmudev-box-head">

        <h2 class="{{ ( _.isFalse(is_new) ) ? 'wpmudev-hidden' : '' }}" ><?php esc_attr_e( "Add Email Service", 'hustle' ); ?></h2>
        <h2 class="{{ ( _.isTrue(is_new) ) ? 'wpmudev-hidden' : '' }}" ><?php esc_attr_e( "Update Email Service", 'hustle' ); ?></h2>

        <?php $this->render("general/icons/icon-close" ); ?>

    </div>

    <div class="wpmudev-box-body">

        <form id="wph-optin-service-details-form">
            <div id="wph-provider-select" class="wpmudev-provider-block">
                <label><?php esc_attr_e('Choose email service provider:', 'hustle' ); ?></label>

                <select name="optin_provider_name" class="wpmudev-select" data-nonce="<?php echo esc_attr( wp_create_nonce('change_provider_name') ); ?>" value="{{service}}">

                <?php foreach( $providers as $provider ) : ?>

                    <option value="<?php echo esc_attr( $provider['slug'] ); // phpcs:ignore ?>" {{_.isTrue('<?php echo esc_html( $provider['slug'] ); ?>' === service) ? 'selected' : ''}}><?php echo esc_html( $provider['title'] ); ?></option>

                <?php endforeach; ?>

                </select>

            </div><?php // #wph-provider-select ?>

            <div id="wph-provider-account-details" class="wpmudev-provider-block"></div>

            <div id="optin-provider-account-options" class="wpmudev-provider-block"></div>

            <div id="wpoi-loading-indicator" style="display: none;">

                <label class="wpmudev-label--loading">

                    <span><?php esc_attr_e('Wait a bit, content is being loaded...', 'hustle' ); ?></span>

                </label>

            </div>
        </form>

    </div>

    <div class="wpmudev-box-footer"></div>

</script>

<script id="wpmudev-hustle-modal-view-form-fields-tpl" type="text/template">

	<# if( 'object' === typeof form_fields && Object.keys(form_fields).length ) {
		_.each( form_fields , function( form_field ) {
			var required = '',
				asterisk = '';
			if ( 'true' == form_field.required || 'recaptcha' === form_field.type ){
				required = 'class="wpmudev-field-required"';
				asterisk = '<span class="wpdui-fi wpdui-fi-asterisk"></span>';
			}
			#>
			<tr>
				<td {{{required}}} data-text="<?php esc_attr_e( "Form Element", 'hustle' ); ?>">{{{asterisk}}} {{ 'recaptcha' === form_field.type ? '' : form_field.label }}</td>
				<td data-text="<?php esc_attr_e( "Form Type", 'hustle' ); ?>">{{form_field.type}}</td>
				<td data-text="<?php esc_attr_e( "Default Text", 'hustle' ); ?>">{{ 'recaptcha' === form_field.type ? '' : form_field.placeholder }}</td>
			</tr>
			<#
		});
	}#>
</script>

