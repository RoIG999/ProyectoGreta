<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <title>Login - Greta Estética</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <link rel="stylesheet" href="css/estilos.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600&family=Montserrat:wght@300;500&display=swap" rel="stylesheet">
  <style>
    body {
      font-family: 'Montserrat', sans-serif;
      margin: 0;
      padding: 0;
    }

    header {
      background: #000;
      color: white;
      padding: 10px 40px;
      display: flex;
      align-items: center;
      justify-content: space-between;
    }
    header img {
      height: 55px;
    }
    nav a {
      color: white;
      margin-left: 20px;
      text-decoration: none;
      font-weight: 500;
    }
    nav a:hover {
      color: #f0c0d0;
    }
    nav a.activo {
      border-bottom: 2px solid #f0c0d0;
    }
    :root{
      --bg: #000000ff;
      --card: #000000ff;
      --card-2: #000000ff;
      --text: #fafafa;
      --muted: #bdbdbd;
      --border: #2a2d31;
      --focus: rgba(255,255,255,.16);
      --white: #fff;
      --black: #000000ff;
    }
    *{box-sizing:border-box}
    html,body{height:100%}
    body {
    margin: 0; 
    font-family: 'Montserrat', system-ui, -apple-system, Segoe UI, Roboto, Arial, sans-serif;
    color: var(--text); 
    overflow: hidden;
    display: flex; 
    align-items: center; 
    justify-content: center; 
    padding: 200px; /* ← ELIMINAR el 'px;' extra */
    /* Imagen de fondo para estética */
    background: url("img/login.png") no-repeat center center fixed; /* ← AGREGAR 'url()' */
    background-size: cover; /* ← CAMBIAR 'left' por 'cover' o 'contain' */
    position: relative;
}
    
    /* Overlay para mejorar contraste */
    body::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      width: 300%;
      height: 200%;
      background: rgba(12, 12, 14, 0.55);
      z-index: -1;
    }

    /* ===== Background entretenido (grid + glows) ===== */
    .stage{
      position:fixed; inset:0; overflow:hidden; z-index:-1;
      background:
        radial-gradient(1000px 600px at 20% -10%, #ffffff0f, #0000 70%),
        radial-gradient(900px 600px at 110% 110%, #ffffff08, #0000 70%);
    }
    .grid{
      position:absolute; inset:-50px; opacity:.15;
      background:
        linear-gradient(90deg, #fff2 1px, transparent 1px) 0 0/ 40px 40px,
        linear-gradient(180deg, #fff2 1px, transparent 1px) 0 0/ 40px 40px;
      animation: drift 26s linear infinite;
      filter: blur(0.2px);
    }
    @keyframes drift{
      from{ transform: translate3d(0,0,0) }
      to{ transform: translate3d(-40px,-40px,0) }
    }
    .orb{
      position:absolute; width:520px; height:520px; border-radius:50%;
      background: radial-gradient(circle at 30% 30%, #ffffff1a, #ffffff00 60%);
      filter: blur(20px); mix-blend-mode: screen; animation: float 14s ease-in-out infinite alternate;
    }
    .orb.o2{ width:420px; height:420px; right:-120px; top:-60px; animation-duration: 18s;}
    @keyframes float{ from{ transform: translateY(-10px)} to{ transform: translateY(16px)} }

    /* ===== Card con tilt + glare ===== */
    .shell{ width:100%; max-width:420px; perspective: 1200px; }
    .card{
      position:relative; background: linear-gradient(180deg, var(--card) 0%, var(--card-2) 100%);
      border:1px solid var(--border); border-radius:18px; padding:30px 26px;
      box-shadow: 0 30px 70px rgba(0,0,0,.45);
      transform-style: preserve-3d; transition: transform .12s ease-out, box-shadow .2s ease-out;
      will-change: transform;
    }
    .shell:hover .card{ box-shadow: 0 40px 90px rgba(0,0,0,.55); }
    .card::after{
      content:"";
      position:absolute; inset:-2px; border-radius:18px;
      background: radial-gradient(350px 220px at var(--mx,50%) var(--my,0%), #ffffff10, #0000 60%);
      pointer-events:none; transition: background .08s linear;
    }

    /* Marca */
    .brand{ display:flex; flex-direction:column; align-items:center; gap:10px; margin-bottom:18px; transform: translateZ(30px); }
    .brand img{height:64px; width:auto; display:block; filter: grayscale(1) contrast(1.05) brightness(1.1); }
    .brand h1{ margin:0; font-family:'Playfair Display',serif; font-weight:600; font-size:22px; letter-spacing:.5px; }
    .brand span{font-size:13px; color:var(--muted); letter-spacing:.28px}

    /* Campos */
    .field{ margin-top:12px; transform: translateZ(24px); position:relative; }
    .label{ display:flex; align-items:center; gap:8px; font-size:.86rem; color:var(--muted); margin-bottom:6px }
    .input-wrap{ position:relative; }
    .input{
      width:100%; padding:12px 44px 12px 14px; border-radius:10px; color:var(--text);
      background:#0b0c0d; border:1px solid var(--border);
      outline:none; transition:border-color .18s, box-shadow .18s, transform .06s;
    }
    .input::placeholder{color:#7a7d82}
    .input:focus{border-color:#ffffff33; box-shadow:0 0 0 4px var(--focus)}
    .input:active{transform:scale(.999)}

    /* Toggle ver password */
    .toggle{
      position:absolute;
      right:10px;
      top:50%;
      transform: translateY(-50%);
      width:32px; height:32px;
      border-radius:50%;
      display:grid; place-items:center;
      cursor:pointer;
      background: transparent;
      border: none;
      opacity:.9;
      transition: all .2s ease;
    }
    .toggle:hover{
      background:rgba(255,255,255,0.15);
      opacity:1;
    }
    .toggle svg{
      width:20px; height:20px;
      fill:#fff;          /* siempre blanco */
      stroke:#000;        /* borde fino para contraste */
      stroke-width:1px;
      transition: fill .2s ease, transform .2s ease;
    }
    .toggle:hover svg{
      transform: scale(1.1);
      fill:#f5f5f5;       /* un blanco más vivo */
    }

    /* Animación de parpadeo del icono */
    @keyframes blinkEye {
      0%   { transform: scaleY(1); opacity:1; }
      45%  { transform: scaleY(0.1); opacity:0.6; }
      55%  { transform: scaleY(0.1); opacity:0.6; }
      100% { transform: scaleY(1); opacity:1; }
    }
    .toggle svg.blink { animation: blinkEye .3s ease; }

    /* Botón con ripple + spinner */
    .btn{
      position:relative; overflow:hidden;
      width:100%; margin-top:18px; padding:12px 14px; border-radius:10px;
      background:var(--white); color:#0b0b0b; font-weight:700; border:1px solid #fff;
      cursor:pointer; transition: transform .08s ease, box-shadow .18s ease, opacity .18s ease;
      box-shadow: 0 10px 24px rgba(255,255,255,.12);
      transform: translateZ(20px);
    }
    .btn:hover{transform: translateZ(20px) translateY(-1px)}
    .btn:active{transform: translateZ(20px) translateY(0)}
    .btn:disabled{opacity:.6; cursor:not-allowed}
    .ripple{
      position:absolute; border-radius:50%; background:#0001; pointer-events:none; transform: scale(0);
      animation: ripple .6s ease-out forwards;
    }
    @keyframes ripple{ to{ transform: scale(10); opacity:0 } }

    .spinner{
      --s: 16px;
      width: var(--s); height: var(--s); border-radius:50%;
      border: 2px solid #0002; border-top-color:#000; display:inline-block; vertical-align:middle;
      animation: spin .7s linear infinite; margin-right:8px;
    }
    @keyframes spin{ to{ transform: rotate(360deg) } }

    /* Mensajes */
    .error{min-height:1.4em; color:#ffb3b3; font-size:.9rem; margin-top:12px; transform: translateZ(18px); }
    .hint{ color:#d9d27a; font-size:.82rem; margin-top:8px; min-height:1.1em; }

    /* Shake en error */
    .shake{ animation: shake .45s cubic-bezier(.36,.07,.19,.97) both; }
    @keyframes shake {
      10%, 90% { transform: translate3d(-1px, 0, 0); }
      20%, 80% { transform: translate3d(2px, 0, 0); }
      30%, 50%, 70% { transform: translate3d(-4px, 0, 0); }
      40%, 60% { transform: translate3d(4px, 0, 0); }
    }

    .meta{ margin-top:18px; display:flex; justify-content:center; gap:8px; color:var(--muted); font-size:.8rem; transform: translateZ(14px); }
    .meta a{ color:#fff; text-decoration:none; border-bottom:1px dashed #fff0; transition:border-color .18s }
    .meta a:hover{ border-color:#fff6 }

    /* Accesibilidad */
    a:focus, .toggle:focus, .btn:focus, .input:focus{ outline: none; box-shadow: 0 0 0 4px var(--focus); border-radius:10px }
  </style>
</head>
<body>
  

  <!-- Fondo entretenido -->
  <div class="stage" aria-hidden="true">
    <div class="grid"></div>
    <div class="orb" style="left:-140px; bottom:-120px;"></div>
    <div class="orb o2"></div>
  </div>

  <!-- Tarjeta -->
  <main class="shell" aria-label="Inicio de sesión">
    <section class="card" id="card" role="form">
      <header class="brand">
        <img src="img/LogoGreta.jpeg" alt="GRETA Estética">
        <h1>Acceso Personal</h1>
      </header>

      <div class="field">
        <label class="label" for="usuario">Usuario</label>
        <div class="input-wrap">
          <input class="input" id="usuario" type="text" placeholder="Tu usuario" autocomplete="username">
        </div>
      </div>

      <div class="field">
        <label class="label" for="clave">Contraseña</label>
        <div class="input-wrap">
          <input class="input" id="clave" type="password" placeholder="••••••••" autocomplete="current-password">
          <button class="toggle" type="button" id="btnTogglePass" aria-label="Mostrar u ocultar contraseña">
            <!-- Ojo abierto -->
            <svg id="iconEye" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
              <path d="M12 5C7 5 2.73 8.11 1 12c1.73 3.89 6 7 11 7s9.27-3.11 11-7c-1.73-3.89-6-7-11-7zm0 12c-2.76
              0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5z"/>
              <circle cx="12" cy="12" r="2.5"/>
            </svg>
            <!-- Ojo tachado -->
            <svg id="iconEyeOff" style="display:none" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
              <path d="M12 6a9.77 9.77 0 0 0-7.5 3.5L2 12l2.5 2.5A9.77 9.77 0 0 0 12 18c3.2 0 6.1-1.3 8.2-3.5L22 12l-1.8-2.5C18.1 7.3 15.2 6 12 6zM12 16c-2.2 0-4-1.8-4-4 0-.6.1-1.2.4-1.7l5.3 5.3c-.5.3-1.1.4-1.7.4zm3.6-2.3l-5.3-5.3c.5-.3 1.1-.4 1.7-.4 2.2 0 4 1.8 4 4 0 .6-.1 1.2-.4 1.7z"/>
            </svg>
          </button>
        </div>
        <div class="hint" id="capsHint" aria-live="polite"></div>
      </div>

      <button class="btn" id="btnLogin"><span>Ingresar</span></button>
      <p class="error" id="error" role="alert" aria-live="polite"></p>

      <div class="meta">
        <span>© 2025 GRETA</span>
        <span>·</span>
        <a href="Inicio.php">Volver al sitio</a>
      </div>
    </section>
  </main>

  <script>
    // ====== Elements
    const $u   = document.getElementById("usuario");
    const $p   = document.getElementById("clave");
    const $btn = document.getElementById("btnLogin");
    const $err = document.getElementById("error");
    const $card= document.getElementById("card");
    const $hint= document.getElementById("capsHint");
    const $tgl = document.getElementById("btnTogglePass");
    const $eye = document.getElementById("iconEye");
    const $eyeOff = document.getElementById("iconEyeOff");

    // ====== 3D tilt + glare
    const clamp = (n,min,max)=> Math.max(min, Math.min(max, n));
    function setTilt(e){
      const r = $card.getBoundingClientRect();
      const cx = r.left + r.width/2;
      const cy = r.top  + r.height/2;
      const dx = e.clientX - cx;
      const dy = e.clientY - cy;
      const rx = clamp((-dy / r.height) * 10, -8, 8); // rotX
      const ry = clamp(( dx / r.width)  * 12, -10,10); // rotY
      $card.style.transform = `rotateX(${rx}deg) rotateY(${ry}deg)`;
      // glare
      const mx = ((e.clientX - r.left) / r.width) * 100;
      const my = ((e.clientY - r.top ) / r.height) * 100;
      $card.style.setProperty('--mx', mx + '%');
      $card.style.setProperty('--my', my + '%');
    }
    function resetTilt(){ $card.style.transform = 'rotateX(0) rotateY(0)'; }
    document.addEventListener('mousemove', setTilt);
    document.addEventListener('mouseleave', resetTilt);

    // ====== Toggle password (con parpadeo)
    $tgl.addEventListener('click', ()=>{
      const esPassword = $p.getAttribute('type') === 'password';
      $p.setAttribute('type', esPassword ? 'text' : 'password');

      if (esPassword) {
        $eye.style.display = 'none';
        $eyeOff.style.display = 'block';
        $eyeOff.classList.add('blink');
        setTimeout(()=> $eyeOff.classList.remove('blink'), 300);
      } else {
        $eyeOff.style.display = 'none';
        $eye.style.display = 'block';
        $eye.classList.add('blink');
        setTimeout(()=> $eye.classList.remove('blink'), 300);
      }
    });

    // ====== Caps Lock detection
    function capsCheck(e){
      if (typeof e.getModifierState === 'function' && e.getModifierState('CapsLock')) {
        $hint.textContent = 'Bloq Mayús activado';
      } else {
        $hint.textContent = '';
      }
    }
    $p.addEventListener('keyup', capsCheck);
    $p.addEventListener('keydown', capsCheck);

    // ====== Ripple + spinner
    function ripple(e){
      const rect = $btn.getBoundingClientRect();
      const x = e.clientX - rect.left, y = e.clientY - rect.top;
      const s = Math.max(rect.width, rect.height) * 1.2;
      const span = document.createElement('span');
      span.className = 'ripple';
      span.style.width = span.style.height = s + 'px';
      span.style.left = (x - s/2) + 'px';
      span.style.top  = (y - s/2) + 'px';
      $btn.appendChild(span);
      setTimeout(()=> span.remove(), 650);
    }

    function setBusy(b){
      $btn.disabled = b;
      if (b) {
        $btn.innerHTML = '<span class="spinner"></span>Ingresando...';
      } else {
        $btn.innerHTML = '<span>Ingresar</span>';
      }
    }

    // ====== Login flow
    async function login(e){
      if (e && e.type === 'click') ripple(e);
      $err.textContent = "";
      $card.classList.remove('shake');

      const usuario = $u.value.trim();
      const clave   = $p.value;

      if(!usuario || !clave){
        $err.textContent = "Ingrese usuario y contraseña";
        $card.classList.add('shake');
        return;
      }

      try{
        setBusy(true);
        const res = await fetch("api/login.php", {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify({ usuario, clave })
        });

        const data = await res.json().catch(()=> ({}));
        if(!res.ok){
          $err.textContent = data.error || "Usuario o contraseña incorrectos";
          $card.classList.add('shake');
          return;
        }

        localStorage.setItem("usuarioId", data.id);
        localStorage.setItem("usuarioNombre", data.nombre);
        localStorage.setItem("usuarioRol", data.rol);

        const rol = (data.rol || "").toLowerCase();
        if (rol === "dueña" || rol === "duena" || rol === "admin") {
          window.location.href = "Panel-dueña.php";
        } else if (rol === "empleado" || rol === "empleada") {
          window.location.href = "Panel-empleada.php";
        } else if (rol === "supervisor") {
          window.location.href = "Panel-supervisora.php";
        } else {
          $err.textContent = "Rol no reconocido";
          $card.classList.add('shake');
        }
      }catch(err){
        console.error(err);
        $err.textContent = "Error de red o servidor";
        $card.classList.add('shake');
      }finally{
        setBusy(false);
      }
    }

    $btn.addEventListener("click", login);
    document.addEventListener("keydown", (e)=>{ if(e.key === "Enter") login(e); });
  </script>
</body>
</html>