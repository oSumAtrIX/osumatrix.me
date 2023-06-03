<script lang="ts">
	import type { IconDefinition } from '@fortawesome/free-brands-svg-icons';
	import Fa from 'svelte-fa';

	export let text: string;
	export let icon: IconDefinition;
</script>

<h2>
	<Fa {icon} />
	<span>{text}</span>
</h2>

<style lang="scss">
	h2 {
		margin: 0;
	}

	span {
		color: var(--highlight);
	}
</style>
