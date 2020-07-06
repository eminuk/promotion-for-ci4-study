<!-- Using Layout -->
<?= $this->extend('Layouts/Admin_layout') ?>


<!-- head_add_area -->
<?= $this->section('head_add_area') ?>
<style type="text/css">
    [show-group~="excel_upload"] { display:none; }
</style>
<?= $this->endSection() ?>


<!-- content_area -->
<?= $this->section('content_area') ?>
    <form name="form_search" method="get" action="" accept-charset="utf-8">
        <input type="hidden" name="page_num" value="1" />
        <?= csrf_field() ?>
        신청여부: 
        <select name="is_select">
            <option value="ALL">전체</option>
            <option value="N">미신청</option>
            <option value="Y">신청완료</option>
        </select>
        <br />

        조회기간: 
        <input type="date" name="sdate" value="" /> ~
        <input type="date" name="edate" value="" />
        <br />

        <select name="search_key">
            <option value="kw_code">상품</option>
            <option value="car_number">차량번호</option>
            <option value="car_model">모델</option>
            <option value="cus_name">고객</option>
            <option value="cus_mobile">연락처</option>
            <option value="cus_zip">우편번호</option>
            <option value="cus_addr1">주소</option>
            <option value="cus_addr2">상세주소</option>
            <option value="bnft_price">상품권금액</option>
            <option value="bnft_code">상품코드</option>
            <option value="product">신청상품</option>
        </select>
        <input type="text" name="search_value" placeholder="Search" value="" />
        <button type="button" onclick="return doSearch(1);" >검색</button>
    </form>
    <br />

    <div>
        <table>
            <caption>여기 리스트</caption>
            <colgroup>
                <col />
            </colgroup>
            <colgroup span="7">
                <col />
                <col />
                <col />
                <col />
                <col />
                <col />
                <col />
            </colgroup>
            <colgroup>
                <col />
            </colgroup>
            <colgroup span="7">
                <col />
                <col />
                <col />
                <col />
                <col />
                <col />
                <col />
            </colgroup>
            <colgroup>
                <col />
            </colgroup>
            <thead>
                <tr>
                    <th></th>
                    <th>순번</th>
                    <th>상품</th>
                    <th>모델</th>
                    <th>차량번호</th>
                    <th>고객</th>
                    <th>연락처</th>
                    <th>상품권금액</th>
                    <th>상품코드</th>
                    <th>신청여부</th>
                    <th>신청일자</th>
                    <th>우편번호</th>
                    <th>주소</th>
                    <th>상세주소</th>
                    <th>신청상품</th>
                    <th>발송상태</th>
                    <th>SMS</th>
                </tr>
            </thead>
            <tbody id="list_body">
            </tbody>
        </table>
    </div>
    <br />

    <div>
        <button type="button" onclick="return doSearch(1);">1</button>
        <button type="button" onclick="return doSearch(2);">2</button>
        <button type="button" onclick="return doSearch(3);">3</button>
        <button type="button" onclick="return doSearch(4);">4</button>
        <button type="button" onclick="return doSearch(5);">5</button>
        <button type="button" onclick="return doSearch(6);">6</button>
        <button type="button" onclick="return doSearch(7);">7</button>
        <button type="button" onclick="return doSearch(8);">8</button>
        <button type="button" onclick="return doSearch(9);">9</button>
        <button type="button" onclick="return doSearch(10);">10</button>
        <button type="button" onclick="return doSearch(11);">11</button>
    </div>
    <br />

    <div>
        <button type="button" onclick="return doDelete();">삭제</button>
        <button type="button" onclick="return downloadExcel();">EXCEL 다운</button>
        <button type="button" onclick="return excelUploadUi();">EXCEL 업로드</button>
    </div>
    <br />


    <fieldset show-group="excel_upload">
        <legend>EXCEL 업로드</legend>
        <form name="form_upload" method="post" action="" enctype="multipart/form-data">
            <div>
                <a href="/admin/kcar/kwExcelSample" >양식 샘플 다운로드</a>
            </div>
            <div>
                <input type="file" name="file_excel" accept="application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" />
            </div>

            <hr />

            <button type="button" onclick="return doExcelUpload();">등록</button>
            <button type="button" onclick="return excelUploadUi();">취소</button>
        </form>
    </fieldset>
    <br />
<?= $this->endSection() ?>


<!-- footer_script_area -->
<?= $this->section('footer_script_area') ?>
<script type="text/javascript">
    // ajax - 검색
    function doSearch (page_num) {
        $('input[name=page_num]').val(page_num);
        $.ajax({
            url: '/api/admin/Kcar/kwList',
            type: 'GET',
            dataType: 'json',
            data: $('form[name=form_search]').serialize(),
            timeout: 30000,
            beforeSubmit: function (arr, form, options) {},
            beforeSend: function (jqXHR, settings) {},
            uploadProgress: function (event, position, total, percentComplete) {},
            success: function (data, textStatus, jqXHR) {
                if (!data.result) {
                    alert(data.message);
                    return;
                }

                $('#list_body').empty();

                if (data.data.list.length == 0) {
                    $('#list_body').append($('<tr/>')
                        .append($('<td/>'))
                        .append($('<td/>', { colspan: 16, text: '생성된 리스트가 없습니다.' }))
                    );
                }

                $.each(data.data.list, function (index, item) {
                    $('#list_body').append($('<tr/>')
                        .append($('<td/>')
                            .append($('<input/>', { 
                                type: 'checkbox',
                                name: 'kw_ids[]', 
                                value: item.id 
                            }))
                        )
                        .append($('<td/>', { text: item.id }))
                        .append($('<td/>', { text: item.kw_code }))
                        .append($('<td/>', { text: item.car_model }))
                        .append($('<td/>', { text: item.car_number }))
                        .append($('<td/>', { text: item.cus_name }))
                        .append($('<td/>', { text: item.cus_mobile }))
                        .append($('<td/>', { text: item.bnft_price }))
                        .append($('<td/>')
                            .append($('<button/>', {
                                type: 'button',
                                text: item.bnft_code,
                                click: function () {
                                    getProductInfo(item.kw_code, item.bnft_price);
                                }
                            }))
                        )
                        .append($('<td/>', { text: item.is_select_kr }))
                        .append($('<td/>', { text: item.select_at }))
                        .append($('<td/>', { text: item.cus_zip }))
                        .append($('<td/>', { text: item.cus_addr1 }))
                        .append($('<td/>', { text: item.cus_addr2 }))
                        .append($('<td/>', { text: item.type_kr }))
                        .append($('<td/>', { text: item.send_sms_kr }))
                        .append($('<td/>')
                            .append($('<button/>', {
                                type: 'button',
                                text: '발송',
                                click: function () {
                                    sendSms(item.id);
                                }
                            }))
                        )
                    );
                });


            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.log(jqXHR, textStatus, errorThrown);
                alert("Ajax error has occurred. \n" + errorThrown);
                return false;
            },
            complete: function (jqXHR, textStatus, form) {}
        });
        return false;
    }

    // ajax - 상품정보
    function getProductInfo (kw_code, bnft_price) {
        $.ajax({
            url: '/api/admin/Kcar/productInfo/' + kw_code + '/' + bnft_price,
            type: 'GET',
            dataType: 'json',
            data: {},
            timeout: 30000,
            beforeSubmit: function (arr, form, options) {},
            beforeSend: function (jqXHR, settings) {},
            uploadProgress: function (event, position, total, percentComplete) {},
            success: function (data, textStatus, jqXHR) {
                if (!data.result) {
                    alert(data.message);
                    return;
                }

                let msg = '상품구성 상세정보\n\r\n\r';
                msg += '상품코드: ' + data.data.bnft_code + '\n\r';
                msg += '세자상품: ' + data.data.wash_service + '\n\r';
                msg += '세차용품: ' + data.data.wash_goods + '\n\r';
                msg += '차량용품: ' + data.data.car_goods + '\n\r';

                alert(msg);
            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.log(jqXHR, textStatus, errorThrown);
                alert("Ajax error has occurred. \n" + errorThrown);
                return false;
            },
            complete: function (jqXHR, textStatus, form) {}
        });
        return false;
    }

    // ajax sms 발송요청
    function sendSms(id) {
        $.ajax({
            url: '/api/admin/Kcar/sms/',
            type: 'GET',
            dataType: 'json',
            data: { kw_id: id },
            timeout: 30000,
            beforeSubmit: function (arr, form, options) {},
            beforeSend: function (jqXHR, settings) {},
            uploadProgress: function (event, position, total, percentComplete) {},
            success: function (data, textStatus, jqXHR) {
                if (!data.result) {
                    alert(data.message);
                    return;
                }
                alert('SMS 전송 완료되었습니다.');
            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.log(jqXHR, textStatus, errorThrown);
                alert("Ajax error has occurred. \n" + errorThrown);
                return false;
            },
            complete: function (jqXHR, textStatus, form) {}
        });
        return false;
    }

    // ajax 삭제
    function doDelete() {
        let ids_cnt = $('input[name="kw_ids[]"]:checked').length;
        if (ids_cnt == 0) {
            alert('삭제할 항목을 선택해주세요.');
            return false;
        }

        if (!confirm('총' + ids_cnt + '개의 항목을 삭제하시겠습니까?')) {
            return false;
        }

        $.ajax({
            url: '/api/admin/Kcar/kw',
            type: 'DELETE',
            dataType: 'json',
            data: $('input[name="kw_ids[]"]:checked').serialize(),
            timeout: 30000,
            beforeSubmit: function (arr, form, options) {},
            beforeSend: function (jqXHR, settings) {},
            uploadProgress: function (event, position, total, percentComplete) {},
            success: function (data, textStatus, jqXHR) {
                if (!data.result) {
                    alert(data.message);
                    return;
                }

                // alert(data.data.affected_row + '건이 삭제 되었습니다.');
                alert('삭제되었습니다.');

                // Reload list
                doSearch($('input[name=page_num]').val());
            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.log(jqXHR, textStatus, errorThrown);
                alert("Ajax error has occurred. \n" + errorThrown);
                return false;
            },
            complete: function (jqXHR, textStatus, form) {}
        });
        return false;
    }

    // ajax EXCEL 업로드
    function doExcelUpload() {
        let file_obj = $('form[name=form_upload] input[name=file_excel]');

        if (file_obj.val() == '') {
            alert('파일을 선택해주세요.');
            return false;
        }

        // Make and Add form data
        let form_data = new FormData($('form[name=form_upload]')[0]);
        form_data.append("file_excel", file_obj[0].files[0]);

        $.ajax({
            url: '/api/admin/Kcar/kw',
            type: 'POST',
            dataType: 'json',
            processData: false,
            contentType: false,
            data: form_data,
            timeout: 30000,
            beforeSubmit: function (arr, form, options) {},
            beforeSend: function (jqXHR, settings) {},
            uploadProgress: function (event, position, total, percentComplete) {},
            success: function (data, textStatus, jqXHR) {
                if (!data.result) {
                    alert(data.message);
                    return;
                }

                console.log(data); return;

                // alert(data.data.affected_row + '건이 삭제 되었습니다.');
                alert('등록되었습니다.');

                // Reload list
                doSearch(1);
            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.log(jqXHR, textStatus, errorThrown);
                alert("Ajax error has occurred. \n" + errorThrown);
                return false;
            },
            complete: function (jqXHR, textStatus, form) {}
        });
        return false;
    }

    // Download excel
    function downloadExcel () {
        if ($('input[name=sdate]').val() == '') {
            alert('날짜를 선택해 주세요.');
            return;
        }
        if ($('input[name=edate]').val() == '') {
            alert('날짜를 선택해 주세요.');
            return;
        }
        location.href = '/admin/kcar/kwListExcel?' + $('form[name=form_search]').serialize();
    }

    // toggle
    function excelUploadUi() {
        $('form[name=form_upload]')[0].reset();
        $('[show-group~="excel_upload"]').toggle();
    }


    // 페이지 로딩시 기본 검색
    doSearch(1);
</script>
<?= $this->endSection() ?>