/**
* Model: A Video
* Collection: A number of Models
*/

/**
* View: Video Error
*/
wp.media.view.EnviraVideosError = wp.Backbone.View.extend( {

	// The outer tag and class name to use. The item is wrapped in this
    tagName   : 'div',
    className : 'envira-gallery-error envira-videos-error',

    render: function() {

    	// Load the template to render
    	this.template = wp.media.template( 'envira-videos-error' );

    	// Define the HTML for the template
       	this.$el.html( this.template( this.model ) );

       	// Return the template
        return this;

    }

} );

/**
* Model: Video Item
*/
wp.media.model.EnviraVideo = Backbone.Model.extend( {

	/**
	* Define defaults
	*/
	defaults: {
		title: 	'',
		link: 	'', // Video URL
		image: 	'', // Image Placeholder URL (if self hosted)
		caption:'',
		alt: 	'',
		hosted_video: false,
	}

} );

/**
* View: Videos Item
*/
wp.media.view.EnviraVideosItem = wp.Backbone.View.extend( {

	// The outer tag and class name to use. The item is wrapped in this
    tagName   : 'li',
    className : 'attachment envira-videos-attachment',

    /**
    * Events
    */
    events: {
    	// Update Model on input change
    	'keyup input': 'updateItem',
    	'change input': 'updateItem',
    	// Delete Model on view deletion
		'click .envira-videos-delete': 'deleteItem',
  	},

  	/**
  	* Initialize
  	*/
  	initialize: function() {

  		this.model.view = this;

  	},

  	/**
  	* Update the model associated with this view (i.e. the EnviraVideo model)
  	* when a change to an input happens
  	*/
  	updateItem: function( event ) {

  		this.model.set( event.target.name, event.target.value, { silent: true } );

  		// If the target is the video field, check whether the video entered is a self hosted
  		// video or not
  		if ( event.target.name == 'link' ) {
  			// If no video link, revert the hosted_video flag
  			if ( event.target.value == '' ) {
  				this.model.set( 'hosted_video', false );
  			} else {
  				// Perform an AJAX query to determine the video type
  				// This allows addons to hook into the PHP function to determine whether their own
  				// video types are hosted videos or not
  				wp.media.ajax( 'envira_videos_is_hosted_video', {
					context: this,
					data: {
						nonce: 	 	envira_videos_media_view.nonce,
						video_url:  event.target.value
					},
					success: function( response ) {
						if ( response ) {
							// Is a self hosted video
							this.model.set( 'hosted_video', true );
						} else {
							// Not a self hosted video
							this.model.set( 'hosted_video', false );
						}
					},
					error: function( error_message ) {
						// Something went wrong
						// Assume it isn't a hosted video
						this.model.set( 'hosted_video', false );

						// Tell wp.media we've finished, but there was an error
						this.frame.content.get().trigger( 'loaded loaded:error', error_message );
					}
				} );
  			}
  		}

  		// Refresh view
		if ( this.model.get( 'hosted_video' ) ) {
			this.model.view.$el.find( 'div.image' ).show();
		} else {
			this.model.view.$el.find( 'div.image' ).hide();
		}

  	},

  	/**
  	* Destroys the model and view when deleted
  	*/
  	deleteItem: function( event ) {

  		// Trigger the loading event
		this.trigger( 'loading' );

		// Delete the view from the modal
		var item = jQuery( event.target );
		item.parent().parent().remove();

		// Delete the model
		// This will automatically remove the model from the collection
		this.model.destroy();

		// Trigger the loaded event
		this.trigger( 'loaded loaded:success' );

  	},

  	/**
  	* Render the HTML output
  	*/
    render: function() {

    	// Load the template to render
    	this.template = wp.media.template( 'envira-videos-item' );

    	// Define the HTML for the template
       	this.$el.html( this.template( this.model.toJSON() ) );

       	// Return the template
        return this;

    }

} );

/**
* View: Bottom Toolbar
*/
wp.media.view.Toolbar.EnviraVideos = wp.media.view.Toolbar.extend( {

	/**
	* Initialize
	*/
	initialize: function() {
		_.defaults( this.options, {

		    event: 'envira_videos_insert',
		    close: false,
			items: {
				/**
				* Insert Button
				*/
			    envira_videos_insert: {
			    	id: 'envira-videos-button',
			    	style: 'primary',
			        text: wp.media.view.l10n.insertIntoPost, // Will read "Insert into Gallery", as we modify this in Envira Gallery metaboxes.php::media_view_strings()
			        priority: 80,
			        requires: false,

			        /**
			        * On Click
			        */
			        click: function() {
			        	// Insert the selected videos into the Gallery
			        	this.controller.state().enviraVideosInsert();
			        }
			    }
			}
		});

		// Initialize the bottom toolbar
		wp.media.view.Toolbar.prototype.initialize.apply( this, arguments );

	},

    /**
    * Refreshes the toolbar items (i.e. button) enable/disabled state, depending on whether any items were selected
    * Fired by the main controller when an item is selected or deselected
    */
	refresh: function() {

		// Disable the Insert into Gallery Button if nothing was selected
		this.get( 'envira_videos_insert' ).model.set( 'disabled', false );

		// Apply the refresh
		wp.media.view.Toolbar.prototype.refresh.apply( this, arguments );

	}

} );

/**
* View: Media Content Area
*/
wp.media.view.EnviraVideos = wp.media.View.extend({

	/**
	* Define any events you want to watch for here, for example
	* a search, item selection / deselection etc.
	*/
	events: {
		// Add
		'click .envira-videos-add': 'addItem',

		// Change Video URL
		'keyup input': 'refreshView',
	},

	/**
	* Initialize
	* - Fired when the main UI view is loaded by clicking "Insert Videos"
	*/
	initialize: function() {

		// Define a collection, which will store the items (folders and images)
		this.collection = new Backbone.Collection();

		// Define some flags
		this.is_loading = false; // Tells us whether we're making an AJAX request or doing something

		// Initialise the view, comprising of a sidebar and attachment (items) area
		this.$el.prepend( wp.media.template( 'envira-videos-router' ) );
		this.$el.prepend( wp.media.template( 'envira-videos-side-bar' ) );
		this.$el.prepend( wp.media.template( 'envira-videos-items' ) );

		// Define events
		this.on( 'loading',       this.loading, this );
		this.on( 'loaded',        this.loaded, this );

	},

	/**
	* Displays the loading spinner
	*/
	loading: function() {

		// Set a flag so we know we're loading data
		this.is_loading = true;

		// Show the spinner
		this.$el.find( '.spinner' ).addClass( 'is-active' );

	},

	/**
	* Hides the loading spinner
	*/
	loaded: function( response ) {

		// Set a flag so we know we're not loading anything now
		this.is_loading = false;

		// Hide the spinner
		this.$el.find( '.spinner' ).removeClass( 'is-active' );

		// Remove any existing errors
		this.$el.find( 'div.envira-gallery-error' ).remove();

		// Extract the error message
		if ( typeof response == 'object' ) {
			response = response.responseText;
		}

		// Display the error message, if it's provided
		if ( typeof response !== 'undefined' ) {
			this.$el.find( 'div.media-toolbar' ).after( this.renderError( response ) );
			this.$el.find( 'ul.attachments.envira-videos-attachments' ).css( 'margin-top', this.$el.find( 'div.envira-gallery-error' ).height() + 20 );
		}

		// Update toolbar
		this.controller.toolbar.get().refresh();

	},

	/**
	* Clears items from the media view
	*/
	clearItems: function() {

		this.$el.find( 'ul.envira-videos-attachments' ).empty();

	},

	/**
	* Renders an individual error, by calling
	* wp.media.view.EnviraVideosError
	*/
	renderError: function( error ) {

		// Define model
		var model = {};
		model.error = error;

		// Define view
		var view = new wp.media.view.EnviraVideosError( {
			model: model
		} );

		// Return rendered view
		return view.render().el;

	},

	/**
	* Adds a Video to the media modal view, for the user to complete
	*/
	addItem: function( event ) {

		// Trigger the loading event
		this.trigger( 'loading' );

		// Create a new EnviraVideo model
		model = new wp.media.model.EnviraVideo;

		// Add the model to the selection
		this.getSelection().add( model );

		// Load the view, assigning it to the model
		var view = new wp.media.view.EnviraVideosItem( {
			model : model,
			controller: this,
		} );

		// Render the view in the modal window
		this.$el.find( 'ul.envira-videos-attachments' ).append( view.render().el );

		// Trigger the loaded event
		this.trigger( 'loaded loaded:success' );

	},

	/**
	* Iterates through each model in the collection, checking whether
	* the hosted_video flag is true or false.  Depending on this, we then
	* show/hide a field in the view attached to that model
	*/
	refreshView: function( event ) {

		this.model.each( function( model ) {
			if ( model.get( 'hosted_video' ) ) {
				model.view.$el.find( 'div.image' ).show();
			} else {
				model.view.$el.find( 'div.image' ).hide();
			}
		} );

	},

	/**
	* Get the selected items
	*/
	getSelection: function() {

		return this.controller.state().props;

	},

	/**
	* Clears all selected items
	*/
	clearSelection: function() {

		// Get selection
		this.selection = this.getSelection();

		// Remove from UI
		jQuery( 'li.attachment.envira-videos-attachment' ).remove();

		// Clear the selected models
		this.selection.reset();

	}

} );

/**
* View: Media Frame
*/
var envira_videos_post_frame = wp.media.view.MediaFrame.Post;
wp.media.view.MediaFrame.Post = envira_videos_post_frame.extend({

	/**
	* Init
	*/
	initialize: function() {

		envira_videos_post_frame.prototype.initialize.apply( this, arguments );

		// Add the Video Importer to the modal's left hand menu
		this.states.add( [
            new wp.media.controller.EnviraVideos( {
                id:         'envira-videos',
                content: 	'envira-videos-content',
                toolbar: 	'envira-videos-toolbar',
                menu:       'default',
                title:      wp.media.view.l10n.enviraVideosTitle,
				priority:   200,
				type: 		'link'
            } )
        ] );

        // Main UI (where attachments are displayed)
        this.on( 'content:render:envira-videos-content', this.renderEnviraVideosContent, this );

        // Bottom Toolbar (where the selected items and button are displayed)
        this.on( 'toolbar:create:envira-videos-toolbar', this.createEnviraVideosToolbar, this );

	},

	/**
	* Main UI
	*/
	renderEnviraVideosContent: function() {

        this.content.set( new wp.media.view.EnviraVideos( {
        	controller: this,
        	model: this.state().props,
        	className: 'attachments-browser envira-gallery envira-videos'
        } ) );

	},

	/**
	* Bottom Toolbar
	*/
	createEnviraVideosToolbar: function( toolbar ) {

		toolbar.view = new wp.media.view.Toolbar.EnviraVideos({
			controller: this
		} );

	}

});

/**
* Controller
*/
wp.media.controller.EnviraVideos = wp.media.controller.State.extend( {

	/**
	* Init
	*/
    initialize: function( options ) {

        this.props = new Backbone.Collection();

    },

	/**
	* Called when the Insert button is clicked
	*/
	enviraVideosInsert: function() {

		// Get selected items
		var frame 		= this.frame.content.get(),
			videos	  	= [],
			validated 	= true;

		// Get toolbar button
		this.button = this.frame.toolbar.get().get( 'envira_videos_insert' );

		// Disable button and change label
		this.button.model.set( 'text', wp.media.view.l10n.inserting );
		this.button.model.set( 'disabled', true );

		// Tell wp.media we're loading items
		frame.trigger( 'loading' );

		// Build an array of items, validating them along the way
		frame.getSelection().each( function( model ) {
			// Validate the model to ensure it has the required fields
			if ( model.get( 'title' ) == '' ) {
				// Cancel operation
				validated = false;
			}
			if ( model.get( 'url' ) == '' ) {
				// Cancel operation
				validated = false;
			}

			// If a self hosted video, we need an image placeholder
			if ( model.get( 'hosted_video' ) ) {
				if ( model.get( 'image' ) == '' ) {
					// Cancel
					validated = false;
				}
			}

			videos.push( model.toJSON() ); // toJSON will take the model's keys and build an array for us
		}, this );

		// If inputs failed validation, stop
		if ( ! validated ) {
			// Tell wp.media we're finished, but there was an error
			frame.trigger( 'loaded loaded:error', wp.media.view.l10n.enviraVideosValidationError );

			// Revert the button back to its original state
			this.button.model.set( 'text', wp.media.view.l10n.insertIntoPost );
			this.button.model.set( 'disabled', false );

			// Exit
			return false;
		}

		// Make an AJAX request to import these items into the gallery
		wp.media.ajax( 'envira_videos_insert_videos', {
			context: this,
			data: {
				nonce: 	 envira_videos_media_view.nonce,
				post_id: envira_videos_media_view.post_id,
				videos:  videos
			},
			success: function( response ) {

				// Set the image grid to the HTML we received
                jQuery( '#envira-gallery-output' ).html( response );

                // Tell wp.media we've finished
				frame.trigger( 'loaded loaded:success' );

				// Revert the button back to its original state
				this.button.model.set( 'text', wp.media.view.l10n.insertIntoPost );
				this.button.model.set( 'disabled', false );

				// Reset the selection
				frame.clearSelection();

				//Add insert trigger
				jQuery( document ).trigger( 'enviraInsert' );

				// Close the modal
				this.frame.close();
			},
			error: function( error_message ) {
				// Revert the button back to its original state
				this.button.model.set( 'text', wp.media.view.l10n.insertIntoPost );
				this.button.model.set( 'disabled', false );

				// Tell wp.media we've finished, but there was an error
				frame.trigger( 'loaded loaded:error', error_message );
			}
		} );

	}

} );