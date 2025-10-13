<script lang="ts">
	import Fa from 'svelte-fa';
	import { scale } from '../store/cursor';
	import { faKey, faUserAlt } from '@fortawesome/free-solid-svg-icons';
	import { faEnvelope, faChessKnight, faGamepad } from '@fortawesome/free-solid-svg-icons';
	import {
		faDiscord,
		faGithub,
		faReddit,
		faTelegramPlane,
		faTwitter,
		faYoutube
	} from '@fortawesome/free-brands-svg-icons';
	import Social from '../components/Social.svelte';
	import Link from '../components/Link.svelte';

	let time = new Date().toLocaleTimeString('de-DE', {
		timeZone: 'Europe/Berlin',
		hour: '2-digit',
		minute: '2-digit'
	});

	let birthday = new Date(2002, 2, 15);
	let age = Math.floor((Date.now() - birthday.getTime()) / 3.15576e10);
</script>

<main>
	<!-- svelte-ignore a11y_mouse_events_have_key_events -->
	<div id="rotating-image">
		<img
			src="osumatrix.webp"
			alt="oSumAtrIX"
			on:mouseover={() => scale.set(1.2)}
			on:mouseleave={() => scale.set(0)}
		/>
	</div>
	<div id="rotating-card">
		<div id="card">
			<h1>oSumAtrIX</h1>
			<p>
				{age} years. CS at
				<Link href="https://uni-augsburg.de/">University of Augsburg</Link>.
				<br />
				I do photo, video, music and speak <Link href="https://github.com/osumatrix">computer</Link>
				fluently.
				<br />
				It is <b>{time}</b> for me in Germany.
			</p>
			<ul>
				<Social faIcon={faEnvelope} link="mailto:mail@osumatrix.me" />
				<Social faIcon={faKey} link="https://github.com/oSumAtrIX.gpg" />
				<Social faIcon={faDiscord} link="https://discord.com/users/7373y631117598811" />
				<Social faIcon={faGithub} link="https://github.com/oSumAtrIX" />
				<Social faIcon={faTelegramPlane} link="https://t.me/oSumAtrIX" />
				<Social faIcon={faReddit} link="https://reddit.com/u/oSumAtrIX" />
				<Social
					faIcon={faYoutube}
					link="https://www.youtube.com/channel/UCk9pnU2BGmIafksQPvTJfuA"
				/>
				<Social faIcon={faTwitter} link="https://twitter.com/oSumAtrIX" />
				<Social faIcon={faGamepad} link="https://osu.ppy.sh/u/oSumAtrIX" />
				<Social faIcon={faChessKnight} link="https://lichess.org/@/oSumAtrIX" />
			</ul>
		</div>
	</div>
</main>

<style lang="scss">
	h1,
	p {
		margin: 0;
	}

	#card {
		animation: float 2.9s ease-in-out infinite;
		animation-delay: 3s;
		transition: all 0.8s;
	}

	#rotating-image {
		z-index: 1;
	}
	img {
		cursor: pointer;
		width: 400px;
		border-radius: 20px;
		transition: all 0.8s;

		&:hover {
			filter: brightness(1.3);
		}

		animation: float 3s ease-in-out infinite;

		position: fixed;
		margin-left: -80px;
		transform: rotate(4deg);
		margin-top: -50px;
	}

	@keyframes float {
		0% {
			transform: translatey(0px);
		}
		50% {
			transform: translatey(-10px);
		}
		100% {
			transform: translatey(0px);
		}
	}

	#rotating-card {
		transform: rotate(-4deg) skewX(5deg) skewY(-5deg);
		transition: all 0.8s;
	}

	#rotating-image {
		transform: rotate(4deg);
		transition: all 0.8s;
	}

	#card {
		opacity: 0.4;
	}
	main {
		transition: all 0.8s;
		display: flex;
		justify-content: right;
		width: 400px;

		&:hover {
			margin-right: 350px;
			#card {
				opacity: 1;
				margin-right: -330px;
			}
			#rotating-card {
				transform: rotate(6deg) scale(1.1);
			}

			img {
				margin-left: -400px;
			}
			#rotating-image {
				transform: rotate(-6deg) translateY(-50px) scale(0.9);
			}
		}
	}

	ul {
		gap: 1rem;
		flex-wrap: wrap;
		margin: 0;
		font-size: 2rem;
		display: flex;
		list-style: none;
	}

	#card {
		gap: 1rem;
		display: flex;
		justify-content: center;
		flex-direction: row;
		border-radius: 15px;
		padding: 2rem;
		background: #131313;
		min-width: 300px;
		max-width: 300px;
		flex-direction: column;

		// Mobile
		@media (max-width: 505px) {
		}
	}

	@media (max-width: 505px) {
		img {
			margin: 0 !important;
			position: relative;
			transform: none !important;
			width: 100%;
		}

		#card {
			opacity: 1;
			animation: none;
			min-width: none;
			max-width: none;
			background: none;
			margin: 0 !important;
		}

		main {
			padding: 1rem;
			align-items: center;
			flex-direction: column;
			border-radius: 0;
			width: initial !important;
			min-width: none;
			max-width: none;
			margin: 0 !important;
			transform: none !important;
			justify-content: flex-start;
		}

		#rotating-card,
		#rotating-image {
			transform: rotate(0deg) skewX(0deg) skewY(0deg) !important;
		}
	}
</style>
