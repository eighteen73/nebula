#!/usr/bin/env node

/**
 * @file
 * Creates a new block in the src directory.
 *
 * This script uses the WordPress Create Block tool to generate a new block.
 * This is only a workaround because of the following issue:
 * https://github.com/WordPress/gutenberg/issues/56059
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/packages/packages-create-block/
 */

const { execSync } = require('child_process');
const path = require('path');

try {
	// Run create-block in interactive mode

	/* eslint-disable no-console */
	console.log('Creating new block...');
	console.log('Please follow the prompts to configure your block.');
	console.log(
		'Note: The block will be moved to the src directory after creation.\n'
	);
	/* eslint-enable no-console */

	execSync(
		'npx @wordpress/create-block@latest --no-plugin --template @eighteen73/nebula-create-block-template',
		{
			stdio: 'inherit',
		}
	);

	// After creation, we need to find the newly created block directory
	// It will be the most recently created directory in the current folder
	const blockDir = execSync('ls -t | head -n1').toString().trim();

	// Move the block to the src directory
	const targetDir = path.join('src', blockDir);

	/* eslint-disable no-console */
	console.log(`\nMoving block to ${targetDir}...`);
	execSync(`mv ${blockDir} ${targetDir}`, { stdio: 'inherit' });

	console.log(`\nBlock created successfully in ${targetDir}!`);
	/* eslint-enable no-console */
} catch (error) {
	/* eslint-disable no-console */
	console.error('Error creating block:', error.message);
	process.exit(1);
	/* eslint-enable no-console */
}
