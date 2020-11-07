/**
 * @file
 * Preview for the Troth theme.
 */

(function ($, Drupal, drupalSettings) {


  Drupal.color = {
    logoChanged: false,
    callback: function (context, settings, form, farb, height, width) {
        // Change the logo to be the real one.
        if (!this.logoChanged) {
        $('#preview #preview-logo img').attr('src', drupalSettings.color.logo_path);
        this.logoChanged = true;
        }
        // Remove the logo if the setting is toggled off.
        if (drupalSettings.color.logo_path == null) {
        $('div').remove('#preview-logo');
        }

        // Text preview.
        form.find('#preview').css('color', form.find('.js-color-palette input[name="palette[base]"]').val());
        form.find('#preview a').css('color', form.find('.js-color-palette input[name="palette[link]"]').val());
        form.find('#preview-header-menu a').css('color', form.find('.js-color-palette input[name="palette[headermenulink]"]').val());
        form.find('#preview-footer a').css('color', form.find('.js-color-palette input[name="palette[footerlink]"]').val());
        form.find('#preview-footer-bottom a').css('color', form.find('.js-color-palette input[name="palette[footerlink]"]').val());
        form.find('#preview-slogan').css('color', form.find('.js-color-palette input[name="palette[slogan]"]').val());

        // Headings.
        var headingscolor = $('.js-color-palette input[name="palette[link]"]', form).val();
        var headingsshadow = $('.js-color-palette input[name="palette[headingshadow]"]', form).val();

        $('#preview h1', form).attr('style', "color: " + headingscolor + "; text-shadow: 1px 1px 1px " + headingsshadow + ";");

        // Header.
        var gradient_headertop = $('.js-color-palette input[name="palette[headertop]"]', form).val();
        var gradient_headerbottom = $('.js-color-palette input[name="palette[headerbottom]"]', form).val();

        $('#preview #preview-header', form).attr('style', "background-color: " + gradient_headertop + "; background-image: -webkit-gradient(linear, 0% 0%, 0% 100%, from(" + gradient_headertop + "), to(" + gradient_headerbottom + ")); background-image: -moz-linear-gradient(-90deg, " + gradient_headertop + ", " + gradient_headerbottom + ");");

        // Header-menu.
        form.find('#preview-header-menu').css('background-color', form.find('.js-color-palette input[name="palette[headermenu]"]').val());
        form.find('#preview-header-menu').css('border-top-color', form.find('.js-color-palette input[name="palette[headermenuborder]"]').val());
        form.find('#preview-header-menu').css('border-bottom-color', form.find('.js-color-palette input[name="palette[headermenuborder]"]').val());
        
        // Banner.
        var gradient_bannertop = $('.js-color-palette input[name="palette[bannertop]"]', form).val();
        var gradient_bannerbottom = $('.js-color-palette input[name="palette[bannerbottom]"]', form).val();
        var bannerborder = $('.js-color-palette input[name="palette[bannerborder]"]', form).val();

        $('#preview #preview-banner', form).attr('style', "background-color: " + gradient_bannertop + "; background-image: -webkit-gradient(linear, 0% 0%, 0% 100%, from(" + gradient_bannertop + "), to(" + gradient_bannerbottom + ")); background-image: -moz-linear-gradient(-90deg, " + gradient_bannertop + ", " + gradient_bannerbottom + "); border-bottom: 1px solid " + bannerborder + ";");

        // Content.
        var gradient_contenttop = $('.js-color-palette input[name="palette[contenttop]"]', form).val();
        var gradient_contentbottom = $('.js-color-palette input[name="palette[contentbottom]"]', form).val();

        $('#preview #preview-content', form).attr('style', "background-color: " + gradient_contenttop + "; background-image: -webkit-gradient(linear, 0% 0%, 0% 100%, from(" + gradient_contenttop + "), to(" + gradient_contentbottom + ")); background-image: -moz-linear-gradient(-90deg, " + gradient_contenttop + ", " + gradient_contentbottom + ");");

        // Block.
        var blockbg = $('.js-color-palette input[name="palette[blockbg]"]', form).val();
        $('#preview .block', form).attr('style', "background-color: " + blockbg + ";");

        // Footer.
        var gradient_footer = $('.js-color-palette input[name="palette[footer]"]', form).val();
        $('#preview #preview-footer', form).attr('style', "background-color: " + gradient_footer + ";");

        // Footer bottom.
        var gradient_footerbottomtop = $('.js-color-palette input[name="palette[footerbottomtop]"]', form).val();
        var gradient_footerbottombottom = $('.js-color-palette input[name="palette[footerbottombottom]"]', form).val();

        $('#preview-footer-bottom', form).attr('style', "background-color: " + gradient_footerbottomtop + "; background-image: -webkit-gradient(linear, 0% 0%, 0% 100%, from(" + gradient_footerbottomtop + "), to(" + gradient_footerbottombottom + ")); background-image: -moz-linear-gradient(-90deg, " + gradient_footerbottomtop + ", " + gradient_footerbottombottom + ");");
        form.find('#preview-footer-bottom').css('border-top-color', form.find('.js-color-palette input[name="palette[headermenuborder]"]').val());

        // Button.
        var gradient_buttontop = $('.js-color-palette input[name="palette[buttontop]"]', form).val();
        var gradient_buttonbottom = $('.js-color-palette input[name="palette[buttonbottom]"]', form).val();
        var buttontext = $('.js-color-palette input[name="palette[buttontext]"]', form).val();
        var buttontextshadow = $('.js-color-palette input[name="palette[buttontextshadow]"]', form).val();
        var buttonboxshadow = $('.js-color-palette input[name="palette[buttonboxshadow]"]', form).val();

        $('#preview a.more', form).attr('style', "background-color: " + gradient_buttontop + "; background-image: -webkit-gradient(linear, 0% 0%, 0% 100%, from(" + gradient_buttontop + "), to(" + gradient_buttonbottom + ")); background-image: -moz-linear-gradient(-90deg, " + gradient_buttontop + ", " + gradient_buttonbottom + "); -webkit-box-shadow: 0px 1px 2px " + buttonboxshadow + "; -moz-box-shadow: 0px 1px 2px " + buttonboxshadow + "; box-shadow: 0px 1px 2px " + buttonboxshadow + "; text-shadow: 0 1px 1px " + buttontextshadow + "; color: " + buttontext + ";");

    }
  };
})(jQuery, Drupal, drupalSettings);
