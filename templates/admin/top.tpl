
        <div style="margin-left: 5px">
            <h2 >管理者トップページ</h2>
            <form action="/employee/" method="GET">
                <input type="hidden" name="action" value="userRegist" />
                ・<input type="submit" value="ユーザ登録CSV" />
            </form>
            <form action="/employee/" method="GET">
                <input type="hidden" name="action" value="exit" />
                ・<input type="submit" value="終了" />
            </form>
        </div>