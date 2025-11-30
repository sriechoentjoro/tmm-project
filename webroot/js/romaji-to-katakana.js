/**
 * Romaji to Katakana Converter
 * Automatically converts romaji input to katakana characters
 * Compatible with PHP 5.6 / CakePHP 3.9
 */

(function() {
    'use strict';

    // Romaji to Katakana mapping table
    var romajiToKatakana = {
        // Vowels
        'a': 'ア', 'i': 'イ', 'u': 'ウ', 'e': 'エ', 'o': 'オ',
        
        // K-row
        'ka': 'カ', 'ki': 'キ', 'ku': 'ク', 'ke': 'ケ', 'ko': 'コ',
        'kya': 'キャ', 'kyu': 'キュ', 'kyo': 'キョ',
        
        // G-row
        'ga': 'ガ', 'gi': 'ギ', 'gu': 'グ', 'ge': 'ゲ', 'go': 'ゴ',
        'gya': 'ギャ', 'gyu': 'ギュ', 'gyo': 'ギョ',
        
        // S-row
        'sa': 'サ', 'shi': 'シ', 'si': 'シ', 'su': 'ス', 'se': 'セ', 'so': 'ソ',
        'sha': 'シャ', 'sya': 'シャ', 'shu': 'シュ', 'syu': 'シュ', 'sho': 'ショ', 'syo': 'ショ',
        
        // Z-row
        'za': 'ザ', 'ji': 'ジ', 'zi': 'ジ', 'zu': 'ズ', 'ze': 'ゼ', 'zo': 'ゾ',
        'ja': 'ジャ', 'jya': 'ジャ', 'ju': 'ジュ', 'jyu': 'ジュ', 'jo': 'ジョ', 'jyo': 'ジョ',
        
        // T-row
        'ta': 'タ', 'chi': 'チ', 'ti': 'チ', 'tsu': 'ツ', 'tu': 'ツ', 'te': 'テ', 'to': 'ト',
        'cha': 'チャ', 'tya': 'チャ', 'chu': 'チュ', 'tyu': 'チュ', 'cho': 'チョ', 'tyo': 'チョ',
        
        // D-row
        'da': 'ダ', 'di': 'ヂ', 'du': 'ヅ', 'de': 'デ', 'do': 'ド',
        
        // N-row
        'na': 'ナ', 'ni': 'ニ', 'nu': 'ヌ', 'ne': 'ネ', 'no': 'ノ',
        'nya': 'ニャ', 'nyu': 'ニュ', 'nyo': 'ニョ',
        'n': 'ン',
        
        // H-row
        'ha': 'ハ', 'hi': 'ヒ', 'fu': 'フ', 'hu': 'フ', 'he': 'ヘ', 'ho': 'ホ',
        'hya': 'ヒャ', 'hyu': 'ヒュ', 'hyo': 'ヒョ',
        
        // B-row
        'ba': 'バ', 'bi': 'ビ', 'bu': 'ブ', 'be': 'ベ', 'bo': 'ボ',
        'bya': 'ビャ', 'byu': 'ビュ', 'byo': 'ビョ',
        
        // P-row
        'pa': 'パ', 'pi': 'ピ', 'pu': 'プ', 'pe': 'ペ', 'po': 'ポ',
        'pya': 'ピャ', 'pyu': 'ピュ', 'pyo': 'ピョ',
        
        // M-row
        'ma': 'マ', 'mi': 'ミ', 'mu': 'ム', 'me': 'メ', 'mo': 'モ',
        'mya': 'ミャ', 'myu': 'ミュ', 'myo': 'ミョ',
        
        // Y-row
        'ya': 'ヤ', 'yu': 'ユ', 'yo': 'ヨ',
        
        // R-row
        'ra': 'ラ', 'ri': 'リ', 'ru': 'ル', 're': 'レ', 'ro': 'ロ',
        'rya': 'リャ', 'ryu': 'リュ', 'ryo': 'リョ',
        
        // W-row
        'wa': 'ワ', 'wi': 'ヰ', 'we': 'ヱ', 'wo': 'ヲ',
        
        // Special characters
        '-': 'ー',
        ' ': ' ',
        
        // Katakana middle dot
        '.': '・',
        '·': '・'
    };

    /**
     * Convert romaji string to katakana
     * @param {string} romaji - Input romaji string
     * @return {string} Converted katakana string
     */
    function convertToKatakana(romaji) {
        if (!romaji) return '';
        
        var result = '';
        var input = romaji.toLowerCase();
        var i = 0;
        
        while (i < input.length) {
            var matched = false;
            
            // Try to match 3-character combinations first
            if (i + 2 < input.length) {
                var three = input.substr(i, 3);
                if (romajiToKatakana[three]) {
                    result += romajiToKatakana[three];
                    i += 3;
                    matched = true;
                }
            }
            
            // Try 2-character combinations
            if (!matched && i + 1 < input.length) {
                var two = input.substr(i, 2);
                
                // Handle double consonants (っ)
                if (two.charAt(0) === two.charAt(1) && 
                    two.charAt(0) !== 'n' && 
                    two.charAt(0).match(/[a-z]/)) {
                    result += 'ッ';
                    i += 1;
                    matched = true;
                } else if (romajiToKatakana[two]) {
                    result += romajiToKatakana[two];
                    i += 2;
                    matched = true;
                }
            }
            
            // Try single character
            if (!matched) {
                var one = input.charAt(i);
                if (romajiToKatakana[one]) {
                    result += romajiToKatakana[one];
                } else if (one === ' ') {
                    result += ' ';
                } else {
                    // Keep unrecognized characters as-is (allows mixed input)
                    result += romaji.charAt(i);
                }
                i += 1;
            }
        }
        
        return result;
    }

    /**
     * Initialize katakana converter on input fields
     */
    function initKatakanaConverter() {
        // Find all katakana input fields
        var katakanaFields = document.querySelectorAll('.katakana-input, input[name*="katakana"], input[name*="_kana"]');
        
        katakanaFields.forEach(function(field) {
            // Add placeholder hint
            if (!field.placeholder) {
                field.placeholder = 'Type in romaji (e.g., tanaka tarou)';
            }
            
            // Add CSS class for styling
            field.classList.add('katakana-converter-active');
            
            // Real-time conversion on input
            field.addEventListener('input', function(e) {
                var cursorPosition = this.selectionStart;
                var originalLength = this.value.length;
                var converted = convertToKatakana(this.value);
                
                // Only update if changed
                if (converted !== this.value) {
                    this.value = converted;
                    
                    // Try to maintain cursor position
                    var lengthDiff = this.value.length - originalLength;
                    var newPosition = cursorPosition + lengthDiff;
                    this.setSelectionRange(newPosition, newPosition);
                }
            });
            
            // Also convert on blur to ensure full conversion
            field.addEventListener('blur', function() {
                this.value = convertToKatakana(this.value);
            });
            
            // Visual indicator that converter is active
            field.style.backgroundColor = '#fffef0';
            field.style.borderLeft = '3px solid #4CAF50';
            
            // Add tooltip
            field.title = 'Romaji will be automatically converted to Katakana';
        });
        
        console.log('Katakana converter initialized on ' + katakanaFields.length + ' field(s)');
    }

    // Auto-initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initKatakanaConverter);
    } else {
        initKatakanaConverter();
    }

    // Expose converter function globally
    window.RomajiToKatakana = {
        convert: convertToKatakana,
        init: initKatakanaConverter
    };
})();
