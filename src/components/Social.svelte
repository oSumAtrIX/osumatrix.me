<script lang="ts">
	import Fa from 'svelte-fa';
	import type { IconDefinition } from '@fortawesome/free-brands-svg-icons';
	import Link from './Link.svelte';

	export let faIcon: IconDefinition;
	export let link: string;
</script>

<Link href={link}>
	<Fa icon={faIcon} />
</Link>
