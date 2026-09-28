<?php
/**
 * Setup Wizard: Step 1 - System Analysis
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
        <!-- Progress Header -->
        <div class="mb-12 text-center">
            <h1 class="text-4xl font-black text-white italic tracking-tighter mb-2">Ftoni<span class="text-blue-600">Nuke</span></h1>
            <p class="text-xs text-gray-500 uppercase tracking-[0.3em]"><?= __('setup_wizard') ?></p>
        </div>

        <div class="bg-gray-900 border border-gray-800 rounded-3xl p-10 shadow-2xl relative overflow-hidden">
            <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-blue-600 to-cyan-400 opacity-20"></div>
            
            <h2 class="text-xl font-bold text-white mb-8 flex items-center gap-3">
                <span class="w-8 h-8 bg-blue-600/20 text-blue-500 rounded-full flex items-center justify-center text-sm">01</span>
                <?= __('system_check') ?>
            </h2>

            <div class="space-y-4 mb-10">
                <?php foreach ($checks as $key => $status): ?>
                    <div class="flex items-center justify-between p-4 bg-gray-950/50 rounded-xl border border-gray-800/50">
                        <span class="text-sm font-medium capitalize"><?= str_replace('_', ' ', $key) ?></span>
                        <?php if ($status): ?>
                            <span class="text-green-500 text-xs font-bold uppercase tracking-widest flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                <?= __('ready') ?>
                            </span>
                        <?php else: ?>
                            <span class="text-red-500 text-xs font-bold uppercase tracking-widest flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"></path></svg>
                                <?= __('failed') ?>
                            </span>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>

            <?php if ($allGood): ?>
                <div class="bg-green-500/10 border border-green-500/20 p-4 rounded-xl mb-10">
                     <p class="text-xs text-green-400 leading-relaxed font-medium"><?= __('pre_flight_msg') ?></p>
                </div>
                <a href="?step=2" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-4 rounded-2xl transition-all block text-center shadow-lg shadow-blue-600/20">
                    <?= __('next') ?>
                </a>
            <?php else: ?>
                <div class="bg-red-500/10 border border-red-500/20 p-4 rounded-xl mb-10">
                     <p class="text-xs text-red-400 leading-relaxed font-medium">Critical issues detected. Please check folder permissions (CHMOD 775/777) and mandatory PHP extensions.</p>
                </div>
                <button onclick="window.location.reload()" class="w-full bg-gray-800 text-gray-400 font-bold py-4 rounded-2xl cursor-not-allowed">
                    Fix and Re-scan
                </button>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
