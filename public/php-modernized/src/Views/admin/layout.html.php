<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars($pageTitle ?? 'Admin') ?> — FtoniNuke</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: -apple-system, system-ui, sans-serif; }
    </style>
</head>
<body class="bg-gray-950 text-gray-300">
    <div class="flex min-h-screen">
        <!-- Sidebar -->
        <aside class="w-64 bg-gray-900 border-r border-gray-800 p-5 flex-shrink-0">
            <h2 class="text-white text-lg font-black mb-6 tracking-tight">🚀 FtoniNuke</h2>

            <nav class="space-y-1">
                <a href="/public/admin"
                   class="block px-4 py-2 rounded-lg text-sm font-medium transition <?= ($active ?? '') === 'dashboard' ? 'bg-blue-600 text-white' : 'text-gray-400 hover:bg-gray-800 hover:text-white' ?>">
                    📊 Dashboard
                </a>
                <a href="/public/admin/media"
                   class="block px-4 py-2 rounded-lg text-sm font-medium transition <?= ($active ?? '') === 'media' ? 'bg-blue-600 text-white' : 'text-gray-400 hover:bg-gray-800 hover:text-white' ?>">
                    🖼️ Medya
                </a>
                <a href="/public/admin/themes"
                   class="block px-4 py-2 rounded-lg text-sm font-medium transition <?= ($active ?? '') === 'themes' ? 'bg-blue-600 text-white' : 'text-gray-400 hover:bg-gray-800 hover:text-white' ?>">
                    🎨 Temalar
                </a>
                <a href="/public/admin/blocks"
                   class="block px-4 py-2 rounded-lg text-sm font-medium transition <?= ($active ?? '') === 'blocks' ? 'bg-blue-600 text-white' : 'text-gray-400 hover:bg-gray-800 hover:text-white' ?>">
                    🧱 Bloklar
                </a>
				<a href="/public/admin/menu"
                   class="block px-4 py-2 rounded-lg text-sm font-medium transition <?= ($active ?? '') === 'menu' ? 'bg-blue-600 text-white' : 'text-gray-400 hover:bg-gray-800 hover:text-white' ?>">
                    🧭 Menü
                </a>
				<a href="/public/admin/comments"
                    class="block px-4 py-2 rounded-lg text-sm font-medium transition <?= ($active ?? '') === 'comments' ? 'bg-blue-600 text-white' : 'text-gray-400 hover:bg-gray-800 hover:text-white' ?> flex justify-between items-center">
                   <span>💬 Yorumlar</span>
                  <?php
                      try {
                   $pendingCount = (new \FtoniNuke\Repository\CommentRepository())->countPending();
                   if ($pendingCount > 0) {
                     echo '<span class="bg-yellow-500 text-black text-xs font-bold px-2 rounded-full">' . $pendingCount . '</span>';
                   }
                   } catch (\Throwable $e) {}
                   ?>
                </a>
				
				<a href="/public/admin/pages"
                class="block px-4 py-2 rounded-lg text-sm font-medium transition <?= ($active ?? '') === 'pages' ? 'bg-blue-600 text-white' : 'text-gray-400 hover:bg-gray-800 hover:text-white' ?>">
                📄 Sayfalar
                </a>
                <a href="/public/admin/messages"
                  class="block px-4 py-2 rounded-lg text-sm font-medium transition <?= ($active ?? '') === 'messages' ? 'bg-blue-600 text-white' : 'text-gray-400 hover:bg-gray-800 hover:text-white' ?> flex justify-between items-center">
                <span>📬 Mesajlar</span>
                <?php
                try {
                 $unread = (new \FtoniNuke\Repository\PageRepository())->countUnreadMessages();
                 if ($unread > 0) {
                  echo '<span class="bg-yellow-500 text-black text-xs font-bold px-2 rounded-full">' . $unread . '</span>';
                 }
                 } catch (\Throwable $e) {}
                 ?>
                 </a>
            </nav>

            <div class="mt-8 pt-4 border-t border-gray-800 space-y-1">
                <a href="/public/" class="block px-4 py-2 rounded-lg text-sm text-gray-400 hover:bg-gray-800 hover:text-white">
                    🏠 Siteye Dön
                </a>
                <a href="/public/logout" class="block px-4 py-2 rounded-lg text-sm text-red-400 hover:bg-red-500/10 hover:text-red-300">
                    🚪 Çıkış Yap
                </a>
            </div>
        </aside>

        <!-- Main -->
        <main class="flex-1 p-8">
            <div class="bg-gray-900 rounded-xl p-6 mb-6 flex justify-between items-center border border-gray-800">
                <h1 class="text-xl font-bold text-white"><?= htmlspecialchars($pageTitle ?? 'Admin') ?></h1>
                <span class="text-sm text-gray-500">👤 <?= htmlspecialchars($_SESSION['username'] ?? '') ?></span>
            </div>

            <div>
                <?= $content ?? '' ?>
            </div>
        </main>
    </div>
</body>
</html>