
        <div class="work_time main-wrapper">
            <h1>作業時間入力</h1>

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
                        <input type="hidden" name="action" value="workTimeConfirm" />
                        <input type="hidden" name="csrf_token" value="{$CSRF_TOKEN}">

                        <h2>月別項目</h2>

                        <div class="form-row">
                            <label class="col-sm-3 col-form-label">氏名 <span class="badge badge-danger">必須</span></label>
                            <div class="col-sm-9">
                                <p>
                                    <input type="text" name="worker" class="form-control" required maxlength="255" />
                                </p>
                            </div>
                        </div>

                        <div class="form-row">
                            <label class="col-sm-3 col-form-label">客先 <span class="badge badge-danger">必須</span></label>
                            <div class="col-sm-9">
                                <p>
                                    <input type="text" name="customer" class="form-control" required maxlength="255" />
                                </p>
                            </div>
                        </div>

                        <h2>日別項目</h2>

                        <div class="form-row">
                            <label class="col-sm-3 col-form-label">勤務フラグ <span class="badge badge-danger">必須</span></label>
                            <div class="col-sm-9">
                                <p>
                                    <select class="custom-select w-50" title="勤務フラグ" name="work_flag">
                                        <option value="0" selected>平日通常出社</option>
                                        <option value="1">全日休暇</option>
                                        <option value="2">午前半休</option>
                                        <option value="3">午後半休</option>
                                        <option value="4">振替休暇</option>
                                        <option value="5">振替出勤</option>
                                        <option value="6">休日出勤</option>
                                        <option value="9">休日</option>
                                    </select>
                                </p>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="form-row">
                                <label for="datetimepicker-date" class="col-sm-3 col-form-label">日付 <span class="badge badge-danger">必須</span></label>
                                <div class="input-group col-sm-4 date" id="datetimepicker-date" data-target-input="nearest">
                                    <input type="text" class="form-control datetimepicker-input" name="date" data-target="#datetimepicker-date" required />
                                    <div class="input-group-append" data-target="#datetimepicker-date" data-toggle="datetimepicker">
                                        <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                    </div>
                                </div>
                                <div class="col-sm-5"></div>
                            </div>  
                        </div>

                        <div class="form-group">
                            <div class="form-row">
                                <label for="datetimepicker-start_time" class="col-sm-3 col-form-label">開始時刻 <span class="badge badge-danger">必須</span></label>
                                <div class="input-group col-sm-4 date" id="datetimepicker-start_time" data-target-input="nearest">
                                    <input type="text" class="form-control datetimepicker-input" name="start_time" data-target="#datetimepicker-start_time" required />
                                    <div class="input-group-append" data-target="#datetimepicker-start_time" data-toggle="datetimepicker">
                                        <div class="input-group-text"><i class="fa fa-clock-o"></i></div>
                                    </div>
                                </div>
                                <div class="col-sm-5"></div>
                            </div>  
                        </div>

                        <div class="form-group">
                            <div class="form-row">
                                <label for="datetimepicker-end_time" class="col-sm-3 col-form-label">終了時刻 <span class="badge badge-danger">必須</span></label>
                                <div class="input-group col-sm-4 date" id="datetimepicker-end_time" data-target-input="nearest">
                                    <input type="text" class="form-control datetimepicker-input" name="end_time" data-target="#datetimepicker-end_time" required />
                                    <div class="input-group-append" data-target="#datetimepicker-end_time" data-toggle="datetimepicker">
                                        <div class="input-group-text"><i class="fa fa-clock-o"></i></div>
                                    </div>
                                </div>
                                <div class="col-sm-5"></div>
                            </div>  
                        </div>

                        <div class="form-row">
                            <label class="col-sm-3 col-form-label">休憩時間 <span class="badge badge-danger">必須</span></label>
                            <div class="col-sm-2">
                                <p>
                                    <input type="text" name="break_time" class="form-control" required maxlength="5" />
                                </p>
                            </div>
                            <div class="col-sm-7"></div>
                        </div>

                        <div class="form-row">
                            <label class="col-sm-3 col-form-label">稼働時間</label>
                            <div class="col-sm-2">
                                <p>
                                    <input type="text" name="work_time" class="form-control" disabled />
                                </p>
                            </div>
                            <div class="col-sm-7"></div>
                        </div>

                        <div class="form-row">
                            <label class="col-sm-3 col-form-label">残業時間</label>
                            <div class="col-sm-2">
                                <p>
                                    <input type="text" name="over_time" class="form-control" maxlength="5" />
                                </p>
                            </div>
                            <div class="col-sm-7"></div>
                        </div>

                        <div class="form-row">
                            <label class="col-sm-3 col-form-label">休出時間</label>
                            <div class="col-sm-2">
                                <p>
                                    <input type="text" name="holiday_time" class="form-control" maxlength="5" />
                                </p>
                            </div>
                            <div class="col-sm-7"></div>
                        </div>

                        <div class="form-row">
                            <label class="col-sm-3 col-form-label">深夜時数</label>
                            <div class="col-sm-2">
                                <p>
                                    <input type="text" name="midnight_time" class="form-control" maxlength="5" />
                                </p>
                            </div>
                            <div class="col-sm-7"></div>
                        </div>

                        <div class="form-row">
                            <label class="col-sm-3 col-form-label">作業内容</span></label>
                            <div class="col-sm-9">
                                <p>
                                    <input type="text" name="work_content" class="form-control" maxlength="255" />
                                </p>
                            </div>
                        </div>

                        <div class="text-center my-5">
                            <button type="submit" class="btn btn-primary">確認する</button>
                        </div>
                    </form>
                </div>
            </section>
        </div>