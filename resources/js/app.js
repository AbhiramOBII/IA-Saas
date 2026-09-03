import './bootstrap';
import { Livewire, Alpine } from '../../vendor/livewire/livewire/dist/livewire.esm';

// Make Alpine globally accessible for inline scripts and browser console
window.Alpine = Alpine;

// Register Alpine plugins here before starting, e.g.:
// import Collapse from '@alpinejs/collapse';
// Alpine.plugin(Collapse);

Livewire.start();
