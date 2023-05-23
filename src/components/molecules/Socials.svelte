<script lang="ts">
	import { faLink, faEnvelope, faChessKnight, faGamepad } from '@fortawesome/free-solid-svg-icons';
	import {
		faDiscord,
		faGithub,
		faReddit,
		faTelegramPlane,
		faTwitter,
		faYoutube
	} from '@fortawesome/free-brands-svg-icons';
	import ParagraphHeader from '../atoms/ParagraphHeader.svelte';
	import Social from '../atoms/Social.svelte';
</script>

<section>
	<ParagraphHeader text="Socials" icon={faLink} />
	<ul>
		<Social
			faIcon={faDiscord}
			link="https://discord.com/users/737323631117598811"
			anchor="discord"
			overrideText="oSumAtrIX#5767"
		/>
		<Social faIcon={faReddit} link="https://reddit.com/u/oSumAtrIX" anchor="reddit" />
		<Social faIcon={faGithub} link="https://github.com/oSumAtrIX" anchor="github" />
		<Social faIcon={faTelegramPlane} link="https://t.me/oSumAtrIX" anchor="telegram" />
		<Social
			faIcon={faYoutube}
			link="https://www.youtube.com/channel/UCk9pnU2BGmIafksQPvTJfuA"
			anchor="youtube"
		/>
		<Social faIcon={faTwitter} link="https://twitter.com/oSumAtrIX" anchor="twitter" />
		<Social faIcon={faGamepad} link="https://osu.ppy.sh/u/oSumAtrIX" anchor="osu" />
		<Social faIcon={faChessKnight} link="https://lichess.org/@/oSumAtrIX" anchor="chess" />
		<Social
			faIcon={faEnvelope}
			link="mailto:mail@osumatrix.me"
			anchor="mail"
			overrideText="mail@osumatrix.me"
		/>
	</ul>
</section>

<style lang="scss">
	ul {
		list-style: none;
	}
</style>
