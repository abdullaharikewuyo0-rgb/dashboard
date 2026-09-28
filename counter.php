<?php require 'auth.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Character Counter · Student Dashboard</title>
  <style>
    * { box-sizing: border-box; margin: 0; padding: 0; }

    body {
      font-family: Arial, Helvetica, sans-serif;
      background: gainsboro;
      color: darkslategray;
      line-height: 1.6;
    }

    .layout { display: grid; grid-template-columns: 1fr; min-height: 100vh; }

    .sidebar {
      background: darkslategray;
      color: white;
      padding: 15px 20px;
      position: sticky;
      top: 0;
      z-index: 100;
    }
    .brand {
      color: yellow;
      font-size: 22px;
      letter-spacing: 1px;
      display: inline-block;
    }
    .nav-toggle { display: none; }
    .nav-label {
      display: block;
      float: right;
      color: yellow;
      font-size: 26px;
      cursor: pointer;
      user-select: none;
      transition: transform 0.3s ease, color 0.3s ease;
    }
    .nav-label:hover { color: white; transform: scale(1.15); }
    .nav-label:active { color: white; transform: scale(1.2) rotate(90deg); }
    .nav { display: none; clear: both; margin-top: 15px; }
    .nav ul { list-style: none; }
    .nav a {
      display: block;
      color: white;
      padding: 12px 15px;
      text-decoration: none;
      border-radius: 6px;
      font-weight: bold;
      letter-spacing: 1px;
      transition: background 0.3s ease, color 0.3s ease,
                  letter-spacing 0.3s ease, padding-left 0.3s ease;
    }
    .nav a:hover {
      background: yellow;
      color: black;
      letter-spacing: 2px;
      padding-left: 25px;
    }
    .nav a.active { background: steelblue; color: white; }
    .nav-toggle:checked ~ .nav { display: block; animation: slideDown 0.4s ease; }
    @keyframes slideDown {
      from { opacity: 0; transform: translateY(-20px); }
      to   { opacity: 1; transform: translateY(0); }
    }

    .main { padding: 20px; display: grid; gap: 20px; }

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
    .icon-btn:hover { background: lightyellow; transform: translateY(-2px); }
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
    .logout-btn {
      background: crimson;
      color: white;
      text-decoration: none;
      font-weight: bold;
      font-size: 13px;
      padding: 8px 14px;
      border-radius: 6px;
      letter-spacing: 0.5px;
      transition: background 0.3s, transform 0.2s;
    }
    .logout-btn:hover { background: darkred; transform: translateY(-10px); }

    .panel {
      background: white;
      padding: 25px;
      border-radius: 10px;
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
      max-width: 800px;
      margin: 0 auto;
      width: 100%;
    }
    .panel h3 {
      margin-bottom: 20px;
      color: darkslategray;
      border-bottom: 2px solid gainsboro;
      padding-bottom: 10px;
      text-align: center;
    }

    textarea {
      width: 100%;
      min-height: 200px;
      padding: 15px;
      border: 2px solid gainsboro;
      border-radius: 10px;
      font-size: 15px;
      font-family: inherit;
      resize: vertical;
      transition: border-color 0.2s, box-shadow 0.2s;
    }
    textarea:focus {
      outline: none;
      border-color: steelblue;
      box-shadow: 0 0 0 3px rgba(70, 130, 180, 0.2);
    }

    .stats-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
      gap: 15px;
      margin-top: 20px;
    }

    .stat-box {
      background: whitesmoke;
      padding: 18px;
      border-radius: 10px;
      text-align: center;
      border-top: 4px solid steelblue;
      transition: transform 0.2s, box-shadow 0.2s;
    }
    .stat-box:hover {
      transform: scale(1.1) translateY(-8px);
      box-shadow: 0 8px 18px rgba(0, 0, 0, 0.1);
    }
    .stat-box h4 {
      font-size: 12px;
      text-transform: uppercase;
      letter-spacing: 1px;
      color: gray;
      margin-bottom: 6px;
    }
    .stat-box .value {
      font-size: 26px;
      font-weight: bold;
      color: darkslategray;
    }

    .stat-box.chars      { border-top-color: steelblue; }
    .stat-box.words      { border-top-color: mediumseagreen; }
    .stat-box.sentences  { border-top-color: darkorange; }
    .stat-box.paragraphs { border-top-color: mediumpurple; }

    .controls {
      display: flex;
      gap: 10px;
      flex-wrap: wrap;
      margin-top: 20px;
      justify-content: center;
    }

    .btn {
      background: steelblue;
      color: white;
      border: none;
      padding: 10px 20px;
      border-radius: 8px;
      font-weight: bold;
      font-size: 14px;
      cursor: pointer;
      transition: background 0.3s, transform 0.2s;
    }
    .btn:hover { background: black; transform: scale(1.1) translateY(-2px); }
    .btn.danger { background: crimson; }
    .btn.danger:hover { background: darkred; }

    .footer {
      background: darkslategray;
      color: white;
      text-align: center;
      padding: 15px 20px;
      border-radius: 10px;
      display: flex;
      flex-wrap: wrap;
      align-items: center;
      justify-content: space-between;
      gap: 10px;
    }
    .footer p { font-size: 13px; color: gainsboro; }
    .social-links { display: flex; gap: 8px; flex-wrap: wrap; justify-content: center; }
    .social-btn {
      width: 32px; height: 32px;
      border-radius: 50%;
      background: rgba(255, 255, 255, 0.1);
      color: white;
      display: flex; align-items: center; justify-content: center;
      text-decoration: none;
      transition: background 0.3s, transform 0.2s, color 0.3s;
    }
    .social-btn:hover { transform: translateY(-3px) scale(1.1); color: white; }
    .social-btn.whatsapp:hover  { background: #25D366; }
    .social-btn.facebook:hover  { background: #1877F2; }
    .social-btn.youtube:hover   { background: #FF0000; }
    .social-btn.x:hover         { background: #000000; }
    .social-btn.instagram:hover { background: #FFB6C1; color: darkslategray; }

    @media (min-width: 901px) {
      .layout { grid-template-columns: 240px 1fr; }
      .sidebar { height: 100vh; position: sticky; top: 0; padding: 25px 20px; }
      .brand { display: block; margin-bottom: 20px; }
      .nav-label { display: none; }
      .nav { display: block; margin-top: 0; }
    }

    @media (max-width: 600px) {
      .footer { flex-direction: column; }
    }
  </style>
</head>
<body>

  <div class="layout">

    <aside class="sidebar">
      <h1 class="brand">Student Dashboard</h1>
      <input type="checkbox" id="nav-toggle" class="nav-toggle">
      <label for="nav-toggle" class="nav-label">&#9776;</label>
      <nav class="nav">
        <ul>
            <li><a href="students.php">Students</a></li>
          <li><a href="dashboard.php">Dashboard</a></li>
          <li><a href="courses.php">Courses</a></li>
          <li><a href="assignments.php">Assignments</a></li>
          <li><a href="grades.php">Grades</a></li>
          <li><a href="schedule.php">Schedule</a></li>
          <li><a href="calculator.php">Calculator</a></li>
          <li><a href="counter.php" class="active">Character Counter</a></li>
          <li><a href="clock.php">Digital Clock</a></li>
          <li><a href="todo.php">To-Do List</a></li>
        </ul>
      </nav>
    </aside>

    <div class="main">

      <header class="header">
        <div class="header-title">
          <h2>Character Counter</h2>
          <p>Live word, character & sentence statistics</p>
        </div>
        <div class="header-actions">
          <button class="icon-btn" title="Notifications">
            &#128276;
            <span class="badge">3</span>
          </button>
          <div class="mini-profile">
            <div class="avatar-sm"><?= htmlspecialchars($userInitials) ?></div>
            <span><?= htmlspecialchars($userName) ?></span>
          </div>
          <a href="logout.php" class="logout-btn">Logout</a>
        </div>
      </header>

      <section class="panel">
        <h3>Type or paste your text below</h3>

        <textarea id="textInput" placeholder="Start typing to see live statistics..."></textarea>

        <div class="stats-grid">
          <div class="stat-box chars">
            <h4>Characters</h4>
            <div class="value" id="charCount">0</div>
          </div>
          <div class="stat-box words">
            <h4>Words</h4>
            <div class="value" id="wordCount">0</div>
          </div>
          <div class="stat-box sentences">
            <h4>Sentences</h4>
            <div class="value" id="sentenceCount">0</div>
          </div>
          <div class="stat-box paragraphs">
            <h4>Paragraphs</h4>
            <div class="value" id="paragraphCount">0</div>
          </div>
        </div>

        <div class="stats-grid" style="margin-top: 12px;">
          <div class="stat-box chars">
            <h4>Characters (no spaces)</h4>
            <div class="value" id="charNoSpace">0</div>
          </div>
          <div class="stat-box words">
            <h4>Reading Time</h4>
            <div class="value" id="readTime">0m</div>
          </div>
        </div>

        <div class="controls">
          <button class="btn" id="copyBtn">Copy Text</button>
          <button class="btn" id="upperBtn">UPPERCASE</button>
          <button class="btn" id="lowerBtn">lowercase</button>
          <button class="btn danger" id="clearBtn">Clear</button>
        </div>
      </section>

      <footer class="footer">
        <p>&copy; Assignment</p>
        <div class="social-links">
          <a href="#" class="social-btn whatsapp" title="WhatsApp">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
          </a>
          <a href="#" class="social-btn facebook" title="Facebook">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
          </a>
          <a href="#" class="social-btn youtube" title="YouTube">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M23.498 6.186a3.016 3.016 0 00-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 00.502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 002.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 002.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>
          </a>
          <a href="#" class="social-btn x" title="X">
            <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="currentColor"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
          </a>
          <a href="#" class="social-btn instagram" title="Instagram">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/></svg>
          </a>
        </div>
      </footer>

    </div>
  </div>

  <script>
    (function () {
      const input   = document.getElementById('textInput');
      const chars   = document.getElementById('charCount');
      const words   = document.getElementById('wordCount');
      const sents   = document.getElementById('sentenceCount');
      const paras   = document.getElementById('paragraphCount');
      const noSpace = document.getElementById('charNoSpace');
      const readTime= document.getElementById('readTime');

      const update = () => {
        const text = input.value;

        chars.textContent = text.length;
        noSpace.textContent = text.replace(/\s/g, '').length;

        const wordList = text.trim() === '' ? [] : text.trim().split(/\s+/);
        words.textContent = wordList.length;

        const sentenceList = text.split(/[.!?]+/).filter(s => s.trim().length > 0);
        sents.textContent = sentenceList.length;

        const paraList = text.split(/\n+/).filter(p => p.trim().length > 0);
        paras.textContent = paraList.length;

        const minutes = Math.ceil(wordList.length / 200);
        readTime.textContent = minutes + 'm';
      };

      input.addEventListener('input', update);

      document.getElementById('clearBtn').addEventListener('click', () => {
        input.value = '';
        update();
        input.focus();
      });

      document.getElementById('upperBtn').addEventListener('click', () => {
        input.value = input.value.toUpperCase();
        update();
      });

      document.getElementById('lowerBtn').addEventListener('click', () => {
        input.value = input.value.toLowerCase();
        update();
      });

      document.getElementById('copyBtn').addEventListener('click', async () => {
        try {
          await navigator.clipboard.writeText(input.value);
          const btn = document.getElementById('copyBtn');
          const original = btn.textContent;
          btn.textContent = 'Copied!';
          setTimeout(() => btn.textContent = original, 1500);
        } catch (err) {
          alert('Copy failed.');
        }
      });

      update();
    })();
  </script>

</body>
</html>