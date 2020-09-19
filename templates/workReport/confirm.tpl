
        <div class="confirm_work_report main-wrapper">
            <h1>作業報告確認</h1>

            <section>
                <div class="container">
                    <form action="/employee/" method="POST">
                        <input type="hidden" name="action" value="completeWorkReport" />
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
                                <p class="px-1 py-2">{$workReport['customer']}</p>
                            </div>
                        </div>

                        <h2>日別項目</h2>

                        <div class="form-row">
                            <label class="col-sm-3 col-form-label">勤務フラグ <span class="badge badge-danger">必須</span></label>
                            <div class="col-sm-9">
                                <p class="px-1 py-2">{$workReport['work_flag_name']}</p>
                            </div>
                        </div>

                        <div class="form-row">
                            <label class="col-sm-3 col-form-label">日付 <span class="badge badge-danger">必須</span></label>
                            <div class="col-sm-9">
                                <p class="px-1 py-2">{$workReport['date']}</p>
                            </div>
                        </div>

                         <div class="form-row">
                            <label class="col-sm-3 col-form-label">開始時刻 <span class="badge badge-danger">必須</span></label>
                            <div class="col-sm-9">
                                <p class="px-1 py-2">{$workReport['start_time']}</p>
                            </div>
                        </div>

                         <div class="form-row">
                            <label class="col-sm-3 col-form-label">終了時刻 <span class="badge badge-danger">必須</span></label>
                            <div class="col-sm-9">
                                <p class="px-1 py-2">{$workReport['end_time']}</p>
                            </div>
                        </div>

                        <div class="form-row">
                            <label class="col-sm-3 col-form-label">休憩時間 <span class="badge badge-danger">必須</span></label>
                            <div class="col-sm-9">
                                <p class="px-1 py-2">{$workReport['break_hours']}</p>
                            </div>
                        </div>

                        <div class="form-row">
                            <label class="col-sm-3 col-form-label">稼働時間</label>
                            <div class="col-sm-9">
                                <p class="px-1 py-2">{$workReport['operation_hours']}</p>
                            </div>
                        </div>

                        <div class="form-row">
                            <label class="col-sm-3 col-form-label">残業時間</label>
                            <div class="col-sm-9">
                                <p class="px-1 py-2">{$workReport['overtime_hours']}</p>
                            </div>
                        </div>

                        <div class="form-row">
                            <label class="col-sm-3 col-form-label">休出時間</label>
                            <div class="col-sm-9">
                                <p class="px-1 py-2">{$workReport['holiday_hours']}</p>
                            </div>
                        </div>

                        <div class="form-row">
                            <label class="col-sm-3 col-form-label">深夜時数</label>
                            <div class="col-sm-9">
                                <p class="px-1 py-2">{$workReport['midnight_hours']}</p>
                            </div>
                        </div>

                        <div class="form-row">
                            <label class="col-sm-3 col-form-label">作業内容</label>
                            <div class="col-sm-9">
                                <p class="px-1 py-2">{$workReport['work_description']}</p>
                            </div>
                        </div>

                        <div class="btn-toolbar justify-content-around my-5">
                            <div class="btn-group">
                                <button type="button" onclick="javascript:postToRegistWorkReport()" class="btn btn-secondary">戻る</button>
                            </div>
                            <div class="btn-group">
                                <button type="submit" class="btn btn-success">登録する</button>
                            </div>
                        </div>
                    </form>
                </div>
            </section>
        </div>