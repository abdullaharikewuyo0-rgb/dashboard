<?php require 'auth.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>To-Do List · Student Dashboard</title>
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

    .input-row {
      display: grid;
      grid-template-columns: 1fr;
      gap: 10px;
      margin-bottom: 20px;
    }
    @media (min-width: 640px) {
      .input-row {
        grid-template-columns: 2fr 1fr auto;
        align-items: center;
      }
    }

    .input-row input[type="text"],
    .input-row input[type="time"] {
      padding: 12px 14px;
      border: 2px solid gainsboro;
      border-radius: 8px;
      font-size: 15px;
      font-family: inherit;
      width: 100%;
      transition: border-color 0.2s, box-shadow 0.2s;
    }
    .input-row input:focus {
      outline: none;
      border-color: steelblue;
      box-shadow: 0 0 0 3px rgba(70, 130, 180, 0.2);
    }

    .add-btn {
      background: steelblue;
      color: white;
      border: none;
      padding: 12px 24px;
      border-radius: 8px;
      font-weight: bold;
      font-size: 14px;
      cursor: pointer;
      transition: background 0.3s, transform 0.2s;
      white-space: nowrap;
    }
    .add-btn:hover { background: darkslateblue; transform: translateY(-2px); }

    .stats-row {
      display: flex;
      justify-content: space-around;
      flex-wrap: wrap;
      gap: 12px;
      background: whitesmoke;
      border-radius: 10px;
      padding: 15px;
      margin-bottom: 20px;
    }
    .stats-row .stat { text-align: center; }
    .stats-row .stat .num {
      font-size: 22px;
      font-weight: bold;
      color: steelblue;
    }
    .stats-row .stat .lbl {
      font-size: 12px;
      color: gray;
      text-transform: uppercase;
      letter-spacing: 1px;
    }
    .stats-row .stat.done .num { color: seagreen; }
    .stats-row .stat.pending .num { color: darkorange; }

    .filter-row {
      display: flex;
      gap: 8px;
      margin-bottom: 15px;
      flex-wrap: wrap;
    }
    .filter-btn {
      background: whitesmoke;
      color: darkslategray;
      border: 2px solid transparent;
      padding: 6px 16px;
      border-radius: 20px;
      font-weight: bold;
      font-size: 13px;
      cursor: pointer;
      transition: all 0.2s;
    }
    .filter-btn:hover { background: gainsboro; }
    .filter-btn.active {
      background: steelblue;
      color: white;
    }

    .todo-list {
      list-style: none;
      display: grid;
      gap: 10px;
    }

    .todo-item {
      display: flex;
      align-items: center;
      gap: 12px;
      background: whitesmoke;
      padding: 12px 16px;
      border-radius: 8px;
      border-left: 4px solid steelblue;
      transition: transform 0.2s, background 0.2s, opacity 0.2s;
      animation: slideIn 0.3s ease;
    }
    @keyframes slideIn {
      from { opacity: 0; transform: translateX(-15px); }
      to   { opacity: 1; transform: translateX(0); }
    }
    .todo-item:hover { transform: translateX(12px); background: lightblue; }
    .todo-item.completed { opacity: 0.6; border-left-color: seagreen; }
    .todo-item.completed .todo-text {
      text-decoration: line-through;
      color: gray;
    }

    .todo-checkbox {
      width: 20px;
      height: 20px;
      accent-color: seagreen;
      cursor: pointer;
      flex-shrink: 0;
    }

    .todo-text {
      flex: 1;
      color: darkslategray;
      font-weight: bold;
      word-break: break-word;
      display: flex;
      flex-direction: column;
    }

    .todo-time {
      font-size: 12px;
      color: steelblue;
      font-weight: bold;
      display: inline-flex;
      align-items: center;
      gap: 4px;
      margin-top: 3px;
    }

    .todo-date {
      font-size: 11px;
      color: silver;
      display: block;
      font-weight: normal;
      margin-top: 2px;
    }

    .action-btn {
      width: 32px;
      height: 32px;
      border-radius: 50%;
      cursor: pointer;
      font-size: 15px;
      display: flex;
      align-items: center;
      justify-content: center;
      transition: background 0.2s, transform 0.2s;
      flex-shrink: 0;
      border: none;
      color: white;
    }
    .edit-btn { background: steelblue; }
    .edit-btn:hover { background: darkslateblue; transform: scale(1.15); }

    .delete-btn { background: crimson; }
    .delete-btn:hover { background: darkred; transform: scale(1.15); }

    .empty-state {
      text-align: center;
      color: silver;
      padding: 40px 20px;
      font-style: italic;
    }

    .list-actions {
      display: flex;
      justify-content: space-between;
      margin-top: 20px;
      flex-wrap: wrap;
      gap: 10px;
    }
    .btn-clear {
      background: crimson;
      color: white;
      border: none;
      padding: 10px 20px;
      border-radius: 8px;
      font-weight: bold;
      font-size: 13px;
      cursor: pointer;
      transition: background 0.3s, transform 0.2s;
    }
    .btn-clear:hover { background: darkred; transform: translateY(-2px); }

    .btn-clear-done {
      background: darkorange;
      color: white;
      border: none;
      padding: 10px 20px;
      border-radius: 8px;
      font-weight: bold;
      font-size: 13px;
      cursor: pointer;
      transition: background 0.3s, transform 0.2s;
    }
    .btn-clear-done:hover { background: orangered; transform: translateY(-5px); }

    .modal-overlay {
      position: fixed;
      inset: 0;
      background: rgba(0, 0, 0, 0.55);
      display: none;
      align-items: center;
      justify-content: center;
      z-index: 999;
      padding: 20px;
      animation: fadeIn 0.2s ease;
    }
    .modal-overlay.open { display: flex; }
    @keyframes fadeIn {
      from { opacity: 0; }
      to   { opacity: 1; }
    }

    .modal {
      background: white;
      border-radius: 12px;
      max-width: 620px;
      width: 100%;
      max-height: 90vh;
      display: flex;
      flex-direction: column;
      box-shadow: 0 20px 50px rgba(0, 0, 0, 0.35);
      animation: popIn 0.25s ease;
    }
    @keyframes popIn {
      from { transform: scale(0.92); opacity: 0; }
      to   { transform: scale(1); opacity: 1; }
    }

    .modal.confirm-modal { border-top: 6px solid crimson; }
    .modal.edit-modal    { border-top: 6px solid steelblue; }

    .modal-header {
      padding: 20px 24px 12px;
      border-bottom: 2px solid gainsboro;
    }
    .modal-header h3 {
      font-size: 20px;
      display: flex;
      align-items: center;
      gap: 10px;
    }
    .modal.confirm-modal .modal-header h3 { color: crimson; }
    .modal.edit-modal    .modal-header h3 { color: steelblue; }
    .modal-header p {
      color: gray;
      font-size: 13px;
      margin-top: 6px;
    }

    .modal-body {
      padding: 20px 24px;
      overflow-y: auto;
      flex: 1;
    }

    .form-group { margin-bottom: 18px; }
    .form-group label {
      display: block;
      font-size: 13px;
      font-weight: bold;
      color: darkslategray;
      margin-bottom: 6px;
      letter-spacing: 0.5px;
    }
    .form-group input {
      width: 100%;
      padding: 11px 14px;
      border: 2px solid gainsboro;
      border-radius: 8px;
      font-size: 15px;
      font-family: inherit;
      transition: border-color 0.2s, box-shadow 0.2s;
    }
    .form-group input:focus {
      outline: none;
      border-color: steelblue;
      box-shadow: 0 0 0 3px rgba(70, 130, 180, 0.2);
    }
    .form-group .field-error {
      color: crimson;
      font-size: 12px;
      margin-top: 5px;
      font-weight: bold;
      display: none;
    }
    .form-group.has-error input {
      border-color: crimson;
      background: #fff5f5;
    }
    .form-group.has-error .field-error { display: block; }

    .modal-table-wrapper {
      max-height: 320px;
      overflow-y: auto;
      border: 1px solid gainsboro;
      border-radius: 8px;
    }

    .modal-table {
      width: 100%;
      border-collapse: collapse;
      font-size: 13px;
    }
    .modal-table thead {
      background: darkslategray;
      color: white;
      position: sticky;
      top: 0;
    }
    .modal-table th, .modal-table td {
      padding: 9px 12px;
      text-align: left;
      border-bottom: 1px solid gainsboro;
    }
    .modal-table tbody tr:nth-child(even) { background: whitesmoke; }
    .modal-table tbody tr:last-child td { border-bottom: none; }
    .modal-table .idx { color: gray; width: 40px; text-align: center; }
    .modal-table .status-done    { color: seagreen;  font-weight: bold; }
    .modal-table .status-pending { color: darkorange; font-weight: bold; }

    .modal-footer {
      padding: 16px 24px 20px;
      border-top: 2px solid gainsboro;
      display: flex;
      justify-content: flex-end;
      gap: 10px;
      flex-wrap: wrap;
    }

    .modal-btn {
      padding: 10px 22px;
      border-radius: 8px;
      font-weight: bold;
      font-size: 14px;
      border: none;
      cursor: pointer;
      transition: background 0.25s, transform 0.2s;
    }
    .modal-btn.cancel {
      background: gainsboro;
      color: darkslategray;
    }
    .modal-btn.cancel:hover {
      background: silver;
      transform: translateY(-2px);
    }
    .modal-btn.confirm {
      background: crimson;
      color: white;
    }
    .modal-btn.confirm:hover {
      background: darkred;
      transform: translateY(-2px);
    }
    .modal-btn.save {
      background: steelblue;
      color: white;
    }
    .modal-btn.save:hover {
      background: darkslateblue;
      transform: translateY(-2px);
    }

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
      .modal-footer { justify-content: center; }
      .modal-btn { flex: 1; }
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
          <li><a href="counter.php">Character Counter</a></li>
          <li><a href="clock.php">Digital Clock</a></li>
          <li><a href="todo.php" class="active">To-Do List</a></li>
        </ul>
      </nav>
    </aside>

    <div class="main">

      <header class="header">
        <div class="header-title">
          <h2>To-Do List</h2>
          <p>Track your daily tasks & deadlines</p>
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
        <h3>My Tasks</h3>

        <div class="input-row">
          <input type="text" id="taskInput" placeholder="What needs to be done?" maxlength="120">
          <input type="time" id="taskTime" title="Optional time">
          <button class="add-btn" id="addBtn">+ Add Task</button>
        </div>

        <div class="stats-row">
          <div class="stat">
            <div class="num" id="totalCount">0</div>
            <div class="lbl">Total</div>
          </div>
          <div class="stat pending">
            <div class="num" id="pendingCount">0</div>
            <div class="lbl">Pending</div>
          </div>
          <div class="stat done">
            <div class="num" id="doneCount">0</div>
            <div class="lbl">Done</div>
          </div>
        </div>

        <div class="filter-row">
          <button class="filter-btn active" data-filter="all">All</button>
          <button class="filter-btn" data-filter="pending">Pending</button>
          <button class="filter-btn" data-filter="completed">Completed</button>
        </div>

        <ul class="todo-list" id="todoList"></ul>

        <div class="list-actions">
          <button class="btn-clear-done" id="clearDone">Clear Completed</button>
          <button class="btn-clear" id="clearAll">Clear All</button>
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

  <div class="modal-overlay" id="confirmAllModal">
    <div class="modal confirm-modal" role="dialog" aria-modal="true">
      <div class="modal-header">
        <h3>⚠️ Confirm Delete All Tasks</h3>
        <p id="confirmAllSubtitle">This action cannot be undone.</p>
      </div>

      <div class="modal-body">
        <p style="font-weight:bold; color:darkslategray; margin-bottom:10px;">
          Tasks to be deleted:
        </p>
        <div class="modal-table-wrapper">
          <table class="modal-table">
            <thead>
              <tr>
                <th class="idx">#</th>
                <th>Task</th>
                <th>Time</th>
                <th>Status</th>
              </tr>
            </thead>
            <tbody id="confirmAllTableBody"></tbody>
          </table>
        </div>
      </div>

      <div class="modal-footer">
        <button class="modal-btn cancel" id="confirmAllCancel">Cancel</button>
        <button class="modal-btn confirm" id="confirmAllConfirm">Yes, Delete All</button>
      </div>
    </div>
  </div>

  <div class="modal-overlay" id="confirmOneModal">
    <div class="modal confirm-modal" role="dialog" aria-modal="true">
      <div class="modal-header">
        <h3>⚠️ Confirm Delete Task</h3>
        <p>Are you sure you want to delete this task? This action cannot be undone.</p>
      </div>

      <div class="modal-body">
        <p style="font-weight:bold; color:darkslategray; margin-bottom:10px;">
          Task to be deleted:
        </p>
        <div class="modal-table-wrapper">
          <table class="modal-table">
            <thead>
              <tr>
                <th>Task</th>
                <th>Time</th>
                <th>Status</th>
              </tr>
            </thead>
            <tbody id="confirmOneTableBody"></tbody>
          </table>
        </div>
      </div>

      <div class="modal-footer">
        <button class="modal-btn cancel" id="confirmOneCancel">Cancel</button>
        <button class="modal-btn confirm" id="confirmOneConfirm">Yes, Delete</button>
      </div>
    </div>
  </div>

  <div class="modal-overlay" id="editModal">
    <div class="modal edit-modal" role="dialog" aria-modal="true">
      <div class="modal-header">
        <h3>✏️ Edit Task</h3>
        <p>Modify the task text or time, then click Save Changes.</p>
      </div>

      <div class="modal-body">
        <div class="form-group" id="editTextGroup">
          <label for="editTaskText">Task</label>
          <input type="text" id="editTaskText" maxlength="120" placeholder="Task description">
          <div class="field-error">Task cannot be empty.</div>
        </div>

        <div class="form-group">
          <label for="editTaskTime">Time (optional)</label>
          <input type="time" id="editTaskTime">
        </div>
      </div>

      <div class="modal-footer">
        <button class="modal-btn cancel" id="editCancel">Cancel</button>
        <button class="modal-btn save" id="editSave"> Save Changes</button>
      </div>
    </div>
  </div>

  <script>
    (function () {
      const STORAGE_KEY = 'student_dashboard_todos';
      const input       = document.getElementById('taskInput');
      const timeInput   = document.getElementById('taskTime');
      const addBtn      = document.getElementById('addBtn');
      const list        = document.getElementById('todoList');
      const totalCount  = document.getElementById('totalCount');
      const pendingCount= document.getElementById('pendingCount');
      const doneCount   = document.getElementById('doneCount');
      const clearAll    = document.getElementById('clearAll');
      const clearDone   = document.getElementById('clearDone');

      const confirmAllModal    = document.getElementById('confirmAllModal');
      const confirmAllTableBody= document.getElementById('confirmAllTableBody');
      const confirmAllCancel   = document.getElementById('confirmAllCancel');
      const confirmAllConfirm  = document.getElementById('confirmAllConfirm');
      const confirmAllSubtitle = document.getElementById('confirmAllSubtitle');

      const confirmOneModal    = document.getElementById('confirmOneModal');
      const confirmOneTableBody= document.getElementById('confirmOneTableBody');
      const confirmOneCancel   = document.getElementById('confirmOneCancel');
      const confirmOneConfirm  = document.getElementById('confirmOneConfirm');

      const editModal     = document.getElementById('editModal');
      const editTaskText  = document.getElementById('editTaskText');
      const editTaskTime  = document.getElementById('editTaskTime');
      const editTextGroup = document.getElementById('editTextGroup');
      const editCancel    = document.getElementById('editCancel');
      const editSave      = document.getElementById('editSave');

      let todos         = [];
      let filter        = 'all';
      let pendingDelete = null;
      let pendingEdit   = null;

      const load = () => {
        try {
          todos = JSON.parse(localStorage.getItem(STORAGE_KEY)) || [];
        } catch (e) {
          todos = [];
        }
      };

      const save = () => {
        localStorage.setItem(STORAGE_KEY, JSON.stringify(todos));
      };

      const escapeHtml = (str) => {
        const div = document.createElement('div');
        div.textContent = str;
        return div.innerHTML;
      };

      const formatDate = (ts) => {
        const d = new Date(ts);
        return d.toLocaleDateString() + ' ' + d.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
      };

      const hourPad = (n) => String(n).padStart(2, '0');
      const minPad  = (n) => String(n).padStart(2, '0');

      const formatTime = (timeStr) => {
        if (!timeStr) return '';
        const [h, m] = timeStr.split(':').map(Number);
        const period = h >= 12 ? 'PM' : 'AM';
        const hour12 = h % 12 || 12;
        return `⏰ ${hourPad(hour12)}:${minPad(m)} ${period}`;
      };

      const render = () => {
        list.innerHTML = '';

        const filtered = todos.filter(t => {
          if (filter === 'pending')   return !t.completed;
          if (filter === 'completed') return t.completed;
          return true;
        });

        if (filtered.length === 0) {
          list.innerHTML = '<li class="empty-state">No tasks to show</li>';
        } else {
          filtered.forEach(todo => {
            const li = document.createElement('li');
            li.className = 'todo-item' + (todo.completed ? ' completed' : '');
            li.innerHTML = `
              <input type="checkbox" class="todo-checkbox" ${todo.completed ? 'checked' : ''} data-id="${todo.id}">
              <div class="todo-text">
                ${escapeHtml(todo.text)}
                ${todo.time ? `<span class="todo-time">${formatTime(todo.time)}</span>` : ''}
                <span class="todo-date">${formatDate(todo.createdAt)}</span>
              </div>
              <button class="action-btn edit-btn" data-id="${todo.id}" title="Edit">✏️</button>
              <button class="action-btn delete-btn" data-id="${todo.id}" title="Delete">×</button>
            `;
            list.appendChild(li);
          });
        }

        const total   = todos.length;
        const done    = todos.filter(t => t.completed).length;
        const pending = total - done;

        totalCount.textContent   = total;
        pendingCount.textContent = pending;
        doneCount.textContent    = done;
      };

      const renderConfirmAllTable = () => {
        confirmAllTableBody.innerHTML = '';

        if (todos.length === 0) {
          confirmAllTableBody.innerHTML = '<tr><td colspan="4" style="text-align:center; color:silver; padding:20px;">No tasks</td></tr>';
          return;
        }

        todos.forEach((todo, i) => {
          const tr = document.createElement('tr');

          const tdIdx = document.createElement('td');
          tdIdx.className = 'idx';
          tdIdx.textContent = i + 1;

          const tdText = document.createElement('td');
          tdText.textContent = todo.text;

          const tdTime = document.createElement('td');
          tdTime.textContent = todo.time ? todo.time : '—';

          const tdStatus = document.createElement('td');
          tdStatus.textContent = todo.completed ? 'Completed' : 'Pending';
          tdStatus.className = todo.completed ? 'status-done' : 'status-pending';

          tr.appendChild(tdIdx);
          tr.appendChild(tdText);
          tr.appendChild(tdTime);
          tr.appendChild(tdStatus);
          confirmAllTableBody.appendChild(tr);
        });
      };

      const openConfirmAll = () => {
        renderConfirmAllTable();
        confirmAllSubtitle.textContent =
          `You are about to permanently delete ${todos.length} task${todos.length === 1 ? '' : 's'}. This action cannot be undone.`;
        confirmAllModal.classList.add('open');
        document.body.style.overflow = 'hidden';
      };

      const closeConfirmAll = () => {
        confirmAllModal.classList.remove('open');
        document.body.style.overflow = '';
      };

      const openConfirmOne = (id) => {
        const todo = todos.find(t => t.id === id);
        if (!todo) return;

        pendingDelete = id;
        confirmOneTableBody.innerHTML = '';

        const tr = document.createElement('tr');

        const tdText = document.createElement('td');
        tdText.textContent = todo.text;

        const tdTime = document.createElement('td');
        tdTime.textContent = todo.time ? todo.time : '—';

        const tdStatus = document.createElement('td');
        tdStatus.textContent = todo.completed ? 'Completed' : 'Pending';
        tdStatus.className = todo.completed ? 'status-done' : 'status-pending';

        tr.appendChild(tdText);
        tr.appendChild(tdTime);
        tr.appendChild(tdStatus);
        confirmOneTableBody.appendChild(tr);

        confirmOneModal.classList.add('open');
        document.body.style.overflow = 'hidden';
      };

      const closeConfirmOne = () => {
        confirmOneModal.classList.remove('open');
        document.body.style.overflow = '';
        pendingDelete = null;
      };

      const openEdit = (id) => {
        const todo = todos.find(t => t.id === id);
        if (!todo) return;

        pendingEdit = id;
        editTaskText.value = todo.text;
        editTaskTime.value = todo.time || '';
        editTextGroup.classList.remove('has-error');

        editModal.classList.add('open');
        document.body.style.overflow = 'hidden';
        setTimeout(() => editTaskText.focus(), 100);
      };

      const closeEdit = () => {
        editModal.classList.remove('open');
        document.body.style.overflow = '';
        pendingEdit = null;
      };

      const addTask = () => {
        const text = input.value.trim();
        if (text === '') {
          input.focus();
          return;
        }
        todos.unshift({
          id: Date.now() + Math.random(),
          text,
          time: timeInput.value || '',
          completed: false,
          createdAt: Date.now(),
        });
        input.value = '';
        timeInput.value = '';
        save();
        render();
        input.focus();
      };

      addBtn.addEventListener('click', addTask);
      input.addEventListener('keydown', (e) => {
        if (e.key === 'Enter') addTask();
      });
      timeInput.addEventListener('keydown', (e) => {
        if (e.key === 'Enter') addTask();
      });

      list.addEventListener('change', (e) => {
        if (!e.target.classList.contains('todo-checkbox')) return;
        const id = parseFloat(e.target.dataset.id);
        const todo = todos.find(t => t.id === id);
        if (todo) {
          todo.completed = e.target.checked;
          save();
          render();
        }
      });

      list.addEventListener('click', (e) => {
        const editBtn = e.target.closest('.edit-btn');
        if (editBtn) {
          openEdit(parseFloat(editBtn.dataset.id));
          return;
        }

        const delBtn = e.target.closest('.delete-btn');
        if (delBtn) {
          openConfirmOne(parseFloat(delBtn.dataset.id));
          return;
        }
      });

      document.querySelectorAll('.filter-btn').forEach(btn => {
        btn.addEventListener('click', () => {
          filter = btn.dataset.filter;
          document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
          btn.classList.add('active');
          render();
        });
      });

      clearAll.addEventListener('click', () => {
        if (todos.length === 0) return;
        openConfirmAll();
      });
      confirmAllCancel.addEventListener('click', closeConfirmAll);
      confirmAllConfirm.addEventListener('click', () => {
        todos = [];
        save();
        render();
        closeConfirmAll();
      });
      confirmAllModal.addEventListener('click', (e) => {
        if (e.target === confirmAllModal) closeConfirmAll();
      });

      confirmOneCancel.addEventListener('click', closeConfirmOne);
      confirmOneConfirm.addEventListener('click', () => {
        if (pendingDelete !== null) {
          todos = todos.filter(t => t.id !== pendingDelete);
          save();
          render();
        }
        closeConfirmOne();
      });
      confirmOneModal.addEventListener('click', (e) => {
        if (e.target === confirmOneModal) closeConfirmOne();
      });

      editCancel.addEventListener('click', closeEdit);
      editSave.addEventListener('click', () => {
        const newText = editTaskText.value.trim();
        if (newText === '') {
          editTextGroup.classList.add('has-error');
          editTaskText.focus();
          return;
        }
        const todo = todos.find(t => t.id === pendingEdit);
        if (todo) {
          todo.text = newText;
          todo.time = editTaskTime.value || '';
          save();
          render();
        }
        closeEdit();
      });
      editTaskText.addEventListener('input', () => {
        if (editTaskText.value.trim() !== '') {
          editTextGroup.classList.remove('has-error');
        }
      });
      editTaskText.addEventListener('keydown', (e) => {
        if (e.key === 'Enter') editSave.click();
      });
      editTaskTime.addEventListener('keydown', (e) => {
        if (e.key === 'Enter') editSave.click();
      });
      editModal.addEventListener('click', (e) => {
        if (e.target === editModal) closeEdit();
      });

      document.addEventListener('keydown', (e) => {
        if (e.key !== 'Escape') return;
        if (confirmAllModal.classList.contains('open')) closeConfirmAll();
        if (confirmOneModal.classList.contains('open')) closeConfirmOne();
        if (editModal.classList.contains('open'))       closeEdit();
      });

      clearDone.addEventListener('click', () => {
        const hadCompleted = todos.some(t => t.completed);
        if (!hadCompleted) return;
        todos = todos.filter(t => !t.completed);
        save();
        render();
      });

      load();
      render();
    })();
  </script>

</body>
</html>