<?php
/**
 * Yorum bölümü partial
 * Kullanım: $story, $siteName tanımlı olmalı
 */

use FtoniNuke\Service\CommentService;

$commentService = new CommentService();
$repo = $commentService->getRepo();

$comments = $repo->byStory((int)$story['sid']);
$count    = $repo->countByStory((int)$story['sid']);

$captcha = $commentService->generateCaptcha();
$honeypotField = $commentService->getHoneypotField();

$flash = $_SESSION['comment_flash'] ?? null;
unset($_SESSION['comment_flash']);

$old = $_SESSION['comment_old'] ?? [];
unset($_SESSION['comment_old']);

$currentUser = !empty($_SESSION['user_id']);
$userName    = $_SESSION['username'] ?? '';

$formStartedAt = time();

// Threaded yorum render
function renderComment(array $c, int $depth = 0): string
{
    $html = '<div class="comment-item" style="margin-left:' . ($depth * 40) . 'px">';
    $html .= '<div class="comment-avatar">' . mb_strtoupper(mb_substr($c['author_name'], 0, 1)) . '</div>';
    $html .= '<div class="comment-body">';
    $html .= '<div class="comment-header">';
    $html .= '<strong class="comment-author">' . htmlspecialchars($c['author_name']) . '</strong>';
    $html .= '<span class="comment-date">' . date('d.m.Y H:i', (int)$c['created_at']) . '</span>';
    if ($depth < 2) {
        $html .= '<button class="comment-reply-btn" onclick="replyTo(' . (int)$c['cid'] . ', \'' . htmlspecialchars(addslashes($c['author_name'])) . '\')">↩ Yanıtla</button>';
    }
    $html .= '</div>';
    $html .= '<div class="comment-content">' . nl2br($c['content']) . '</div>';
    $html .= '</div></div>';

    if (!empty($c['children'])) {
        foreach ($c['children'] as $child) {
            $html .= renderComment($child, $depth + 1);
        }
    }

    return $html;
}
?>

<style>
    .comments-section {
        margin-top: 40px;
        padding-top: 32px;
        border-top: 2px solid #f0f2f5;
    }
    .comments-title {
        display: flex; align-items: center; gap: 12px;
        font-size: 1.3em; color: #2c3e50; margin: 0 0 24px;
    }
    .comments-title .count {
        background: #3498db; color: #fff;
        padding: 2px 12px; border-radius: 12px;
        font-size: .7em; font-weight: 800;
    }
    .comment-flash {
        padding: 14px 18px; border-radius: 8px;
        margin-bottom: 20px; font-size: .95em;
    }
    .comment-flash.success { background: #d4edda; color: #155724; border-left: 4px solid #27ae60; }
    .comment-flash.error   { background: #f8d7da; color: #721c24; border-left: 4px solid #e74c3c; }

    /* Yorum listesi */
    .comment-list { margin-bottom: 32px; }
    .comment-item {
        display: flex; gap: 14px;
        padding: 16px 0;
        border-bottom: 1px solid #f0f2f5;
    }
    .comment-item:last-child { border-bottom: none; }
    .comment-avatar {
        width: 44px; height: 44px; flex-shrink: 0;
        background: linear-gradient(135deg, #3498db, #2c3e50);
        color: #fff; border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-weight: 800; font-size: 1.1em;
    }
    .comment-body { flex: 1; min-width: 0; }
    .comment-header {
        display: flex; align-items: center; gap: 12px;
        margin-bottom: 6px; flex-wrap: wrap;
    }
    .comment-author { color: #2c3e50; font-size: .95em; }
    .comment-date { color: #95a5a6; font-size: .8em; }
    .comment-reply-btn {
        background: none; border: none; cursor: pointer;
        color: #3498db; font-size: .8em; font-weight: 600;
        padding: 2px 8px; border-radius: 4px;
    }
    .comment-reply-btn:hover { background: #ebf5fb; }
    .comment-content {
        color: #444; font-size: .95em; line-height: 1.6;
        word-wrap: break-word;
    }

    /* Form */
    .comment-form-wrap {
        background: #f8f9fa;
        padding: 24px; border-radius: 10px;
        margin-top: 24px;
    }
    .comment-form-wrap h3 {
        margin: 0 0 20px; color: #2c3e50; font-size: 1.1em;
    }
    .cf-row { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 12px; }
    .cf-input, .cf-textarea {
        width: 100%;
        padding: 12px 14px;
        border: 1px solid #dcdde1;
        border-radius: 6px;
        font-size: .95em;
        font-family: inherit;
        background: #fff;
    }
    .cf-textarea { min-height: 120px; resize: vertical; }
    .cf-input:focus, .cf-textarea:focus {
        outline: none; border-color: #3498db;
        box-shadow: 0 0 0 3px rgba(52,152,219,.15);
    }
    .cf-captcha {
        display: flex; align-items: center; gap: 12px;
        margin-bottom: 16px; flex-wrap: wrap;
    }
    .cf-captcha .question {
        background: #2c3e50; color: #fff;
        padding: 10px 20px; border-radius: 6px;
        font-weight: 700; font-family: monospace;
        font-size: 1.1em;
        letter-spacing: 1px;
    }
    .cf-captcha input {
        width: 100px;
        padding: 10px 14px;
        border: 1px solid #dcdde1; border-radius: 6px;
        font-size: 1em; text-align: center;
    }
    .cf-submit {
        background: #2c3e50; color: #fff;
        padding: 12px 32px; border: none; border-radius: 6px;
        font-weight: 700; font-size: .95em;
        cursor: pointer;
        transition: all .2s;
    }
    .cf-submit:hover { background: #34495e; transform: translateY(-1px); }
    .cf-honeypot {
        position: absolute; left: -9999px;
        width: 1px; height: 1px; opacity: 0;
    }
    .cf-info {
        font-size: .8em; color: #95a5a6; margin-top: 8px;
    }
    .reply-indicator {
        display: none;
        background: #ebf5fb; border-left: 4px solid #3498db;
        padding: 8px 12px; border-radius: 4px;
        font-size: .85em; color: #2c3e50;
        margin-bottom: 12px;
        align-items: center; justify-content: space-between;
    }
    .reply-indicator.active { display: flex; }
    .reply-indicator button {
        background: none; border: none; cursor: pointer;
        color: #e74c3c; font-weight: 700; font-size: 1.1em;
    }
    .no-comments {
        text-align: center; padding: 32px;
        color: #95a5a6; font-style: italic;
    }

    @media (max-width: 600px) {
        .cf-row { grid-template-columns: 1fr; }
        .comment-item[style*="margin-left"] { margin-left: 20px !important; }
    }
</style>

<section class="comments-section" id="comments">
    <h2 class="comments-title">
        💬 Yorumlar
        <span class="count"><?= $count ?></span>
    </h2>

    <?php if ($flash): ?>
        <div class="comment-flash <?= $flash['type'] ?>">
            <?= htmlspecialchars($flash['msg']) ?>
        </div>
    <?php endif; ?>

    <!-- Yorum Listesi -->
    <div class="comment-list">
        <?php if (empty($comments)): ?>
            <div class="no-comments">Henüz yorum yok. İlk yorumu siz yapın!</div>
        <?php else: ?>
            <?php foreach ($comments as $c): ?>
                <?= renderComment($c) ?>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <!-- Yorum Formu -->
    <div class="comment-form-wrap">
        <h3>✍️ Yorum Yap</h3>

        <div id="reply-indicator" class="reply-indicator">
            <span>↩ <strong id="reply-to-name"></strong> kullanıcısına yanıt veriyorsunuz</span>
            <button type="button" onclick="cancelReply()">×</button>
        </div>

        <form method="post" action="/public/comment/submit">
            <input type="hidden" name="sid" value="<?= (int)$story['sid'] ?>">
            <input type="hidden" name="pid" id="pid-input" value="0">
            <input type="hidden" name="form_started_at" value="<?= $formStartedAt ?>">

            <!-- Honeypot (bot tuzağı — gizli) -->
            <input type="text" name="<?= $honeypotField ?>" class="cf-honeypot" tabindex="-1" autocomplete="off">

            <?php if (!$currentUser): ?>
                <div class="cf-row">
                    <input type="text" name="author_name" class="cf-input" required
                           placeholder="Adınız *"
                           value="<?= htmlspecialchars($old['author_name'] ?? '') ?>">
                    <input type="email" name="author_email" class="cf-input"
                           placeholder="E-posta (yayınlanmaz)"
                           value="<?= htmlspecialchars($old['author_email'] ?? '') ?>">
                </div>
            <?php else: ?>
                <p style="margin:0 0 12px;color:#555">
                    👤 <strong><?= htmlspecialchars($userName) ?></strong> olarak yorum yapıyorsunuz
                </p>
            <?php endif; ?>

            <textarea name="content" class="cf-textarea" required
                      placeholder="Yorumunuz… (min 5 karakter)"
                      minlength="5" maxlength="3000"><?= htmlspecialchars($old['content'] ?? '') ?></textarea>

            <div class="cf-captcha">
                <span class="question" id="captcha-question"><?= htmlspecialchars($captcha['question']) ?></span>
                <input type="number" name="captcha_answer" required placeholder="Cevap" min="0" max="100">
                <button type="button" onclick="refreshCaptcha()" title="Yeni soru"
                        style="background:none;border:none;cursor:pointer;font-size:1.2em;color:#3498db">🔄</button>
            </div>

            <button type="submit" class="cf-submit">💬 Yorumu Gönder</button>
            <p class="cf-info">Yorumunuz onaylandıktan sonra yayınlanır.</p>
        </form>
    </div>
</section>

<script>
function replyTo(cid, name) {
    document.getElementById('pid-input').value = cid;
    document.getElementById('reply-to-name').textContent = name;
    document.getElementById('reply-indicator').classList.add('active');
    document.querySelector('.cf-textarea').focus();
    document.querySelector('.comment-form-wrap').scrollIntoView({ behavior: 'smooth', block: 'center' });
}

function cancelReply() {
    document.getElementById('pid-input').value = 0;
    document.getElementById('reply-indicator').classList.remove('active');
}

async function refreshCaptcha() {
    try {
        const res = await fetch('/public/comment/captcha', { credentials: 'same-origin' });
        const data = await res.json();
        document.getElementById('captcha-question').textContent = data.question;
    } catch (e) {
        console.error(e);
    }
}
</script>
