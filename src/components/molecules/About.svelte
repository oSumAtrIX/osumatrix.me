<script lang="ts">
	import { faAddressCard } from '@fortawesome/free-solid-svg-icons';
	import SectionHeader from '../atoms/SectionHeader.svelte';
</script>

<section>
	<SectionHeader text="About me" icon={faAddressCard} />
	<p>
		21-year-old hyper-enthusiastic full-time blockhead, who had the great idea to study computer
		science at the <a target="_blank" href="https://uni-augsburg.de/">University of Augsburg</a>.
		<br />
		My interests and expertise expand from graphics-, motion- and sound design in dedicated software
		like Adobe Suite and Image-Line FL Studio up to programming in lots of different languages like
		<b>C#</b>,
		<b>Rust</b>, <b>Kotlin</b>, <b>Java</b>, <b>PHP</b>, <b>HTML</b>, <b>JavaScript</b>, <b>CSS</b>,
		<b>MySQL</b>, <b>Objective-C</b>, <b>C</b>, <b>Python</b> and <b>Visual Basic</b>, with the
		first being my most and last being my least used languages. I mostly use my brief time
		productively, but if I don't, then I usually play <a href="#chess">chess</a>,
		<a href="#osu">osu!</a>, or Krunker. As a reason of peer pressure, I generally hang around on
		<a href="#discord">Discord</a> but maintain daily activity on my email inbox and a couple of other
		forums.
	</p>
</section>

<style>
	a {
		white-space: nowrap;
	}

	p {
		text-align: justify;
	}
</style>
