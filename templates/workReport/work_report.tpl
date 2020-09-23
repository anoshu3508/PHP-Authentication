
        <div class="work_report main-wrapper">
            <h1>作業報告一覧</h1>

            {if isset($message)}
                {if $errorFlag}
                    <p class="error_message">{$message}</p>
                {else}
                    <p class="success_message">{$message}</p>
                {/if}
            {/if}

            <section class="work_report_monthly">
                <div class="container">
                    <form action="/employee/" method="POST">
                        <input type="hidden" name="action" value="outputWorkReport" />
                        <input type="hidden" name="csrf_token" value="{$CSRF_TOKEN}" />

                        <div class="d-flex justify-content-between mx-1 mb-2">
                            <div class="input-group col-8 mr-auto yyyymm" id="datetimepicker-yyyymm" data-target-input="nearest">
                                <div class="form-control datetimepicker-input" id="yyyymm-div">{$yyyymm}</div>
                                <input type="text" class="d-none form-control datetimepicker-input" name="yyyymm" value="{$yyyymm}" data-target="#datetimepicker-yyyymm" placeholder="月日を選択" />
                                <div class="input-group-append" data-target="#datetimepicker-yyyymm" data-toggle="datetimepicker">
                                    <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                </div>
                            </div>
                            <div class="col-4 text-right">
                                <button type="submit" class="btn btn-danger">出力</button>
                            </div>
                        </div>

                        <table class="table table-bordered">
                            <colgroup>
                                <col style="width: 35%" />
                                <col style="width: 65%" />
                            </colgroup>
                            <thead>
                                <tr class="table-warning">
                                    <th>氏名</th>
                                    <th>客先</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>{$worker}</td>
                                    <td>{$workReportMonthly->customer|default:'－'}</td>
                                </tr>
                            </tbody>
                        </table>
                    </form>
                </div>
            </section>

            <section class="work_report_daily">
                <div class="container">
                    <form action="/employee/" method="POST">
                        <input type="hidden" name="action" value="registWorkReport" />
                        <input type="hidden" name="csrf_token" value="{$CSRF_TOKEN}" />
                        <input type="hidden" name="yyyymm" value="{$yyyymm}" />

                        <div class="d-flex justify-content-between mx-2 mb-2">
                            <div>
                                <button type="button" class="regist_btn d-none btn btn-primary">作業時間入力</button>
                                <button type="button" class="regist_btn d-block btn btn-primary">作業入力</button>
                            </div>
                            <div class="d-flex justify-content-end text-sm-right">
                                <div>
                                    <input type="text" name="day" class="day form-control" value="1" maxlength="2" autocomplete="off" />
                                </div>
                                <div>
                                    <span class="mx-1">日を</span>
                                    <button type="submit" class="btn btn-primary">編集</button>
                                </div>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <colgroup>
                                    <col/>
                                    <col/>
                                    <col/>
                                    <col/>
                                    <col/>
                                    <col/>
                                    <col/>
                                    <col/>
                                    <col/>
                                    <col/>
                                </colgroup>
                                <thead>
                                    <tr class="table-success">
                                        <th>日</th>
                                        <th>曜日</th>
                                        <th>開始時刻</th>
                                        <th>終了時刻</th>
                                        <th>休憩時間</th>
                                        <th>稼働時間</th>
                                        <th>残業時間</th>
                                        <th>休出時間</th>
                                        <th>深夜時数</th>
                                        <th>作業内容</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {foreach from=$workReportDateList item=workReport key=day}
                                        <tr {if $workReport.holiday_flag === 1}class="table-secondary"{/if}>
                                            <td>{$day}</td>
                                            <td>{$workReport.week_name|default:'-'}</td>
                                            <td>{$workReport.start_time|default:''}</td>
                                            <td>{$workReport.end_time|default:''}</td>
                                            <td class="number">{$workReport.break_hours|default:''}</td>
                                            <td class="number">{$workReport.operation_hours|default:''}</td>
                                            <td class="number">
                                                {if isset($workReport.overtime_hours) && $workReport.overtime_hours !== '0.00'}
                                                    {$workReport.overtime_hours|default:''}
                                                {/if}
                                            </td>
                                            <td class="number">
                                                {if isset($workReport.holiday_hours) && $workReport.holiday_hours !== '0.00'}
                                                    {$workReport.holiday_hours|default:''}
                                                {/if}
                                            </td>
                                            <td class="number">
                                                {if isset($workReport.midnight_hours) && $workReport.midnight_hours !== '0.00'}
                                                    {$workReport.midnight_hours|default:''}
                                                {/if}
                                            </td>
                                            <td class="text">
                                                {if isset($workReport.work_flag) && $workReport.work_flag !== '0' && $workReport.work_flag !== '9'}
                                                    [{$workFlagList[$workReport.work_flag]}]
                                                {/if}
                                                {$workReport.work_description|default:''}
                                            </td>
                                        </tr>
                                    {foreachelse}
                                        <tr>
                                            <td>作業報告情報がありません。</td>
                                        </tr>
                                    {/foreach}
                                </tbody>
                            </table>
                        </div>
                    </form>
                </div>
            </section>
        </div>