/**
 * jQuery AutoKana Plugin
 * Automatically converts Japanese input to Katakana
 * Based on harisenbon/autokana
 */
(function ($) {
    'use strict';

    $.fn.autoKana = function (furiganaElement, options) {
        var settings = $.extend({
            katakana: true
        }, options);

        var sourceElement = $(this);
        var targetElement = $(furiganaElement);

        if (!sourceElement.length || !targetElement.length) {
            return this;
        }

        // Conversion maps
        var hiraganaToKatakana = {
            'あ': 'ア', 'い': 'イ', 'う': 'ウ', 'え': 'エ', 'お': 'オ',
            'か': 'カ', 'き': 'キ', 'く': 'ク', 'け': 'ケ', 'こ': 'コ',
            'さ': 'サ', 'し': 'シ', 'す': 'ス', 'せ': 'セ', 'そ': 'ソ',
            'た': 'タ', 'ち': 'チ', 'つ': 'ツ', 'て': 'テ', 'と': 'ト',
            'な': 'ナ', 'に': 'ニ', 'ぬ': 'ヌ', 'ね': 'ネ', 'の': 'ノ',
            'は': 'ハ', 'ひ': 'ヒ', 'ふ': 'フ', 'へ': 'ヘ', 'ほ': 'ホ',
            'ま': 'マ', 'み': 'ミ', 'む': 'ム', 'め': 'メ', 'も': 'モ',
            'や': 'ヤ', 'ゆ': 'ユ', 'よ': 'ヨ',
            'ら': 'ラ', 'り': 'リ', 'る': 'ル', 'れ': 'レ', 'ろ': 'ロ',
            'わ': 'ワ', 'を': 'ヲ', 'ん': 'ン',
            'が': 'ガ', 'ぎ': 'ギ', 'ぐ': 'グ', 'げ': 'ゲ', 'ご': 'ゴ',
            'ざ': 'ザ', 'じ': 'ジ', 'ず': 'ズ', 'ぜ': 'ゼ', 'ぞ': 'ゾ',
            'だ': 'ダ', 'ぢ': 'ヂ', 'づ': 'ヅ', 'で': 'デ', 'ど': 'ド',
            'ば': 'バ', 'び': 'ビ', 'ぶ': 'ブ', 'べ': 'ベ', 'ぼ': 'ボ',
            'ぱ': 'パ', 'ぴ': 'ピ', 'ぷ': 'プ', 'ぺ': 'ペ', 'ぽ': 'ポ',
            'ゃ': 'ャ', 'ゅ': 'ュ', 'ょ': 'ョ',
            'ぁ': 'ァ', 'ぃ': 'ィ', 'ぅ': 'ゥ', 'ぇ': 'ェ', 'ぉ': 'ォ',
            'っ': 'ッ', 'ゎ': 'ヮ', 'ゐ': 'ヰ', 'ゑ': 'ヱ',
            'ー': 'ー', '、': '、', '。': '。', '「': '「', '」': '」'
        };

        function convertToKatakana(str) {
            if (!settings.katakana) {
                return str;
            }

            return str.split('').map(function (char) {
                return hiraganaToKatakana[char] || char;
            }).join('');
        }

        function isKana(char) {
            var code = char.charCodeAt(0);
            // Hiragana: 0x3040-0x309F, Katakana: 0x30A0-0x30FF
            return (code >= 0x3040 && code <= 0x309F) || (code >= 0x30A0 && code <= 0x30FF);
        }

        var previousValue = '';
        var furiganaValue = '';

        sourceElement.on('input', function () {
            var currentValue = $(this).val();

            // If text was deleted
            if (currentValue.length < previousValue.length) {
                var deletedLength = previousValue.length - currentValue.length;
                furiganaValue = furiganaValue.slice(0, -deletedLength);
            } else {
                // Text was added
                var addedText = currentValue.slice(previousValue.length);

                // Only add kana characters to furigana
                var kanaText = addedText.split('').filter(isKana).join('');

                if (kanaText) {
                    furiganaValue += convertToKatakana(kanaText);
                }
            }

            previousValue = currentValue;
            targetElement.val(furiganaValue);
        });

        // Initialize
        previousValue = sourceElement.val() || '';

        return this;
    };

})(jQuery);
