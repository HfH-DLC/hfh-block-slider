/**
 * Retrieves the translation of text.
 *
 * @see https://developer.wordpress.org/block-editor/packages/packages-i18n/
 */
import { __ } from '@wordpress/i18n';

/**
 * React hook that is used to mark the block wrapper element.
 * It provides all the necessary props like the class name.
 *
 * @see https://developer.wordpress.org/block-editor/packages/packages-block-editor/#useBlockProps
 */
import { useBlockProps } from '@wordpress/block-editor';

/**
 * Lets webpack process CSS, SASS or SCSS files referenced in JavaScript files.
 * Those files can contain any CSS code that gets applied to the editor.
 *
 * @see https://www.npmjs.com/package/@wordpress/scripts#using-css
 */
import './editor.scss';

import { SelectControl, TextControl } from '@wordpress/components';

/**
 * The edit function describes the structure of your block in the context of the
 * editor. This represents what the editor will render when the block is used.
 *
 * @see https://developer.wordpress.org/block-editor/developers/block-api/block-edit-save/#edit
 *
 * @return {WPElement} Element to render.
 */
export default function Edit({ attributes, setAttributes, categories, isRequesting }) {
	if (isRequesting) {
		return (
			<p {...useBlockProps()}>
				Loading...
			</p>
		)
	}
	if (categories && categories.length > 0) {
		const options = categories.map(category => { return { value: category.id, label: category.name } });
		const updateCategory = (option) => {
			setAttributes({ categoryId: parseInt(option) });
		}
		const updateNumberOfSlides = value => {
			setAttributes({ numberOfSlides: parseInt(value) });
		}
		const selectedCategoryId = attributes.categoryId;
		if (!selectedCategoryId) {
			updateCategory(categories[0].id);
		}
		const numberOfSlides = attributes.numberOfSlides;
		return (
			<div {...useBlockProps()}>
				<div class="components-placeholder is-large">
					<div class="components-placeholder__label">HfH Slider</div>
					<div class="components-placeholder__instructions">Choose the category of the pages to be displayed in the slider.</div>
					<SelectControl
						label="Category"
						value={selectedCategoryId}
						options={options}
						onChange={updateCategory}
					/>
					<TextControl
						label="Number of slides to display"
						value={numberOfSlides}
						type="number"
						onChange={updateNumberOfSlides}
					/>
				</div>
			</div>
		);
	}
	return (
		<div {...useBlockProps()}>
			<div class="components-placeholder is-large">
				<div class="components-placeholder__label">HfH Slider</div>
				<div class="components-placeholder__instructions">No categories found.</div>
			</div>
		</div>
	);
}
