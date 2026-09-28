<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?? 'FtoniNuke Modernization' ?></title>
    <!-- Modern Assets managed by AssetManager -->
    <?= $this->assetManager.renderStyles() ?>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: 'Inter', sans-serif; }
        .sidebar { min-width: 280px; }
    </style>
</head>
<body class="flex flex-col min-h-screen bg-gray-50 dark:bg-gray-950 dark:text-gray-200">

    <!-- Header Navigation -->
    <header class="bg-white dark:bg-gray-900 border-b border-gray-200 dark:border-gray-800 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 bg-blue-600 rounded-lg"></div>
                    <span class="text-xl font-black tracking-tighter uppercase italic">Ftoni<span class="text-blue-600">Nuke</span></span>
                </div>
                <nav class="hidden md:flex space-x-8 text-sm font-medium">
                    <a href="/public/" class="text-blue-600">Home</a>
                    <a href="/news" class="hover:text-blue-600">News</a>
                    <a href="/forum" class="hover:text-blue-600">Forum</a>
                    <a href="/downloads" class="hover:text-blue-600">Files</a>
                </nav>
                <div class="flex items-center gap-4">
                    <button class="p-2 text-gray-400 hover:text-gray-500">
                        <i data-lucide="search" class="w-5 h-5"></i>
                    </button>
                    <a href="/your-account" class="p-2 bg-gray-100 dark:bg-gray-800 rounded-full">
                        <i data-lucide="user" class="w-5 h-5"></i>
                    </a>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content Grid -->
    <main class="flex-grow max-w-7xl mx-auto w-full px-4 sm:px-6 lg:px-8 py-10">
        <div class="flex flex-col lg:flex-row gap-10">
            
            <!-- Left Sidebar -->
            <?php if (!empty($leftSidebar)): ?>
                <aside class="sidebar lg:w-72 order-2 lg:order-1 flex flex-col">
                    <?= $leftSidebar ?>
                </aside>
            <?php endif; ?>

            <!-- Main Dynamic Content -->
            <section class="flex-grow order-1 lg:order-2 space-y-10">
                <?= $content ?>
            </section>

            <!-- Right Sidebar -->
            <?php if (!empty($rightSidebar)): ?>
                <aside class="sidebar lg:w-72 order-3 flex flex-col">
                    <?= $rightSidebar ?>
                </aside>
            <?php endif; ?>

        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-white dark:bg-gray-900 border-t border-gray-200 dark:border-gray-800 py-12 mt-auto">
        <div class="max-w-7xl mx-auto px-4 text-center">
            <p class="text-gray-500 text-sm">
                &copy; <?= date('Y') ?> FtoniNuke v2.0 - Powered by Modern PHP 8.3 Architecture
            </p>
        </div>
    </footer>

    <!-- Scripts -->
    <?= $this->assetManager.renderScripts() ?>
    <script>
        lucide.createIcons();
    </script>
</body>
</html>
