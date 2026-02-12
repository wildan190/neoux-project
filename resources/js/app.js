import './bootstrap';
import './echo';
import Alpine from 'alpinejs';
import Swal from 'sweetalert2';
import Chart from 'chart.js/auto';
import feather from 'feather-icons';

window.feather = feather;
window.Chart = Chart;

window.Swal = Swal;
window.Alpine = Alpine;
Alpine.start();

