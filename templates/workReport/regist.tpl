
        <div class="regist_work_report main-wrapper">
            <h1>作業報告登録</h1>

            {if isset($message)}
                {if $errorFlag}
                    <p class="error_message">{$message}</p>
                {else}
                    <p class="success_message">{$message}</p>
                {/if}
            {/if}

            <section>
                <div class="container">
                    <form action="/employee/" method="POST">
                        <input type="hidden" name="action" value="confirmWorkReport" />
                        <input type="hidden" name="csrf_token" value="{$CSRF_TOKEN}">

                        <h2>月別項目</h2>

                        <div class="form-row">
                            <label class="col-sm-3 col-form-label">氏名 <span class="badge badge-danger">必須</span></label>
                            <div class="col-sm-9">
                                <p class="px-1 py-2">{$worker}</p>
                            </div>
                        </div>

                        <div class="form-row">
                            <label class="col-sm-3 col-form-label">客先 <span class="badge badge-danger">必須</span></label>
                            <div class="col-sm-9">
                                <p>
                                    <input type="text" name="customer" class="form-control" value="{$workReport['customer']}" required maxlength="255" />
                                </p>
                            </div>
                        </div>

                        <h2>日別項目</h2>

                        <div class="form-row">
                            <label class="col-sm-3 col-form-label">勤務フラグ <span class="badge badge-danger">必須</span></label>
                            <div class="col-sm-9">
                                <p>
                                    <select class="custom-select w-50" title="勤務フラグ" name="work_flag">
                                        {foreach from=$workFlagSelectList item=name key=value}
                                            <option value="{$value}"{if $value==$workReport.work_flag} selected{/if}>{$name}</option>
                                        {/foreach}
                                    </select>
                                </p>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="form-row">
                                <label for="datetimepicker-date" class="col-sm-3 col-form-label">日付 <span class="badge badge-danger">必須</span></label>
                                <div class="input-group col-sm-4 col-7 date" id="datetimepicker-date" data-target-input="nearest">
                                    <input type="text" class="form-control datetimepicker-input" name="date" value="{$workReport['date']}" data-target="#datetimepicker-date" required />
                                    <div class="input-group-append" data-target="#datetimepicker-date" data-toggle="datetimepicker">
                                        <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                    </div>
                                </div>
                                <div class="col-sm-5 col-5"></div>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="form-row">
                                <label for="datetimepicker-start_time" class="col-sm-3 col-form-label">開始時刻 <span class="badge badge-danger">必須</span></label>
                                <div class="input-group col-sm-4 col-7 date" id="datetimepicker-start_time" data-target-input="nearest">
                                    <input type="text" class="form-control datetimepicker-input" name="start_time" value="{$workReport['start_time']}" data-target="#datetimepicker-start_time" required />
                                    <div class="input-group-append" data-target="#datetimepicker-start_time" data-toggle="datetimepicker">
                                        <div class="input-group-text"><i class="fa fa-clock-o"></i></div>
                                    </div>
                                </div>
                                <div class="col-sm-5 col-5"></div>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="form-row">
                                <label for="datetimepicker-end_time" class="col-sm-3 col-form-label">終了時刻 <span class="badge badge-danger">必須</span></label>
                                <div class="input-group col-sm-4 col-7 date" id="datetimepicker-end_time" data-target-input="nearest">
                                    <input type="text" class="form-control datetimepicker-input" name="end_time" value="{$workReport['end_time']}" data-target="#datetimepicker-end_time" required />
                                    <div class="input-group-append" data-target="#datetimepicker-end_time" data-toggle="datetimepicker">
                                        <div class="input-group-text"><i class="fa fa-clock-o"></i></div>
                                    </div>
                                </div>
                                <div class="col-sm-5 col-5"></div>
                            </div>
                        </div>

                        <div class="form-row">
                            <label class="col-sm-3 col-form-label">休憩時間 <span class="badge badge-danger">必須</span></label>
                            <div class="col-sm-2 col-5">
                                <p>
                                    <input type="text" name="break_hours" class="form-control" value="{$workReport['break_hours']}" required maxlength="5" />
                                </p>
                            </div>
                            <div class="col-sm-7 col-7"></div>
                        </div>

                        <div class="form-row">
                            <label class="col-sm-3 col-form-label">稼働時間</label>
                            <div class="col-sm-2 col-5">
                                <p>
                                    <input type="text" name="operation_hours" class="form-control" value="{$workReport['operation_hours']}" disabled />
                                </p>
                            </div>
                            <div class="col-sm-7 col-7"></div>
                        </div>

                        <div class="form-row">
                            <label class="col-sm-3 col-form-label">残業時間</label>
                            <div class="col-sm-2 col-5">
                                <p>
                                    <input type="text" name="overtime_hours" class="form-control" value="{$workReport['overtime_hours']}" maxlength="5" />
                                </p>
                            </div>
                            <div class="col-sm-7 col-7"></div>
                        </div>

                        <div class="form-row">
                            <label class="col-sm-3 col-form-label">休出時間</label>
                            <div class="col-sm-2 col-5">
                                <p>
                                    <input type="text" name="holiday_hours" class="form-control" value="{$workReport['holiday_hours']}" maxlength="5" />
                                </p>
                            </div>
                            <div class="col-sm-7 col-7"></div>
                        </div>

                        <div class="form-row">
                            <label class="col-sm-3 col-form-label">深夜時数</label>
                            <div class="col-sm-2 col-5">
                                <p>
                                    <input type="text" name="midnight_hours" class="form-control" value="{$workReport['midnight_hours']}" maxlength="5" />
                                </p>
                            </div>
                            <div class="col-sm-7 col-7"></div>
                        </div>

                        <div class="form-row">
                            <label class="col-sm-3 col-form-label">作業内容</span></label>
                            <div class="col-sm-9">
                                <p>
                                    <input type="text" name="work_description" class="form-control" value="{$workReport['work_description']}" maxlength="255" />
                                </p>
                            </div>
                        </div>

                        <div class="btn-toolbar justify-content-around my-5">
                            <div class="btn-group">
                                <button type="button" onclick="javascript:postToWorkReport()" class="btn btn-secondary">戻る</button>
                            </div>
                            <div class="btn-group">
                                <button type="submit" class="btn btn-success">確認する</button>
                            </div>
                        </div>
                    </form>
                </div>
                {* JavaScript で使用する要素 *}
                <select class="d-none" name="work_flag_all">
                    {foreach from=$workFlagSelectAllList item=name key=value}
                        <option value="{$value}"{if $value==$workReport.work_flag} selected{/if}>{$name}</option>
                    {/foreach}
                </select>
            </section>
        </div>