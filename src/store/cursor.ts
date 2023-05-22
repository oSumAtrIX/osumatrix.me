import { writable } from 'svelte/store';

const { subscribe, set, update } = writable(0);

export const burst = {
	subscribe,
	increment: () => update((n) => (n < 1 ? n + 0.15 : n)),
	decrement: () => update((n) => n - 0.1),
	reset: () => set(0)
};

export const scale = writable(0);
