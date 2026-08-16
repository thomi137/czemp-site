// Copyright (c) 2026 Thomas Prosser. Licensed under GPL-2.0-or-later.

/**
 * "The next anchor" contract (see context/current-feature.md): the first
 * direct child of this block's nearest content-root ancestor that comes
 * after this block's own top-level wrapper in document order and carries
 * a non-empty id. The content root is `.entry-content` — the wrapper
 * `core/post-content` renders around every template that uses it
 * (front-page, page, page-gallery, single-artwork, index) — falling back
 * to `<main>` for the templates that don't (archive-artwork,
 * taxonomy-collection, 404), where content sits directly in `<main>`
 * instead. Scoped to the content root's direct children on purpose — ids
 * show up deeper in the markup too (the core/image lightbox trigger, form
 * fields, …) and a naive "first [id] anywhere later in the DOM" search
 * would occasionally lock onto one of those instead of the intended
 * section.
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

document.querySelectorAll('.cz-current-exhibitions__arrow').forEach((button) => {
	button.addEventListener('click', () => {
		// An explicit editor-picked target (data-target-anchor, set via the
		// "Sprungziel" dropdown) wins if it resolves to something on the
		// page; otherwise fall back to automatic next-anchor detection —
		// including when the picked anchor was since renamed or removed.
		const targetAnchor = button.dataset.targetAnchor;
		const target = (targetAnchor && document.getElementById(targetAnchor)) || findNextAnchor(button);
		if (target) {
			target.scrollIntoView({ behavior: 'smooth', block: 'start' });
		}
	});
});
