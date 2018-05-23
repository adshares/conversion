const app = {
    settings: {
        burnAddress: '0x45a6C392161da9536B7B38Cd562Ee53C70710B43',
        minTokenAmount: 0.0,
        minMasterNodeTokenAmount: 20000,
        dataPrefix: '',
        dataPostfix: ''
    }
};

String.prototype.hexEncode = function () {
    let hex, i;

    let result = "";
    for (i = 0; i < this.length; i++) {
        hex = this.charCodeAt(i).toString(16);
        result += ("000" + hex).slice(-4);
    }

    return result
};

String.prototype.hexDecode = function () {
    let j;
    let hexes = this.match(/.{1,4}/g) || [];
    let back = "";
    for (j = 0; j < hexes.length; j++) {
        back += String.fromCharCode(parseInt(hexes[j], 16));
    }

    return back;
};

Number.prototype.formatMoney = function (precision, decimal, thousand) {
    let n = this,
        c = isNaN(precision = Math.abs(precision)) ? 2 : precision,
        d = typeof decimal === 'undefined' ? "." : decimal,
        t = typeof thousand === 'undefined' ? "," : thousand,
        s = n < 0 ? "-" : "",
        i = String(parseInt(n = Math.abs(Number(n) || 0).toFixed(c))),
        j = i.length > 3 ? i.length % 3 : 0;
    return s + (j ? i.substr(0, j) + t : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t) + (c ? d + Math.abs(n - i).toFixed(c).slice(2) : "");
};

app.generateTransactionData = function (key) {
    let data = app.settings.dataPrefix + key + app.settings.dataPostfix;

    return data.hexEncode();
};

$(document).ready(function ($) {


    $('.burnAddress').text(app.settings.burnAddress);
    $('.minTokenAmount').text(app.settings.minTokenAmount);
    $('.minMasterNodeTokenAmount').text(app.settings.minMasterNodeTokenAmount.formatMoney(2));

    let converterForm = $('#converterForm');
    converterForm.submit(function (event) {

        event.preventDefault();
        event.stopPropagation();

        let valid = converterForm[0].checkValidity();
        converterForm.addClass('was-validated');

        if (valid) {
            let amountVal = $('#amountInput').val();
            let key = $('#keyInput').val();

            $('#transactionAmount').text(amountVal);
            $('#transactionData').text(app.generateTransactionData(key));

            if (parseInt(amountVal) < app.settings.minMasterNodeTokenAmount) {
                $('#masterNodeWarning').show();
            } else {
                $('#masterNodeWarning').hide();
            }

            $('#convertModal').modal({})
        }
    });

    $('#generateButton').removeAttr('disabled');

});
