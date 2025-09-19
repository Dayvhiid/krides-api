<!DOCTYPE html>
<html lang="en"><head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>Email Verified</title>
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<link href="https://fonts.googleapis.com" rel="preconnect"/>
<link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect"/>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;700;800&amp;display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet"/>
<script>
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "primary": "#1173d4",
                        "background-light": "#f6f7f8",
                        "background-dark": "#101922",
                    },
                    fontFamily: {
                        "display": ["Plus Jakarta Sans"]
                    },
                    borderRadius: {
                        "DEFAULT": "0.25rem",
                        "lg": "0.5rem",
                        "xl": "0.75rem",
                        "full": "9999px"
                    },
                },
            },
        }
    </script>
<style>
    body {
      min-height: max(884px, 100dvh);
    }
  </style>
  </head>
<body class="bg-background-light dark:bg-background-dark font-display">
<div class="flex flex-col h-screen">
<header class="flex-shrink-0">
<div class="p-4">
<h1 class="text-lg font-bold text-center text-gray-900 dark:text-white">Verification</h1>
</div>
</header>
<main class="flex-grow flex flex-col items-center justify-center text-center px-6">
<div class="w-20 h-20 bg-primary/10 dark:bg-primary/20 rounded-full flex items-center justify-center mb-6">
<span class="material-symbols-outlined text-primary text-4xl">
                    check_circle
                </span>
</div>
<h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">Email Verified!</h2>
<p class="text-gray-600 dark:text-gray-300 max-w-sm">
                Your email has been successfully verified. You can now continue to use the app and enjoy your rides.
            </p>
</main>
<footer class="flex-shrink-0 p-4">
<button class="w-full bg-primary text-white font-bold py-3 px-5 rounded-lg hover:bg-primary/90 transition-colors">
                Continue on the app
            </button>
</footer>
</div>

</body></html>