import { login, register, checkAuth, logout } from './js/api.js';
import { showSuccess, showError, showInfo } from './js/notifications.js';
import { isValidFutureDate, isValidEmail, getMinDate } from './js/validators.js';

// Pequeñas interacciones: menú móvil, loader, validación simple de formulario y año dinámico
function showLoader() {
  if (document.getElementById('loaderOverlay')) return;
  const overlay = document.createElement('div');
  overlay.className = 'loader-overlay';
  overlay.id = 'loaderOverlay';
  overlay.innerHTML = '<div class="loader" aria-label="Cargando"></div>';
  document.body.appendChild(overlay);
}
function hideLoader() {
  const overlay = document.getElementById('loaderOverlay');
  if (overlay) overlay.remove();
}

document.addEventListener('DOMContentLoaded', async function () {
  // Verificar autenticación globalmente si es necesario
  const userSession = await checkAuth();
  const navLogin = document.querySelector('a[href="auth.html"]');

  if (userSession.success) {
    // Sincronizar localStorage con la sesión del servidor para evitar bucles de redirección
    if (userSession.user) {
      localStorage.setItem('user', JSON.stringify(userSession.user));
    }

    // Si el usuario está logueado y estamos en auth.html, redirigir al dashboard
    if (window.location.pathname.includes('auth.html')) {
      const path = window.location.pathname.substring(0, window.location.pathname.lastIndexOf('/'));
      window.location.href = path + '/dashboard.html';
    }
    // Cambiar botón de login por logout o dashboard
    if (navLogin) {
      navLogin.textContent = 'Dashboard';
      const path = window.location.pathname.substring(0, window.location.pathname.lastIndexOf('/'));
      navLogin.href = path + '/dashboard.html';
    }
  } else {
    // Si NO está autenticado, limpiar localStorage para evitar inconsistencias
    localStorage.removeItem('user');

    // Si NO está logueado y estamos en dashboard.html, redirigir a auth
    if (window.location.pathname.includes('dashboard.html')) {
      const path = window.location.pathname.substring(0, window.location.pathname.lastIndexOf('/'));
      window.location.href = path + '/auth.html';
    }
  }

  // Logout logic
  const logoutBtn = document.getElementById('logoutBtn');
  if (logoutBtn) {
    logoutBtn.addEventListener('click', async (e) => {
      e.preventDefault();
      showLoader();
      await logout();
      // Limpiar localStorage al cerrar sesión
      localStorage.removeItem('user');
      window.location.href = 'index.html';
    });
  }

  // Alternancia de formularios en auth.html
  const loginCard = document.getElementById('loginCard');
  const registerCard = document.getElementById('registerCard');
  const tabLogin = document.getElementById('tabLogin');
  const tabRegister = document.getElementById('tabRegister');
  const showRegister = document.getElementById('showRegister');
  const showLogin = document.getElementById('showLogin');

  function mostrarLogin() {
    if (loginCard) loginCard.style.display = '';
    if (registerCard) registerCard.style.display = 'none';
    if (tabLogin) tabLogin.classList.add('active');
    if (tabRegister) tabRegister.classList.remove('active');
  }
  function mostrarRegistro() {
    if (loginCard) loginCard.style.display = 'none';
    if (registerCard) registerCard.style.display = '';
    if (tabLogin) tabLogin.classList.remove('active');
    if (tabRegister) tabRegister.classList.add('active');
  }
  if (tabLogin) tabLogin.addEventListener('click', mostrarLogin);
  if (tabRegister) tabRegister.addEventListener('click', mostrarRegistro);
  if (showRegister) showRegister.addEventListener('click', function (e) { e.preventDefault(); mostrarRegistro(); });
  if (showLogin) showLogin.addEventListener('click', function (e) { e.preventDefault(); mostrarLogin(); });
  mostrarLogin();

  // Login de usuario
  const loginForm = document.getElementById('loginForm');
  if (loginForm) {
    loginForm.addEventListener('submit', async function (e) {
      e.preventDefault();
      const correo = loginForm.email.value.trim();
      const password = loginForm.password.value;

      if (!correo || !password) {
        showError('Por favor ingresa tu correo y contraseña.');
        return;
      }

      if (!isValidEmail(correo)) {
        showError('Por favor ingresa un correo válido.');
        return;
      }

      showLoader();
      const submitBtn = e.target.querySelector('button[type="submit"]');
      if (submitBtn) submitBtn.classList.add('btn-loading');

      try {
        const data = await login(correo, password);
        hideLoader();
        if (submitBtn) submitBtn.classList.remove('btn-loading');

        if (data.success && data.user) {
          localStorage.setItem('user', JSON.stringify(data.user));
          showSuccess('¡Bienvenido! Redirigiendo...');
          setTimeout(() => {
            const path = window.location.pathname.substring(0, window.location.pathname.lastIndexOf('/'));
            window.location.href = path + '/dashboard.html';
          }, 500);
        } else {
          showError(data.message || 'Credenciales incorrectas.');
        }
      } catch (err) {
        hideLoader();
        if (submitBtn) submitBtn.classList.remove('btn-loading');
        showError('Error de conexión con el servidor.');
      }
    });
  }

  // Registro de nuevos usuarios
  const registerForm = document.getElementById('registerForm');
  if (registerForm) {
    registerForm.addEventListener('submit', async function (e) {
      e.preventDefault();
      const nombre = registerForm.nombre.value.trim();
      const apellido = registerForm.apellido.value.trim();
      const genero = registerForm.genero.value;
      const telefono = registerForm.telefono.value.trim();
      const direccion = registerForm.direccion.value.trim();
      const rol = registerForm.rol.value;
      const email = registerForm.email.value.trim();
      const password = registerForm.password.value;

      if (!nombre || !apellido || !genero || !telefono || !direccion || !rol || !email || !password) {
        showError('Por favor completa todos los campos.');
        return;
      }

      if (!isValidEmail(email)) {
        showError('Ingresa un correo válido.');
        return;
      }

      if (password.length < 6) {
        showError('La contraseña debe tener al menos 6 caracteres.');
        return;
      }

      showLoader();
      const submitBtn = e.target.querySelector('button[type="submit"]');
      if (submitBtn) submitBtn.classList.add('btn-loading');

      try {
        const data = await register({ nombre, apellido, genero, telefono, direccion, rol, correo: email, password });
        hideLoader();
        if (submitBtn) submitBtn.classList.remove('btn-loading');

        if (data.success) {
          localStorage.setItem('user', JSON.stringify(data.usuario));
          showSuccess('¡Registro exitoso! Bienvenido/a, ' + data.usuario.nombre + '.');
          setTimeout(() => {
            const path = window.location.pathname.substring(0, window.location.pathname.lastIndexOf('/'));
            window.location.href = path + '/dashboard.html';
          }, 1000);
        } else {
          showError(data.error || 'Ocurrió un error al registrar.');
        }
      } catch (err) {
        hideLoader();
        if (submitBtn) submitBtn.classList.remove('btn-loading');
        showError('Error de conexión con el servidor.');
      }
    });
  }

  // Botón flotante 'Ir arriba'
  const scrollBtn = document.getElementById('scrollTopBtn');
  if (scrollBtn) {
    window.addEventListener('scroll', function () {
      if (window.scrollY > 300) {
        scrollBtn.style.display = 'flex';
        scrollBtn.style.opacity = '1';
      } else {
        scrollBtn.style.opacity = '0';
        setTimeout(() => { if (scrollBtn.style.opacity === '0') scrollBtn.style.display = 'none'; }, 200);
      }
    });
    scrollBtn.addEventListener('click', function () {
      window.scrollTo({ top: 0, behavior: 'smooth' });
    });
  }

  const menuToggle = document.getElementById('menuToggle');
  const mainNav = document.getElementById('mainNav');
  if (menuToggle) {
    menuToggle.addEventListener('click', () => {
      if (mainNav.style.display === 'block') mainNav.style.display = '';
      else mainNav.style.display = 'block';
    });
  }

  // Año en el footer
  const yearEl = document.getElementById('year');
  if (yearEl) yearEl.textContent = new Date().getFullYear();

  // Formulario: validación básica, loader y simulación de envío
  const form = document.getElementById('contactForm');
  const formMsg = document.getElementById('formMsg');
  if (form) {
    form.addEventListener('submit', function (e) {
      e.preventDefault();
      formMsg.textContent = '';
      const name = form.name.value.trim();
      const email = form.email.value.trim();
      const msg = form.message.value.trim();

      if (!name || !email || !msg) {
        formMsg.style.color = 'crimson';
        formMsg.textContent = 'Por favor completa todos los campos.';
        return;
      }

      if (!isValidEmail(email)) {
        formMsg.style.color = 'crimson';
        formMsg.textContent = 'Ingresa un correo válido.';
        return;
      }

      showLoader();
      setTimeout(function () {
        hideLoader();
        formMsg.style.color = 'green';
        formMsg.textContent = '¡Gracias! Tu mensaje ha sido enviado (simulado). Nos contactaremos pronto.';
        form.reset();
        formMsg.classList.add('in-view');
        setTimeout(() => formMsg.classList.remove('in-view'), 3500);
      }, 1200);
    });
  }

  // Animaciones al hacer scroll
  const observer = 'IntersectionObserver' in window ? new IntersectionObserver((entries, obs) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        entry.target.classList.add('in-view');
        obs.unobserve(entry.target);
      }
    })
  }, { threshold: 0.12 }) : null;

  if (observer) {
    document.querySelectorAll('.will-animate').forEach(el => observer.observe(el));
  } else {
    document.querySelectorAll('.will-animate').forEach(el => el.classList.add('in-view'));
  }
});
