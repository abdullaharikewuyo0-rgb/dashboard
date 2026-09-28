<?php
session_start();
require 'db.php';

if (isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($email === '' || $password === '') {
        $error = 'Please enter both email and password.';
    } else {
        $stmt = $pdo->prepare('SELECT id, name, initials, email, password_hash FROM users WHERE email = ? LIMIT 1');
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if (!$user || !password_verify($password, $user['password_hash'])) {
            $error = 'Invalid email or password.';
        } else {
            session_regenerate_id(true);
            $_SESSION['user_id']  = $user['id'];
            $_SESSION['name']     = $user['name'];
            $_SESSION['initials'] = $user['initials'];
            $_SESSION['email']    = $user['email'];
            $_SESSION['login_at'] = time();
            header('Location: dashboard.php');
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login · Student Dashboard</title>
  <style>
    * { box-sizing: border-box; margin: 0; padding: 0; }

    body {
      font-family: Arial, Helvetica, sans-serif;
      background: gainsboro;
      color: darkslategray;
      line-height: 1.6;
    }

    .layout { display: grid; grid-template-columns: 1fr; min-height: 100vh; }

    .main {
      padding: 20px;
      display: grid;
      gap: 20px;
      align-content: start;
    }

    .header {
      display: flex;
      flex-wrap: wrap;
      justify-content: space-between;
      align-items: center;
      gap: 15px;
      background: white;
      padding: 15px 20px;
      border-radius: 10px;
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }
    .header-title h2 { color: darkslategray; }
    .header-title p { color: gray; font-size: 14px; }
    .header-actions { display: flex; align-items: center; gap: 15px; flex-wrap: wrap; }
    .icon-btn {
      position: relative;
      background: whitesmoke;
      border: none;
      padding: 10px 14px;
      border-radius: 8px;
      font-size: 18px;
      cursor: pointer;
      transition: background 0.3s ease, transform 0.2s ease;
    }
    .icon-btn:hover { background: lightyellow; transform: scale(1.2) translateY(-2px); }
    .badge {
      position: absolute;
      top: -5px; right: -5px;
      background: crimson;
      color: white;
      font-size: 11px;
      padding: 2px 6px;
      border-radius: 999px;
      font-weight: bold;
    }
    .mini-profile {
      display: flex; align-items: center; gap: 10px;
      font-weight: bold; color: darkslategray;
    }
    .avatar-sm {
      width: 36px; height: 36px;
      background: steelblue;
      color: white;
      border-radius: 50%;
      display: flex; align-items: center; justify-content: center;
      font-size: 14px; font-weight: bold;
    }
    .login-link {
      background: steelblue;
      color: white;
      text-decoration: none;
      font-weight: bold;
      font-size: 13px;
      padding: 8px 14px;
      border-radius: 6px;
      letter-spacing: 0.5px;
      transition: background 0.3s, transform 0.2s;
    }
    .login-link:hover { background: darkslateblue; transform: translateY(-2px); }

    .login-wrap {
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 40px 20px 10px;
    }

    .login-card {
      background: white;
      width: 100%;
      max-width: 420px;
      padding: 35px 30px;
      border-radius: 12px;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
      border-top: 6px solid yellow;
    }

    .login-card h1 {
      font-size: 24px;
      color: darkslategray;
      margin-bottom: 6px;
      letter-spacing: 1px;
    }

    .login-card .subtitle {
      font-size: 14px;
      color: gray;
      margin-bottom: 25px;
    }

    .form-group { margin-bottom: 18px; }

    label {
      display: block;
      font-size: 13px;
      font-weight: bold;
      color: darkslategray;
      margin-bottom: 6px;
      letter-spacing: 0.5px;
    }

    input[type="email"],
    input[type="password"],
    input[type="text"] {
      width: 100%;
      padding: 12px 14px;
      border: 2px solid gainsboro;
      border-radius: 8px;
      font-size: 15px;
      transition: border-color 0.2s, box-shadow 0.2s;
    }

    input[type="email"]:focus,
    input[type="password"]:focus,
    input[type="text"]:focus {
      outline: none;
      border-color: steelblue;
      box-shadow: 0 0 0 3px rgba(70, 130, 180, 0.2);
    }

    .password-wrap {
      position: relative;
      width: 100%;
    }

    .password-wrap input {
      padding-right: 48px;
    }

    .toggle-password {
      position: absolute;
      top: 50%;
      right: 10px;
      transform: translateY(-50%);
      background: transparent;
      border: none;
      padding: 6px;
      cursor: pointer;
      color: gray;
      display: flex;
      align-items: center;
      justify-content: center;
      border-radius: 6px;
      transition: color 0.2s, background 0.2s, transform 0.2s;
    }

    .toggle-password:hover {
      color: steelblue;
      background: whitesmoke;
      transform: translateY(-50%) scale(1.1);
    }

    .toggle-password:active {
      transform: translateY(-50%) scale(0.95);
    }

    .toggle-password svg {
      width: 20px;
      height: 20px;
      display: block;
    }

    .btn-login {
      width: 100%;
      padding: 13px;
      background: steelblue;
      color: white;
      border: none;
      border-radius: 8px;
      font-size: 15px;
      font-weight: bold;
      letter-spacing: 1px;
      cursor: pointer;
      transition: background 0.3s, transform 0.2s, letter-spacing 0.3s;
      margin-top: 6px;
    }

    .btn-login:hover {
      background: darkslateblue;
      letter-spacing: 2px;
      transform: scale(1.05) translateY(-2px);
    }

    .btn-login:active {
      background: midnightblue;
      transform: scale(0.98);
    }

    .error {
      background: #ffe5e5;
      color: crimson;
      padding: 10px 14px;
      border-radius: 8px;
      border-left: 4px solid crimson;
      font-size: 14px;
      margin-bottom: 18px;
    }

    .brand-badge {
      display: inline-block;
      background: darkslategray;
      color: yellow;
      font-size: 11px;
      font-weight: bold;
      letter-spacing: 1px;
      padding: 4px 10px;
      border-radius: 20px;
      margin-bottom: 14px;
    }

    .form-footer-link {
      text-align: center;
      margin-top: 5px;
      font-size: 14px;
      color: gray;
    }
    .form-footer-link a {
      color: steelblue;
      font-weight: bold;
      text-decoration: none;
      transition: color 0.2s;
    }
    .form-footer-link a:hover {
      text-decoration: underline;
      color: darkslateblue;
    }

    .footer {
      background: darkslategray;
      color: white;
      text-align: center;
      padding: 25px 20px;
      border-radius: 10px;
      display: flex;
      flex-direction: column;
      align-items: center;
      gap: 15px;
    }

    .footer p {
      font-size: 13px;
      color: gainsboro;
    }

    .social-links {
      display: flex;
      gap: 14px;
      flex-wrap: wrap;
      justify-content: center;
    }

    .social-btn {
      width: 44px;
      height: 44px;
      border-radius: 50%;
      background: rgba(255, 255, 255, 0.1);
      color: white;
      display: flex;
      align-items: center;
      justify-content: center;
      text-decoration: none;
      font-size: 20px;
      transition: background 0.3s ease, transform 0.2s ease, color 0.3s ease;
    }

    .social-btn:hover {
      transform: translateY(-4px) scale(1.1);
      color: white;
    }

    .social-btn.whatsapp:hover  { background: #25D366; }
    .social-btn.facebook:hover  { background: #1877F2; }
    .social-btn.youtube:hover   { background: #FF0000; }
    .social-btn.x:hover         { background: #000000; }
    .social-btn.instagram:hover { background: #FFB6C1; color: darkslategray; }

    @media (min-width: 901px) {
      .main { padding: 25px 30px; }
    }
  </style>
</head>
<body>

  <div class="layout">

    <div class="main">

      <header class="header">
        <div class="header-title">
          <h2>Student Dashboard</h2>
          <p>Please sign in to continue</p>
        </div>
        <div class="header-actions">
          <button class="icon-btn" title="Notifications">
            &#128276;
            <span class="badge">3</span>
          </button>
          <div class="mini-profile">
            <div class="avatar-sm">?</div>
            <span>Guest</span>
          </div>
        </div>
      </header>

      <div class="login-wrap">
        <div class="login-card">
          <span class="brand-badge">STUDENT DASHBOARD</span>
          <h1>Welcome Back</h1>
          <p class="subtitle">Sign in to access your dashboard</p>

          <?php if ($error): ?>
            <div class="error"><?= htmlspecialchars($error) ?></div>
          <?php endif; ?>

          <form method="POST" action="login.php" autocomplete="off">
            <div class="form-group">
              <label for="email">Email</label>
              <input type="email" id="email" name="email"
                     placeholder="yourmail@gmail.com"
                     value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
            </div>

            <div class="form-group">
              <label for="password">Password</label>
              <div class="password-wrap">
                <input type="password" id="password" name="password"
                       placeholder="Enter your password" required>
                <button type="button" class="toggle-password" id="togglePassword" aria-label="Show password" title="Show password">
                  <svg id="eyeOpen" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                    <circle cx="12" cy="12" r="3"/>
                  </svg>
                  <svg id="eyeClosed" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:none;">
                    <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/>
                    <line x1="1" y1="1" x2="23" y2="23"/>
                  </svg>
                </button>
              </div>
            </div>

            <button type="submit" class="btn-login">Log In</button>
          </form>
        </div>
      </div>

      <div class="form-footer-link">
        Don't have an account? <a href="register.php">Register here</a>
      </div>

      <footer class="footer">
        <div class="social-links">
          <a href="#" class="social-btn whatsapp" title="WhatsApp">
            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="currentColor">
              <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
            </svg>
          </a>
          <a href="#" class="social-btn facebook" title="Facebook">
            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="currentColor">
              <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
            </svg>
          </a>
          <a href="#" class="social-btn youtube" title="YouTube">
            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="currentColor">
              <path d="M23.498 6.186a3.016 3.016 0 00-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 00.502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 002.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 002.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/>
            </svg>
          </a>
          <a href="#" class="social-btn x" title="X">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
              <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
            </svg>
          </a>
          <a href="#" class="social-btn instagram" title="Instagram">
            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="currentColor">
              <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/>
            </svg>
          </a>
        </div>
        <p>&copy; STUDENT DASHBOARD</p>
      </footer>

    </div>
  </div>

  <script>
    (function () {
      const toggle = document.getElementById('togglePassword');
      const pwd    = document.getElementById('password');
      const open   = document.getElementById('eyeOpen');
      const closed = document.getElementById('eyeClosed');

      toggle.addEventListener('click', function () {
        const isHidden = pwd.type === 'password';
        pwd.type = isHidden ? 'text' : 'password';
        open.style.display   = isHidden ? 'none' : 'block';
        closed.style.display = isHidden ? 'block' : 'none';
        toggle.setAttribute('aria-label', isHidden ? 'Hide password' : 'Show password');
        toggle.setAttribute('title',      isHidden ? 'Hide password' : 'Show password');
        pwd.focus();
      });
    })();
  </script>

</body>
</html>