
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
                        <input type="hidden" name="action" value="registWorkReport" />
                        <input type="hidden" name="csrf_token" value="{$CSRF_TOKEN}">

                            <div class="d-flex justify-content-around mb-2">
                                <div class="input-group col-8 mr-auto yyyymm" id="datetimepicker-yyyymm" data-target-input="nearest">
                                    <div class="form-control datetimepicker-input" id="yyyymm-div">{$yyyymm}</div>
                                    <input type="text" class="form-control datetimepicker-input" name="yyyymm" value="{$yyyymm}" data-target="#datetimepicker-yyyymm" placeholder="月日を選択" />
                                    <div class="input-group-append" data-target="#datetimepicker-yyyymm" data-toggle="datetimepicker">
                                        <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                    </div>
                                </div>
                                <div class="col-4 text-right">
                                    <button type="submit" class="btn btn-primary">登録</button>
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
                                        <td>{$workReportMonthly->customer|default:'-'}</td>
                                    </tr>
                                </tbody>
                            </table>
                    </form>
                </div>
            </section>

            <section class="work_report_daily">
                {if $workReportMonthly}
                    <div class="container">
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
                                            <td class="number">{$workReport.overtime_hours|default:''}</td>
                                            <td class="number">{$workReport.holiday_hours|default:''}</td>
                                            <td class="number">{$workReport.midnight_hours|default:''}</td>
                                            <td class="text">{$workReport.work_description|default:''}</td>
                                        </tr>
                                    {/foreach}
                                </tbody>
                            </table>
                        </div>
                    </div>
                {else}
                    <p class="text-center my-5">作業報告情報がありません。</p>
                {/if}
            </section>
        </div>