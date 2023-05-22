<script lang="ts">
	import { onMount } from 'svelte';
	import '../styles/global.scss';
	import '../styles/fonts.scss';
	import Cursor from '../components/atoms/Cursor.svelte';
	import { burst } from '../store/cursor';

	let playClickSound: (() => void) | undefined;

	onMount(() => {
		const audioCtx = new window.AudioContext();
		const gainNode = audioCtx.createGain();
		gainNode.gain.value = 1;

		const request = new XMLHttpRequest();
		request.open('GET', 'sounds/click.mp3', true);
		request.responseType = 'arraybuffer';
		request.onload = function () {
			audioCtx.decodeAudioData(request.response, (buffer) => {
				playClickSound = () => {
					const source = audioCtx.createBufferSource();

					// Set playback rate between 0.6 and 1.3.
					source.playbackRate.value = Math.random() * 0.7 + 0.6;
					source.buffer = buffer;

					// Adjust volume based on cursor burst.
					gainNode.gain.exponentialRampToValueAtTime(
						Math.max($burst, 0.2),
						audioCtx.currentTime + 0.1
					);

					source.connect(gainNode);

					// Connect to output.
					gainNode.connect(audioCtx.destination);

					// Play sound.
					source.start(0);
				};
			});
		};
		request.send();
	});
</script>

<svelte:head>
	<meta charset="UTF-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<meta name="og:title" content="osumatrix.me" />
	<meta content="/osumatrix.webp" property="og:image" />
	<meta property="og:description" content="My personal website" />
	<meta name="twitter:image" itemprop="image" content="/osumatrix.webp" />
	<meta name="twitter:card" content="summary" />
	<meta name="theme-color" content="#000" />
	<title>oSumAtrIX</title>
</svelte:head>

<svelte:window on:mousedown={playClickSound} on:contextmenu={(e) => e.preventDefault()} />

<Cursor />
<slot />
