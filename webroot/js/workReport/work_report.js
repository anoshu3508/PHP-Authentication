$(function() {
    /**
     * 日付カレンダー
     */
    $('.work_report #datetimepicker-yyyymm').datetimepicker({
        // dayViewHeaderFormat: 'YYYY年 MMMM',
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
        format: 'YYYY/MM',
        locale: 'ja',
        buttons: {
            // showClose: true
        }
    });

    /**
     * 年月選択
     */
    $('html').click(function() {
        $('.work_report input[name="yyyymm"]').blur();
    });
    var yyyymmOld = $('.work_report input[name="yyyymm"]').val();
    $('.work_report input[name="yyyymm"]').on('input', function() {
        var yyyymm = $(this).val();
        if (yyyymm != '') {
            if (yyyymm != yyyymmOld) {
                $('.work_report #yyyymm-div').text($(this).val());
            }
            yyyymmOld = yyyymm;
        }
	});
    $(document).on('DOMSubtreeModified propertychange', '.work_report #yyyymm-div', function() {
        $('<form/>',{action:"/employee/", method:"post"})
        .append('<input type="hidden" name="action" value="workReport">')
        .append('<input type="hidden" name="csrf_token" value="' + CSRF_TOKEN + '">')
        .append('<input type="hidden" name="yyyymm" value="' + $(this).text() + '">')
        .appendTo($('body'))
        .submit();
	});
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
 * 「作業報告登録」に移動
 * 
 * @return {undefined}
 */
function postToRegistWorkReport() {
    postToAction('registWorkReport');
}