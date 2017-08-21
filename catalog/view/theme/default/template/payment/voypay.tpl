<style type="text/css">.inputLabelPayment{width:auto;display:block;float: none;padding-left:2em;margin-top:1em;}</style>
<div id="CardpayDirect">
    <form id="voypayForm" action="<?php echo $formAction; ?>" method="post" class="form-horizontal">
        <input type="hidden" name="Newcard_os" id="Newcard_os" value="" />
        <input type="hidden" name="Newcard_brower" id="Newcard_brower" value="" />
        <input type="hidden" name="Newcard_brower_lang" id="Newcard_brower_lang" value="" />
        <input type="hidden" name="Newcard_time_zone" id="Newcard_time_zone" value="" />
        <input type="hidden" name="Newcard_resolution" id="Newcard_resolution" value="" />
        <input type="hidden" name="Newcard_is_copycard" id="Newcard_is_copycard" value="0" />
        <input type="hidden" name="Newcard_ip" id="Newcard_ip"  value=""/>
        <input type="hidden" name="cardNoError" id="cardNoError"  value="<?php echo  $entry_cc_number_check; ?>"/>
        <input type="hidden" name="cardtype" id="cardtype"  value=""/>
        <div class="col-sm-12 col-md-12 col-xs-12 pull-left">
            <label  class="inputLabelPayment"><?php echo $entry_cc_number; ?><span style="color:red;display:inline-block;" id="">*</span></label>
        </div>
        <div class="col-sm-12 col-md-12 col-xs-12 pull-left">
            <input type="<?php echo $numberType;?>" name="Newcard_cardNo" id="Newcard_cardNo" maxlength="16"
                   autocomplete="off" onpaste="return false" oncopy="return false"
                   onkeyup="this.value=this.value.replace(/\D/g,''); checkCardType(this);"

                   onblur="onblurs(this);"
                   style='border: 1px solid #BBBBBB;float: left;margin: 5px 10px 1em 2em;height:34px;font: 15px/20px Verdana;color: #666666;
            background-image:url("image/voypay/vmj.png");background-position:right center;background-repeat:no-repeat;width:280px;'/>
        </div>
        <div class="col-sm-12 col-md-12 col-xs-12 pull-left">
            <label  class="inputLabelPayment"><?php echo $entry_cc_expire_date; ?><span style="color:red;display:inline-block;">*</span></label>
        </div>
        <div class="col-sm-12 col-md-12 col-xs-12 pull-left">
            <select name="Newcard_cardExpireMonth" id="Newcard_cardExpireMonth" style="border: 1px solid #BBBBBB;margin: 5px 10px 1em 2em;padding:0px;height:34px;font: 15px/20px Verdana;color: #666666;width:80px;">
                <option value=""><?php echo $entry_cc_expire_month; ?></option>
                <?php echo $entry_cc_month_select; ?>
            </select>
            &nbsp;
            <select name="Newcard_cardExpireYear" id="Newcard_cardExpireYear" style="border: 1px solid #BBBBBB;margin: 5px 10px 1em 2em;padding:0px;height:34px;font: 15px/20px Verdana;color: #666666;width:80px;">
                <option value=""><?php echo $entry_cc_expire_year;  ?></option>
                <?php echo $entry_cc_year_select;?>
            </select>
        </div>
        <div class="col-sm-12 col-md-12 col-xs-12 pull-left">
            <label  class="inputLabelPayment"><?php echo $entry_cc_cvv2;?><span style="color:red;display:inline-block;">*</span></label>
        </div>
        <div class="col-sm-12 col-md-12 col-xs-12 pull-left">
            <input type="password" name="Newcard_cardSecurityCode" id="Newcard_cardSecurityCode" maxlength="3"
                   autocomplete="off"  onpaste="return false" oncopy="return false"   onkeyup="this.value=this.value.replace(/\D/g,'');"

                   style='border: 1px solid #BBBBBB;float: left;margin: 5px 10px 1em 2em;height:30px;font: 15px/20px Verdana;color: #666666;width:60px;'/>
            <img src="<?php echo $text_whatIsThis; ?>" alt="<?php echo  $cvvNote; ?>" title="<?php echo  $cvvNote; ?>" style='margin: 5px 10px 1em 0em;width:46px;height:27px' />
        </div>
        <div class="col-sm-12 col-md-12 col-xs-12 pull-left">
            <br/>
            <a onclick="location = '<?php echo $back; ?>'"
               style='margin: 5px 10px 1em 2em;'   class="btn btn-primary"><span><?php echo  $button_back; ?></span></a>
            <a style="padding-left: 30px;margin: 5px 10px 1em 2em;" class="btn btn-primary" id="CardpayDirect_button"><span><?php echo  $button_confirm; ?></span></a>
        </div>
    </form>
    <div class="col-sm-12 col-md-12 col-xs-12 pull-left">
        <br/>
        <img src="<?php echo $text_brand;?>" style="padding: 20px 30px 20px;"/>
    </div>
</div>

<script type="text/javascript">
    function onblurs(obj){
        obj.value=obj.value.replace(/\D/g,'');
        if(obj.value.length != 16){

            if(confirm(document.getElementById("cardNoError").value)){
                obj.value='';
                obj.focus();
                if(getBrowser()=='Firefox'){
                    window.setTimeout( function(){   obj.focus(); }, 0);
                }
                obj.select();
            }

        }

    }


    function broserInit() {
        document.getElementById("Newcard_os").value = getOS();
        document.getElementById("Newcard_resolution").value=getResolution();
        document.getElementById("Newcard_brower").value = getBrowser();
        document.getElementById("Newcard_brower_lang").value=getBrowserLang();
        document.getElementById("Newcard_time_zone").value=getTimezone();

    }

    function checkCardNum(cardNumber) {
        if(cardNumber == null || cardNumber == "" || cardNumber.length > 16 || cardNumber.length < 16) {
            return false;
        }else if(cardNumber.charAt(0) != 3 && cardNumber.charAt(0) != 4 && cardNumber.charAt(0) != 5 && cardNumber.charAt(0) != 6){
            return false;
        }else{
            return true;
        }
    }

    function checkExpdate(expdate) {
        if(expdate == null || expdate == "" || expdate.length < 1) {
            return false;
        }else {
            return true;
        }
    }
    function checkCvv(cvv) {
        if(cvv == null || cvv =="" || cvv.length < 3 || cvv.length > 3 || isNaN(cvv)) {
            return false;
        }else {
            return true;
        }
    }

    function getResolution() {
        return window.screen.width + "x" + window.screen.height;
    }
    function getTimezone() {
        return new Date().getTimezoneOffset()/60*(-1);
    }
    function getBrowser() {
        var userAgent = navigator.userAgent;
        var isOpera = userAgent.indexOf("Opera") > -1;
        if (isOpera) {
            return "Opera"
        }
        if (userAgent.indexOf("Chrome") > -1) {
            return "Chrome";
        }
        if (userAgent.indexOf("Firefox") > -1) {
            return "Firefox";
        }
        if (userAgent.indexOf("Safari") > -1) {
            return "Safari";
        }
        if (userAgent.indexOf("compatible") > -1 && userAgent.indexOf("MSIE") > -1
                && !isOpera) {
            return "IE";
        }
    }
    function getBrowserLang() {
        return navigator.language || window.navigator.browserLanguage;
    }

    function getOS() {
        var sUserAgent = navigator.userAgent;
        var isWin = (navigator.platform == "Win32")
                || (navigator.platform == "Windows");
        var isMac = (navigator.platform == "Mac68K")
                || (navigator.platform == "MacPPC")
                || (navigator.platform == "Macintosh")
                || (navigator.platform == "MacIntel");
        if (isMac)
            return "Mac";
        var isUnix = (navigator.platform == "X11") && !isWin && !isMac;
        if (isUnix)
            return "Unix";
        var isLinux = (String(navigator.platform).indexOf("Linux") > -1);
        if (isLinux)
            return "Linux";
        if (isWin) {
            var isWin2K = sUserAgent.indexOf("Windows NT 5.0") > -1
                    || sUserAgent.indexOf("Windows 2000") > -1;
            if (isWin2K)
                return "Win2000";
            var isWinXP = sUserAgent.indexOf("Windows NT 5.1") > -1
                    || sUserAgent.indexOf("Windows XP") > -1;
            if (isWinXP)
                return "WinXP";
            var isWin2003 = sUserAgent.indexOf("Windows NT 5.2") > -1
                    || sUserAgent.indexOf("Windows 2003") > -1;
            if (isWin2003)
                return "Win2003";
            var isWin2003 = sUserAgent.indexOf("Windows NT 6.0") > -1
                    || sUserAgent.indexOf("Windows Vista") > -1;
            if (isWin2003)
                return "WinVista";
            var isWin2003 = sUserAgent.indexOf("Windows NT 6.1") > -1
                    || sUserAgent.indexOf("Windows 7") > -1;
            if (isWin2003)
                return "Win7";
        }
        return "None";
    }
    function getOsLang() {
        return navigator.language || window.navigator.systemLanguage;
    }

    $('#CardpayDirect_button').bind('click', function() {
        var Newcard_cardNo = $.trim($('#Newcard_cardNo').val());
        var Newcard_cardExpireMonth = $.trim($('#Newcard_cardExpireMonth').val());
        var Newcard_cardExpireYear = $.trim($('#Newcard_cardExpireYear').val());
        var Newcard_cardSecurityCode = $.trim($('#Newcard_cardSecurityCode').val());

        if(!checkCardNum(Newcard_cardNo)) {
            alert("<?php echo  $entry_cc_number_check; ?>");
            $("#Newcard_cardNo").focus();
            return false;
        }
        if(!checkExpdate(Newcard_cardExpireMonth)) {
            alert("<?php echo $entry_cc_expire_month_check;?>");
            $("#Newcard_cardExpireMonth").focus();
            return false;
        }
        if(!checkExpdate(Newcard_cardExpireYear)) {
            alert("<?php echo $entry_cc_expire_year_check;?>");
            $("#Newcard_cardExpireYear").focus();
            return false;
        }
        if(!checkCvv(Newcard_cardSecurityCode)) {
            alert("<?php echo $entry_cc_cvv2_check;?>");
            $("#Newcard_cardSecurityCode").focus();
            return false;
        }
        broserInit();
        $('#CardpayDirect_button').attr('disabled', true);
        $('#CardpayDirect').before('<div class="wait" style="color:red;font-size:12px;font-weight:bold;"><img src="<?php echo $entry_website_url;?>/image/voypay/loading.gif" alt="" /><?php echo $card_pay_wait;?></div>');
        $("#voypayForm").submit();
    });

    function checkCardType(input) {
        var creditcardnumber = input.value;

        if (creditcardnumber.length < 2) {
            input.style.backgroundImage="url('image/voypay/vmj.png')";
        }
        else {
            switch (creditcardnumber.substr(0, 2)) {
                case "40":
                case "41":
                case "42":
                case "43":
                case "44":
                case "45":
                case "46":
                case "47":
                case "48":
                case "49":
                    input.style.backgroundImage="url('image/voypay/visa.png')";
                    $('#cardtype').attr("value","V");
                    break;
                case "51":
                case "52":
                case "53":
                case "54":
                case "55":
                    input.style.backgroundImage="url('image/voypay/mastercard.png')";
                    $('#cardtype').attr("value","M");
                    break;
                case "35":
                    input.style.backgroundImage="url('image/voypay/jcb.png')";
                    $('#cardtype').attr("value","J");
                    break;
                case "34":
                case "37":
                    $('#cardtype').attr("value","A");
                    break;
                case "30":
                case "36":
                case "38":
                case "39":
                case "60":
                case "64":
                case "65":
                    $('#cardtype').attr("value","D");
                    break;
                default:$('#cardtype').attr("value","");
            }
        }
    }


</script>