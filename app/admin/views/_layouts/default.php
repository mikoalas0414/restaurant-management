<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="en">
<head>
    <meta name="description" content="<?= setting('meta_description') ?>">
    <meta name="keywords" content="<?= setting('meta_keywords') ?>">
    <?= get_metas(); ?>
    <?= get_favicon(); ?>
    <title><?= sprintf(lang('admin::default.site_title'), Template::getTitle(), setting('site_name')); ?></title>
    <?= get_style_tags(['ui', 'widget', 'custom', 'theme']); ?>
    <?= get_script_tags('app'); ?>
</head>
<body class="page <?= $this->bodyClass; ?>">
    <?php if (AdminAuth::isLogged()) { ?>

        <?= $this->makePartial('top_nav') ?>

        <?= AdminMenu::render('side_nav'); ?>

    <?php } ?>

    <div class="page-wrapper">
        <div id="notification">
            <?= $this->makePartial('flash') ?>
        </div>

        <?= Template::getBlock('body') ?>

    </div>
    <?php if (AdminAuth::isLogged()) { ?>
        <div class="footer navbar-footer">
            <div class="wrap-vertical">
                <div class="row">
                    <div class="col-9 text-copyright">
                        <?= lang('system::default.tastyigniter.copyright'); ?>
                    </div>
                    <div class="col text-version">
                        <?= sprintf(lang('system::default.tastyigniter.version'), params('ti_version')); ?>
                    </div>
                </div>
            </div>
        </div>
    <?php } ?>
    <?= get_script_tags(['widget', 'custom', 'theme']); ?>
</body>
</html>