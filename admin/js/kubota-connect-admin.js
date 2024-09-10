jQuery(document).ready(function ($) {
	function updateImagePreview(containerId) {
		var $container = $(containerId);
		var imageUrl = $container.find('.cf-text input[type="text"]:eq(1)').val(); // Get value from the second input field
		if (imageUrl) {
			$container
				.find(".cf-html__content")
				.html('<img src="' + imageUrl + '" alt="Image Preview" />');
		}

		// Hide all divs within the container with the classes cf-field cf-text
		$container.find(".cf-field.cf-text").hide();
	}

	// List of container IDs to apply the script to
	var containerIds = [
		"#carbon_fields_container_hero_image",
		"#carbon_fields_container_image",
		"#carbon_fields_container_studio_image",
		"#carbon_fields_container_hero_image1",
		"#carbon_fields_container_image1",
		"#carbon_fields_container_image2",
		".container-carbon_fields_container_image1",
	];

	// Loop through each container and apply the update
	containerIds.forEach(function (containerId) {
		updateImagePreview(containerId);
	});
});
