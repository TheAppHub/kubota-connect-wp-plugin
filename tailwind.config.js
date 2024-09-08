/** @type {import('tailwindcss').Config} */
const withMT = require("@material-tailwind/html/utils/withMT");

module.exports = withMT({
	content: [
		"./admin/js/**/*.js",
		"./admin/css/**/*.css",
		"./admin/*.php",
		"./public/js/**/*.js",
		"./public/css/**/*.css",
		"./public/*.php",
		"./includes/class-kubota-connect.php",
		"./includes/class-kubota-connect-products.php",
		"./includes/class-kubota-connect-highlights.php",
		"./includes/class-kubota-connect-finance-offers.php",
	],
	theme: {
		extend: {
			colors: {
				orange: "#E4551C",
				"kubota-orange": "#E4551C",
			},
			keyframes: {
				"fade-in": {
					"0%": {
						opacity: "0%",
						transform: "translateY(-50%)",
					},
					"100%": {
						opacity: "100%",
						transform: "translateY(0%)",
					},
				},
			},
			animation: {
				"fade-in": "fade-in 0.7s ease-in-out",
			},
		},
	},
	plugins: [],
});

// Run this command to generate the css file for admin area
// npx tailwindcss -i ./admin/css/input.css -o ./admin/css/kubota-connect-admin.css --watch

// Run this command to generate the css file for public area
// npx tailwindcss -i ./public/css/input.css -o ./public/css/kubota-connect-public.css --watch
