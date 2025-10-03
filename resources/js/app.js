import './bootstrap';
import Alpine from 'alpinejs';
import Swal from 'sweetalert2';

// Make SweetAlert2 global
window.Swal = Swal;

window.Alpine = Alpine;
Alpine.start();