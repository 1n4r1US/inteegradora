// Pequeñas interacciones: menú móvil, loader, validación simple de formulario y año dinámico
function showLoader() {
  if(document.getElementById('loaderOverlay')) return;
  const overlay = document.createElement('div');
  overlay.className = 'loader-overlay';
  overlay.id = 'loaderOverlay';
  overlay.innerHTML = '<div class="loader" aria-label="Cargando"></div>';
  document.body.appendChild(overlay);
}
function hideLoader() {
  const overlay = document.getElementById('loaderOverlay');
  if(overlay) overlay.remove();
}

document.addEventListener('DOMContentLoaded',function(){
        // Alternancia de formularios en auth.html
        const loginCard = document.getElementById('loginCard');
        const registerCard = document.getElementById('registerCard');
        const tabLogin = document.getElementById('tabLogin');
        const tabRegister = document.getElementById('tabRegister');
        const showRegister = document.getElementById('showRegister');
        const showLogin = document.getElementById('showLogin');

        function mostrarLogin(){
          if(loginCard) loginCard.style.display = '';
          if(registerCard) registerCard.style.display = 'none';
          if(tabLogin) tabLogin.classList.add('active');
          if(tabRegister) tabRegister.classList.remove('active');
        }
        function mostrarRegistro(){
          if(loginCard) loginCard.style.display = 'none';
          if(registerCard) registerCard.style.display = '';
          if(tabLogin) tabLogin.classList.remove('active');
          if(tabRegister) tabRegister.classList.add('active');
        }
        if(tabLogin) tabLogin.addEventListener('click', mostrarLogin);
        if(tabRegister) tabRegister.addEventListener('click', mostrarRegistro);
        if(showRegister) showRegister.addEventListener('click', function(e){ e.preventDefault(); mostrarRegistro(); });
        if(showLogin) showLogin.addEventListener('click', function(e){ e.preventDefault(); mostrarLogin(); });
        mostrarLogin();
      // Login de usuario
      const loginForm = document.getElementById('loginForm');
      if(loginForm){
        loginForm.addEventListener('submit', function(e){
          e.preventDefault();
          const correo = loginForm.email.value.trim();
          const password = loginForm.password.value;
          if(!correo || !password){
            alert('Por favor ingresa tu correo y contraseña.');
            return;
          }
          showLoader();
          fetch('backend/api/login.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({correo, password})
          })
          .then(res => res.json())
          .then(data => {
            hideLoader();
            if(data.success && data.user){
              // Guardar usuario en localStorage
              localStorage.setItem('user', JSON.stringify(data.user));
              // Redirigir a dashboard
              window.location.href = 'dashboard.html';
            } else {
              alert(data.message || 'Credenciales incorrectas.');
            }
          })
          .catch(err => {
            hideLoader();
            alert('Error de conexión con el servidor.');
          });
        });
      }
    // Registro de nuevos usuarios
    const registerForm = document.getElementById('registerForm');
    if(registerForm){
      registerForm.addEventListener('submit', function(e){
        e.preventDefault();
        // Obtener valores
        const nombre = registerForm.nombre.value.trim();
        const apellido = registerForm.apellido.value.trim();
        const genero = registerForm.genero.value;
        const telefono = registerForm.telefono.value.trim();
        const direccion = registerForm.direccion.value.trim();
        const rol = registerForm.rol.value;
        const email = registerForm.email.value.trim();
        const password = registerForm.password.value;
        // Validación básica
        if(!nombre || !apellido || !genero || !telefono || !direccion || !rol || !email || !password){
          alert('Por favor completa todos los campos.');
          return;
        }
        if(!/^[^@\s]+@[^@\s]+\.[^@\s]+$/.test(email)){
          alert('Ingresa un correo válido.');
          return;
        }
        showLoader();
        fetch('backend/api/register.php', {
          method: 'POST',
          headers: {'Content-Type': 'application/json'},
          body: JSON.stringify({nombre, apellido, genero, telefono, direccion, rol, correo: email, password})
        })
        .then(res => res.json())
        .then(data => {
          hideLoader();
          if(data.success){
            alert('¡Registro exitoso! Bienvenido/a, ' + data.usuario.nombre + '.');
            registerForm.reset();
          } else {
            alert(data.error || 'Ocurrió un error al registrar.');
          }
        })
        .catch(err => {
          hideLoader();
          alert('Error de conexión con el servidor.');
        });
      });
    }
  // Prueba de conexión con backend PHP
  fetch('backend/api/hello.php')
    .then(res => res.json())
    .then(data => {
      console.log('Respuesta backend:', data);
    })
    .catch(err => {
      console.error('Error al conectar con backend:', err);
    });
  // Botón flotante 'Ir arriba'
  const scrollBtn = document.getElementById('scrollTopBtn');
  if(scrollBtn){
    window.addEventListener('scroll', function(){
      if(window.scrollY > 300){
        scrollBtn.style.display = 'flex';
        scrollBtn.style.opacity = '1';
      } else {
        scrollBtn.style.opacity = '0';
        setTimeout(()=>{if(scrollBtn.style.opacity==='0')scrollBtn.style.display='none';}, 200);
      }
    });
    scrollBtn.addEventListener('click', function(){
      window.scrollTo({top:0, behavior:'smooth'});
    });
  }
  const menuToggle = document.getElementById('menuToggle');
  const mainNav = document.getElementById('mainNav');
  if(menuToggle){
    menuToggle.addEventListener('click', ()=>{
      if(mainNav.style.display === 'block') mainNav.style.display = '';
      else mainNav.style.display = 'block';
    });
  }

  // Año en el footer
  const yearEl = document.getElementById('year');
  if(yearEl) yearEl.textContent = new Date().getFullYear();

  // Formulario: validación básica, loader y simulación de envío
  const form = document.getElementById('contactForm');
  const formMsg = document.getElementById('formMsg');
  if(form){
    form.addEventListener('submit', function(e){
      e.preventDefault();
      formMsg.textContent = '';
      const name = form.name.value.trim();
      const email = form.email.value.trim();
      const msg = form.message.value.trim();
      if(!name || !email || !msg){
        formMsg.style.color = 'crimson';
        formMsg.textContent = 'Por favor completa todos los campos.';
        return;
      }
      // Validación simple del email
      if(!/^[^@\s]+@[^@\s]+\.[^@\s]+$/.test(email)){
        formMsg.style.color = 'crimson';
        formMsg.textContent = 'Ingresa un correo válido.';
        return;
      }
      showLoader();
      setTimeout(function(){
        hideLoader();
        formMsg.style.color = 'green';
        formMsg.textContent = '¡Gracias! Tu mensaje ha sido enviado (simulado). Nos contactaremos pronto.';
        form.reset();
        formMsg.classList.add('in-view');
        setTimeout(()=>formMsg.classList.remove('in-view'), 3500);
      }, 1200);
    });
  }
  // Loader al navegar entre páginas internas
  document.querySelectorAll('a[href]').forEach(link => {
    const href = link.getAttribute('href');
    if(href && !href.startsWith('http') && !href.startsWith('mailto:') && !href.startsWith('#')){
      link.addEventListener('click', function(e){
        // Solo si es click izquierdo y sin ctrl/meta
        if(e.button === 0 && !e.ctrlKey && !e.metaKey && !e.shiftKey && !e.altKey){
          showLoader();
        }
      });
    }
  });
  
  // Animaciones al hacer scroll: detectar elementos con .will-animate y añadir .in-view
  const observer = 'IntersectionObserver' in window ? new IntersectionObserver((entries, obs)=>{
    entries.forEach(entry=>{
      if(entry.isIntersecting){
        entry.target.classList.add('in-view');
        obs.unobserve(entry.target);
      }
    })
  },{threshold:0.12}) : null;

  if(observer){
    document.querySelectorAll('.will-animate').forEach(el=>observer.observe(el));
  } else {
    // Fallback: añadir clase inmediatamente
    document.querySelectorAll('.will-animate').forEach(el=>el.classList.add('in-view'));
  }
});
