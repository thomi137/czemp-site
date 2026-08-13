// Copyright (c) 2026 Thomas Prosser. Licensed under GPL-2.0-or-later.

// Same media-picker behavior as seo-image-picker.js, but scoped by class
// (not id) since this runs inside the per-post "SEO & Social" meta box
// rather than a one-off settings page.
jQuery(function ($) {
    var frame;
    var $button = $('.cz-seo-image-button');
    var $remove = $('.cz-seo-image-remove');
    var $id = $('.cz-seo-image-id');
    var $preview = $('.cz-seo-image-preview');

    $button.on('click', function (e) {
        e.preventDefault();
        if (frame) {
            frame.open();
            return;
        }
        frame = wp.media({
            title: 'Bild auswählen',
            multiple: false,
            library: { type: 'image' },
        });
        frame.on('select', function () {
            var attachment = frame.state().get('selection').first().toJSON();
            $id.val(attachment.id);
            $preview.html('<img src="' + attachment.url + '" style="max-width:100%;height:auto;display:block;margin-bottom:6px;">');
            $remove.show();
        });
        frame.open();
    });

    $remove.on('click', function (e) {
        e.preventDefault();
        $id.val('');
        $preview.html('');
        $remove.hide();
    });
});
