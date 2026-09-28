<?php
/**
 * Modern Admin Dashboard View - Technical UI
 */
?>
<div class="admin-dashboard bg-gray-950 min-h-screen text-gray-300 font-mono text-sm">
    <!-- Top Bar -->
    <header class="border-b border-gray-800 bg-gray-900/50 p-4 flex justify-between items-center backdrop-blur-sm sticky top-0 z-50">
        <div class="flex items-center gap-4">
            <span class="text-blue-500 font-black tracking-tighter">NIU <span class="text-gray-500">v1.0</span></span>
            <span class="text-xs text-gray-600">/ root / dashboard</span>
        </div>
        <div class="flex items-center gap-6">
            <div class="flex items-center gap-2">
                <span class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></span>
                <span class="text-[10px] uppercase tracking-widest text-gray-400">System Online</span>
            </div>
            <button class="text-gray-500 hover:text-white transition-colors">
                <i data-lucide="bell" class="w-4 h-4"></i>
            </button>
        </div>
    </header>

    <div class="flex">
        <!-- Admin Sidebar -->
        <aside class="w-64 border-r border-gray-800 h-[calc(100vh-64px)] p-6 hidden lg:block sticky top-16">
            <nav class="space-y-8">
                <div>
                    <div class="text-[10px] uppercase tracking-[0.2em] text-gray-600 mb-4">Core Systems</div>
                    <ul class="space-y-2">
                        <li><a href="/public/admin" class="flex items-center gap-3 text-blue-400"><i data-lucide="layout-dashboard" class="w-4 h-4"></i> Dashboard</a></li>
                        <li><a href="/public/admin/users" class="flex items-center gap-3 hover:text-white"><i data-lucide="users" class="w-4 h-4"></i> Users</a></li>
                        <li><a href="/public/admin/security" class="flex items-center gap-3 hover:text-white"><i data-lucide="shield" class="w-4 h-4"></i> Security</a></li>
                    </ul>
                </div>
                <div>
                    <div class="text-[10px] uppercase tracking-[0.2em] text-gray-600 mb-4">Modules</div>
                    <ul class="space-y-2">
                        <li><a href="/public/admin/news" class="flex items-center gap-3 hover:text-white"><i data-lucide="newspaper" class="w-4 h-4"></i> News & Stories</a></li>
                        <li><a href="/public/admin/blocks" class="flex items-center gap-3 hover:text-white"><i data-lucide="box" class="w-4 h-4"></i> Block Manager</a></li>
                        <li><a href="/public/admin/topics" class="flex items-center gap-3 hover:text-white"><i data-lucide="tag" class="w-4 h-4"></i> Topic Editor</a></li>
                    </ul>
                </div>
                <div class="pt-8 border-t border-gray-800">
                    <ul class="space-y-2">
                         <li><a href="/public/admin/settings" class="flex items-center gap-3 hover:text-white"><i data-lucide="settings" class="w-4 h-4"></i> System Settings</a></li>
                         <li><a href="/public/" class="flex items-center gap-3 text-red-500/80 hover:text-red-500"><i data-lucide="log-out" class="w-4 h-4"></i> Exit Terminal</a></li>
                    </ul>
                </div>
            </nav>
        </aside>

        <!-- Main Content Area -->
        <main class="flex-grow p-8">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
                <!-- Health Cards -->
                <div class="bg-gray-900 border border-gray-800 p-5 rounded-lg">
                    <div class="text-[10px] text-gray-500 mb-2 uppercase">Runtime Engine</div>
                    <div class="text-xl font-bold flex items-center gap-2">
                        PHP <span class="text-blue-500"><?= $stats['php_version'] ?></span>
                    </div>
                </div>
                <div class="bg-gray-900 border border-gray-800 p-5 rounded-lg">
                    <div class="text-[10px] text-gray-500 mb-2 uppercase">Database Link</div>
                    <div class="text-sm font-bold text-green-500"><?= $stats['db_status'] ?></div>
                </div>
                <div class="bg-gray-900 border border-gray-800 p-5 rounded-lg">
                    <div class="text-[10px] text-gray-500 mb-2 uppercase">Traffic Flow</div>
                    <div class="text-xl font-bold"><?= $stats['active_users'] ?> <span class="text-xs text-gray-600">Active</span></div>
                </div>
                <div class="bg-gray-900 border border-gray-800 p-5 rounded-lg">
                    <div class="text-[10px] text-gray-500 mb-2 uppercase">Content Index</div>
                    <div class="text-xl font-bold"><?= $stats['total_stories'] ?> <span class="text-xs text-gray-600">Units</span></div>
                </div>
            </div>

            <div class="grid grid-cols-1 xl:grid-cols-3 gap-10">
                <!-- Recent Activity Log -->
                <div class="xl:col-span-2 space-y-4">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-xs font-black uppercase tracking-widest text-gray-500">System Activity Logs</h2>
                        <button class="text-[10px] text-blue-500 hover:underline">View All Logs</button>
                    </div>
                    <div class="bg-gray-900 border border-gray-800 rounded-lg overflow-hidden">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="border-b border-gray-800 bg-gray-800/20">
                                    <th class="p-4 text-[10px] font-bold text-gray-600 uppercase">Timestamp</th>
                                    <th class="p-4 text-[10px] font-bold text-gray-600 uppercase">Operator</th>
                                    <th class="p-4 text-[10px] font-bold text-gray-600 uppercase">Command</th>
                                    <th class="p-4 text-[10px] font-bold text-gray-600 uppercase">Subject</th>
                                </tr>
                            </thead>
                            <tbody class="text-[12px]">
                                <?php foreach ($recentLogs as $log): ?>
                                    <tr class="border-b border-gray-800/50 hover:bg-white/5 transition-colors">
                                        <td class="p-4 text-gray-500"><?= date('H:i:s', (int)$log['created_at']) ?></td>
                                        <td class="p-4 text-blue-400 font-bold"><?= htmlspecialchars($log['username']) ?></td>
                                        <td class="p-4 italic">/<?= strtolower($log['action']) ?></td>
                                        <td class="p-4 text-gray-400"><?= htmlspecialchars($log['module']) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                                <?php if(empty($recentLogs)): ?>
                                    <tr>
                                        <td colspan="4" class="p-8 text-center text-gray-700 italic">No activity recorded for current session.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- System Health Monitor placeholder -->
                <div class="space-y-6">
                    <h2 class="text-xs font-black uppercase tracking-widest text-gray-500">Health Monitor</h2>
                    <div class="terminal-box border-gray-800 opacity-80 h-64 overflow-hidden relative">
                         <div class="absolute top-4 left-4 right-4 flex flex-col gap-2 font-mono text-[10px]">
                            <div class="flex justify-between"><span>CPU Load</span><span class="text-green-500">0.04%</span></div>
                            <div class="flex justify-between"><span>Memory Usage</span><span class="text-green-500">12.4 MB</span></div>
                            <div class="flex justify-between"><span>Disk I/O</span><span class="text-blue-500">STABLE</span></div>
                            <div class="mt-4 text-gray-600">> Monitoring secure sessions...</div>
                            <div class="text-gray-600">> All security middlewares active.</div>
                            <div class="text-gray-600">> FtoniNuke PSR-4 Autoloader OK.</div>
                            <div class="text-green-500 mt-2">> system.ready();</div>
                         </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>
