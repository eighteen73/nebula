#!/usr/bin/env node

/**
 * @file
 * Creates a new post type with optional taxonomy.
 *
 * This script is a wrapper around the wp nebula create post-type command
 * to handle the optional taxonomy parameter without requiring additional -- between arguments.
 *
 * Usage:
 * npm run create:post-type post_type_name [taxonomy_name]
 */

const { execSync } = require('child_process');

try {
	// Get command line arguments
	const args = process.argv.slice(2);

	if (args.length === 0) {
		/* eslint-disable no-console */
		console.error('Error: Post type name is required');
		console.log(
			'Usage: npm run create:post-type post_type_name [taxonomy_name]'
		);
		/* eslint-enable no-console */
		process.exit(1);
	}

	// Extract post type name and taxonomy
	const postTypeName = args[0];
	const taxonomyName = args[1] || null;

	// Build the command
	let command = `wp nebula create post-type ${postTypeName}`;

	// Add taxonomy if provided
	if (taxonomyName) {
		command += ` --taxonomy=${taxonomyName}`;
	}

	// Execute the command
	/* eslint-disable no-console */
	console.log(`Executing: ${command}`);
	/* eslint-enable no-console */
	execSync(command, { stdio: 'inherit' });

	/* eslint-disable no-console */
	console.log('\nPost type created successfully!');
	/* eslint-enable no-console */
} catch (error) {
	/* eslint-disable no-console */
	console.error('Error creating post type:', error.message);
	/* eslint-enable no-console */
	process.exit(1);
}
