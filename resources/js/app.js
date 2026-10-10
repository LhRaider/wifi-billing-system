import Alpine from 'alpinejs';
import Chart from 'chart.js/auto';
import '@fortawesome/fontawesome-free/css/all.min.css';

window.Alpine = Alpine;
window.Chart = Chart;

document.dispatchEvent(new Event('chartjs:ready'));

Alpine.start();