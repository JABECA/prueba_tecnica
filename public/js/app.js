(() => {
  
  function askAndSubmit(form) {
    const msg = form.getAttribute('data-confirm') || '¿Estás seguro?';
    Swal.fire({
      title: 'Confirmar',
      text: msg,
      icon: 'warning',
      showCancelButton: true,
      confirmButtonText: 'Sí, continuar',
      cancelButtonText: 'Cancelar',
      confirmButtonColor: '#009EE0',
      cancelButtonColor: '#102A83',
    }).then((r) => {
      if (r.isConfirmed) {
        form.dataset.confirmed = '1';
        form.submit();
      }
    });
  }

  document.addEventListener('submit', function (e) {
    const form = e.target && e.target.closest('form[data-confirm]');
    if (!form) return;
    if (form.dataset.confirmed === '1') return;
    e.preventDefault();
    askAndSubmit(form);
  }, true);

  if (window.SwalMsg) {
    Swal.fire({
      toast: true,
      position: 'top-end',
      icon: 'success',
      title: window.SwalMsg,
      showConfirmButton: false,
      timer: 2200,
      timerProgressBar: true,
    });
  }

  const burger  = document.getElementById('btn-burger');
  const overlay = document.getElementById('overlay');
  burger?.addEventListener('click', () => document.body.classList.toggle('sidebar-open'));
  overlay?.addEventListener('click', () => document.body.classList.remove('sidebar-open'));

})();
