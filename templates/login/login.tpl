
        <div class="login main-wrapper">
            <div class="form-wrapper">
                <h1>Sign In</h1>
                <form action="/employee/" method="POST">
                    <input type="hidden" name="action" value="authentication" />
                    <input type="hidden" name="csrf_token" value="{$CSRF_TOKEN}">

                    {if $errorFlg}
                        <div class="form-item">
                            <span class="error_message">メールアドレス または パスワードが間違っています</span>
                        </div>
                    {/if}
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
                    {* <p><a href="#">Create an account</a></p> *}
                    {* <p><a href="#">Forgot password?</a></p> *}
                </div>
            </div>
        </div>