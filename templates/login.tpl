<html>
    <head>
        <title>ログイン | アクティブゼロ管理画面</title>
        <meta charset="UTF-8">
        <link rel="stylesheet" href="/admin/css/login.css">
    </head>
    <body>
        <div class="form-wrapper">
            <h1>Sign In</h1>
            <form>
                <div class="form-item">
                    <label for="email"></label>
                    <input type="email" name="email" required="required" placeholder="メールアドレス"></input>
                </div>
                <div class="form-item">
                    <label for="password"></label>
                    <input type="password" name="password" required="required" placeholder="パスワード"></input>
                </div>
                <div class="button-panel">
                    <input type="submit" class="button" title="ログイン" value="ログイン"></input>
                </div>
            </form>
            <div class="form-footer">
                {*<p><a href="#">Create an account</a></p>*}
                {*<p><a href="#">Forgot password?</a></p>*}
            </div>
        </div>
    </body>
</html>