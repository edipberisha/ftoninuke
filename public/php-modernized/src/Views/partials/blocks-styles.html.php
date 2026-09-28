<style>
    /* Blok kutusu */
    .block {
        background: #fff;
        border-radius: 10px;
        box-shadow: 0 1px 3px rgba(0,0,0,.08);
        margin-bottom: 20px;
        overflow: hidden;
    }
    .block-title {
        margin: 0;
        padding: 14px 18px;
        background: linear-gradient(135deg, #2c3e50, #34495e);
        color: #fff;
        font-size: .95em;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .5px;
        border-bottom: 2px solid #3498db;
    }
    .block-content {
        padding: 16px 18px;
        color: #333;
        font-size: .92em;
        line-height: 1.6;
    }
    .block-content ul {
        list-style: none; margin: 0; padding: 0;
    }
    .block-content li {
        padding: 8px 0;
        border-bottom: 1px solid #f0f0f0;
    }
    .block-content li:last-child { border-bottom: none; }
    .block-content a {
        color: #2980b9; text-decoration: none;
    }
    .block-content a:hover { text-decoration: underline; }

    /* Ana menü bloğu */
    .block-menu li a { display: block; }
    .block-menu ul ul { padding-left: 16px; }
    .block-menu ul ul a { color: #7f8c8d; font-size: .9em; }

    /* Son haberler bloğu */
    .block-list li small {
        display: block; color: #95a5a6; font-size: .8em; margin-top: 3px;
    }

    /* İstatistikler bloğu */
    .block-stats li {
        display: flex; justify-content: space-between; align-items: center;
    }
    .block-stats .label { color: #555; }
    .block-stats .value {
        background: #3498db; color: #fff;
        padding: 2px 10px; border-radius: 12px;
        font-size: .85em; font-weight: 700;
    }

    /* Sosyal medya bloğu */
    .social-grid {
        display: flex; flex-wrap: wrap; gap: 8px;
    }
    .social-link {
        flex: 1 1 calc(50% - 4px);
        display: flex; align-items: center; gap: 8px;
        padding: 8px 12px;
        background: #f8f9fa;
        border-radius: 6px;
        text-decoration: none !important;
        color: #333 !important;
        font-size: .85em;
        transition: all .2s;
        border-left: 3px solid var(--brand-color, #3498db);
    }
    .social-link:hover {
        background: var(--brand-color, #3498db);
        color: #fff !important;
        transform: translateX(3px);
    }
    .social-icon { font-size: 1.2em; }
    .social-label { font-weight: 600; }

    /* Reklam bloğu */
    .block-ad .block-content {
        padding: 10px;
    }
    .block-ad img {
        width: 100%; height: auto; border-radius: 6px; display: block;
    }
</style>
