<?php
/**
 * Setup Wizard: Step 4 - Commander-in-Chief Setup
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= __('installer_title') ?> | FtoniNuke v1.4</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&display=swap" rel="stylesheet">
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="bg-[#050505] text-gray-300 min-h-screen flex items-center justify-center p-6">
    <div class="max-w-xl w-full">
        <div class="mb-12 text-center">
            <h1 class="text-4xl font-black text-white italic tracking-tighter mb-2">Ftoni<span class="text-blue-600">Nuke</span></h1>
            <p class="text-xs text-gray-500 uppercase tracking-[0.3em]"><?= __('setup_wizard') ?></p>
        </div>

        <div class="bg-gray-900 border border-gray-800 rounded-3xl p-10 shadow-2xl relative overflow-hidden">
            <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-blue-600 to-cyan-400 opacity-20"></div>
            
            <h2 class="text-xl font-bold text-white mb-2 flex items-center gap-3">
                <span class="w-8 h-8 bg-blue-600/20 text-blue-500 rounded-full flex items-center justify-center text-sm">04</span>
                <?= __('admin_setup') ?>
            </h2>

            <form action="?step=4" method="POST" class="space-y-6 mt-8">
                 <div>
                    <label class="block text-[10px] uppercase tracking-widest text-gray-500 mb-2 font-bold"><?= __('site_name') ?></label>
                    <input type="text" name="sitename" value="FtoniNuke Ecosystem" class="w-full bg-gray-950 border border-gray-800 rounded-xl px-4 py-3 text-sm focus:border-blue-600 outline-none transition-all" required>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[10px] uppercase tracking-widest text-gray-500 mb-2 font-bold"><?= __('admin_username') ?></label>
                        <input type="text" name="username" placeholder="admin" class="w-full bg-gray-950 border border-gray-800 rounded-xl px-4 py-3 text-sm focus:border-blue-600 outline-none transition-all" required>
                    </div>
                    <div>
                        <label class="block text-[10px] uppercase tracking-widest text-gray-500 mb-2 font-bold"><?= __('admin_email') ?></label>
                        <input type="email" name="email" placeholder="admin@domain.com" class="w-full bg-gray-950 border border-gray-800 rounded-xl px-4 py-3 text-sm focus:border-blue-600 outline-none transition-all" required>
                    </div>
                </div>
                <div>
                    <label class="block text-[10px] uppercase tracking-widest text-gray-500 mb-2 font-bold"><?= __('admin_password') ?></label>
                    <input type="password" name="password" class="w-full bg-gray-950 border border-gray-800 rounded-xl px-4 py-3 text-sm focus:border-blue-600 outline-none transition-all" required>
                </div>

                <button type="submit" class="w-full mt-10 bg-[#5eff00] hover:bg-[#4edb00] text-black font-black py-4 rounded-2xl transition-all shadow-lg shadow-[#5eff00]/10 uppercase tracking-widest italic">
                    <?= __('finish') ?>
                </button>
            </form>
        </div>
    </div>
</body>
</html>
