import './bootstrap';
import './echo';
import Alpine from 'alpinejs';
import Swal from 'sweetalert2';
import Chart from 'chart.js/auto';

window.Chart = Chart;

window.Swal = Swal;
window.Alpine = Alpine;
Alpine.start();

