$(function() {
    // 勤務フラグ別に関連項目を更新
    updateRelatedItemsByWorkFlag();

    /**
     * ページを離れる際の確認メッセージ
     */
    $(window).on('beforeunload', function() {
        return '入力内容を破棄します。';
    });

    /**
     * フォーム送信前処理
     */
    $(function() {
        var submitFlag = false;
        $('.regist_work_report form').submit(function() {
            if (submitFlag) {
                return false;
            }
            submitFlag = true;
            $(window).off('beforeunload');
        });
    });

    /**
     * 勤務フラグ
     */
    $('.regist_work_report select[name="work_flag"]').change(function() {
        // 勤務フラグ別に関連項目を更新
        updateRelatedItemsByWorkFlag();
	});

    /**
     * 日付
     */
    $(function() {
        var holidayList;

        $('.regist_work_report #datetimepicker-date').datetimepicker({
            dayViewHeaderFormat: 'YYYY年 MMMM',
            tooltips: {
                close: '閉じる',
                selectMonth: '月を選択',
                prevMonth: '前月',
                nextMonth: '次月',
                selectYear: '年を選択',
                prevYear: '前年',
                nextYear: '次年',
                selectTime: '時間を選択',
                selectDate: '日付を選択',
                prevDecade: '前期間',
                nextDecade: '次期間',
                selectDecade: '期間を選択',
                prevCentury: '前世紀',
                nextCentury: '次世紀'
            },
            format: 'YYYY/MM/DD',
            locale: 'ja',
            buttons: {
                // showClose: true
            }
        });

        $('.regist_work_report #datetimepicker-date').on('change.datetimepicker', function(e) {
            if (e.date === undefined) {
                return true;
            }
            var year = e.date.year(),
                regex = new RegExp(/^(200[5-9]{1}|20[1-9]{1}[0-9]{1})$/),
                holidayDate;

            if (!regex.test(year)) {
                alert('日付は2005～2099年の間を指定してください');
                $('.regist_work_report input[name="date"]').val(e.oldDate.format('YYYY/MM/DD'))
                return false;
            }

            // 同じ年の祝日情報が存在する場合処理終了
            if (holidayList !== undefined && holidayList.length > 0) {
                holidayDate = moment(holidayList[0].replace(/\//g, '-'));
                if (holidayDate.year() === year) {
                    // 勤務フラグのセレクトボックスを更新
                    updateWorkFlagSelectBox();
                    // 勤務フラグ別に関連項目を更新
                    updateRelatedItemsByWorkFlag();
                    return false;
                }
            }

            // 祝日情報を非同期で取得
            try {
                $.ajax({
                    url: '/employee/?api=holidayJson',
                    type: 'POST',
                    cache: false,
                    data: {
                        'year': year
                    },
                    dataType: 'json'
                })
                .done(function(ret) {
                    holidayList = ret;
                    // 勤務フラグのセレクトボックスを更新
                    updateWorkFlagSelectBox();
                    // 勤務フラグ別に関連項目を更新
                    updateRelatedItemsByWorkFlag();
                })
                .fail(function() {
                    alert('祝日情報の取得に失敗しました。')
                });
            } catch(e) {
                console.log(e);
            }
        });

        /**
         * 勤務フラグのセレクトボックスを更新
         *
         * @return {undefined}
         */
        function updateWorkFlagSelectBox() {
            var $workFlagSelectAllBox = $('.regist_work_report select[name="work_flag_all"]').clone(),
                $workFlagSelectBox = $('.regist_work_report select[name="work_flag"]'),
                date = $('.regist_work_report input[name="date"]').val(),
                dayOfWeek,
                holidayResult;

            // 曜日を取得
            dayOfWeek = moment(date.replace(/\//g, '-')).weekday()

            // 土日かどうかチェック
            if (dayOfWeek === 0 || dayOfWeek === 6) {
                holidayResult = date;
            }

            // 土日以外の場合、祝日かどうかチェック
            if (!holidayResult) {
                holidayResult = holidayList.find((hDate) => hDate === date);
            }

            // 休日と平日で処理切り分け
            if (holidayResult) {
                // 休日
                $workFlagSelectBox.html($workFlagSelectAllBox.children('option').slice(5));
                $workFlagSelectBox.find('option[value="9"]').prop('selected', true);
            } else {
                // 平日
                $workFlagSelectBox.html($workFlagSelectAllBox.children('option').slice(0, 5));
                $workFlagSelectBox.find('option[value="0"]').prop('selected', true);
            }
        }
    });

    /**
     * 開始時刻
     */
    $(function() {
        $('.regist_work_report #datetimepicker-start_time').datetimepicker({
            tooltips: {
                close: '閉じる',
                pickHour: '時間を取得',
                incrementHour: '時間を増加',
                decrementHour: '時間を減少',
                pickMinute: '分を取得',
                incrementMinute: '分を増加',
                decrementMinute: '分を減少',
                pickSecond: '秒を取得',
                incrementSecond: '秒を増加',
                decrementSecond: '秒を減少',
                togglePeriod: '午前/午後切替',
                selectTime: '時間を選択'
            },
            format: 'HH:mm',
            locale: 'ja',
            buttons: {
                // showClose: true
            }
        });

        $('.regist_work_report #datetimepicker-start_time').on('change.datetimepicker', function(e) {
            setTimeout(function() {
                calcOperationHours();
                updateRelatedItemsByWorkFlag();
            }, 20);
        });
    });

    /**
     * 終了時刻
     */
    $(function() {
        $('.regist_work_report #datetimepicker-end_time').datetimepicker({
            tooltips: {
                close: '閉じる',
                pickHour: '時間を取得',
                incrementHour: '時間を増加',
                decrementHour: '時間を減少',
                pickMinute: '分を取得',
                incrementMinute: '分を増加',
                decrementMinute: '分を減少',
                pickSecond: '秒を取得',
                incrementSecond: '秒を増加',
                decrementSecond: '秒を減少',
                togglePeriod: '午前/午後切替',
                selectTime: '時間を選択'
            },
            format: 'HH:mm',
            locale: 'ja',
            buttons: {
                // showClose: true
            }
        });

        $('.regist_work_report #datetimepicker-end_time').on('change.datetimepicker', function(e) {
            setTimeout(function() {
                calcOperationHours();
                updateRelatedItemsByWorkFlag();
            }, 20);
        });
    });

    /**
     * 休憩時間
     */
    $('.regist_work_report input[name="break_hours"]').change(function() {
        // 全角英数字を半角英数字に置き換え
        var numStr = convertZenkakuToHankaku($(this).val());

        // 小数点を含まない または 整数部が3桁以上の場合は、先頭から2文字を取得
        var numStrArray = numStr.split('.');
        if (numStr.indexOf('.') == -1 || numStrArray[0].length > 2) {
            numStr = numStr.substr(0, 2);
        }
        var num = parseFloat(numStr);

        if (isNaN(num)) {
		    $(this).val('');
        } else {
            $(this).val(num.toFixed(2));
        }

        // 稼働時間を計算
        calcOperationHours();
        // 勤務フラグ別に関連項目を更新
        updateRelatedItemsByWorkFlag();
	});

    /**
     * 残業時間
     */
    $('.regist_work_report input[name="overtime_hours"]').change(function() {
        //全角英数字を半角英数字に置き換え
        var numStr = convertZenkakuToHankaku($(this).val());

        // 小数点を含まない または 整数部が3桁以上の場合は、先頭から2文字を取得
        var numStrArray = numStr.split('.');
        if (numStr.indexOf('.') == -1 || numStrArray[0].length > 2) {
            numStr = numStr.substr(0, 2);
        }
        var num = parseFloat(numStr);

        if (isNaN(num)) {
		    $(this).val('');
        } else {
            $(this).val(num.toFixed(2));
        }
        // 勤務フラグ別に関連項目を更新
        updateRelatedItemsByWorkFlag();
	});

    /**
     * 休出時間
     */
    $('.regist_work_report input[name="holiday_hours"]').change(function() {
        //全角英数字を半角英数字に置き換え
        var numStr = convertZenkakuToHankaku($(this).val());

        // 小数点を含まない または 整数部が3桁以上の場合は、先頭から2文字を取得
        var numStrArray = numStr.split('.');
        if (numStr.indexOf('.') == -1 || numStrArray[0].length > 2) {
            numStr = numStr.substr(0, 2);
        }
        var num = parseFloat(numStr);

        if (isNaN(num)) {
		    $(this).val('');
        } else {
            $(this).val(num.toFixed(2));
        }
        // 勤務フラグ別に関連項目を更新
        updateRelatedItemsByWorkFlag();
	});

    /**
     * 深夜時数
     */
    $('.regist_work_report input[name="midnight_hours"]').change(function() {
        //全角英数字を半角英数字に置き換え
        var numStr = convertZenkakuToHankaku($(this).val());

        // 小数点を含まない または 整数部が3桁以上の場合は、先頭から2文字を取得
        var numStrArray = numStr.split('.');
        if (numStr.indexOf('.') == -1 || numStrArray[0].length > 2) {
            numStr = numStr.substr(0, 2);
        }
        var num = parseFloat(numStr);

        if (isNaN(num)) {
		    $(this).val('');
        } else {
            $(this).val(num.toFixed(2));
        }
        // 勤務フラグ別に関連項目を更新
        updateRelatedItemsByWorkFlag();
    });

    /**
     * 全角英数字を半角英数字に変換
     *
     * @param {string} val 全角英数字
     * @return {string} 半角英数字
     */
    function convertZenkakuToHankaku(val) {
        return val.replace(/[Ａ-Ｚａ-ｚ０-９]/g, function(s) {
            return String.fromCharCode(s.charCodeAt(0)-0xFEE0)
        });
    }

    /**
     * 勤務フラグ別に関連項目を更新
     *
     * @return {undefined}
     */
    function updateRelatedItemsByWorkFlag() {
        // 各input要素を取得
        var $overtimeHoursInput = $('.regist_work_report input[name="overtime_hours"]'), // 残業時間
            $holidayHoursInput = $('.regist_work_report input[name="holiday_hours"]'),   // 休出時間
            $midnightHoursInput = $('.regist_work_report input[name="midnight_hours"]'); // 深夜時数

        // 勤務フラグ別に処理
        var workFlag = $('.regist_work_report select[name="work_flag"]').val();
        switch (workFlag) {
            // 平日通常出社
            case '0':
                disabledWorkTimeInput(false);
                calcOvertimeHours(8.00);
                $holidayHoursInput.val('');
                break;
            // 全日休暇
            case '1':
                disabledWorkTimeInput(false);
                $overtimeHoursInput.val('');
                $holidayHoursInput.val('');
                break;
            // 午前半休
            case '2':
                disabledWorkTimeInput(false);
                calcOvertimeHours(5.00);
                $holidayHoursInput.val('');
                break;
            // 午後半休
            case '3':
                disabledWorkTimeInput(false);
                calcOvertimeHours(3.00);
                $holidayHoursInput.val('');
                break;
            // 振替休暇
            case '4':
                disabledWorkTimeInput(true);
                $overtimeHoursInput.val('');
                $holidayHoursInput.val('');
                break;
            // 振替出勤
            case '5':
                disabledWorkTimeInput(false);
                calcOvertimeHours(8.00);
                $holidayHoursInput.val('');
                break;
            // 休日出勤
            case '6':
                disabledWorkTimeInput(false);
                $overtimeHoursInput.val('');
                calcHolidayHours();
                break;
            // 休日
            case '9':
                disabledWorkTimeInput(true);
                $overtimeHoursInput.val('');
                $holidayHoursInput.val('');
                break;

            default:
                break;
        }
        // 稼働時間を計算
        calcOperationHours();
        // 深夜時数を計算
        calcMidnightHours();
    }

    /**
     * 開始時刻・終了時刻・休憩時間の活性／ひっ活性
     *
     * @param {boolean} disabled true:非活性／false:活性
     * @return {undefined}
     */
    function disabledWorkTimeInput(disabled) {
        var $startTimeInput = $('.regist_work_report input[name="start_time"]'),         // 開始時刻
            $endTimeInput = $('.regist_work_report input[name="end_time"]'),             // 終了時刻
            $breakHoursInput = $('.regist_work_report input[name="break_hours"]');       // 休憩時間

        // 非活性化
        if (disabled) {
            if (!$startTimeInput.prop('disabled')) {
                $startTimeInput.val('');
                $startTimeInput.prop('disabled', true);
            }
            if (!$endTimeInput.prop('disabled')) {
                $endTimeInput.val('');
                $endTimeInput.prop('disabled', true);
            }
            if (!$breakHoursInput.prop('disabled')) {
                $breakHoursInput.val('');
                $breakHoursInput.prop('disabled', true);
            }
        // 活性化
        } else {
            if ($startTimeInput.prop('disabled')) {
                $startTimeInput.prop('disabled', false);
            }
            if ($endTimeInput.prop('disabled')) {
                $endTimeInput.prop('disabled', false);
            }
            if ($breakHoursInput.prop('disabled')) {
                $breakHoursInput.prop('disabled', false);
            }
        }
    }

    /**
     * 稼働時間を計算
     *
     * @return {undefined}
     */
    function calcOperationHours() {
        var startTime = $('.regist_work_report input[name="start_time"]').val();
        var endTime = $('.regist_work_report input[name="end_time"]').val();
        var breakHours = $('.regist_work_report input[name="break_hours"]').val();

        if (startTime == '' || endTime == '' || breakHours == '') {
            $('.regist_work_report input[name="operation_hours"]').val('');
            return false;
        }

        var startDateTime = moment(startTime, 'HH:mm');
        var endDateTime = moment(endTime, 'HH:mm');
        var diff = endDateTime.diff(startDateTime, 'hours', true);
        if (diff < 0) {
            endDateTime = endDateTime.add(1, 'days');
            diff = endDateTime.diff(startDateTime, 'hours', true);
        }
        var operationHours = diff - parseFloat(breakHours);
        $('.regist_work_report input[name="operation_hours"]').val(operationHours.toFixed(2))
    }

    /**
     * 残業時間を計算
     *
     * @param {number} scheduledHours 所定労働時間
     * @return {undefined}
     */
    function calcOvertimeHours(scheduledHours) {
        // 残業時間を初期化
        $('.regist_work_report input[name="overtime_hours"]').val('');
        // 稼働時間を取得
        var opHoursNum = parseFloat($('.regist_work_report input[name="operation_hours"]').val());

        if (!isNaN(opHoursNum) && parseFloat(opHoursNum) > 0.00) {
            var num = opHoursNum - scheduledHours;
            if (num > 0.00) {
                $('.regist_work_report input[name="overtime_hours"]').val(num.toFixed(2));
            }
        }
    }

    /**
     * 休出時間を計算
     *
     * @return {undefined}
     */
    function calcHolidayHours() {
        var operationHours = $('.regist_work_report input[name="operation_hours"]').val();
        $('.regist_work_report input[name="holiday_hours"]').val(operationHours);
    }

    /**
     * 深夜時数を計算
     *
     * @return {undefined}
     */
    function calcMidnightHours() {
        var endTime = $('.regist_work_report input[name="end_time"]').val();
        var midnightTime = '22:00';

        if (endTime == '') {
            $('.regist_work_report input[name="midnight_hours"]').val('');
            return false;
        }

        var endDateTime = moment(endTime, 'HH:mm'),
            midnightDateTime = moment(midnightTime, 'HH:mm'),
            midnightHours;
        if (
            endDateTime.hours() > 22
            || (endDateTime.hours() === 22 && endDateTime.minutes() > 0)
            || (endDateTime.hours() >= 0 && endDateTime.hours() <= 5)
        ) {
            midnightHours = endDateTime.diff(midnightDateTime, 'hours', true);
            if (midnightHours < 0) {
                endDateTime = endDateTime.add(1, 'days');
                midnightHours = endDateTime.diff(midnightDateTime, 'hours', true);
            }
            midnightHours = midnightHours.toFixed(2);
        } else {
            midnightHours = '';
        }
        $('.regist_work_report input[name="midnight_hours"]').val(midnightHours);
    }
});

/**
 * 「作業報告一覧」に移動
 *
 * @return {undefined}
 */
function postToWorkReport() {
    postToAction('workReport');
}

/**
 * 「作業報告確認」に移動
 *
 * @return {undefined}
 */
function postToConfirmWorkReport() {
    postToAction('confirmWorkReport');
}