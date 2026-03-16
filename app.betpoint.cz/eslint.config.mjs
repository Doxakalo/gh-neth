import globals from "globals";
import eslint from "@eslint/js";
import tseslint from "typescript-eslint";
import pluginReact from "eslint-plugin-react";

/** @type {import('eslint').Linter.Config[]} */
export default [
	eslint.configs.recommended,

	// TypeScript files: type-aware linting
	...tseslint.configs.recommended,
	{
		files: ["**/*.ts", "**/*.tsx"],
		languageOptions: {
			parser: tseslint.parser,
			parserOptions: {
				project: "./tsconfig.json",
			},
			globals: globals.browser,
		},
		plugins: {
			react: pluginReact,
			typescript: tseslint,
		},
		settings: {
			react: { version: "detect" },
		},
		rules: {
			"react/prop-types": "off",
		},
	},

	// JavaScript files: no type-aware linting
	{
		files: ["**/*.js", "**/*.jsx"],
		languageOptions: {
			parser: tseslint.parser,
			// DO NOT set parserOptions.project here!
			globals: globals.browser,
		},
		plugins: {
			react: pluginReact,
			typescript: tseslint,
		},
		settings: {
			react: { version: "detect" },
		},
		rules: {
			"react/prop-types": "off",
		},
		// Optionally, you can extend disableTypeChecked for JS files:
		// extends: [tseslint.configs.disableTypeChecked],
	},
];
