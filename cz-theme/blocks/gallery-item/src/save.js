import {
    InnerBlocks,
    useBlockProps
} from '@wordpress/block-editor';

import { hexToRgba } from './utils';

export default function save({ attributes }) {

    const {
        imageUrl,
        overlayColor,
        overlayOpacity,
        textAlign,
        verticalAlign,
        fontSize,
        height,
        focalPoint
    } = attributes;

    const blockProps = useBlockProps.save({
        className: 'gallery-item',
        style: {
            height: `${height}px`
        }
    });

    return (
        <div {...blockProps}>

            {imageUrl && (
                <img
                    src={imageUrl}
                    alt=""
                    style={{
                        objectPosition: `${
                            (focalPoint?.x ?? 0.5) * 100
                        }% ${
                            (focalPoint?.y ?? 0.5) * 100
                        }%`
                    }}
                />
            )}

            <div
                className="overlay"
                style={{
                    backgroundColor: hexToRgba(
                        overlayColor,
                        overlayOpacity
                    ),
                    justifyContent: verticalAlign,
                    textAlign,
                    fontSize
                }}
            >

                <InnerBlocks.Content />

            </div>

        </div>
    );
}
