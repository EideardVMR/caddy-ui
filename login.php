<!DOCTYPE html>
<html lang="de">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Login</title>
  <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet"/>
  <style>
    :root {
      --blue-deep:   #0a2ead;
      --blue-mid:    #1a4fff;
      --blue-light:  #4f8aff;
      --blue-sky:    #7ab3f5;
      --blue-pale:   #a8cbff;
      --glass-bg:    rgba(255,255,255,0.08);
      --glass-border:rgba(255,255,255,0.18);
      --white:       #ffffff;
      --input-bg:    rgba(255,255,255,0.92);
      --shadow:      0 32px 80px rgba(10,46,173,0.45);
    }
 
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
 
    body {
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      font-family: 'DM Sans', sans-serif;
      background: linear-gradient(135deg, #5ba4f5 0%, #3a7de8 30%, #1a4fff 65%, #0a2ead 100%);
      overflow: hidden;
      position: relative;
    }
 
    /* ── Ambient blobs ── */
    .blob {
      position: absolute;
      border-radius: 50%;
      filter: blur(0px);
      pointer-events: none;
      animation: float 8s ease-in-out infinite;
    }
    .blob-1 {
      width: 340px; height: 340px;
      background: radial-gradient(circle at 40% 40%, rgba(255,255,255,0.18), rgba(100,160,255,0.08));
      top: -80px; right: -60px;
      border: 1.5px solid rgba(255,255,255,0.12);
      animation-delay: 0s;
    }
    .blob-2 {
      width: 220px; height: 220px;
      background: radial-gradient(circle, rgba(255,255,255,0.13), transparent 70%);
      bottom: 40px; left: -40px;
      border: 1px solid rgba(255,255,255,0.1);
      animation-delay: -3s;
    }
    .blob-3 {
      width: 120px; height: 120px;
      background: rgba(255,255,255,0.10);
      top: 60%; right: 8%;
      border: 1px solid rgba(255,255,255,0.15);
      animation-delay: -5s;
    }
 
    /* Decorative squiggle shapes */
    .deco {
      position: absolute;
      pointer-events: none;
      animation: float 10s ease-in-out infinite;
    }
    .deco svg { opacity: 0.22; }
    .deco-1 { left: 5%;  top: 30%; animation-delay: -1s; }
    .deco-2 { left: 10%; top: 52%; animation-delay: -4s; transform: scale(0.7); }
    .deco-3 { right: 5%; top: 60%; animation-delay: -7s; transform: scale(1.2) scaleX(-1); }
    .deco-4 { right: 12%; top: 20%; animation-delay: -2s; transform: scale(0.55) rotate(30deg); }
 
    @keyframes float {
      0%,100% { transform: translateY(0px) rotate(0deg); }
      50%      { transform: translateY(-18px) rotate(4deg); }
    }
 
    /* ── Card ── */
    .card-wrap {
      position: relative;
      z-index: 10;
      width: min(780px, 96vw);
      background: linear-gradient(145deg, #1248cc 0%, #0d3ab8 50%, #0a2ead 100%);
      border-radius: 28px;
      padding: 52px 52px 48px;
      box-shadow: var(--shadow), inset 0 1px 0 rgba(255,255,255,0.15);
      border: 1px solid rgba(255,255,255,0.12);
      display: flex;
      gap: 0;
      align-items: center;
      animation: cardIn 0.7s cubic-bezier(0.22,1,0.36,1) both;
    }
    @keyframes cardIn {
      from { opacity:0; transform: translateY(32px) scale(0.96); }
      to   { opacity:1; transform: none; }
    }
 
    /* Left decorative column */
    .card-deco {
      flex: 0 0 170px;
      height: 280px;
      position: relative;
      margin-right: 40px;
      display: flex;
      align-items: center;
      justify-content: center;
    }
    .card-deco svg {
      position: absolute;
      filter: drop-shadow(0 8px 24px rgba(0,0,0,0.25));
    }
 
    /* ── Form side ── */
    .form-side {
      flex: 1;
    }
 
    .logo-label {
      font-family: 'DM Sans', sans-serif;
      font-size: 11px;
      font-weight: 500;
      letter-spacing: 0.12em;
      text-transform: uppercase;
      color: rgba(255,255,255,0.5);
      margin-bottom: 2px;
    }
 
    h1 {
      font-family: 'Syne', sans-serif;
      font-size: 38px;
      font-weight: 800;
      color: #ffffff;
      letter-spacing: -0.02em;
      margin-bottom: 28px;
      line-height: 1;
    }
 
    /* Glass form panel */
    .form-glass {
      background: rgba(255,255,255,0.07);
      border: 1px solid rgba(255,255,255,0.14);
      border-radius: 18px;
      padding: 28px 28px 24px;
      backdrop-filter: blur(12px);
    }
 
    .field { margin-bottom: 16px; }
    .field label {
      display: block;
      font-size: 12px;
      font-weight: 500;
      color: rgba(255,255,255,0.8);
      margin-bottom: 6px;
      letter-spacing: 0.04em;
    }
 
    .input-wrap {
      position: relative;
    }
    .input-wrap input {
      width: 100%;
      height: 44px;
      border: none;
      border-radius: 10px;
      background: var(--input-bg);
      padding: 0 42px 0 14px;
      font-family: 'DM Sans', sans-serif;
      font-size: 14px;
      color: #1a2a55;
      outline: none;
      transition: box-shadow 0.2s, background 0.2s;
    }
    .input-wrap input::placeholder { color: #9aabcc; }
    .input-wrap input:focus {
      background: #fff;
      box-shadow: 0 0 0 3px rgba(74,130,255,0.35);
    }
    .input-wrap .eye {
      position: absolute;
      right: 12px;
      top: 50%;
      transform: translateY(-50%);
      cursor: pointer;
      color: #9aabcc;
      display: flex;
    }
 
    .forgot {
      font-size: 11.5px;
      color: rgba(255,255,255,0.55);
      margin-top: 6px;
      cursor: pointer;
      transition: color 0.2s;
    }
    .forgot:hover { color: rgba(255,255,255,0.9); }
 
    .btn-signin {
      margin-top: 18px;
      width: 100%;
      height: 46px;
      background: linear-gradient(90deg, #0a2ead, #1a4fff);
      border: none;
      border-radius: 10px;
      color: #fff;
      font-family: 'Syne', sans-serif;
      font-size: 15px;
      font-weight: 700;
      letter-spacing: 0.04em;
      cursor: pointer;
      transition: transform 0.15s, box-shadow 0.15s, filter 0.15s;
      box-shadow: 0 6px 20px rgba(10,46,173,0.5);
      position: relative;
      overflow: hidden;
    }
    .btn-signin::after {
      content: '';
      position: absolute;
      inset: 0;
      background: linear-gradient(90deg, transparent, rgba(255,255,255,0.12), transparent);
      transform: translateX(-100%);
      transition: transform 0.4s;
    }
    .btn-signin:hover { transform: translateY(-1px); filter: brightness(1.1); }
    .btn-signin:hover::after { transform: translateX(100%); }
    .btn-signin:active { transform: scale(0.98); }
 
    .divider {
      display: flex;
      align-items: center;
      gap: 10px;
      margin: 18px 0 14px;
      color: rgba(255,255,255,0.35);
      font-size: 12px;
      letter-spacing: 0.06em;
    }
    .divider::before, .divider::after {
      content: '';
      flex: 1;
      height: 1px;
      background: rgba(255,255,255,0.15);
    }
 
    .social-row {
      display: flex;
      gap: 10px;
      justify-content: center;
    }
    .social-btn {
      width: 48px; height: 36px;
      background: rgba(255,255,255,0.92);
      border: none;
      border-radius: 9px;
      cursor: pointer;
      display: flex;
      align-items: center;
      justify-content: center;
      transition: transform 0.15s, box-shadow 0.15s;
      box-shadow: 0 2px 8px rgba(0,0,0,0.18);
    }
    .social-btn:hover { transform: translateY(-2px); box-shadow: 0 6px 16px rgba(0,0,0,0.22); }
    .social-btn svg { width: 18px; height: 18px; }
 
    .register-line {
      text-align: center;
      margin-top: 16px;
      font-size: 12.5px;
      color: rgba(255,255,255,0.45);
    }
    .register-line a {
      color: rgba(255,255,255,0.85);
      font-weight: 600;
      text-decoration: none;
      border-bottom: 1px solid rgba(255,255,255,0.3);
      padding-bottom: 1px;
      transition: color 0.2s, border-color 0.2s;
    }
    .register-line a:hover { color: #fff; border-color: #fff; }
 
    .error {
      margin-top: 30px;
      color: red;
      text-align: center;
      font-size: 1.3em;
      padding: 20px;
      border-radius: 20px;
      background-color: #a8cbff;
    }

    /* ── Responsive ── */
    @media (max-width: 600px) {
      .card-wrap { padding: 36px 24px 32px; flex-direction: column; }
      .card-deco  { display: none; }
      h1 { font-size: 30px; margin-bottom: 20px; }
    }
  </style>
</head>
<body>
 
  <!-- Ambient blobs -->
  <div class="blob blob-1"></div>
  <div class="blob blob-2"></div>
  <div class="blob blob-3"></div>
 
  <!-- Decorative squiggles -->
  <div class="deco deco-1">
    <svg width="90" height="130" viewBox="0 0 90 130" fill="none">
      <path d="M45 10 C20 10 10 35 30 50 C50 65 70 55 65 80 C60 105 30 110 20 120" stroke="white" stroke-width="14" stroke-linecap="round" fill="none"/>
    </svg>
  </div>
  <div class="deco deco-2">
    <svg width="70" height="100" viewBox="0 0 70 100" fill="none">
      <path d="M35 5 C15 5 8 25 22 38 C36 51 55 42 50 62 C45 82 22 87 15 95" stroke="white" stroke-width="11" stroke-linecap="round" fill="none"/>
    </svg>
  </div>
  <div class="deco deco-3">
    <svg width="80" height="120" viewBox="0 0 80 120" fill="none">
      <path d="M40 8 C18 8 10 30 26 44 C42 58 60 48 56 70 C52 92 26 98 18 108" stroke="white" stroke-width="12" stroke-linecap="round" fill="none"/>
    </svg>
  </div>
  <div class="deco deco-4">
    <svg width="80" height="120" viewBox="0 0 80 120" fill="none">
      <path d="M40 8 C18 8 10 30 26 44 C42 58 60 48 56 70 C52 92 26 98 18 108" stroke="white" stroke-width="12" stroke-linecap="round" fill="none"/>
    </svg>
  </div>
 
  <form method="post" action="?p=login">
    <!-- Card -->
    <div class="card-wrap">
    
        <!-- Form side -->
        <div class="form-side">
        <p class="logo-label">Caddy-UI</p>
        <h1>Login</h1>
    
        <div class="form-glass">
            <div class="field">
            <label>Username</label>
            <div class="input-wrap">
                <input type="text" name="username" placeholder="username"/>
            </div>
            </div>
    
            <div class="field">
            <label>Password</label>
            <div class="input-wrap">
                <input type="password" name="password" id="pwInput" placeholder="Password"/>
                <span class="eye" onclick="togglePw()">
                <svg id="eyeIcon" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>
                </svg>
                </span>
            </div>
            </div>
    
            <button class="btn-signin">Sign in</button>

            <?php

                if($_POST['username']) {

                  if(UserDB::login($_POST['username'], $_POST['password'])) {
                    header("Location: ?p=hosts");
                    exit();
                  } else {
                    echo '
                      <div class="error">
                        Kombination aus Benutzername und Passwort ist leider falsch.
                      </div>                    
                    ';
                  }
                }
            ?>
    
        </div>
        </div>
    
    </div>
  </form>
  <script>
    function togglePw() {
      const inp = document.getElementById('pwInput');
      const ico = document.getElementById('eyeIcon');
      if (inp.type === 'password') {
        inp.type = 'text';
        ico.innerHTML = '<path d="M17.94 17.94A10.07 10.07 0 0112 20c-7 0-11-8-11-8a18.45 18.45 0 015.06-5.94M9.9 4.24A9.12 9.12 0 0112 4c7 0 11 8 11 8a18.5 18.5 0 01-2.16 3.19m-6.72-1.07a3 3 0 11-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/>';
      } else {
        inp.type = 'password';
        ico.innerHTML = '<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>';
      }
    }
  </script>
</body>
</html>
 
