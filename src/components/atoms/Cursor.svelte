<script lang="ts">
	import { burst, scale } from '../../store/cursor';

	let burstInterval: ReturnType<typeof setInterval>;
	let randomInterval: ReturnType<typeof setInterval>;

	let mouseX: number;
	let mouseY: number;

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
</script>

<svelte:window
	on:mousemove={(e) => {
		mouseX = e.clientX;
		mouseY = e.clientY;
	}}
	on:mousedown={() => {
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

<div
	class="pointer"
	style="
	transform:	scaleX({1 + $burst + randomScaleX + $scale})
				scaleY({1 + $burst + randomScaleY + $scale})
				translate({randomPositionXOffset}px, {randomPositionYOffset}px);
	left: {mouseX - 20}px; top: {mouseY - 20}px;
	box-shadow: 0px 0px {$burst * 50}px var(--white);
	"
/>

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
		z-index: 1;
		left: 50%;
		top: 50%;

		@media (pointer: coarse) {
			display: none;
		}
	}
</style>
