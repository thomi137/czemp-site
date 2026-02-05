import { RichText, InspectorControls, PanelColorSettings, MediaUpload, MediaUploadCheck } from '@wordpress/block-editor';
import {PanelBody, SelectControl, Button, RangeControl} from '@wordpress/components';
import { useState } from 'react';
import { hexToRgba } from "./utils";

export default function Edit({ attributes, setAttributes }) {
    const { imageUrl, title, description, overlayColor, overlayOpacity, textAlign, verticalAlign, fontSize } = attributes;

    const [ localTitle, setLocalTitle ] = useState(title);
    const [ localDescription, setLocalDescription ] = useState(description);
    const [ localOverlayColor, setLocalOverlayColor ] = useState(overlayColor);
    const [ localOverlayOpacity, setLocalOverlayOpacity ] = useState(overlayOpacity)

    return (
        <>
            <InspectorControls>
                <PanelBody title="Background Image">
                    <MediaUploadCheck>
                        <MediaUpload
                            onSelect={ media => setAttributes({ imageUrl: media.url }) }
                            allowedTypes={['image']}
                            render={({ open }) => (
                                <Button onClick={open}>
                                    {imageUrl ? 'Change Image' : 'Select Image'}
                                </Button>
                            )}
                        />
                    </MediaUploadCheck>
                </PanelBody>
                <PanelBody  title="Overlay">
                    <PanelColorSettings
                        colorSettings={[
                            {
                                value: attributes.overlayColor,
                                onChange: (color) => {
                                    setAttributes({ overlayColor: color });
                                },
                                label: 'Overlay Color',
                            },
                        ]}
                    />

                    <RangeControl
                        label="Overlay Opacity"
                        value={attributes.overlayOpacity}
                        onChange={(value) => {
                            setLocalOverlayOpacity(value)
                            setAttributes({ overlayOpacity: value })}
                        }
                        min={0}
                        max={1}
                        step={0.05}
                    />

                </PanelBody>


                <PanelBody title="Text Settings">
                    <SelectControl
                        label="Horizontal Alignment"
                        value={textAlign}
                        options={[
                            { label: 'Left', value: 'flex-start' },
                            { label: 'Center', value: 'center' },
                            { label: 'Right', value: 'flex-end' }
                        ]}
                        onChange={(value) => setAttributes({ textAlign: value })}
                    />
                    <SelectControl
                        label="Vertical Alignment"
                        value={verticalAlign}
                        options={[
                            { label: 'Top', value: 'flex-start' },
                            { label: 'Middle', value: 'center' },
                            { label: 'Bottom', value: 'flex-end' }
                        ]}
                        onChange={(value) => setAttributes({ verticalAlign: value })}
                    />
                    <SelectControl
                        label="Font Size"
                        value={fontSize}
                        options={[
                            { label: 'Small', value: '14px' },
                            { label: 'Normal', value: '16px' },
                            { label: 'Large', value: '20px' },
                            { label: 'Extra Large', value: '24px' },
                        ]}
                        onChange={(value) => setAttributes({ fontSize: value })}
                    />
                </PanelBody>
            </InspectorControls>

            <div className="gallery-item">
                {imageUrl && <img src={imageUrl} alt={title} />}
                <div className="overlay"
                     style={{
                         backgroundColor: hexToRgba(attributes.overlayColor, attributes.overlayOpacity),
                         justifyContent: attributes.verticalAlign,
                         alignItems: attributes.textAlign,
                         fontSize: attributes.fontSize,
                         pointerEvents: 'auto'   // allow clicks

                     }}
                >
                    <RichText
                        tagName="h3"
                        value={attributes.title}
                        onChange={(value) => setAttributes({ title: value })}
                        placeholder="Title"
                        formattingControls={['bold', 'italic', 'underline']}
                        allowedFormats={['core/bold','core/italic','core/underline']}
                    />
                    <RichText
                        tagName="p"
                        value={attributes.description}
                        onChange={(value) => setAttributes({ description: value })}
                        placeholder="Description"
                        formattingControls={['bold', 'italic', 'strikethrough']}
                    />
                </div>
            </div>
        </>
    );
}