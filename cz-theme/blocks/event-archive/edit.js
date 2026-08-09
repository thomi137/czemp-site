// Copyright (c) 2026 Thomas Prosser. Licensed under GPL-2.0-or-later.

import { InspectorControls, useBlockProps } from '@wordpress/block-editor';
import { PanelBody, ToggleControl, TextControl } from '@wordpress/components';
import ServerSideRender from '@wordpress/server-side-render';
import metadata from './block.json';

export default function Edit({ attributes, setAttributes }) {
    const { currentYearOnly, pastEventsUrl } = attributes;
    const blockProps = useBlockProps();

    return (
        <>
            <InspectorControls>
                <PanelBody title="Einstellungen">
                    <ToggleControl
                        label="Nur aktuelles Jahr anzeigen"
                        checked={currentYearOnly}
                        onChange={(value) => setAttributes({ currentYearOnly: value })}
                        help={
                            currentYearOnly
                                ? 'Zeigt nur datierte Beiträge aus dem laufenden Jahr.'
                                : 'Zeigt alle datierten Beiträge aus vergangenen Jahren, nach Jahr gruppiert.'
                        }
                    />
                    {currentYearOnly && (
                        <TextControl
                            label="Link zu vergangenen Veranstaltungen"
                            value={pastEventsUrl}
                            onChange={(value) => setAttributes({ pastEventsUrl: value })}
                            placeholder="https://…"
                            help="Wird als Button unter der Liste angezeigt, falls gesetzt."
                        />
                    )}
                </PanelBody>
            </InspectorControls>
            <div {...blockProps}>
                <ServerSideRender block={metadata.name} attributes={attributes} />
            </div>
        </>
    );
}
