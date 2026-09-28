<?php
/**
 * Setup Wizard: Step 2 - Database Connection
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
            
            <h2 class="text-xl font-bold text-white mb-8 flex items-center gap-3">
                <span class="w-8 h-8 bg-blue-600/20 text-blue-500 rounded-full flex items-center justify-center text-sm">02</span>
                <?= __('db_setup') ?>
            </h2>

            <?php if (isset($error)): ?>
                <div class="bg-red-500/10 border border-red-500/20 p-4 rounded-xl mb-6 flex items-center gap-3">
                     <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                     <p class="text-xs text-red-400 font-medium"><?= $error ?></p>
                </div>
            <?php endif; ?>

            <form action="?step=2" method="POST" class="space-y-6">
                <div>
                    <label class="block text-[10px] uppercase tracking-widest text-gray-500 mb-2 font-bold"><?= __('db_host') ?></label>
                    <input type="text" name="db_host" value="database-server-db-1" class="w-full bg-gray-950 border border-gray-800 rounded-xl px-4 py-3 text-sm focus:border-blue-600 focus:ring-0 outline-none transition-all" required>
                </div>
                <div>
                    <label class="block text-[10px] uppercase tracking-widest text-gray-500 mb-2 font-bold"><?= __('db_name') ?></label>
                    <input type="text" name="db_name" placeholder="e.g. ftoni_nuke_db" class="w-full bg-gray-950 border border-gray-800 rounded-xl px-4 py-3 text-sm focus:border-blue-600 focus:ring-0 outline-none transition-all" required>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[10px] uppercase tracking-widest text-gray-500 mb-2 font-bold"><?= __('db_user') ?></label>
                        <input type="text" name="db_user" placeholder="root" class="w-full bg-gray-950 border border-gray-800 rounded-xl px-4 py-3 text-sm focus:border-blue-600 focus:ring-0 outline-none transition-all" required>
                    </div>
                    <div>
                        <label class="block text-[10px] uppercase tracking-widest text-gray-500 mb-2 font-bold"><?= __('db_pass') ?></label>
                        <input type="password" name="db_pass" class="w-full bg-gray-950 border border-gray-800 rounded-xl px-4 py-3 text-sm focus:border-blue-600 focus:ring-0 outline-none transition-all">
                    </div>
                </div>

                <button type="submit" class="w-full mt-8 bg-blue-600 hover:bg-blue-700 text-white font-bold py-4 rounded-2xl transition-all shadow-lg shadow-blue-600/20">
                    <?= __('db_verify_save') ?>
                </button>
            </form>
        </div>
    </div>
</body>
</html>
