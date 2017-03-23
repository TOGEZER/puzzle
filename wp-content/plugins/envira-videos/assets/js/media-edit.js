/**
* View
*/
var EnviraVideosView = Backbone.View.extend( {

	/**
    * The Tag Name and Tag's Class(es)
    */
    tagName:    'div',
    className:  'envira-video',

    /**
    * Template
    * - The template to load inside the above tagName element
    */
    template:   wp.template( 'envira-meta-editor-video' ),

    /**
    * Initialize
    */
    initialize: function( args ) {

        this.model = args.model;

    },

    /**
    * Render
    */
    render: function() {
	
        // Set the template HTML
		this.$el.html( this.template( this.model.attributes ) );

	    return this;
	
	}
    
} );

// Add the view to the EnviraGalleryChildViews, so that it's loaded in the modal
EnviraGalleryChildViews.push( EnviraVideosView );