(function () {
    'use stirct';
    jQuery(document).ready(function () {
        function initColorPicker(widget) {
            widget.find('input.ays_color_picker').wpColorPicker();
            jQuery(document).find('.wp-picker-default').val("Default");
            jQuery(document).find('.wp-color-result-text').text("Select Color");
        }

        function onFormUpdate(event, widget) {
            initColorPicker(widget);
        }

        jQuery(document).on('widget-added widget-updated', onFormUpdate);
        jQuery('#widgets-right .widget:has(.ays_color_picker)').each(function () {
            initColorPicker(jQuery(this));
        });

        function media_upload(button_selector) {
            var _custom_media = true,
                _orig_send_attachment = wp.media.editor.send.attachment;
            jQuery(document).on('click', button_selector, function () {
                var button_id = jQuery(this).attr('id');
                wp.media.editor.send.attachment = function (props, attachment) {
                    if (_custom_media) {
                        jQuery('.' + button_id + '_img').attr('src', attachment.url);
                        jQuery('.' + button_id + '_url').val(attachment.url).trigger('change');
                    } else {
                        return _orig_send_attachment.apply(jQuery('#' + button_id), [props, attachment]);
                    }
                }
                wp.media.editor.open(jQuery('#' + button_id));
                return false;
            });

        }
        media_upload('.js_custom_upload_media');
        jQuery(document).on('click', '.ays_button_closer', function () {
            console.log(jQuery(this));
            jQuery(this).parent().find('img#ays_button').attr('src', ' ');
            jQuery(this).parent().find('input.widefat').val(" ").trigger('change');
        });
    });
})();
