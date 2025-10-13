<script lang="ts">
	import { scale } from '../store/cursor';

	export let href: string;
	export let newTab: boolean = true;
</script>

<!-- svelte-ignore a11y_mouse_events_have_key_events -->
<a
	on:mouseover={() => scale.set(-0.6)}
	on:mouseleave={() => scale.set(0)}
	{href}
	rel="noopener noreferrer"
	target={newTab ? '_blank' : '_self'}
>
	<slot />
</a>

<style>
	a:hover {
		color: var(--white);
	}
</style>
