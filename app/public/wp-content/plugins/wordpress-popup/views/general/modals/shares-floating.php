<div class="hustle-shares-floating {{ ( _.isTrue(floating_social_animate_icons) ) ? 'hustle-shares-animated' : '' }}">

    <#
    if ( !_.isEmpty(social_icons) ) {
        _.each( social_icons, function( icon, key ){ #>
            <a data-social="{{key}}" href="{{ ( 'custom' === service_type ) ? icon.link : '#' }}" {{ ( 'custom' === service_type ) ? 'target="_blank"' : '' }} class="hustle-social-icon hustle-social-icon-{{service_type}} hustle-social-icon-counter-{{click_counter}} hustle-icon-{{icon_style}} {{ ( _.isFalse(customize_colors) ) ? 'hustle-icon-' + key : '' }} {{ ( 'flat' === icon_style && ( 'native' === service_type && 'none' !== click_counter ) ) ? 'has-counter' : '' }} {{ ( 'native' === service_type && 'none' !== click_counter && _.isTrue(floating_inline_count) ) ? 'hustle-social-inline' : '' }}" aria-label="Share on <# if ( 'facebook' === key ) { #>Facebook<# } #><# if ( 'twitter' === key ) { #>Twitter<# } #><# if ( 'pinterest' === key ) { #>Pinterest<# } #><# if ( 'reddit' === key ) { #>Reddit<# } #><# if ( 'linkedin' === key ) { #>Linkedin<# } #><# if ( 'vkontakte' === key ) { #>Vkontakte<# } #><# if ( 'fivehundredpx' === key ) { #>500px<# } #><# if ( 'houzz' === key ) { #>Houzz<# } #><# if ( 'instagram' === key ) { #>Instagram<# } #><# if ( 'twitch' === key ) { #>Twitch<# } #><# if ( 'youtube' === key ) { #>YouTube<# } #><# if ( 'telegram' === key ) { #>Telegram<# } #>">

                <div class="hustle-icon-container" aria-hidden="true">

                    <# if ( 'facebook' === key ) { #>
                        <?php $this->render("general/icons/social/facebook"); ?>
                    <# } #>
                    <# if ( 'twitter' === key ) { #>
                        <?php $this->render("general/icons/social/twitter"); ?>
                    <# } #>
                    <# if ( 'pinterest' === key ) { #>
                        <?php $this->render("general/icons/social/pinterest"); ?>
                    <# } #>
                    <# if ( 'reddit' === key ) { #>
                        <?php $this->render("general/icons/social/reddit"); ?>
                    <# } #>
                    <# if ( 'linkedin' === key ) { #>
                        <?php $this->render("general/icons/social/linkedin"); ?>
                    <# } #>
                    <# if ( 'vkontakte' === key ) { #>
                        <?php $this->render("general/icons/social/vkontakte"); ?>
                    <# } #>
                    <# if ( 'fivehundredpx' === key ) { #>
                        <?php $this->render("general/icons/social/fivehundredpx"); ?>
                    <# } #>
                    <# if ( 'houzz' === key ) { #>
                        <?php $this->render("general/icons/social/houzz"); ?>
                    <# } #>
                    <# if ( 'instagram' === key ) { #>
                        <?php $this->render("general/icons/social/instagram"); ?>
                    <# } #>
					<# if ( 'twitch' === key ) { #>
                        <?php $this->render("general/icons/social/twitch"); ?>
                    <# } #>
					<# if ( 'youtube' === key ) { #>
                        <?php $this->render("general/icons/social/youtube"); ?>
                    <# } #>
					<# if ( 'telegram' === key ) { #>
                        <?php $this->render("general/icons/social/telegram"); ?>
                    <# } #>

                </div>

				<# if ( 'native' === service_type ) {

					if ( 'click' === click_counter ) { #>

						<div class="hustle-shares-counter"><span>{{ icon.counter }}</span></div>

					<# } else if ( 'native' === click_counter && _.isFalse( _.isUndefined( icon.native_counter ) ) && icon.native_counter !== 0 ) { #>

						<div class="hustle-shares-counter"><span>{{ icon.native_counter }}</span></div>

					<# } else if ( <?php echo isset( $is_preview ) ? 'true' : 'false' ; ?> ) { #>

						<div class="hustle-shares-counter"><span>9</span></div>

					<# }

				} #>
            </a>
    <#
        } );
    }
    #>

</div>
