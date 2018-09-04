/*global $*/

'use strict';

import bip39 from 'bip39';
import {getMasterKeyFromSeed, getPublicKey} from 'ed25519-hd-key';

String.prototype.hexToByte = function () {

    if (!this) {
        return new Uint8Array(0);
    }

    let a = [];
    for (let i = 0, len = this.length; i < len; i += 2) {
        a.push(parseInt(this.substr(i, 2), 16));
    }

    return new Uint8Array(a);
};

Uint8Array.prototype.byteToHex = function toHexStringReduce() {
    return this.reduce((output, elem) => (output + ('0' + elem.toString(16)).slice(-2)), '');
};

/**
 * Returns mnemonic (12 random words).
 *
 * @returns {string} mnemonic
 */
function generateMnemonic() {
    return bip39.generateMnemonic();
}

/**
 * Create seed from mnemonic.
 *
 * @param mnemonic mnemonic
 * @returns {string} seed
 */
function mnemonicToSeedHex(mnemonic) {
    return bip39.mnemonicToSeedHex(mnemonic);
}

/**
 * Generates key pair.
 *
 * @param seed seed in hex
 */
function generateKeyPair(seed) {
    let keyPair = {};
    let secretKey = getMasterKeyFromSeed(seed).key;
    keyPair.secretKey = secretKey.toString('hex');
    // slice(2) because public key is left pad with '00'
    keyPair.publicKey = getPublicKey(secretKey).toString('hex').slice(2);
    return keyPair;
}

/**
 * Validates mnemonic.
 *
 * @param mnemonic mnemonic
 * @returns {boolean} true if mnemonic is valid
 */
function isMnemonicValid(mnemonic) {
    return (mnemonic.split(/\s+/g).length >= 12) && bip39.validateMnemonic(mnemonic);
}

$(document).ready(function () {

    // fill text area with random mnemonic
    function fillMnemonicTextArea() {
        $('#text-mnemonic').val(generateMnemonic());
    }

    // random mnemonic
    $('#btn-random').click(function () {
        $('#text-mnemonic').removeClass('is-invalid').removeClass('is-valid');
        fillMnemonicTextArea();
    });

    // generate keys
    $('#btn-generate').click(function () {
        const mnemonicTextArea = $('#text-mnemonic');
        let mnemonic = mnemonicTextArea.val().trim();
        if (isMnemonicValid(mnemonic)) {
            mnemonicTextArea.removeClass('is-invalid').addClass('is-valid');
            let seed = mnemonicToSeedHex(mnemonic);
            let {publicKey, secretKey} = generateKeyPair(seed);
            $('#seed').text(seed);
            $('#secretKey').text(secretKey);
            $('#publicKey').text(publicKey);
            // key used to sign is concatenation of secret and public key
            let key = secretKey + publicKey;
            let signature = nacl.sign.detached(new Uint8Array(0), key.hexToByte()).byteToHex();
            $('#signature').text(signature);
            $('#keygenModal').modal();
        } else {
            mnemonicTextArea.removeClass('is-valid').addClass('is-invalid');
        }
    });

    // initially fill text area with mnemonic
    fillMnemonicTextArea();
});