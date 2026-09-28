<?php
/**
 * Modern Profile View Template
 * 
 * Replaces legacy table-based PHP-Nuke layouts with modern CSS Grid/Flexbox.
 */
?>
<div class="user-profile-container max-w-4xl mx-auto p-6 bg-white dark:bg-gray-800 rounded-xl shadow-lg mt-10">
    <header class="mb-10 border-b border-gray-100 dark:border-gray-700 pb-6 flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-extrabold text-gray-900 dark:text-white tracking-tight">Your Account</h1>
            <p class="text-gray-500 mt-1">Manage your identity and preference within FtoniNuke.</p>
        </div>
        <div class="profile-badge p-3 bg-blue-50 dark:bg-blue-900/20 rounded-full">
            <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
            </svg>
        </div>
    </header>

    <?php if (!empty($_SESSION['flash_message'])): ?>
        <div class="bg-green-50 text-green-700 p-4 rounded-lg mb-6 border border-green-200">
            <?= htmlspecialchars($_SESSION['flash_message']) ?>
            <?php unset($_SESSION['flash_message']); ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($_SESSION['flash_errors'])): ?>
        <div class="bg-red-50 text-red-700 p-4 rounded-lg mb-6 border border-red-200">
            <ul class="list-disc list-inside text-sm">
                <?php foreach ($_SESSION['flash_errors'] as $error): ?>
                    <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
            <?php unset($_SESSION['flash_errors']); ?>
        </div>
    <?php endif; ?>

    <form action="/update-profile.php" method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-8">
        <!-- Secure CSRF Token -->
        <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">

        <div class="form-group flex flex-col space-y-2">
            <label for="username" class="text-sm font-semibold text-gray-600 dark:text-gray-400">Username</label>
            <input type="text" id="username" value="<?= htmlspecialchars($user['username']) ?>" disabled 
                   class="bg-gray-50 border border-gray-200 p-3 rounded-lg text-gray-500 cursor-not-allowed">
        </div>

        <div class="form-group flex flex-col space-y-2">
            <label for="user_email" class="text-sm font-semibold text-gray-600 dark:text-gray-400">Email Address</label>
            <input type="email" id="user_email" name="user_email" value="<?= htmlspecialchars($user['user_email']) ?>" 
                   class="border border-gray-300 dark:border-gray-600 dark:bg-gray-700 p-3 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
        </div>

        <div class="form-group flex flex-col space-y-2">
            <label for="name" class="text-sm font-semibold text-gray-600 dark:text-gray-400">Full Name</label>
            <input type="text" id="name" name="name" value="<?= htmlspecialchars($user['name'] ?? '') ?>" 
                   class="border border-gray-300 dark:border-gray-600 dark:bg-gray-700 p-3 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
        </div>

        <div class="form-group flex flex-col space-y-2">
            <label for="user_from" class="text-sm font-semibold text-gray-600 dark:text-gray-400">Location</label>
            <input type="text" id="user_from" name="user_from" value="<?= htmlspecialchars($user['user_from'] ?? '') ?>" 
                   class="border border-gray-300 dark:border-gray-600 dark:bg-gray-700 p-3 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
        </div>

        <div class="form-group md:col-span-2 flex flex-col space-y-2">
            <label for="user_occ" class="text-sm font-semibold text-gray-600 dark:text-gray-400">Occupation</label>
            <textarea id="user_occ" name="user_occ" rows="3"
                      class="border border-gray-300 dark:border-gray-600 dark:bg-gray-700 p-3 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none"><?= htmlspecialchars($user['user_occ'] ?? '') ?></textarea>
        </div>

        <div class="md:col-span-2 flex justify-end pt-6 border-t border-gray-100 dark:border-gray-700 mt-4">
            <button type="submit" 
                    class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-8 rounded-lg transition duration-200 transform hover:scale-105 shadow-md">
                Update Profile
            </button>
        </div>
    </form>
</div>
