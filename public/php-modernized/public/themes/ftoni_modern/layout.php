<?php
/**
 * Theme: Ftoni Modern
 * Layout File
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= $pageTitle ?? 'Ftoni Modern' ?></title>
    <?php wp_head(); ?>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-50 font-sans text-slate-900">

    <?php get_header(); ?>

    <main class="max-w-7xl mx-auto py-12 px-6">
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-12">
            
            <aside class="lg:col-span-1">
                <?php get_sidebar('l'); ?>
            </aside>

            <article class="lg:col-span-2 prose prose-slate max-w-none">
                <?php the_content($content); ?>
            </article>

            <aside class="lg:col-span-1">
                <?php get_sidebar('r'); ?>
            </aside>

        </div>
    </main>

    <?php get_footer(); ?>

    <?php wp_footer(); ?>
</body>
</html>
