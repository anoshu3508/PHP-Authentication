
        <div class="file_sharing main-wrapper">
            <h1>ファイル共有</h1>

            {if isset($message)}
                <p class="message">{$message}</p>
            {/if}

            <section class="file_upload">
                <h2>ファイルをアップロード</h2>
                <form action="/employee/" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="action" value="uploadShareFile" />
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
                                    <input type="hidden" name="share_all_flag" value="0" />
                                    <label for="share_all_flag">
                                        <input type="checkbox" name="share_all_flag" value="1" id="share_all_flag" />全員に共有
                                    </label>
                                </td>
                                <td class="share_selectbox">
                                    <span>共有者（複数選択可）</span>
                                    <select multiple="multiple" placeholder="選択してください">
                                        {foreach from=$userList item=user}
                                            <option value="{$user.id}">{$user.last_name} {$user.first_name}</option>
                                        {/foreach}
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2">
                                    <input type="file" name="share_file" />
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
                        <col style="width: 20%" />
                        <col style="width: 10%" />
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
                                        <div class="ico_download"></div>
                                    </a>
                                </td>
                                <td>{$uploadFile.uploaded_at}</td>
                                <td>{$uploadFile.file_size|byte_format}</td>
                                <td>{$uploadFile.last_name}</td>
                                <td class="delete_button">
                                    <a href="#" data-remodal-target="delete_modal" data-delete-id="{$uploadFile.id}"><div class="ico_trash"></div></a>
                                </td>
                            </tr>
                        {/foreach}
                        {* <tr>
                            <td class="share_file_name">
                                <a href="javascript:postToDownloadShareFile(0)">sample.txt</a>
                            </td>
                            <td>2020/11/15 15:12</td>
                            <td class="num">35,661,784</td>
                            <td><a class="delete_button" href="#" data-remodal-target="delete_modal" data-delete-id="0"></a></td>
                        </tr> *}
                    </tbody>
                </table>
                <input type="hidden" name="delete_id" value="" />
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
                                        <div class="ico_download"></div>
                                    </a>
                                </td>
                                <td>{$shareFile.uploaded_at}</td>
                                <td>{$shareFile.file_size|byte_format}</td>
                                <td>{$shareFile.last_name}</td>
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
            {{include file="parts/deleteModal.tpl"}}
        </div>