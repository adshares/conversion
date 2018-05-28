/*global converter*/
/*global $*/

String.prototype.sanitizeHex = function () {
    return this.replace(/^0x/, "").toLowerCase();
};

String.prototype.hexToByte = function () {

    if (!this) {
        return new Uint8Array();
    }

    var a = [];
    for (var i = 0, len = this.length; i < len; i += 2) {
        a.push(parseInt(this.substr(i, 2), 16));
    }

    return new Uint8Array(a);
}

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

converter.generateTransactionData = function (amount, key) {

    let data = "0x";
    data += converter.settings.transferMethod.sanitizeHex();
    data += converter.settings.burnAddress.sanitizeHex().padStart(64, "0");
    data += Number(amount).toString(16).padStart(64, "0");
    data += key.sanitizeHex().padStart(64, "0");

    return data;
};

converter.verifyKey = function (signature, key) {

    if (!key || !signature) {
        return false;
    }

    return nacl.sign.detached.verify(new Uint8Array(), signature.sanitizeHex().hexToByte(), key.sanitizeHex().hexToByte());
};

converter.validateForm = function (filed) {

    const amountInput = $('#amountInput');
    const keyInput = $('#keyInput');
    const signatureInput = $('#signatureInput');

    let amount = amountInput.val().trim();
    let key = keyInput.val().trim();
    let signature = signatureInput.val().trim();
    let doubleCheck = $('#doubleCheckInput').prop('checked');

    let valid = true;

    if (typeof filed === 'undefined' || filed === 'amount') {
        if (!/^[0-9]+$/.test(amount) || parseInt(amount) < converter.settings.minTokenAmount) {
            amountInput.removeClass('is-valid').addClass('is-invalid');
            valid = false;
        } else {
            amountInput.removeClass('is-invalid').addClass('is-valid');
        }
    }

    if (typeof filed === 'undefined' || filed === 'key') {
        if (!/^(0x)?[0-9a-fA-F]{64}$/.test(key)) {
            keyInput.removeClass('is-valid').addClass('is-invalid');
            valid = false;
        } else {
            keyInput.removeClass('is-invalid').addClass('is-valid');
        }
    }

    if (typeof filed === 'undefined' || filed === 'signature') {
        if (doubleCheck) {
            if (!/^(0x)?[0-9a-fA-F]{128}$/.test(signature)) {
                signatureInput.removeClass('is-valid').addClass('is-invalid');
                valid = false;
            } else if (!converter.verifyKey(signature, key)) {
                signatureInput.removeClass('is-valid').addClass('is-invalid');
                valid = false;
                if (key && (key != converter.tmp.key || signature != converter.tmp.signature || typeof filed === 'undefined')) {
                    $('#keyWarningModal').modal();
                }
            } else {
                signatureInput.removeClass('is-invalid').addClass('is-valid');
            }

            converter.tmp.key = key;
            converter.tmp.signature = signature;
        }
    }

    return valid;
}

$(document).ready(function ($) {

    $('.contractAddress').text(converter.settings.contractAddress);
    $('.minTokenAmount').text(converter.settings.minTokenAmount);
    $('.minMasterNodeTokenAmount').text(converter.settings.minMasterNodeTokenAmount.formatMoney(0));

    const amountInput = $('#amountInput');
    const keyInput = $('#keyInput');
    const signatureInput = $('#signatureInput');
    const doubleCheckInput = $('#doubleCheckInput');

    converter.tmp = {key: '', signature: ''};

    $('#converterForm').submit(function (event) {

        event.preventDefault();
        event.stopPropagation();

        if (converter.validateForm()) {

            let amount = parseInt($('#amountInput').val().trim());
            let key = $('#keyInput').val().trim();

            $('#transactionData').text(converter.generateTransactionData(amount, key));

            if (amount < converter.settings.minMasterNodeTokenAmount) {
                $('#masterNodeWarning').show();
            } else {
                $('#masterNodeWarning').hide();
            }

            $('#convertModal').modal()
        }
    });

    amountInput.bind("keyup change", function (event) {
        converter.validateForm('amount');
    });

    keyInput.bind("keyup change", function (event) {
        if (converter.validateForm('key') && signatureInput.val()) {
            converter.validateForm('signature');
        }
    });

    signatureInput.bind("keyup change", function (event) {
        converter.validateForm('key');
        converter.validateForm('signature');
    });

    doubleCheckInput.change(function (event) {
        if (doubleCheckInput.prop('checked')) {
            signatureInput.prop('disabled', false);
            if (signatureInput.val()) {
                converter.validateForm('signature');
            }
        } else {
            signatureInput
                .prop('disabled', true)
                .removeClass('is-valid')
                .removeClass('is-invalid');
        }
    });

    $('#generateButton').removeAttr('disabled');
});
