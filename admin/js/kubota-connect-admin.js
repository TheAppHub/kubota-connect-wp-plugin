(function ($) {
	"use strict";

	$(document).ready(function () {
		// setup our wp ajax URL
		var wpAjaxUrl =
			document.location.protocol +
			"//" +
			document.location.host +
			"/wp-admin/admin-ajax.php";

		$("#kc-connect").click(function (event) {
			event.preventDefault();

			$("#kc-connect").addClass("hidden");
			$("#kc-connect-processing").removeClass("hidden");

			const token = $("#kc-token").val();

			$.ajax({
				method: "POST",
				url: wpAjaxUrl,
				data: {
					action: "kubota_connect_test_connection",
					data: {
						token: token,
					},
				},
				complete: function (response) {
					console.log(response["responseJSON"]);
					var msg = response["responseJSON"]["message"];
					var statusCode = response["responseJSON"]["statusCode"];

					if (statusCode == 200) {
						$("#successModal").removeClass("hidden");
						$("#kc-success-msg").text(msg);
					} else {
						$("#errorModal").removeClass("hidden");
						$("#kc-error-msg").text(msg);
					}

					$("#kc-connect-processing").addClass("hidden");
					$("#kc-connect").removeClass("hidden");
				},
			});
		});

		$(".kc-close-error").click(function (event) {
			event.preventDefault();
			$("#errorModal").addClass("hidden");
			location.reload();
		});

		$(".kc-close-success").click(function (event) {
			event.preventDefault();
			$("#successModal").addClass("hidden");
			location.reload();
		});

		$("#kc-sync").click(function (event) {
			event.preventDefault();

			$("#kc-sync").addClass("hidden");
			$("#kc-sync-processing").removeClass("hidden");

			$.ajax({
				method: "POST",
				url: wpAjaxUrl,
				data: {
					action: "kubota_connect_sync_all_data",
				},
				complete: function (response) {
					var msg = response["responseJSON"]["message"];
					var statusCode = response["responseJSON"]["statusCode"];
					console.log(response["responseJSON"]);

					if (statusCode == 200) {
						$("#successModal").removeClass("hidden");
						$("#kc-success-msg").html(msg);
					} else {
						$("#errorModal").removeClass("hidden");
						$("#kc-error-msg").html(msg);
					}

					$("#kc-sync-processing").addClass("hidden");
					$("#kc-sync").removeClass("hidden");
				},
			});
		});
	});

	$(document).ready(function ($) {
		// stop admin menu from collapsing when Categories is chosen
		if (
			$('body[class*=" post-type-kubota-products taxonomy-category"]').length
		) {
			$("#toplevel_page_kubota-connect")
				.removeClass("wp-not-current-submenu")
				.addClass("wp-has-current-submenu")
				.addClass("wp-menu-open");

			$('a:contains("Categories")').parent().addClass("current");
		}
	});
})(jQuery);
