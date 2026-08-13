// Copyright (c) 2026 Thomas Prosser. Licensed under GPL-2.0-or-later.

jQuery(function ($) {
    var frame;
    var $button = $('#cz-seo-default-image-button');
    var $remove = $('#cz-seo-default-image-remove');
    var $id = $('#cz-seo-default-image-id');
    var $preview = $('#cz-seo-default-image-preview');

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
            $preview.html('<img src="' + attachment.url + '" alt="" style="max-width:300px;height:auto;display:block;margin-bottom:8px;">');
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
