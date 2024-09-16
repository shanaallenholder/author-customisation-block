import { __ } from '@wordpress/i18n';
import { useBlockProps, MediaUpload, InspectorControls} from '@wordpress/block-editor';
import { PanelBody } from '@wordpress/components';
import { select, useSelect } from '@wordpress/data';
import { useEffect, useState, Fragment } from '@wordpress/element';
import './editor.scss';
import { RichText } from '@wordpress/block-editor';

//
export default function Edit( { attributes, setAttributes } ) {
	const { authorImage, selectedAuthor, biography } = attributes;
	const [ loaded, setLoaded ] = useState(false);
	const [ author, setAuthor ] = useState(false);
	
	// useSelect is a react hook that allows us to access the data from the wordpress data store
	// select('core') access the core data store
	// who: authors will fetch a list of authors who are authors 
	const authors = useSelect((select)=>{
		return select('core').getUsers({ who: 'authors' });
	});

	
// This async function handles changes in a user selection (from my customised drop down box). Updates the blocks state and attributes and retrievies detailed user information from the REST API/ wordpress data store. 
	const selectChange = async ( event ) => {
		setAttributes({selectedAuthor: parseInt(event.target.value) });

		setAuthor(select('core').getUsers({id: event.target.value}));

		let response = await wp.apiFetch({path: '/wp/v2/users/' + event.target.value});

		console.log(response);
	}

// This useEffect will run everytime  the authors array changes. it will check if authors exists and if at least one item then it will return the authors array.
	useEffect( () => {
		console.log(selectedAuthor);
		if( authors && authors.length ) {
			setLoaded( true )
		} 
    }, [authors] )



	// select provides a user interface for selecting a author. It is a dropdown menu in HTML that allows users to select one option from a list
	// The onChange is set to selectChange which is called every time the user selects a different option from the dropdown
	// dafaultValue={''} ensures the drop down starts with no selected author first until one is selected.
	// My map maps over the authors array to generate options elements for each author.
	return (
		<Fragment>
		<p { ...useBlockProps() }>
			<select onChange={selectChange} defaultValue={''}>
			{ loaded ? authors.map((author, index) => ( <option key={index} value={author.id}> {author.name} </option> )) : 'Authors Loading..'}
			</select>
		</p>
		<InspectorControls>
			<PanelBody title={__('Image upload')} initialOpen={false}>
				<MediaUpload
				onSelect={(authorImage) => {
					setAttributes({
						authorImage: {
							title: authorImage.title || 'No title',
							url: authorImage.url || '',
						}
						
					});
				}}
				allowedTypes={['images']} // Only images allows in upload
				multiple={false} // only one image at a time
				render={({ open }) => (
					<>
					<button onClick={open}>
						{!authorImage ? __('+ Upload file' ) : __('Change Image')}
					</button>
					{authorImage?.url && (
					<p>
                    {__('Current Image: ')}
					<img
					src={authorImage.url}
					alt={authorImage.title}
					style={{maxWidth: '100%', height: 'auto'}}
					/>
					</p>
						)}
					</>
				)}
				/>
			</PanelBody>
		</InspectorControls>
		<div className={"author-customisation-profile-bio"}>
				<RichText
			    tagName="p"
				placeholder={__('Author Biography here...')}
				value={biography}
				onChange={(newBiography) => setAttributes({ biography: newBiography})}
			/>
			</div>
		</Fragment>
	
	);
}
