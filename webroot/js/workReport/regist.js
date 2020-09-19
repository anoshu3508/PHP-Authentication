$(function() {
    /**
     * ページを離れる際の確認メッセージ
     */
    $(window).on('beforeunload', function() {
        return '入力内容を破棄します。';
    });

    $('.regist_work_report form').submit(function() {
        $(window).off('beforeunload');
    })

    /**
     * 日付カレンダー
     */
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

    /**
     * 開始時刻
     */
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
        }, 20);
    })

    /**
     * 終了時刻
     */
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
        }, 20);
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
     * 稼働時間を計算
     * 
     * @return {undefined}
     */
    function calcOperationHours() {
        var startTime = $('.regist_work_report input[name="start_time"]').val();
        var endTime = $('.regist_work_report input[name="end_time"]').val();
        var breakHours = $('.regist_work_report input[name="break_hours"]').val();

        if (startTime == '' || endTime == '' || breakHours == '') {
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