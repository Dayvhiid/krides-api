<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Email Verified</title>
  <style>
    :root {
      --primary: #1173d4;
      --background-light: #f6f7f8;
      --background-dark: #101922;
      --text-light: #ffffff;
      --text-dark: #1a1a1a;
    }

    body {
      margin: 0;
      font-family: system-ui, -apple-system, "Segoe UI", Roboto, sans-serif;
      background: var(--background-light);
      color: var(--text-dark);
      min-height: 100vh;
      display: flex;
      flex-direction: column;
    }

    header {
      padding: 1rem;
      text-align: center;
      font-weight: bold;
      font-size: 1.2rem;
    }

    main {
      flex-grow: 1;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      text-align: center;
      padding: 1rem;
    }

    .icon-circle {
      width: 80px;
      height: 80px;
      background: rgba(17, 115, 212, 0.1);
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      margin-bottom: 1.5rem;
    }

    .icon-circle svg {
      width: 48px;
      height: 48px;
      fill: var(--primary);
    }

    h2 {
      font-size: 1.8rem;
      margin: 0 0 0.5rem;
    }

    p {
      max-width: 320px;
      font-size: 0.95rem;
      color: #555;
    }

    footer {
      padding: 1rem;
    }

    button {
      width: 100%;
      max-width: 400px;
      background: var(--primary);
      color: var(--text-light);
      font-weight: bold;
      border: none;
      border-radius: 0.5rem;
      padding: 0.9rem 1.2rem;
      font-size: 1rem;
      cursor: pointer;
      transition: background 0.2s ease;
    }

    button:hover {
      background: #0f66bb;
    }
  </style>
</head>
<body>
  <header>
    Verification
  </header>

  <main>
    <div class="icon-circle">
      <!-- ✅ Checkmark Icon (SVG, no external fonts) -->
      <svg viewBox="0 0 24 24">
        <path d="M9 16.2l-3.5-3.5 1.4-1.4L9 13.4l8.1-8.1 1.4 1.4z"/>
      </svg>
    </div>
    <h2>Email Verified!</h2>
    <p>Your email has been successfully verified. You can now continue to use the app and enjoy your rides.</p>
  </main>

  {{-- <footer>
    <button>Continue on the app</button>
  </footer> --}}
</body>
</html>
