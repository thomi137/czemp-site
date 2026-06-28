import {
    useBlockProps,
    InspectorControls
} from '@wordpress/block-editor';

import {
    PanelBody,
    ToggleControl,
    RangeControl
} from '@wordpress/components';

export default function Edit({ attributes, setAttributes }) {

    const {
        showExcerpt,
        showImage,
        imageSize
    } = attributes;

    const blockProps = useBlockProps({
        className: 'post-list-item'
    });

    return (
        <>
            <InspectorControls>

                <PanelBody title="Settings">

                    <ToggleControl
                        label="Show Image"
                        checked={showImage}
                        onChange={(value) =>
                            setAttributes({ showImage: value })
                        }
                    />

                    <ToggleControl
                        label="Show Excerpt"
                        checked={showExcerpt}
                        onChange={(value) =>
                            setAttributes({ showExcerpt: value })
                        }
                    />

                    <RangeControl
                        label="Image Size"
                        value={imageSize}
                        onChange={(value) =>
                            setAttributes({ imageSize: value })
                        }
                        min={60}
                        max={300}
                    />

                </PanelBody>

            </InspectorControls>

            <div {...blockProps}>

                {showImage && (
                    <div
                        className="post-list-item__image"
                        style={{
                            width: `${imageSize}px`
                        }}
                    />
                )}

                <div className="post-list-item__content">

                    <h3>Post Title</h3>

                    {showExcerpt && (
                        <p>
                            Post excerpt preview...
                        </p>
                    )}

                </div>

            </div>
        </>
    );
}
