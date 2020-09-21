
        <div class="admin">
            <h2 >ユーザ登録CSV</h2>
            {if isset($message)}
                <div>{$message}</div>
                <br/>
            {/if}
            <form action="/employee/" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="action" value="userRegist" />
                <input type="hidden" name="csrf_token" value="{$CSRF_TOKEN}" />
                <input type="file" name="csv_file" accept=".csv" />
                <input type="submit" value="送信" />
            </form>
        </div>