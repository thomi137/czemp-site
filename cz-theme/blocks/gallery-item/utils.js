// Copyright (c) 2026 Thomas Prosser. Licensed under GPL-2.0-or-later.

// Extra <img> props (srcSet, sizes, width, height, loading) for a
// responsive image — built from what edit.js's MediaUpload onSelect
// captures (see block.json's imageId/imageSizes/imageWidth/imageHeight).
// Returns {} (today's plain <img src> shape, nothing extra) whenever
// there's nothing usable to add — no imageId (every already-published
// post predating this attribute, which parses to its default 0) or an
// imageId with zero usable sizes (e.g. a non-raster upload). save.js
// spreads the result onto the <img> unconditionally, so this single
// function is what keeps old content rendering byte-for-byte identical
// to before — no separate deprecated save() needed.
export function buildResponsiveImageProps({ imageId, imageUrl, imageSizes, imageWidth, imageHeight }) {
    if (!imageId) {
        return {};
    }

    // Keyed by width so the full-size original, if its width happens to
    // match one of the registered sizes exactly, doesn't produce a
    // duplicate (and invalid) descriptor in the srcset.
    const byWidth = new Map();
    Object.values(imageSizes ?? {}).forEach((size) => {
        if (size?.url && size?.width) {
            byWidth.set(size.width, size.url);
        }
    });
    if (imageUrl && imageWidth) {
        byWidth.set(imageWidth, imageUrl);
    }

    if (byWidth.size === 0) {
        return {};
    }

    const srcSet = [...byWidth.entries()]
        .sort((a, b) => a[0] - b[0])
        .map(([width, url]) => `${url} ${width}w`)
        .join(', ');

    const props = {
        srcSet,
        // Matches the showcase/gallery/events grids' actual 1/2/3
        // column breakpoints (assets/scss/_breakpoints.scss: $sm/$md).
        sizes: '(min-width: 900px) 33vw, (min-width: 600px) 50vw, 100vw',
        loading: 'lazy',
    };

    if (imageWidth) {
        props.width = imageWidth;
    }
    if (imageHeight) {
        props.height = imageHeight;
    }

    return props;
}

export function hexToRgba(hex, opacity = 1) {
    let c;
    if(/^#([A-Fa-f0-9]{3}){1,2}$/.test(hex)){
        c= hex.substring(1).split('');
        if(c.length=== 3){
            c= [c[0], c[0], c[1], c[1], c[2], c[2]];
        }
        c= '0x'+c.join('');
        return 'rgba('+[(c>>16)&255, (c>>8)&255, c&255].join(',')+','+opacity+')';
    }
    return hex; // fallback if not hex
}
