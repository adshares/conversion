/*global converter*/
/*global $*/

String.prototype.sanitizeHex = function () {
    return this.replace(/^0x/, "").toLowerCase();
};

String.prototype.hexToByte = function () {

    if (!this) {
        return new Uint8Array();
    }

    let a = [];
    for (let i = 0, len = this.length; i < len; i += 2) {
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

converter.generateTransactionData = function (amount, address) {

    let data = "0x";
    data += converter.settings.transferMethod.sanitizeHex();
    data += converter.settings.burnAddress.sanitizeHex().padStart(64, "0");
    data += Number(amount).toString(16).padStart(64, "0");
    data += address.replace(/[^0-9a-fA-F]/g, '').sanitizeHex();

    return data;
};

converter.crc16 = function(data) {
    data = data.sanitizeHex().hexToByte();

    let crc = 0x1d0f;
    for (let b of data) {
        let x = (crc >> 8) ^ b;
        x ^= x >> 4;
        crc = ((crc << 8) ^ ((x << 12)) ^ ((x << 5)) ^ (x)) & 0xFFFF;
    }

    return crc.toString(16)
}

converter.verifyAddress = function (address) {

    if (!address) {
        return false;
    }

    const addressRegexp = /^([0-9a-fA-F]{4})-([0-9a-fA-F]{8})-([0-9a-fA-F]{4})$/;
    const matches = addressRegexp.exec(address);

    if (matches.length !== 4) {
        return false;
    }

    return matches[3].sanitizeHex() === converter.crc16(matches[1] + matches[2]);
};

converter.validateForm = function (filed) {

    const amountInput = $('#amountInput');
    const addressInput = $('#addressInput');

    let amount = amountInput.val().trim();
    let address = addressInput.val().trim();

    let valid = true;

    if (typeof filed === 'undefined' || filed === 'amount') {
        if (!/^[0-9]+$/.test(amount) || parseInt(amount) < converter.settings.minTokenAmount) {
            amountInput.removeClass('is-valid').addClass('is-invalid');
            valid = false;
        } else {
            amountInput.removeClass('is-invalid').addClass('is-valid');
        }
    }

    if (typeof filed === 'undefined' || filed === 'address') {
        if (!/^[0-9a-fA-F]{4}-[0-9a-fA-F]{8}-[0-9a-fA-F]{4}$/.test(address)) {
            addressInput.removeClass('is-valid').addClass('is-invalid');
            valid = false;
        } else if (!converter.verifyAddress(address)) {
          addressInput.removeClass('is-valid').addClass('is-invalid');
            valid = false;
            if (address !== converter.tmp.address || typeof filed === 'undefined') {
                $('#addressWarningModal').modal();
            }
        } else {
          addressInput.removeClass('is-invalid').addClass('is-valid');
        }

        converter.tmp.address = address;

    }

    return valid;
}

$(document).ready(function ($) {

    $('.contractAddress').text(converter.settings.contractAddress);
    $('.minTokenAmount').text(converter.settings.minTokenAmount);

    const amountInput = $('#amountInput');
    const addressInput = $('#addressInput');

    converter.tmp = {address: ''};

    $('#converterForm').submit(function (event) {

        event.preventDefault();
        event.stopPropagation();

        if (converter.validateForm()) {

            let amount = parseInt($('#amountInput').val().trim());
            let address = $('#addressInput').val().trim();

            $('.tokenAmount').text(amount.formatMoney(0));
            $('#transactionData').text(converter.generateTransactionData(amount, address));

            $('#convertModal').modal()
        }
    });

    amountInput.bind("keyup change", function (event) {
        converter.validateForm('amount');
    });

    addressInput.bind("keyup change", function (event) {
        converter.validateForm('address');
    });

    $('#generateButton').removeAttr('disabled');
});
