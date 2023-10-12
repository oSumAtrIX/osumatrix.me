<script lang="ts">
	import { scale } from '../../store/cursor';
</script>

<!-- svelte-ignore a11y-mouse-events-have-key-events -->
<!-- svelte-ignore a11y-click-events-have-key-events -->
<img
	src="osumatrix.webp"
	alt="oSumAtrIX"
	on:mouseover={() => scale.set(1.2)}
	on:mouseleave={() => scale.set(0)}
/>

<style lang="scss">
	img {
		cursor: pointer;
		width: 450px;
		border-radius: 20px;
		animation-name: hovering;
		animation-timing-function: ease-in-out;
		animation-duration: 2.5s;
		animation-iteration-count: infinite;
		transition: 0.3s filter ease;
		&:hover {
			filter: brightness(1.3);
		}

		@media (max-width: 505px) {
			width: 100%;
		}
	}

	@keyframes hovering {
		0% {
			transform: translateY(6px);
		}

		50% {
			transform: translateY(-6px);
		}
		100% {
			transform: translateY(6px);
		}
	}
</style>
