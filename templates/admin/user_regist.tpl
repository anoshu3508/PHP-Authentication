
        {if isset($message)}
            <div>{$message}</div>
            <br/>
        {/if}
        <form action="/employee/" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="action" value="userRegist" />
            <input type="file" name="csv_file" accept=".csv" />
            <input type="submit" value="送信" />
        </form>