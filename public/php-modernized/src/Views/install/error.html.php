<?php
/**
 * Setup Wizard Error View
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Critical Error | FtoniNuke Setup</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-[#050505] text-gray-300 min-h-screen flex items-center justify-center p-6">
    <div class="max-w-xl w-full bg-red-950/20 border border-red-500/30 p-10 rounded-3xl text-center">
        <h1 class="text-2xl font-black text-red-500 mb-4">MIGRATION CRASHED</h1>
        <p class="text-sm text-red-400/80 mb-8"><?= $error ?? 'Unknown error' ?></p>
        <a href="?step=1" class="text-xs uppercase tracking-widest font-bold text-white hover:underline">Re-run System Check</a>
    </div>
</body>
</html>
