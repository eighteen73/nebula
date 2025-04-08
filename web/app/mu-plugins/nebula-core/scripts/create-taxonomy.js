#!/usr/bin/env node

/**
 * @file
 * Creates a new taxonomy for one or more post types.
 *
 * This script is a wrapper around the wp nebula create taxonomy command
 * to handle the post type parameter without requiring additional -- between arguments.
 *
 * Usage:
 * npm run create:taxonomy taxonomy_name post_type[,post_type2,...]
 */

const { execSync } = require('child_process');

try {
	// Get command line arguments
	const args = process.argv.slice(2);

	if (args.length < 2) {
		/* eslint-disable no-console */
		console.error('Error: Taxonomy name and post type(s) are required');
		console.log(
			'Usage: npm run create:taxonomy taxonomy_name post_type[,post_type2,...]'
		);
		/* eslint-enable no-console */
		process.exit(1);
	}

	// Extract taxonomy name and post types
	const taxonomyName = args[0];
	const postTypes = args[1];

	// Build the command
	const command = `wp nebula create taxonomy ${taxonomyName} ${postTypes}`;

	// Execute the command
	/* eslint-disable no-console */
	console.log(`Executing: ${command}`);
	/* eslint-enable no-console */
	execSync(command, { stdio: 'inherit' });

	/* eslint-disable no-console */
	console.log('\nTaxonomy created successfully!');
	/* eslint-enable no-console */
} catch (error) {
	/* eslint-disable no-console */
	console.error('Error creating taxonomy:', error.message);
	/* eslint-enable no-console */
	process.exit(1);
}
