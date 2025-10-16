<script lang="ts">
	import Fa from 'svelte-fa';
	import type { IconDefinition } from '@fortawesome/free-brands-svg-icons';
	import Link from './Link.svelte';
	import { scale } from '../store/cursor';

	export let faIcon: IconDefinition;
	export let link: string;
</script>

<!-- svelte-ignore a11y_mouse_events_have_key_events -->
<!-- svelte-ignore a11y_no_static_element_interactions -->
<div on:mouseover={() => scale.set(0.8)} on:mouseleave={() => scale.set(0)}>
	<Link href={link}>
		<Fa icon={faIcon} />
	</Link>
</div>

<style>
	div {
		transition: all 0.1s ease-out;

		&:hover {
			transform: scale(1.3);
		}
	}
</style>
