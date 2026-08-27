// Copyright (c) 2026 Thomas Prosser. Licensed under GPL-2.0-or-later.

/**
 * "The next anchor" contract (see current-exhibitions/view.js, which this
 * mirrors verbatim): the first direct child of this block's nearest
 * content-root ancestor that comes after this block's own top-level
 * wrapper in document order and carries a non-empty id. Scoped to the
 * content root's direct children on purpose — ids show up deeper in the
 * markup too (the core/image lightbox trigger, form fields, …) and a
 * naive "first [id] anywhere later in the DOM" search would occasionally
 * lock onto one of those instead of the intended section.
 */
function findNextAnchor(button) {
	const root = button.closest('.entry-content, main');
	if (!root) {
		return null;
	}

	let topLevel = button;
	while (topLevel.parentElement && topLevel.parentElement !== root) {
		topLevel = topLevel.parentElement;
	}
	if (topLevel.parentElement !== root) {
		return null;
	}

	let sibling = topLevel.nextElementSibling;
	while (sibling) {
		if (sibling.id) {
			return sibling;
		}
		sibling = sibling.nextElementSibling;
	}
	return null;
}

// Only "anchor" linkType instances save as a <button> at all — "url"
// instances are a plain <a href>, untouched by this script.
document.querySelectorAll('button.wp-block-czemp-theme-animated-button').forEach((button) => {
	button.addEventListener('click', () => {
		const targetAnchor = button.dataset.targetAnchor;
		const target = (targetAnchor && document.getElementById(targetAnchor)) || findNextAnchor(button);
		if (target) {
			target.scrollIntoView({ behavior: 'smooth', block: 'start' });
		}
	});
});
