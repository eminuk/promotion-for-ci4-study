<!-- Using Layout -->
<?= $this->extend('Layouts/Prom_layout') ?>


<!-- head_add_area -->
<?= $this->section('head_add_area') ?>
<style type="text/css">
</style>
<?= $this->endSection() ?>


<!-- content_area -->
<?= $this->section('content_area') ?>
<h4>Select a promotion benefit.</h4>

<form name="form_product" method="post" action="/front/prom/customer">
    <input type="hidden" name="cus_name" value="<?=$view['cus_name'] ?? ''?>" />
    <input type="hidden" name="cus_mobile" value="<?=$view['cus_mobile'] ?? ''?>" />

    <div>
        <p>Choose one of the benefits below.</p>
        <?php if (!empty($view['option_1'])) :?>
            <label>
                <label>OPTION 1 <input type="radio" name='type' value="1" /></label>
                <p> &nbsp; - <?=$view['option_1']['items']?></p>
            </label>
        <?php endif ?>
        <?php if (!empty($view['option_2'])) :?>
            <label>
                <label>OPTION 2 <input type="radio" name='type' value="2" /></label>
                <p> &nbsp; - <?=$view['option_2']['items']?></p>
            </label>
        <?php endif ?>
        <?php if (!empty($view['option_3'])) :?>
            <label>
                <label>OPTION 3 <input type="radio" name='type' value="3" /></label>
                <p> &nbsp; - <?=$view['option_3']['items']?></p>
            </label>
        <?php endif ?>
    </div>
    <br />

    <div>
        <p>Enter delivery address.</p>
        <div>
            <button type="button" onclick="execDaumPostcode();">Address search</button>
        </div>
        <div id="postcode_wrap" style="display:none; border:1px solid; margin:5px 0; position:relative;">
            <img src="//t1.daumcdn.net/postcode/resource/images/close.png" id="btnFoldWrap" style="display:none; cursor:pointer; position:absolute; right:0px; top:-1px; z-index:1;" onclick="foldDaumPostcode()" alt="접기 버튼">
        </div>
        - Zip code: <input type="text" name="cus_zip" placeholder="" value="" readonly />
        <br />
        - Address: <input type="text" name="cus_addr1" placeholder="" value="" readonly />
        <br />
        - Address detail: <input type="text" name="cus_addr2" placeholder="Enter address detail." value="" />
    </div>
    <br />

    <div>
        <p>Select delivery desired date.</p>
        - Date 1 <input type="text" name="hope_1" placeholder="Select date and time." value="" readonly />
        <br />
        - Date 2 <input type="text" name="hope_2" placeholder="Select date and time." value="" readonly />
        <br />
        - Date 3 <input type="text" name="hope_3" placeholder="Select date and time." value="" readonly />
        <br />
    </div>
    <br />

    <div>
        <p>Notice</p>
        <div>Notice messages.</div>
    </div>
    <br />

    <div>
        <p>Privacy Policy</p>
        <div>Collection & Usage</div>
        <label>
            - Agreement
            <input type="checkbox" name="private_agree" />
        </label>
    </div>
    <br />

    <div>
        <button type="submit" onclick="return doApply();">Apply</button>
    </div>
</form>

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
                    $('form[name=form_product] input[name=cus_zip]').val(data.zonecode);
                    $('form[name=form_product] input[name=cus_addr1]').val(addr);
                    // $('form[name=form_product] input[name=cus_addr2]').val(extraAddr);
                    $('form[name=form_product] input[name=cus_addr2]').val('').prop('readonly', false);
                    $('form[name=form_product] input[name=addr_type]').val('N');

                    // 커서를 상세주소 필드로 이동한다.
                    // document.getElementById("sample3_detailAddress").focus();
                    $('form[name=form_product] input[name=cus_addr2]').focus();

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
        $('form[name=form_product] input[name=hope_1]').datetimepicker(datetimepicker_opt);
        $('form[name=form_product] input[name=hope_2]').datetimepicker(datetimepicker_opt);
        $('form[name=form_product] input[name=hope_3]').datetimepicker(datetimepicker_opt);
    </script>
    <style type="text/css">
        .xdsoft_datetimepicker.xdsoft_noselect.xdsoft_ { left:20px!important; }
    </style>

    <script type="text/javascript">
        // ajax - option select
        function doApply() {
            if (event) {
                event.preventDefault();
                event.stopPropagation();
            }

            let addr1_obj = $('form[name=form_product] input[name=cus_addr1]');
            let addr2_obj = $('form[name=form_product] input[name=cus_addr2]');
            let private_agree_obj = $('form[name=form_product] input[name=private_agree]:checked');
            let type_obj = $('form[name=form_product] input[name=type]:checked');
            let hope_1_obj = $('form[name=form_product] input[name=hope_1]');
            let hope_2_obj = $('form[name=form_product] input[name=hope_2]');
            let hope_3_obj = $('form[name=form_product] input[name=hope_3]');


            if (!type_obj.val()) {
                alert('Choose one of the benefits option.');
                return false;
            }
            if (addr1_obj.val() == '') {
                alert('Enter delivery address.');
                return false;
            }
            if (addr2_obj.val() == '') {
                alert('Enter delivery address. (Min 2 char)');
                addr2_obj.focus();
                return false;
            }
            if (hope_1_obj.val() == '' && hope_2_obj.val() == '' && hope_3_obj.val() == '') {
                alert('Select delivery desired date.');
                hope_1_obj.focus();
                return false;
            }
            if (!private_agree_obj.val() ) {
                alert('Agree Privacy Policy.');
                return false;
            }

            // Request ajax
            $.ajax({
                url: '/api/front/prom/product',
                type: 'PUT',
                dataType: 'json',
                data: $('form[name=form_product]').serialize(),
                timeout: 30000,
                beforeSubmit: function (arr, form, options) {},
                beforeSend: function (jqXHR, settings) {},
                uploadProgress: function (event, position, total, percentComplete) {},
                success: function (data, textStatus, jqXHR) {
                    if (!data.result) {
                        alert(data.message);
                        return;
                    }

                    $('form[name=form_product]').submit();
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