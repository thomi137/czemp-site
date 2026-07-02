import { InnerBlocks, useBlockProps } from '@wordpress/block-editor';

const ALLOWED = ['core/navigation'];
const TEMPLATE = [['core/navigation', {}]];

export default function Edit() {
    const blockProps = useBlockProps({ className: 'cz-sticky-nav' });

    return (
        <div {...blockProps}>
            <div className="cz-sticky-nav__bar">
                <span className="cz-sticky-nav__hamburger-preview" aria-hidden="true" />
                <div className="cz-sticky-nav__inner">
                    <InnerBlocks
                        allowedBlocks={ALLOWED}
                        template={TEMPLATE}
                        templateLock="all"
                    />
                </div>
            </div>
        </div>
    );
}
