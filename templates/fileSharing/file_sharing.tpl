
        <div class="file_sharing main-wrapper">
            <h1>ファイル共有</h1>

            {if isset($message)}
                {if $errorFlag}
                    <p class="error_message">{$message}</p>
                {else}
                    <p class="success_message">{$message}</p>
                {/if}
            {/if}

            <section class="file_upload">
                <h2>ファイルをアップロード</h2>
                <form action="/employee/" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="action" value="uploadShareFile" />
                    <input type="hidden" name="csrf_token" value="{$CSRF_TOKEN}">
                    <table>
                        <thead>
                            <tr>
                                <th></th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="share_checkbox">
                                    <label for="share_all_flag">
                                        <input type="checkbox" name="share_all_flag" value="1" id="share_all_flag" />全員に共有
                                    </label>
                                    <br />
                                    <label for="private_flag">
                                        <input type="checkbox" name="private_flag" value="1" id="private_flag" />非公開
                                    </label>
                                </td>
                                <td class="share_selectbox">
                                    <span>共有する人（複数選択可）</span>
                                    <select name="share_user_id[]" multiple="multiple" id="share_user_id" placeholder="選択してください">
                                        {foreach from=$userList item=user}
                                            <option value="{$user.id}">{$user.last_name} {$user.first_name}</option>
                                        {/foreach}
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2" class="share_file_select">
                                    <input type="file" name="share_file" id="share_file" />
                                    <input type="submit" value="送信" />
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </form>
            </section>

            <section class="upload_file_list">
                <h2>アップロードファイル一覧</h2>
                <table>
                    <colgroup>
                        <col style="width: 30%" />
                        <col style="width: 25%" />
                        <col style="width: 15%" />
                        <col style="width: 22%" />
                        <col style="width: 8%" />
                    </colgroup>
                    <thead>
                        <tr>
                            <th>ファイル名</th>
                            <th>アップロード日時</th>
                            <th>サイズ</th>
                            <th>共有者</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        {foreach from=$uploadFileList item=uploadFile}
                            <tr>
                                <td class="share_file_name">
                                    <a href="javascript:postToDownloadShareFile({$uploadFile.id})">
                                        {$uploadFile.file_name}
                                        <span class="ico_download"></span>
                                    </a>
                                </td>
                                <td>{$uploadFile.uploaded_at}</td>
                                <td>{$uploadFile.file_size|byte_format}</td>
                                <td>{$uploadFile.share_name}</td>
                                <td class="delete_button">
                                    <a data-remodal-target="delete_modal" data-delete-file-name="{$uploadFile.file_name}"><div class="ico_trash"></div></a>
                                </td>
                            </tr>
                        {/foreach}
                        {* <tr>
                            <td class="share_file_name">
                                <a href="javascript:postToDownloadShareFile(0)">sample.txt</a>
                            </td>
                            <td>2020/11/15 15:12</td>
                            <td class="num">35,661,784</td>
                            <td><a class="delete_button" href="#" data-remodal-target="delete_modal" data-delete-file-name="0"></a></td>
                        </tr> *}
                    </tbody>
                </table>
                <input type="hidden" name="delete_file_name" value="" />
            </section>

            <section class="share_file_list">
                <h2>共有ファイル一覧</h2>
                <table>
                    <colgroup>
                        <col style="width: 30%" />
                        <col style="width: 25%" />
                        <col style="width: 15%" />
                        <col style="width: 30%" />
                    </colgroup>
                    <thead>
                        <tr>
                            <th>ファイル名</th>
                            <th>アップロード日時</th>
                            <th>サイズ</th>
                            <th>所有者</th>
                        </tr>
                    </thead>
                    <tbody>
                        {foreach from=$shareFileList item=shareFile}
                            <tr>
                                <td class="share_file_name">
                                    <a href="javascript:postToDownloadShareFile({$shareFile.id})">
                                        {$shareFile.file_name}
                                        <span class="ico_download"></span>
                                    </a>
                                </td>
                                <td>{$shareFile.uploaded_at}</td>
                                <td>{$shareFile.file_size|byte_format}</td>
                                <td>{$shareFile.owner_name}</td>
                            </tr>
                        {/foreach}
                        {* <tr>
                            <td class="share_file_name">
                                <a href="javascript:postToDownloadShareFile(0)">
                                    sample.txt
                                    <div class="ico_download"></div>
                                </a>
                            </td>
                            <td>2020/11/15 15:12</td>
                            <td class="num">35,661,784</td>
                            <td>柿沼</td>
                        </tr> *}
                    </tbody>
                </table>
            </section>
            {include file="parts/deleteModal.tpl"}
        </div>