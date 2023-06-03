<script lang="ts">
	import Image from '../atoms/Image.svelte';
	import Title from '../atoms/Title.svelte';
	import About from '../molecules/About.svelte';
	import Socials from '../molecules/Socials.svelte';
</script>

<div id="card">
	<div id="image">
		<Image />
	</div>
	<div id="card-container">
		<Title />
		<div id="molecules">
			<Socials />
			<About />
		</div>
	</div>
</div>

<style lang="scss">
	#card,
	#molecules {
		gap: 1rem;
		display: flex;
		flex-direction: row;
	}

	#card-container {
		display: flex;
		flex-direction: column;
		justify-content: space-evenly;
	}

	#image {
		transition: margin 1s ease;
		margin-left: -100px;
		margin-top: -100px;
		margin-right: 20px;
	}

	#card {
		border-radius: 15px;
		padding: 25px;
		max-width: 1450px;
		background: #131313eb;

		// Full HD
		@media (max-width: 1800px) {
			max-width: 1300px;
		}

		// Mobile
		@media (max-width: 505px) {
			border-radius: 0;
			width: initial !important;
		}

		// Mobile
		@media (min-width: 506px) {
			max-height: 85vh; // Prevents overflow when screen height is too small.
		}
	}

	// Less then Full HD
	@media (max-width: 1550px) {
		#image {
			margin: 0;
		}

		#card {
			overflow: auto; // Allows scrolling when screen height is too small.
			width: min-content;
		}

		#molecules,
		#card {
			flex-direction: column;
		}
	}
</style>
