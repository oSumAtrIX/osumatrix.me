<script lang="ts">
	import { faAddressCard } from '@fortawesome/free-solid-svg-icons';
	import SectionHeader from '../atoms/SectionHeader.svelte';

	let birthday = new Date(2002, 2, 15);
	let age = Math.floor((Date.now() - birthday.getTime()) / 3.15576e10);
</script>

<section>
	<SectionHeader text="About me" icon={faAddressCard} />
	<p>
		{age}-year-old computer science student 
		at <a target="_blank" href="https://uni-augsburg.de/">University of Augsburg</a>.
		<br />
		My interests and expertise expand from graphics-, motion- and sound design in dedicated software
		like Adobe Suite and Image-Line FL Studio up to programming in lots of different languages like
		<b>Kotlin</b>,
		<b>Rust</b>, <b>C#</b>, <b>Java</b>, <b>PHP</b>, <b>HTML</b>, <b>JavaScript</b>, <b>CSS</b>,
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
