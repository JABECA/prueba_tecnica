import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();



// Confirmación SweetAlert global (usa Swal del CDN)
document.addEventListener('submit', (e) => {
  const form = e.target;
  if (form.matches('form[data-confirm]')) {
    e.preventDefault();
    const msg = form.getAttribute('data-confirm') || '¿Estás seguro?';
    Swal.fire({
      title: msg,
      icon: 'warning',
      showCancelButton: true,
      confirmButtonText: 'Sí, continuar',
      cancelButtonText: 'Cancelar',
      confirmButtonColor: '#102A83',
      cancelButtonColor: '#e02424',
    }).then(r => { if (r.isConfirmed) form.submit(); });
  }
});