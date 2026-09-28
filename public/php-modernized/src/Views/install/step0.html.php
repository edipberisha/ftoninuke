<?php
/**
 * Setup Wizard: Step 0 - Language Selection
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Select Language | FtoniNuke v1.4</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&display=swap" rel="stylesheet">
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="bg-[#050505] text-gray-300 min-h-screen flex items-center justify-center p-6">
    <div class="max-w-xl w-full">
        <!-- Progress Header -->
        <div class="mb-12 text-center">
            <h1 class="text-4xl font-black text-white italic tracking-tighter mb-2">Ftoni<span class="text-blue-600">Nuke</span></h1>
            <p class="text-xs text-gray-500 uppercase tracking-[0.3em]">Magic Setup Wizard v1.4</p>
        </div>

        <div class="bg-gray-900 border border-gray-800 rounded-3xl p-10 shadow-2xl relative overflow-hidden">
            <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-blue-600 to-cyan-400 opacity-20"></div>
            
            <h2 class="text-xl font-bold text-white mb-8 flex items-center gap-3">
                <span class="w-8 h-8 bg-blue-600/20 text-blue-500 rounded-full flex items-center justify-center text-sm">00</span>
                Choose Your Language
            </h2>

            <form action="?step=0" method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <?php foreach ($languages as $code => $name): ?>
                    <label class="relative group cursor-pointer">
                        <input type="radio" name="locale" value="<?= $code ?>" class="peer hidden" <?= $code === 'tr' ? 'checked' : '' ?>>
                        <div class="p-4 bg-gray-950 border border-gray-800 rounded-xl transition-all peer-checked:border-blue-600 peer-checked:bg-blue-600/5 group-hover:border-gray-600">
                            <div class="flex items-center justify-between">
                                <span class="text-sm font-bold tracking-wide"><?= $name ?></span>
                                <div class="w-4 h-4 rounded-full border-2 border-gray-800 flex items-center justify-center peer-checked:border-blue-600">
                                    <div class="w-2 h-2 rounded-full bg-blue-600 opacity-0 transition-opacity peer-checked:opacity-100"></div>
                                </div>
                            </div>
                            <span class="text-[10px] text-gray-600 uppercase tracking-widest mt-1 block"><?= strtoupper($code) ?></span>
                        </div>
                    </label>
                <?php endforeach; ?>

                <div class="md:col-span-2 mt-8">
                    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-4 rounded-2xl transition-all shadow-lg shadow-blue-600/20">
                        Proceed with Selected Language
                    </button>
                </div>
            </form>
        </div>
    </div>
    
    <style>
        input:checked + div {
            border-color: #2563eb !important;
            background-color: rgba(37, 99, 235, 0.05) !important;
        }
        input:checked + div div div {
            opacity: 1 !important;
        }
    </style>
</body>
</html>
