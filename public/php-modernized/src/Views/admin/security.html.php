<div class="bg-gray-900 border border-gray-800 rounded-xl p-8">
    <h2 class="text-lg font-bold text-white mb-4">🛡️ Güvenlik Durumu</h2>
    <ul class="space-y-3 text-sm">
        <li class="flex justify-between border-b border-gray-800 pb-2">
            <span class="text-gray-400">CSRF Koruması</span><span class="text-green-500 font-bold">✓ Aktif</span>
        </li>
        <li class="flex justify-between border-b border-gray-800 pb-2">
            <span class="text-gray-400">Şifre Hash (BCRYPT)</span><span class="text-green-500 font-bold">✓ Aktif</span>
        </li>
        <li class="flex justify-between border-b border-gray-800 pb-2">
            <span class="text-gray-400">Session Cookie HTTPOnly</span><span class="text-green-500 font-bold">✓ Aktif</span>
        </li>
        <li class="flex justify-between border-b border-gray-800 pb-2">
            <span class="text-gray-400">PDO Prepared Statements</span><span class="text-green-500 font-bold">✓ Aktif</span>
        </li>
        <li class="flex justify-between">
            <span class="text-gray-400">PHP Sürümü</span><span class="text-blue-400 font-bold"><?= PHP_VERSION ?></span>
        </li>
    </ul>
</div>