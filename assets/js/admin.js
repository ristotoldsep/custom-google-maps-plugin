jQuery(document).ready(function($) {
    var frame; // Declare frame outside event handler for reuse

    // Upload marker icon
    $('#upload_marker_icon_button').on('click', function(e) {
        e.preventDefault();

        // If the media frame already exists, reopen it.
        if (frame) {
            frame.open();
            return;
        }

        // Create the media frame.
        frame = wp.media({
            title: 'Upload Marker Icon',
            button: {
                text: 'Use this icon',
            },
            multiple: false // Allow only a single image selection
        });

        // When an image is selected in the media frame...
        frame.on('select', function() {
            var attachment = frame.state().get('selection').first().toJSON();
            var image_url = attachment.url;

            // Set the image URL to the hidden input field and update the preview
            $('#my_acf_google_maps_marker_icon').val(image_url);
            $('#marker-icon-preview').html('<img src="'+ image_url +'" /><br><button class="button button-secondary" id="remove_marker_icon_button">Remove Marker Icon</button>');
        });

        // Open the media frame.
        frame.open();
    });

    // Remove marker icon
    $(document).on('click', '#remove_marker_icon_button', function(e) {
        e.preventDefault();
        $('#my_acf_google_maps_marker_icon').val(''); // Clear the value
        $('#marker-icon-preview').html(''); // Remove the preview
    });
});
