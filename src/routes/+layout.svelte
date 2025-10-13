<script lang="ts">
	import { onMount } from 'svelte';
	import '../styles/global.scss';
	import '../styles/fonts.scss';
	import { burst, scale } from '../store/cursor';

	let burstInterval: ReturnType<typeof setInterval>;
	let randomInterval: ReturnType<typeof setInterval>;

	let mouseX: number = -20;
	let mouseY: number = -20;

	let randomScaleX = 0;
	let randomScaleY = 0;
	let randomPositionXOffset = 0;
	let randomPositionYOffset = 0;

	function reduce(number: number, step = 1) {
		if (number > 0) number -= step;
		else if (number < 0) number += step;

		const doubleStep = step * 2;

		if (number !== 0 && number < doubleStep && number > -doubleStep) number = 0;
		return number;
	}

	let playClickSound: () => void = () => {};

	onMount(() => {
		if (window.innerWidth <= 500) return;

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

<div
	class="pointer"
	style="
	transform:	scaleX({1 + $burst + randomScaleX + $scale})
				scaleY({1 + $burst + randomScaleY + $scale})
				translate({randomPositionXOffset}px, {randomPositionYOffset}px);
	left: {mouseX - 20}px; top: {mouseY - 20}px;
	box-shadow: 0px 0px {$burst * 50}px var(--white);
	"
></div>

<svelte:head>
	<meta charset="UTF-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<meta name="og:title" content="oSumAtrIX" />
	<meta content="/osumatrix.webp" property="og:image" />
	<meta property="og:description" content="My personal website" />
	<meta name="twitter:image" itemprop="image" content="/osumatrix.webp" />
	<meta name="twitter:card" content="summary" />
	<meta name="theme-color" content="#000" />
	<title>oSumAtrIX</title>
</svelte:head>

<svelte:window
	on:contextmenu={(e) => e.preventDefault()}
	on:mousemove={(e) => {
		mouseX = e.clientX;
		mouseY = e.clientY;
	}}
	on:mousedown={() => {
		playClickSound();

		if ($burst < 1) burst.increment();

		randomPositionXOffset = Math.random() * 10 - 5;
		randomPositionYOffset = Math.random() * 10 - 5;
		randomScaleY = Math.random() * 0.8;
		randomScaleX = Math.random() * 0.8;

		clearInterval(randomInterval);
		randomInterval = setInterval(() => {
			randomPositionXOffset = reduce(randomPositionXOffset);
			randomPositionYOffset = reduce(randomPositionYOffset);
			randomScaleY = reduce(randomScaleY, 0.1);
			randomScaleX = reduce(randomScaleX, 0.1);

			if (
				randomScaleX === 0 &&
				randomScaleY === 0 &&
				randomPositionXOffset === 0 &&
				randomPositionYOffset === 0
			)
				clearInterval(randomInterval);
		}, 10);

		clearInterval(burstInterval);
		burstInterval = setInterval(() => {
			burst.decrement();
			if ($burst < 0) {
				clearInterval(burstInterval);
				burst.reset();
			}
		}, 100);
	}}
/>

<slot />

<style lang="scss">
	.pointer {
		pointer-events: none;
		height: 40px;
		width: 40px;
		background-color: rgb(255, 255, 255);
		transition: 0.3s transform cubic-bezier(0, 0.68, 0.43, 1.02);
		mix-blend-mode: difference;
		border-radius: 100%;
		position: fixed;
		z-index: 2;
		left: 50%;
		top: 50%;

		@media (pointer: coarse) {
			display: none;
		}
	}
</style>
