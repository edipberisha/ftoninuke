<?php
/**
 * Setup Wizard: Step 5 - Victory & Deployment
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= __('install_success') ?> | FtoniNuke v1.4</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&display=swap" rel="stylesheet">
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="bg-[#050505] text-gray-300 min-h-screen flex items-center justify-center p-6">
    <div class="max-w-xl w-full">
        <div class="bg-gray-900 border border-gray-800 rounded-3xl p-12 shadow-2xl text-center relative overflow-hidden">
            <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-[#5eff00] to-cyan-400"></div>
            
            <div class="w-24 h-24 bg-[#5eff00]/10 rounded-full flex items-center justify-center mx-auto mb-8 shadow-[0_0_40px_rgba(94,255,0,0.1)]">
                <svg class="w-12 h-12 text-[#5eff00]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
            </div>

            <h2 class="text-3xl font-black text-white italic tracking-tighter mb-4">FtoniNuke <span class="text-[#5eff00]">v1.4</span></h2>
            <p class="text-sm text-gray-500 mb-10 leading-relaxed max-w-sm mx-auto"><?= __('install_success') ?></p>

            <div class="grid grid-cols-2 gap-4">
                <a href="/public/" class="bg-gray-800 hover:bg-gray-700 text-white font-bold py-4 rounded-2xl transition-all block text-sm uppercase tracking-widest">
                    <?= __('go_to_front') ?>
                </a>
                <a href="/public/admin" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-4 rounded-2xl transition-all block text-sm uppercase tracking-widest shadow-lg shadow-blue-600/20">
                    <?= __('enter_cockpit') ?>
                </a>
            </div>
            
            <div class="mt-12 pt-10 border-t border-gray-800/50">
                 <p class="text-[10px] text-gray-700 uppercase tracking-widest">Architected by <span class="text-gray-500 font-bold">Ftoni Tech</span> &copy; 2026</p>
            </div>
        </div>
    </div>
</body>
</html>
