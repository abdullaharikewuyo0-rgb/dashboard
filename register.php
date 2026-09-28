<?php
session_start();
require 'db.php';

$errors  = [];
$success = '';
$old     = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $firstName = trim($_POST['first_name'] ?? '');
    $lastName  = trim($_POST['last_name']  ?? '');
    $email     = trim($_POST['email']      ?? '');
    $phone     = trim($_POST['phone']      ?? '');
    $dob       = trim($_POST['dob']        ?? '');
    $gender    = $_POST['gender']          ?? '';
    $course    = $_POST['course']          ?? '';
    $password  = $_POST['password']        ?? '';
    $confirm   = $_POST['confirm']         ?? '';
    $terms     = isset($_POST['terms']);

    $old = compact('firstName','lastName','email','phone','dob','gender','course');

    if ($firstName === '') {
        $errors['first_name'] = 'First name is required.';
    } elseif (!preg_match("/^[A-Za-z]{2,30}$/", $firstName)) {
        $errors['first_name'] = 'Only letters, 2–30 characters.';
    }

    if ($lastName === '') {
        $errors['last_name'] = 'Last name is required.';
    } elseif (!preg_match("/^[A-Za-z]{2,30}$/", $lastName)) {
        $errors['last_name'] = 'Only letters, 2–30 characters.';
    }

    if ($email === '') {
        $errors['email'] = 'Email is required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Enter a valid email address.';
    } else {
        $chk = $pdo->prepare('SELECT id FROM users WHERE email = ?');
        $chk->execute([$email]);
        if ($chk->fetch()) $errors['email'] = 'This email is already registered.';
    }

    if ($phone === '') {
        $errors['phone'] = 'Phone number is required.';
    } elseif (!preg_match('/^[0-9+\-\s]{7,15}$/', $phone)) {
        $errors['phone'] = 'Enter a valid phone (7–15 digits).';
    }

    if ($dob === '') {
        $errors['dob'] = 'Date of birth is required.';
    } else {
        $d = DateTime::createFromFormat('Y-m-d', $dob);
        if (!$d || $d->format('Y-m-d') !== $dob) {
            $errors['dob'] = 'Invalid date.';
        } else {
            $age = (new DateTime())->diff($d)->y;
            if ($age < 16) $errors['dob'] = 'You must be at least 16 years old.';
            if ($age > 100) $errors['dob'] = 'Please enter a valid date of birth.';
        }
    }

    if (!in_array($gender, ['male', 'female', 'other'], true)) {
        $errors['gender'] = 'Please select a gender.';
    }

    if ($course === '') {
        $errors['course'] = 'Please choose a course.';
    }

    if ($password === '') {
        $errors['password'] = 'Password is required.';
    } else {
        if (strlen($password) < 8) {
            $errors['password'] = 'Password must be at least 8 characters.';
        } elseif (!preg_match('/[A-Z]/', $password)) {
            $errors['password'] = 'Must contain at least 1 uppercase letter.';
        } elseif (!preg_match('/[a-z]/', $password)) {
            $errors['password'] = 'Must contain at least 1 lowercase letter.';
        } elseif (!preg_match('/[0-9]/', $password)) {
            $errors['password'] = 'Must contain at least 1 number.';
        } elseif (!preg_match('/[\W_]/', $password)) {
            $errors['password'] = 'Must contain at least 1 special character.';
        }
    }

    if ($confirm === '') {
        $errors['confirm'] = 'Please confirm your password.';
    } elseif ($confirm !== $password) {
        $errors['confirm'] = 'Passwords do not match.';
    }

    if (!$terms) {
        $errors['terms'] = 'You must agree to the terms.';
    }

    if (empty($errors)) {
        $initials = strtoupper(substr($firstName, 0, 1) . substr($lastName, 0, 1));
        $hash     = password_hash($password, PASSWORD_DEFAULT);

        $ins = $pdo->prepare(
            'INSERT INTO users (name, initials, email, password_hash, role)
             VALUES (?, ?, ?, ?, ?)'
        );
        $ins->execute([
            "$firstName $lastName",
            $initials,
            $email,
            $hash,
            'student',
        ]);

        $success = 'Account created successfully! You can now log in.';
        $old = [];
    }
}

function v($key, $default = '') {
    return htmlspecialchars($_POST[$key] ?? $default);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Register · Student Dashboard</title>
  <style>
    * { box-sizing: border-box; margin: 0; padding: 0; }

    body {
      font-family: Arial, Helvetica, sans-serif;
      background: gainsboro;
      color: darkslategray;
      line-height: 1.6;
    }

    .layout { display: grid; grid-template-columns: 1fr; min-height: 100vh; }
    .main { padding: 20px; display: grid; gap: 20px; align-content: start; }

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
    .mini-profile { display: flex; align-items: center; gap: 10px; font-weight: bold; }
    .avatar-sm {
      width: 36px; height: 36px;
      background: steelblue; color: white;
      border-radius: 50%;
      display: flex; align-items: center; justify-content: center;
      font-size: 14px; font-weight: bold;
    }
    .login-link {
      background: steelblue; color: white;
      text-decoration: none; font-weight: bold;
      font-size: 13px; padding: 8px 14px;
      border-radius: 6px;
      transition: background 0.3s, transform 0.2s;
    }
    .login-link:hover { background: darkslateblue; transform: translateY(-2px); }

    .form-wrap {
      display: flex;
      justify-content: center;
      padding: 20px;
    }

    .form-card {
      background: white;
      width: 100%;
      max-width: 700px;
      padding: 35px 30px;
      border-radius: 12px;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
      border-top: 6px solid yellow;
    }

    .form-card h1 {
      font-size: 24px;
      color: darkslategray;
      margin-bottom: 6px;
      letter-spacing: 1px;
    }

    .form-card .subtitle {
      font-size: 14px;
      color: gray;
      margin-bottom: 25px;
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

    .success {
      background: #e3f7e8;
      color: seagreen;
      padding: 12px 16px;
      border-radius: 8px;
      border-left: 4px solid seagreen;
      font-size: 14px;
      margin-bottom: 20px;
      font-weight: bold;
    }

    .form-grid {
      display: grid;
      grid-template-columns: 1fr;
      gap: 18px;
    }

    @media (min-width: 640px) {
      .form-grid.two-col { grid-template-columns: 1fr 1fr; }
      .span-2 { grid-column: span 2; }
    }

    .form-group { display: flex; flex-direction: column; }

    label {
      font-size: 13px;
      font-weight: bold;
      color: darkslategray;
      margin-bottom: 6px;
      letter-spacing: 0.5px;
    }
    label .req { color: crimson; margin-left: 3px; }

    input[type="text"],
    input[type="email"],
    input[type="tel"],
    input[type="date"],
    input[type="password"],
    select {
      width: 100%;
      padding: 11px 14px;
      border: 2px solid gainsboro;
      border-radius: 8px;
      font-size: 15px;
      font-family: inherit;
      transition: border-color 0.2s, box-shadow 0.2s;
      background: white;
    }

    input:focus, select:focus {
      outline: none;
      border-color: steelblue;
      box-shadow: 0 0 0 3px rgba(70, 130, 180, 0.2);
    }

    input.invalid, select.invalid {
      border-color: crimson;
      background: #fff5f5;
    }

    input.valid, select.valid {
      border-color: seagreen;
    }

    .field-error {
      color: crimson;
      font-size: 12px;
      margin-top: 5px;
      font-weight: bold;
    }

    .radio-group {
      display: flex;
      gap: 20px;
      flex-wrap: wrap;
      padding: 10px 4px;
    }
    .radio-group label {
      font-weight: normal;
      display: flex;
      align-items: center;
      gap: 6px;
      cursor: pointer;
      margin-bottom: 0;
    }
    .radio-group input[type="radio"] {
      width: auto;
      accent-color: steelblue;
    }

    .checkbox-group {
      display: flex;
      align-items: flex-start;
      gap: 10px;
    }
    .checkbox-group input[type="checkbox"] {
      width: 18px;
      height: 18px;
      margin-top: 3px;
      accent-color: steelblue;
      cursor: pointer;
    }
    .checkbox-group label {
      font-weight: normal;
      cursor: pointer;
      margin-bottom: 0;
    }

    .password-hint {
      font-size: 12px;
      color: gray;
      margin-top: 5px;
    }

    .strength-meter {
      height: 6px;
      background: gainsboro;
      border-radius: 3px;
      margin-top: 8px;
      overflow: hidden;
    }
    .strength-meter span {
      display: block;
      height: 100%;
      width: 0;
      transition: width 0.3s, background 0.3s;
    }
    .strength-label {
      font-size: 12px;
      margin-top: 5px;
      font-weight: bold;
    }

    .btn-submit {
      width: 100%;
      padding: 14px;
      background: steelblue;
      color: white;
      border: none;
      border-radius: 8px;
      font-size: 16px;
      font-weight: bold;
      letter-spacing: 1px;
      cursor: pointer;
      transition: background 0.3s, transform 0.2s, letter-spacing 0.3s;
      margin-top: 10px;
    }
    .btn-submit:hover {
      background: darkslateblue;
      letter-spacing: 2px;
      transform: translateY(-2px);
    }
    .btn-submit:active { background: midnightblue; transform: scale(0.98); }

    .form-footer-link {
      text-align: center;
      margin-top: 20px;
      font-size: 14px;
      color: gray;
    }
    .form-footer-link a {
      color: steelblue;
      font-weight: bold;
      text-decoration: none;
    }
    .form-footer-link a:hover { text-decoration: underline; }

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
    .footer p { font-size: 13px; color: gainsboro; }

    .social-links { display: flex; gap: 14px; flex-wrap: wrap; justify-content: center; }
    .social-btn {
      width: 44px; height: 44px;
      border-radius: 50%;
      background: rgba(255, 255, 255, 0.1);
      color: white;
      display: flex; align-items: center; justify-content: center;
      text-decoration: none; font-size: 20px;
      transition: background 0.3s, transform 0.2s, color 0.3s;
    }
    .social-btn:hover { transform: translateY(-4px) scale(1.1); color: white; }
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
          <p>Create your account</p>
        </div>
        <div class="header-actions">
          <div class="mini-profile">
            <div class="avatar-sm">?</div>
            <span>Guest</span>
          </div>
          <a href="login.php" class="login-link">Login</a>
        </div>
      </header>

      <div class="form-wrap">
        <div class="form-card">
          <span class="brand-badge">NEW STUDENT</span>
          <h1>Create Account</h1>
          <p class="subtitle">Fill out the form below to register</p>

          <?php if ($success): ?>
            <div class="success"><?= htmlspecialchars($success) ?></div>
          <?php endif; ?>

          <form method="POST" action="register.php" novalidate id="registerForm">
            <div class="form-grid two-col">

              <div class="form-group">
                <label for="first_name">First Name <span class="req">*</span></label>
                <input type="text" id="first_name" name="first_name"
                       value="<?= v('first_name') ?>"
                       class="<?= isset($errors['first_name']) ? 'invalid' : '' ?>"
                       placeholder="Jane">
                <?php if (isset($errors['first_name'])): ?>
                  <div class="field-error"><?= htmlspecialchars($errors['first_name']) ?></div>
                <?php endif; ?>
              </div>

              <div class="form-group">
                <label for="last_name">Last Name <span class="req">*</span></label>
                <input type="text" id="last_name" name="last_name"
                       value="<?= v('last_name') ?>"
                       class="<?= isset($errors['last_name']) ? 'invalid' : '' ?>"
                       placeholder="Smith">
                <?php if (isset($errors['last_name'])): ?>
                  <div class="field-error"><?= htmlspecialchars($errors['last_name']) ?></div>
                <?php endif; ?>
              </div>

              <div class="form-group span-2">
                <label for="email">Email <span class="req">*</span></label>
                <input type="email" id="email" name="email"
                       value="<?= v('email') ?>"
                       class="<?= isset($errors['email']) ? 'invalid' : '' ?>"
                       placeholder="jane.smith@example.edu">
                <?php if (isset($errors['email'])): ?>
                  <div class="field-error"><?= htmlspecialchars($errors['email']) ?></div>
                <?php endif; ?>
              </div>

              <div class="form-group">
                <label for="phone">Phone <span class="req">*</span></label>
                <input type="tel" id="phone" name="phone"
                       value="<?= v('phone') ?>"
                       class="<?= isset($errors['phone']) ? 'invalid' : '' ?>"
                       placeholder="+234 800 000 0000">
                <?php if (isset($errors['phone'])): ?>
                  <div class="field-error"><?= htmlspecialchars($errors['phone']) ?></div>
                <?php endif; ?>
              </div>

              <div class="form-group">
                <label for="dob">Date of Birth <span class="req">*</span></label>
                <input type="date" id="dob" name="dob"
                       value="<?= v('dob') ?>"
                       class="<?= isset($errors['dob']) ? 'invalid' : '' ?>">
                <?php if (isset($errors['dob'])): ?>
                  <div class="field-error"><?= htmlspecialchars($errors['dob']) ?></div>
                <?php endif; ?>
              </div>

              <div class="form-group span-2">
                <label>Gender <span class="req">*</span></label>
                <div class="radio-group">
                  <?php foreach (['male' => 'Male', 'female' => 'Female'] as $val => $lbl): ?>
                    <label>
                      <input type="radio" name="gender" value="<?= $val ?>"
                        <?= (($_POST['gender'] ?? '') === $val) ? 'checked' : '' ?>>
                      <?= $lbl ?>
                    </label>
                  <?php endforeach; ?>
                </div>
                <?php if (isset($errors['gender'])): ?>
                  <div class="field-error"><?= htmlspecialchars($errors['gender']) ?></div>
                <?php endif; ?>
              </div>

              <div class="form-group span-2">
                <label for="course">Course <span class="req">*</span></label>
                <select id="course" name="course"
                        class="<?= isset($errors['course']) ? 'invalid' : '' ?>">
                  <option value="">— Select a course —</option>
                  <?php
                  $courses = [
                    'CS201' => 'Data Structures',
                    'CS210' => 'Web Development',
                    'CS220' => 'Database Systems',
                    'CS230' => 'Operating Systems',
                    'MA240' => 'Discrete Math',
                  ];
                  foreach ($courses as $code => $title):
                  ?>
                    <option value="<?= $code ?>"
                      <?= (($_POST['course'] ?? '') === $code) ? 'selected' : '' ?>>
                      <?= $code ?> — <?= $title ?>
                    </option>
                  <?php endforeach; ?>
                </select>
                <?php if (isset($errors['course'])): ?>
                  <div class="field-error"><?= htmlspecialchars($errors['course']) ?></div>
                <?php endif; ?>
              </div>

              <div class="form-group">
                <label for="password">Password <span class="req">*</span></label>
                <input type="password" id="password" name="password"
                       class="<?= isset($errors['password']) ? 'invalid' : '' ?>"
                       placeholder="Min 8 characters">
                <div class="strength-meter"><span id="strengthBar"></span></div>
                <div class="strength-label" id="strengthLabel"></div>
                <div class="password-hint">
                  Must include: uppercase, lowercase, number, symbol
                </div>
                <?php if (isset($errors['password'])): ?>
                  <div class="field-error"><?= htmlspecialchars($errors['password']) ?></div>
                <?php endif; ?>
              </div>

              <div class="form-group">
                <label for="confirm">Confirm Password <span class="req">*</span></label>
                <input type="password" id="confirm" name="confirm"
                       class="<?= isset($errors['confirm']) ? 'invalid' : '' ?>"
                       placeholder="Repeat password">
                <?php if (isset($errors['confirm'])): ?>
                  <div class="field-error"><?= htmlspecialchars($errors['confirm']) ?></div>
                <?php endif; ?>
              </div>

              <div class="form-group span-2">
                <div class="checkbox-group">
                  <input type="checkbox" id="terms" name="terms"
                    <?= isset($_POST['terms']) ? 'checked' : '' ?>>
                  <label for="terms">
                    I agree to the <strong>Terms of Service</strong> and
                    <strong>Privacy Policy</strong> <span class="req">*</span>
                  </label>
                </div>
                <?php if (isset($errors['terms'])): ?>
                  <div class="field-error"><?= htmlspecialchars($errors['terms']) ?></div>
                <?php endif; ?>
              </div>

              <div class="span-2">
                <button type="submit" class="btn-submit">Create Account</button>
              </div>

            </div>
          </form>

          <div class="form-footer-link">
            Already have an account? <a href="login.php">Log in</a>
          </div>
        </div>
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
      const pwd       = document.getElementById('password');
      const confirm   = document.getElementById('confirm');
      const bar       = document.getElementById('strengthBar');
      const label     = document.getElementById('strengthLabel');
      const form      = document.getElementById('registerForm');

      pwd.addEventListener('input', () => {
        const v = pwd.value;
        let score = 0;
        if (v.length >= 8)            score++;
        if (/[A-Z]/.test(v))          score++;
        if (/[a-z]/.test(v))          score++;
        if (/[0-9]/.test(v))          score++;
        if (/[\W_]/.test(v))          score++;

        const pct = (score / 5) * 100;
        bar.style.width = pct + '%';

        const colors = ['#e74c3c','#e67e22','#f1c40f','#2ecc71','#27ae60'];
        const labels = ['Very Weak','Weak','Fair','Good','Strong'];

        if (v.length === 0) {
          bar.style.width = '0%';
          label.textContent = '';
          label.style.color = '';
          return;
        }

        const idx = Math.max(0, score - 1);
        bar.style.background = colors[idx];
        label.textContent = labels[idx];
        label.style.color = colors[idx];
      });

      confirm.addEventListener('input', () => {
        if (confirm.value && confirm.value !== pwd.value) {
          confirm.classList.add('invalid');
          confirm.classList.remove('valid');
        } else if (confirm.value) {
          confirm.classList.remove('invalid');
          confirm.classList.add('valid');
        } else {
          confirm.classList.remove('invalid', 'valid');
        }
      });

      form.addEventListener('submit', (e) => {
        let ok = true;
        form.querySelectorAll('input[required], select[required]').forEach(el => {
          if (!el.value.trim()) { ok = false; el.classList.add('invalid'); }
        });
        if (!ok) e.preventDefault();
      });
    })();
  </script>

</body>
</html>