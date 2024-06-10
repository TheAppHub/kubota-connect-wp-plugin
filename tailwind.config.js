/** @type {import('tailwindcss').Config} */
const withMT = require("@material-tailwind/html/utils/withMT");

module.exports = withMT({
	content: [
		"./admin/js/**/*.js",
		"./admin/partials/*.php",
		"./public/js/**/*.js",
		"./public/partials/*.php",
	],
	theme: {
		extend: {},
	},
	plugins: [],
});

// Run this command to generate the css file for admin area
// npx tailwindcss -i ./admin/css/input.css -o ./admin/css/kubota-connect-admin.css --watch

// Run this command to generate the css file for public area
// npx tailwindcss -i ./public/css/input.css -o ./public/css/kubota-connect-public.css --watch
