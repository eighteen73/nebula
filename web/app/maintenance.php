<?php
http_response_code( 503 );
header( 'Retry-After: 30' );
header( 'Content-Type: text/html; charset=utf-8' );
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Site Maintenance</title>
	<style>
		:root {
			--bg-color: oklch(96.7% 0.001 286.375);
			--card-bg: oklch(99.5% 0 0);
			--text-main: oklch(37% 0.013 285.805);
			--text-muted: oklch(55.2% 0.016 285.938);
			--accent: oklch(70.5% 0.015 286.067);
			--shadow: 0 4px 6px -1px oklch(21% 0.006 285.885 / 0.1), 0 2px 4px -1px oklch(21% 0.006 285.885 / 0.06);
		}

		@media (prefers-color-scheme: dark) {
			:root {
				--bg-color: oklch(27.4% 0.006 286.033);
				--card-bg: oklch(21% 0.006 285.885);
				--text-main: oklch(96.7% 0.001 286.375);
				--text-muted: oklch(87.1% 0.006 286.286);
				--accent: oklch(87.1% 0.006 286.286);
				--shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.5);
			}
		}

		* {
			box-sizing: border-box;
			margin: 0;
			padding: 0;
		}

		body {
			font-family: system-ui, sans-serif;
			background-color: var(--bg-color);
			color: var(--text-main);
			display: flex;
			align-items: center;
			justify-content: center;
			min-height: 100vh;
			padding: 2rem;
			line-height: 1.5;
		}

		.container {
			background-color: var(--card-bg);
			padding: 3rem 2rem;
			border-radius: .8rem;
			box-shadow: var(--shadow);
			max-width: 28rem;
			width: 100%;
			text-align: center;
			animation: fadeIn 0.6s ease-out;
		}

		svg {
			width: 4rem;
			height: 4rem;
			color: var(--accent);
			margin-bottom: 1.5rem;
			animation: pulse 3s infinite ease-in-out;
		}

		h1 {
			font-size: 1.5rem;
			font-weight: normal;
			margin-bottom: 0.75rem;
			letter-spacing: -0.025em;
		}

		p {
			color: var(--text-muted);
			margin-bottom: 1.5rem;
		}

		@keyframes fadeIn {
			from {
				opacity: 0;
				transform: translateY(0.5rem);
			}
			to {
				opacity: 1;
				transform: translateY(0);
			}
		}

		@keyframes pulse {
			0%, 100% {
				opacity: 1;
			}
			50% {
				opacity: 0.7;
			}
		}
	</style>
</head>
<body>
<div class="container">
	<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 256 256">
		<rect width="256" height="256" fill="none"/>
		<path d="M216,136a16,16,0,0,1-16,16H152a8,8,0,0,0-7.92,9.13L152,208a24,24,0,0,1-48,0l7.92-46.87A8,8,0,0,0,104,152H56a16,16,0,0,1-16-16V112H216Z"
			  opacity="0.2"/>
		<line x1="40" y1="112" x2="216" y2="112" fill="none" stroke="currentColor" stroke-linecap="round"
			  stroke-linejoin="round" stroke-width="16"/>
		<path d="M56,152a16,16,0,0,1-16-16V64A32,32,0,0,1,72,32H216V136a16,16,0,0,1-16,16H152a8,8,0,0,0-7.92,9.13L152,208a24,24,0,0,1-48,0l7.92-46.87A8,8,0,0,0,104,152Z"
			  fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="16"/>
		<line x1="184" y1="32" x2="184" y2="80" fill="none" stroke="currentColor" stroke-linecap="round"
			  stroke-linejoin="round" stroke-width="16"/>
	</svg>
	<h1>We're Performing Maintenance</h1>
	<p>Our website is currently undergoing scheduled maintenance and we expect to be back online shortly. Please check
		again in a few minutes.</p>
</div>
</body>
</html>
