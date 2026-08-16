// Copyright (c) 2026 Thomas Prosser. Licensed under GPL-2.0-or-later.

import { InspectorControls, useBlockProps } from '@wordpress/block-editor';
import { PanelBody, ToggleControl, TextControl } from '@wordpress/components';
import ServerSideRender from '@wordpress/server-side-render';
import metadata from './block.json';

export default function Edit({ attributes, setAttributes }) {
    // The attribute is still called currentYearOnly in storage (renaming
    // the actual key is riskier than it looks: WordPress fills in schema
    // defaults for any attribute missing from a block's saved markup, on
    // every render — a second stored attribute would silently override
    // this one wherever it wasn't already saved, which is exactly the two
    // real pages already using this block. Only the label/help text below
    // changed to describe what the toggle now actually does.
    const { currentYearOnly, pastEventsUrl } = attributes;
    const blockProps = useBlockProps();

    return (
        <>
            <InspectorControls>
                <PanelBody title="Einstellungen">
                    <ToggleControl
                        label="Nur bevorstehende Veranstaltungen anzeigen"
                        checked={currentYearOnly}
                        onChange={(value) => setAttributes({ currentYearOnly: value })}
                        help={
                            currentYearOnly
                                ? 'Zeigt datierte Beiträge, deren Enddatum noch nicht erreicht ist (oder die kein Enddatum haben).'
                                : 'Zeigt alle bereits vergangenen Beiträge, nach Jahr gruppiert.'
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
