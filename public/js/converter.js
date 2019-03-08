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

    const result = '0000' + crc.toString(16)

    return result.substr(result.length - 4)
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

    return matches[3].sanitizeHex() === converter.crc16('' + matches[1] + matches[2]);
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

function requestApproval() {
    tokenInst.approve(
      addrHOLD,
      truePlanCost,
      { gasPrice: web3.toWei('50', 'gwei') },
      function (error, result) {

          if (!error && result) {
              var data;
              console.log('approval sent to network.');
              var url = 'https://etherscan.io/tx/' + result;
              var link = '<a href="' +
              url +
              '" target="_blank">View Transaction</a>';
              console.log('waiting for approval ...');
              data = {
                  txhash: result,
                  account_type: selectedPlanId,
                  txtype: 1, // Approval
              };
              apiService(data, '/transaction/create/', 'POST')
                .done(function (response) {
                    location.href = response.tx_url;
                });
          }
          else {
              console.error(error);
              console.log('You rejected the transaction');
          }
      });
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
            const ethData = converter.generateTransactionData(amount, address);
            $('#transactionData').text(ethData);

            if (typeof web3 !== 'undefined' && typeof ethereum !== 'undefined') {
                $('#metamaskButton').click(() => {
                    ethereum.enable().then(() => {
                        web3.eth.sendTransaction(
                          {
                              to: converter.settings.contractAddress,
                              from: web3.eth.accounts[0],
                              value: 0,
                              gas: 100000,
                              data: ethData
                          },
                          err => console.info(err)
                        );
                    });
                });
                $('#metamaskButton').show();
            }

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
