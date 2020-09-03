$(function() {
    /**
     * ページを離れる際の確認メッセージ
     */
    $(window).on('beforeunload', function() {
        return '入力内容を破棄します。';
    });

    $('.work_time form').submit(function() {
        $(window).off('beforeunload');
    })

    /**
     * 日付カレンダー
     */
    $('.work_time #datetimepicker-date').datetimepicker({
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
    $('.work_time #datetimepicker-start_time').datetimepicker({
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

    /**
     * 終了時刻
     */
    $('.work_time #datetimepicker-end_time').datetimepicker({
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

    /**
     * 休憩時間
     */
    $('.work_time input[name="break_time"]').change(function(){
        var num = parseFloat($(this).val());
        if (isNaN(num)) {
		    $(this).val('0.00');
        } else {
            $(this).val(num.toFixed(2));
        }
	});

    /**
     * 残業時間
     */
    $('.work_time input[name="over_time"]').change(function(){
        var num = parseFloat($(this).val());
        if (isNaN(num)) {
		    $(this).val('0.00');
        } else {
            $(this).val(num.toFixed(2));
        }
	});

    /**
     * 休出時間
     */
    $('.work_time input[name="holiday_time"]').change(function(){
        var num = parseFloat($(this).val());
        if (isNaN(num)) {
		    $(this).val('0.00');
        } else {
            $(this).val(num.toFixed(2));
        }
	});

    /**
     * 深夜時数
     */
    $('.work_time input[name="midnight_time"]').change(function(){
        var num = parseFloat($(this).val());
        if (isNaN(num)) {
		    $(this).val('0.00');
        } else {
            $(this).val(num.toFixed(2));
        }
	});
});

/**
 * 「作業時間確認」に移動
 * 
 * @return {undefined}
 */
function postToWorkTimeConfirm() {
    postToAction('WorkTimeConfirm');
}