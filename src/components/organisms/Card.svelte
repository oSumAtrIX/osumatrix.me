<script lang="ts">
	import Image from '../atoms/Image.svelte';
	import Social from '../atoms/Social.svelte';
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
	#card {
		border-radius: 15px;
		padding: 25px;
		width: 1450px;
		background: #131313eb;
	}

	#image {
		transition: margin 1s ease;
		margin-left: -100px;
		margin-top: -100px;
		margin-right: 20px;
	}

	@media (max-width: 1700px) {
		#card {
			display: flex;
			flex-direction: column;
			width: min-content;
		}

		#image {
			margin: 0;
		}

		#molecules {
			flex-direction: column;
		}
	}

	@media (max-width: 500px) {
		#card {
			border-radius: 0;
			width: initial;
		}
	}
</style>
