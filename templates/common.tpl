<!DOCTYPE html>
<html lang="ja">
    <head>
        <title>{$PAGE_TITLE} | アクティブゼロ社員用ページ</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width,initial-scale=1.0">
        <link rel="shortcut icon" type="image/vnd.microsoft.icon" sizes="16x16" href="/employee/favicon.ico" />
        <!-- CSS -->
        <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-9aIt2nRpC12Uk9gS9baDl411NQApFmC26EwAOH8WgZl5MYYxFfc+NcPb1dKGj7Sk" crossorigin="anonymous">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/tempusdominus-bootstrap-4/5.0.0-alpha14/css/tempusdominus-bootstrap-4.min.css" />
        <link rel="stylesheet" href="https://unpkg.com/scroll-hint@latest/css/scroll-hint.css">
        <link rel="stylesheet" href="/employee/css/vendor/sumoselect/sumoselect.css">
        <link rel="stylesheet" href="/employee/css/vendor/remodal/remodal.css">
        <link rel="stylesheet" href="/employee/css/vendor/remodal/remodal-default-theme.css">
        <link rel="stylesheet" href="/employee/css/common.css">
        {if isset($CSS_FILE_NAME)}
            <link rel="stylesheet" href="/employee/css/{$CSS_FILE_NAME}.css">
        {/if}
        <!-- JavaScript -->
        <script src="/employee/js/vendor/jquery-3.5.1.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js"
        integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js"
        integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.22.2/moment.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.22.2/locale/ja.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/tempusdominus-bootstrap-4/5.0.0-alpha14/js/tempusdominus-bootstrap-4.min.js"></script>
        <script src="https://unpkg.com/scroll-hint@latest/js/scroll-hint.min.js"></script>
        <script src="/employee/js/vendor/jquery.sumoselect.min.js"></script>
        <script src="/employee/js/vendor/remodal.js"></script>
        <script src="/employee/js/common.js"></script>
        {if isset($JS_FILE_NAME)}
            <script src="/employee/js/{$JS_FILE_NAME}.js"></script>
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