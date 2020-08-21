<!DOCTYPE html>
<html lang="ja">
    <head>
        <title>{$PAGE_TITLE} | アクティブゼロ社員用ページ</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width,initial-scale=1.0">
        <link rel="shortcut icon" type="image/vnd.microsoft.icon" sizes="16x16" href="/employee/favicon.ico" />
        <!-- CSS -->
        <link rel="stylesheet" href="/employee/css/vendor/sumoselect/sumoselect.css">
        <link rel="stylesheet" href="/employee/css/vendor/remodal/remodal.css">
        <link rel="stylesheet" href="/employee/css/vendor/remodal/remodal-default-theme.css">
        <link rel="stylesheet" href="/employee/css/common.css">
        {if isset($CSS_FILE_NAME)}
            <link rel="stylesheet" href="/employee/css/{$CSS_FILE_NAME}.css">
        {/if}
        <!-- JavaScript -->
        <script src="/employee/js/vendor/jquery-3.5.1.min.js"></script>
        <script src="/employee/js/vendor/jquery.sumoselect.min.js"></script>
        <script src="/employee/js/vendor/remodal.js"></script>
        <script type="text/javascript" src="/employee/js/common.js"></script>
        {if isset($JS_FILE_NAME)}
            <script type="text/javascript" src="/employee/js/{$JS_FILE_NAME}.js"></script>
        {/if}
        <script>
            var CSRF_TOKEN = "{$CSRF_TOKEN}";
        </script>
    </head>
    <body>
        {include file='parts/header.tpl'}
        {$MAIN_HTML}
        {include file='parts/footer.tpl'}
    </body>
</html>