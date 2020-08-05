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
            <option value="pm_code">등급코드</option>
            <!-- <option value="car_number">차량번호</option> -->
            <!-- <option value="car_model">모델</option> -->
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
                    <th><label for="ids_all"><input type="checkbox" id="ids_all"></label></th>

                    <th>순번</th>
                    <th>등급코드</th>
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
                <a href="/admin/kcar/importExcelSample" >양식 샘플 다운로드</a>
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

<!-- Daum 우편번호 서비스 -->
<script src="https://t1.daumcdn.net/mapjsapi/bundle/postcode/prod/postcode.v2.js"></script>
<script type="text/javascript">
    // 우번번호 찾기 팝업 상태 플레그
    var daum_postcode_pop = false;

    // 우편번호 찾기 찾기 화면을 넣을 element
    var element_wrap = document.getElementById('postcode_wrap');

    // 우번번호 찾기 팝업 닫기
    function foldDaumPostcode() {
        // iframe을 넣은 element를 안보이게 한다.
        element_wrap.style.display = 'none';
    }

    // 우번번호 찾기 팝업 열기/닫기
    function execDaumPostcode() {
        // 팝업 토글
        if (daum_postcode_pop) {
            daum_postcode_pop = false;
            element_wrap.style.display = 'none';
            return false;
        } else {
            daum_postcode_pop = true;
        }

        // 현재 scroll 위치를 저장해놓는다.
        var currentScroll = Math.max(document.body.scrollTop, document.documentElement.scrollTop);
        new daum.Postcode({
            oncomplete: function(data) {
                // 검색결과 항목을 클릭했을때 실행할 코드를 작성하는 부분.

                // 각 주소의 노출 규칙에 따라 주소를 조합한다.
                // 내려오는 변수가 값이 없는 경우엔 공백('')값을 가지므로, 이를 참고하여 분기 한다.
                var addr = ''; // 주소 변수
                var extraAddr = ''; // 참고항목 변수

                // 사용자가 선택한 주소 타입에 따라 해당 주소 값을 가져온다.
                if (data.userSelectedType === 'R') { // 사용자가 도로명 주소를 선택했을 경우
                    addr = data.roadAddress;
                } else { // 사용자가 지번 주소를 선택했을 경우(J)
                    addr = data.jibunAddress;
                }

                // 사용자가 선택한 주소가 도로명 타입일때 참고항목을 조합한다.
                if (data.userSelectedType === 'R') {
                    // 법정동명이 있을 경우 추가한다. (법정리는 제외)
                    // 법정동의 경우 마지막 문자가 "동/로/가"로 끝난다.
                    if (data.bname !== '' && /[동|로|가]$/g.test(data.bname)) {
                        extraAddr += data.bname;
                    }
                    // 건물명이 있고, 공동주택일 경우 추가한다.
                    if (data.buildingName !== '' && data.apartment === 'Y') {
                        extraAddr += (extraAddr !== '' ? ', ' + data.buildingName : data.buildingName);
                    }
                    // 표시할 참고항목이 있을 경우, 괄호까지 추가한 최종 문자열을 만든다.
                    if(extraAddr !== ''){
                        extraAddr = ' (' + extraAddr + ')';
                    }
                    // 조합된 참고항목을 해당 필드에 넣는다.
                    // document.getElementById("sample3_extraAddress").value = extraAddr;
                
                } else {
                    // document.getElementById("sample3_extraAddress").value = '';
                }

                // 우편번호와 주소 정보를 해당 필드에 넣는다.
                // document.getElementById('sample3_postcode').value = data.zonecode;
                // document.getElementById("sample3_address").value = addr;
                $('form[name=form_product_select] input[name=cus_zip]').val(data.zonecode);
                $('form[name=form_product_select] input[name=cus_addr1]').val(addr);
                // $('form[name=form_product_select] input[name=cus_addr2]').val(extraAddr);
                $('form[name=form_product_select] input[name=cus_addr2]').val('');

                // 커서를 상세주소 필드로 이동한다.
                // document.getElementById("sample3_detailAddress").focus();
                $('form[name=form_product_select] input[name=cus_addr2]').focus();

                // iframe을 넣은 element를 안보이게 한다.
                // (autoClose:false 기능을 이용한다면, 아래 코드를 제거해야 화면에서 사라지지 않는다.)
                element_wrap.style.display = 'none';

                // 우편번호 찾기 화면이 보이기 이전으로 scroll 위치를 되돌린다.
                // document.body.scrollTop = currentScroll;

                daum_postcode_pop = false;
            },
            // 우편번호 찾기 화면 크기가 조정되었을때 실행할 코드를 작성하는 부분. iframe을 넣은 element의 높이값을 조정한다.
            onresize : function(size) {
                element_wrap.style.height = size.height + 'px';
            },
            width : '100%',
            height : '100%'
        }).embed(element_wrap);

        // iframe을 넣은 element를 보이게 한다.
        element_wrap.style.display = 'block';
    }
</script>

<!-- jQuery DateTimePicker -->
<link rel="stylesheet" type="text/css" href="/asset/jquery-plugin/datetimepicker/jquery.datetimepicker.min.css" />
<script src="/asset/jquery-plugin/datetimepicker/jquery.datetimepicker.full.min.js"></script>
<script type="text/javascript">
    $.datetimepicker.setLocale('ko');
    let datetimepicker_opt = {
        datepicker: true,
        timepicker: true,
        format: 'Y-m-d H:i:00',
        step: 30,
    };
    $('form[name=form_product_select] input[name=hope_1]').datetimepicker(datetimepicker_opt);
    $('form[name=form_product_select] input[name=hope_2]').datetimepicker(datetimepicker_opt);
    $('form[name=form_product_select] input[name=hope_3]').datetimepicker(datetimepicker_opt);
</script>


<script type="text/javascript">
    $(function () {
        // 리스트 전체 선택/해제
        $('#ids_all').change(function () {
            if ($(this).is(":checked")) {
                $('input[name="ids[]"]').prop('checked', true);
            } else {
                $('input[name="ids[]"]').prop('checked', false);
            }
        });

        // 신청 상품 등록/수정 상품선택 분기
        $('#modal-customre-select input[name=type]').change(function () {
            if ($('#modal-customre-select input[name=type]:checked').val() == 1) {
                $('[show-group~="type_1"]').show();
            } else {
                $('[show-group~="type_1"]').hide();
                $('#modal-customre-select input[name=hope_1]').val('');
                $('#modal-customre-select input[name=hope_2]').val('');
                $('#modal-customre-select input[name=hope_3]').val('');
            }
        });

        // 페이지 로딩시 기본 검색
        doSearch(1);
    });

    // ajax - 검색
    function doSearch (page_num) {
        $('input[name=page_num]').val(page_num);
        $('#ids_all').prop('checked', false);

        $.ajax({
            url: '/api/admin/prom/list',
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
                    return false;
                }

                // Init table
                $('#list_body').empty();

                // Set pagination 
                setPagination(data.data);

                if (data.data.list.length == 0) {
                    $('#list_body').append($('<tr/>')
                        .append($('<td/>'))
                        .append($('<td/>', { colspan: 16, text: '생성된 리스트가 없습니다.', class: 'text-center' }))
                    );
                }

                $.each(data.data.list, function (index, item) {
                    let tr_elem = $('<tr/>');
                    tr_elem.append($('<td/>')
                        .append($('<label/>', { for: 'ids_' + index })
                            .append($('<input/>', { 
                                type: 'checkbox',
                                name: 'ids[]', 
                                id: 'ids_' + index,
                                value: item.id 
                            }))
                        )
                    );
                    tr_elem.append($('<td/>', { text: item.id }));
                    tr_elem.append($('<td/>', { text: item.pm_code }));
                    tr_elem.append($('<td/>', { text: item.cus_name }));
                    tr_elem.append($('<td/>', { text: item.cus_mobile }));
                    tr_elem.append($('<td/>', {
                        text: item.bnft_price.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",")
                    }));

                    tr_elem.append($('<td/>', { class: 'with-btn' })
                        .append($('<button/>', {
                            type: 'button',
                            text: item.bnft_code,
                            class: 'btn btn-xs btn-info width-40 m-r-2',
                            click: function () {
                                getProductInfo(item.id);
                            }
                        }))
                    );
                    tr_elem.append($('<td/>', { text: item.is_select_kr }));
                    tr_elem.append($('<td/>', { text: item.select_at }));
                    tr_elem.append($('<td/>', { text: item.customer_zip }));
                    tr_elem.append($('<td/>', { text: item.customer_addr1 }));
                    tr_elem.append($('<td/>', { text: item.customer_addr2 }));
                    if (item.type) {
                        tr_elem.append($('<td/>', { class: 'with-btn' })
                            .append($('<button/>', {
                                type: 'button',
                                text: item.type_kr,
                                class: 'btn btn-xs btn-info width-90 m-r-2',
                                click: function () {
                                    getSeleetInfo(item.id);
                                }
                            }))
                        );
                    } else {
                        tr_elem.append($('<td/>', { text: '-' }));
                    }
                    tr_elem.append($('<td/>', { text: item.send_sms_kr }));
                    tr_elem.append($('<td/>', { class: 'with-btn' })
                        .append($('<div/>', { class: 'btn-group' })
                            .append($('<button/>', {
                                type: 'button',
                                text: '재발송',
                                class: 'btn btn-xs btn-indigo width-60 m-r-2',
                                click: function () {
                                    sendSms(item.id);
                                }
                            }))
                            .append($('<button/>', {
                                type: 'button',
                                text: '신청정보',
                                class: 'btn btn-xs btn-purple width-60 m-r-2',
                                click: function () {
                                    productSelectUi(item.id);
                                }
                            }))
                        )
                    );

                    $('#list_body').append(tr_elem);
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

    // Set pagination
    function setPagination(data) {
        // Set pagination info
        if (data.list.length == 0) {
            $('#pagination_info_start').text(0);
            $('#pagination_info_end').text(0);
            $('#pagination_info_total').text(0);
        } else {
            let start_row = Number(data.page_size) * (Number(data.page_num) - 1) + 1;
            let end_row = start_row + data.list.length - 1;
            let total_row = data.total_rows;

            $('#pagination_info_start').text(start_row);
            $('#pagination_info_end').text(end_row);
            $('#pagination_info_total').text(total_row);
        }

        // Set pagenation
        let pagination = $('#pagination');
        let block_size = 5;
        let block_start = Math.floor((data.page_num - 1) / block_size) * block_size + 1;
        let block_end = block_start + block_size - 1;
        if (block_end > data.total_pages) {
            block_end = data.total_pages;
        }
        let first_class = (data.page_num <= 1)? ' disabled': '';
        let previous_class = (block_start <= 1)? ' disabled': '';
        let next_class = (block_end >= data.total_pages)? ' disabled': '';
        let last_class = (data.page_num >= data.total_pages)? ' disabled': '';

        pagination.empty();
        pagination.append($('<li/>', { class: 'page-item' + first_class }).append($('<a/>', { class: 'page-link', text: '«', click: function () { return doSearch(1); } })));
        pagination.append($('<li/>', { class: 'page-item' + previous_class }).append($('<a/>', { class: 'page-link', text: '‹', click: function () { return doSearch(block_start - 1); } })));
        for (var i = block_start; i <= block_end; i++) {
            let num_class = (i == data.page_num)? ' active': '';
            let page = i;
            pagination.append($('<li/>', { class: 'page-item' + num_class }).append($('<a/>', { class: 'page-link', text: page, click: function () { return doSearch(page); } })));
        }
        pagination.append($('<li/>', { class: 'page-item' + next_class }).append($('<a/>', { class: 'page-link', text: '›', click: function () { return doSearch(block_end + 1); } })));
        pagination.append($('<li/>', { class: 'page-item' + last_class }).append($('<a/>', { class: 'page-link', text: '»', click: function () { return doSearch(data.total_pages); } })));
    }

    // ajax - 상품정보
    function getProductInfo (pm_id) {
        $.ajax({
            url: '/api/admin/Kcar/productInfo/' + pm_id,
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
                    return false;
                }

                let wash_service = data.data.wash_service.items.replace(/\n/g, ', ');
                let wash_goods = data.data.wash_goods.items.replace(/\n/g, ', ');
                let car_goods = data.data.car_goods.items.replace(/\n/g, ', ');
                if (wash_service == '') {
                    wash_service = '없음';
                }
                if (wash_goods == '') {
                    wash_goods = '없음';
                }
                if (car_goods == '') {
                    car_goods = '없음';
                }

                let msg = '';
                msg += '상품코드: ' + data.data.bnft_code + '\n\n';
                msg += '출장세차 서비스: ' + wash_service + '\n\n';
                msg += 'Car Care 용품: ' + wash_goods + '\n\n';
                msg += '차량 용품: ' + car_goods + '\n\n';

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

    // ajax 신청상품정보
    function getSeleetInfo(id) {
        $.ajax({
            url: '/api/admin/Kcar/selectInfo/' + id,
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
                    return false;
                }

                let item = data.data;
                let msg = '';
                msg = '상품코드: ' + item.bnft_code + '\n\n';
                msg += item.product_type_kr + ': ' + item.product_items.replace(/\n/g, ', ') + '\n\n';
                if (item.product_type == 1) {
                    msg += '희망일자(1): ' + item.hope_1 + '\n';
                    msg += '희망일자(2): ' + item.hope_2 + '\n';
                    msg += '희망일자(3): ' + item.hope_3 + '\n';
                }

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

    // sms 발송요청 확인
    function sendSms(id) {
        layoutCommonConfirm('알림', 'SMS를 발송하겠습니까?', function () {
            ajaxSendSms(id);
        });
    }
    // ajax sms 발송요청
    function ajaxSendSms(id) {
        $.ajax({
            url: '/api/admin/Kcar/sms/',
            type: 'POST',
            dataType: 'json',
            data: { pm_id: id },
            timeout: 30000,
            beforeSubmit: function (arr, form, options) {},
            beforeSend: function (jqXHR, settings) {},
            uploadProgress: function (event, position, total, percentComplete) {},
            success: function (data, textStatus, jqXHR) {
                if (!data.result) {
                    alert(data.message);
                    return false;
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

    // 삭제 확인
    function doDelete() {
        let ids_cnt = $('input[name="ids[]"]:checked').length;
        if (ids_cnt == 0) {
            alert('삭제할 항목을 선택해주세요.');
            return false;
        }

        layoutCommonConfirmAlert('알림', '총' + ids_cnt + '개의 항목을 삭제하시겠습니까?', function () {
            ajaxDelete();
        });
    }
    // ajax 삭제
    function ajaxDelete() {
        $.ajax({
            url: '/api/admin/Kcar/kw',
            type: 'DELETE',
            dataType: 'json',
            data: $('input[name="ids[]"]:checked').serialize(),
            timeout: 30000,
            beforeSubmit: function (arr, form, options) {},
            beforeSend: function (jqXHR, settings) {},
            uploadProgress: function (event, position, total, percentComplete) {},
            success: function (data, textStatus, jqXHR) {
                if (!data.result) {
                    alert(data.message);
                    return false;
                }

                alert(data.data.affected_row + '건이 삭제 되었습니다.');

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
            alert('파일을 선태해주세요.');
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
                    return false;
                }

                let meg = '';
                if (data.message != '') {
                    meg += '입력 데이터에 이상이 있어 정상적으로 처리 되지 않았습니다.\n';
                    meg += '(' + data.message + ')\n\n';
                } else {
                    meg += '정상적으로 등록이 완료되었습니다.\n\n';
                }
                meg += data.data.affected_row + '건이 등록 되었습니다.';
                if (Number(data.data.dupli_row) > 0) {
                    meg += '\n(' + data.data.dupli_row + '건의 중복 데이터가 발견 되었습니다.)';
                }
                alert(meg);

                // Reload list
                doSearch(1);
                excelUploadUi();
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
    function downloadExcel() {
        if ($('input[name=sdate]').val() == '') {
            alert('조회기간을 선택해 주세요.');
            return false;
        }
        if ($('input[name=edate]').val() == '') {
            alert('조회기간을 선택해 주세요.');
            return false;
        }
        location.href = '/admin/kcar/kwListExcel?' + $('form[name=form_search]').serialize();
    }

    // Toggle excel upload ui
    function excelUploadUi() {
        $('form[name=form_upload]')[0].reset();
        $('#modal-excel-upload').modal('toggle');
    }

    // Toggle product select ui
    function productSelectUi(id) {
        let modal = $('#modal-customre-select');
        modal.find('legend').text();
        // modal.find('#form_product_select_radio1').parent().show();
        // modal.find('#form_product_select_radio2').parent().show();
        // modal.find('#form_product_select_radio3').parent().show();
        modal.find('#form_product_select_radio1').prop('disabled', false).siblings('label').removeClass('text-decoration-line-through');
        modal.find('#form_product_select_radio2').prop('disabled', false).siblings('label').removeClass('text-decoration-line-through');
        modal.find('#form_product_select_radio3').prop('disabled', false).siblings('label').removeClass('text-decoration-line-through');
        modal.modal('hide');
        $('form[name=form_product_select]')[0].reset();
        $('[show-group~="type_1"]').hide();

        // ajax 신청상품정보
        $.ajax({
            url: '/api/admin/Kcar/selectInfo/' + id,
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
                    return false;
                }

                let item = data.data;

                modal.find('legend').text(item.cus_name + '(' + item.cus_mobile + ')');
                modal.find('input[name=cus_name').val(item.cus_name);
                modal.find('input[name=cus_mobile').val(item.cus_mobile);
                modal.find('input[name=cus_zip').val(item.customer_zip);
                modal.find('input[name=cus_addr1').val(item.customer_addr1);
                modal.find('input[name=cus_addr2').val(item.customer_addr2);
                modal.find('input[name=hope_1').val(item.hope_1);
                modal.find('input[name=hope_2').val(item.hope_2);
                modal.find('input[name=hope_3').val(item.hope_3);
                if (item.product_type) {
                    modal.find('input[name=type][value=' + item.product_type + ']').prop('checked', true);
                }
                if (item.enable_p1 == 0) {
                    // modal.find('#form_product_select_radio1').parent().hide();
                    modal.find('#form_product_select_radio1').prop('disabled', true).siblings('label').addClass('text-decoration-line-through');
                }
                if (item.enable_p2 == 0) {
                    // modal.find('#form_product_select_radio2').parent().hide();
                    modal.find('#form_product_select_radio2').prop('disabled', true).siblings('label').addClass('text-decoration-line-through');
                }
                if (item.enable_p3 == 0) {
                    // modal.find('#form_product_select_radio3').parent().hide();
                    modal.find('#form_product_select_radio3').prop('disabled', true).siblings('label').addClass('text-decoration-line-through');
                }

                modal.find('input[name=type]').change();
                $('#modal-customre-select').modal('show');
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

    // Ajax - Set select info
    function setSelectInfo() {
        let modal = $('#modal-customre-select');
        let addr1_obj = modal.find('form[name=form_product_select] input[name=cus_addr1]');
        let addr2_obj = modal.find('form[name=form_product_select] input[name=cus_addr2]');
        let private_agree_obj = modal.find('form[name=form_product_select] input[name=private_agree]:checked');
        let type_obj = modal.find('form[name=form_product_select] input[name=type]:checked');
        let hope_1_obj = modal.find('form[name=form_product_select] input[name=hope_1]');
        let hope_2_obj = modal.find('form[name=form_product_select] input[name=hope_2]');
        let hope_3_obj = modal.find('form[name=form_product_select] input[name=hope_3]');

        // 상품을 선택하지 않았을 경우
        if (!type_obj.val()) {
            alert('상품을 선택해주세요.');
            return false;
        }

        // 주소를 입력하지 않았을 경우
        if (addr1_obj.val() == '') {
            alert('주소검색을 통해 주소를 입력해주세요.');
            return false;
        }

        // 상세주소를 입력하지 않았을 경우
        if (addr2_obj.val() == '') {
            alert('상세주소를 입력해주세요. (최소 2글자)');
            addr2_obj.focus();
            return false;
        }

        // 희망일자를 1건도 설정하지 않았을 경우
        if (type_obj.val() == '1') {
            if (hope_1_obj.val() == '' && hope_2_obj.val() == '' && hope_3_obj.val() == '') {
                alert('최소 1개 이상 희망일자를 선택해주세요.');
                hope_1_obj.focus();
                return false;
            }
        }

        // Request ajax
        $.ajax({
            url: '/Api/Kcar/Kw/product',
            type: 'PUT',
            dataType: 'json',
            data: modal.find('form[name=form_product_select]').serialize(),
            timeout: 30000,
            beforeSubmit: function (arr, form, options) {},
            beforeSend: function (jqXHR, settings) {},
            uploadProgress: function (event, position, total, percentComplete) {},
            success: function (data, textStatus, jqXHR) {
                if (!data.result) {
                    alert(data.message);
                    return false;
                }

                // Close modal
                $('#modal-customre-select').modal('hide');
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
</script>
<?= $this->endSection() ?>