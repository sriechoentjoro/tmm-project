/**
 * file-preview.js  –  Content-level preview for every input[type="file"].
 *
 *  PDF    → embedded native browser PDF viewer (full pages, scrollable)
 *  Image  → full-width rendered thumbnail
 *  Excel  → SheetJS table render  (first 30 rows of first sheet)
 *  Word   → mammoth.js HTML render
 *  ZIP    → JSZip file-tree listing
 *  RAR/other → fallback card (icon + name + size)
 *
 * Libraries are loaded lazily from CDN only when the matching file type
 * is first selected – no upfront cost.
 */
(function () {
    'use strict';

    /* ── CDN endpoints ──────────────────────────────────────────────── */
    var CDN = {
        xlsx:    'https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js',
        mammoth: 'https://cdnjs.cloudflare.com/ajax/libs/mammoth/1.4.8/mammoth.browser.min.js',
        jszip:   'https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js',
    };

    /* ── Lazy script loader (cached promises) ───────────────────────── */
    var _scriptCache = {};
    function loadScript(url) {
        if (!_scriptCache[url]) {
            _scriptCache[url] = new Promise(function (res, rej) {
                var s = document.createElement('script');
                s.src = url;
                s.onload = res;
                s.onerror = function () { rej(new Error('Failed to load ' + url)); };
                document.head.appendChild(s);
            });
        }
        return _scriptCache[url];
    }

    /* ── Helpers ────────────────────────────────────────────────────── */
    function fmt(bytes) {
        if (bytes < 1024)    return bytes + ' B';
        if (bytes < 1048576) return (bytes / 1024).toFixed(1) + ' KB';
        return (bytes / 1048576).toFixed(1) + ' MB';
    }
    function esc(s) { var d = document.createElement('div'); d.textContent = s; return d.innerHTML; }

    /* ── File-type detection ────────────────────────────────────────── */
    var MIME_MAP = {
        'application/pdf': 'pdf',
        'application/msword': 'word',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document': 'word',
        'application/vnd.ms-excel': 'excel',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet': 'excel',
        'application/zip': 'zip',
        'application/x-zip-compressed': 'zip',
        'application/x-zip': 'zip',
    };
    var EXT_MAP = {
        pdf: 'pdf', doc: 'word', docx: 'word',
        xls: 'excel', xlsx: 'excel',
        zip: 'zip', rar: 'rar',
        jpg: 'image', jpeg: 'image', png: 'image',
        gif: 'image', webp: 'image', bmp: 'image', svg: 'image',
    };
    function detectType(file) {
        if (file.type.indexOf('image/') === 0) return 'image';
        if (MIME_MAP[file.type]) return MIME_MAP[file.type];
        var ext = (file.name.split('.').pop() || '').toLowerCase();
        return EXT_MAP[ext] || 'generic';
    }

    /* ── Content builders ───────────────────────────────────────────── */
    function buildPdf(file) {
        var url = URL.createObjectURL(file);
        var embed = document.createElement('embed');
        embed.src = url;
        embed.type = 'application/pdf';
        embed.className = 'fp-pdf-embed';
        embed._objectUrl = url; // stored for cleanup
        return Promise.resolve(embed);
    }

    function buildImage(file) {
        return new Promise(function (res, rej) {
            var reader = new FileReader();
            reader.onload = function (e) {
                var img = document.createElement('img');
                img.src = e.target.result;
                img.className = 'fp-img-preview';
                img.alt = file.name;
                res(img);
            };
            reader.onerror = rej;
            reader.readAsDataURL(file);
        });
    }

    function buildExcel(file) {
        return loadScript(CDN.xlsx).then(function () {
            return new Promise(function (res, rej) {
                var reader = new FileReader();
                reader.onload = function (e) {
                    try {
                        /* global XLSX */
                        var wb  = XLSX.read(e.target.result, { type: 'array' });
                        var name = wb.SheetNames[0];
                        var ws   = wb.Sheets[name];
                        var ref  = ws['!ref'] || 'A1';
                        var rng  = XLSX.utils.decode_range(ref);

                        // Limit to 30 rows for preview
                        var MAX_ROWS = 30;
                        var trimmed = Object.assign({}, ws);
                        trimmed['!ref'] = XLSX.utils.encode_range({
                            s: rng.s,
                            e: { r: Math.min(rng.e.r, MAX_ROWS - 1), c: rng.e.c }
                        });
                        var html = XLSX.utils.sheet_to_html(trimmed, { editable: false });

                        var wrap = document.createElement('div');
                        wrap.className = 'fp-excel-wrap';

                        if (wb.SheetNames.length > 1) {
                            var badge = document.createElement('div');
                            badge.className = 'fp-sheet-badge';
                            badge.innerHTML = '<i class="fas fa-table"></i> Sheet: <strong>' +
                                esc(name) + '</strong> &nbsp;·&nbsp; ' + wb.SheetNames.length + ' sheets total';
                            wrap.appendChild(badge);
                        }

                        var tblWrap = document.createElement('div');
                        tblWrap.className = 'fp-table-scroll';
                        tblWrap.innerHTML = html;
                        wrap.appendChild(tblWrap);

                        if (rng.e.r >= MAX_ROWS) {
                            var more = document.createElement('div');
                            more.className = 'fp-more-rows';
                            more.textContent = '… ' + (rng.e.r - MAX_ROWS + 1) + ' more rows not shown';
                            wrap.appendChild(more);
                        }
                        res(wrap);
                    } catch (err) { rej(err); }
                };
                reader.onerror = rej;
                reader.readAsArrayBuffer(file);
            });
        });
    }

    function buildWord(file) {
        return loadScript(CDN.mammoth).then(function () {
            return new Promise(function (res, rej) {
                var reader = new FileReader();
                reader.onload = function (e) {
                    /* global mammoth */
                    mammoth.convertToHtml({ arrayBuffer: e.target.result })
                        .then(function (result) {
                            var div = document.createElement('div');
                            div.className = 'fp-word-preview';
                            div.innerHTML = result.value || '<p><em>Empty document</em></p>';
                            res(div);
                        })
                        .catch(rej);
                };
                reader.onerror = rej;
                reader.readAsArrayBuffer(file);
            });
        });
    }

    function buildZip(file) {
        return loadScript(CDN.jszip).then(function () {
            /* global JSZip */
            return JSZip.loadAsync(file).then(function (zip) {
                var names = Object.keys(zip.files).sort();
                var div   = document.createElement('div');
                div.className = 'fp-zip-preview';

                var hdr = document.createElement('div');
                hdr.className = 'fp-zip-header';
                hdr.innerHTML = '<i class="fas fa-archive"></i> <strong>' + names.length + '</strong> item' +
                    (names.length !== 1 ? 's' : '') + ' inside';
                div.appendChild(hdr);

                var ul = document.createElement('ul');
                ul.className = 'fp-zip-list';
                var limit = Math.min(names.length, 60);
                for (var i = 0; i < limit; i++) {
                    var n = names[i];
                    var li = document.createElement('li');
                    li.className = zip.files[n].dir ? 'fp-zip-dir' : 'fp-zip-file';
                    li.innerHTML = '<i class="fas ' + (zip.files[n].dir ? 'fa-folder' : 'fa-file') + '"></i> ' + esc(n);
                    ul.appendChild(li);
                }
                if (names.length > 60) {
                    var extra = document.createElement('li');
                    extra.className = 'fp-zip-more';
                    extra.textContent = '… and ' + (names.length - 60) + ' more';
                    ul.appendChild(extra);
                }
                div.appendChild(ul);
                return div;
            });
        });
    }

    function buildFallback(file, type) {
        var ICONS = {
            pdf:   { cls: 'fas fa-file-pdf',     col: '#e53935' },
            word:  { cls: 'fas fa-file-word',    col: '#1565c0' },
            excel: { cls: 'fas fa-file-excel',   col: '#2e7d32' },
            zip:   { cls: 'fas fa-file-archive', col: '#e65100' },
            rar:   { cls: 'fas fa-file-archive', col: '#e65100' },
        };
        var ic = ICONS[type] || { cls: 'fas fa-file', col: '#607d8b' };
        var div = document.createElement('div');
        div.className = 'fp-fallback';
        div.innerHTML =
            '<i class="' + ic.cls + '" style="color:' + ic.col + '"></i>' +
            '<div class="fp-fallback-name">' + esc(file.name) + '</div>' +
            '<div class="fp-fallback-meta">' + fmt(file.size) + ' &nbsp;·&nbsp; Preview not available</div>';
        return div;
    }

    /* ── Card wrapper ───────────────────────────────────────────────── */
    function makeCard(file, contentEl) {
        var card = document.createElement('div');
        card.className = 'fp-card';

        var hdr = document.createElement('div');
        hdr.className = 'fp-card-hdr';
        hdr.innerHTML =
            '<div class="fp-card-name"><i class="fas fa-check-circle"></i> ' + esc(file.name) + '</div>' +
            '<div class="fp-card-meta">' + fmt(file.size) + '</div>' +
            '<div class="fp-card-acts">' +
                '<button type="button" class="fp-btn fp-btn--change"><i class="fas fa-folder-open"></i> Change</button>' +
                '<button type="button" class="fp-btn fp-btn--remove"><i class="fas fa-times"></i> Remove</button>' +
            '</div>';

        var body = document.createElement('div');
        body.className = 'fp-card-body';
        body.appendChild(contentEl);

        card.appendChild(hdr);
        card.appendChild(body);
        return card;
    }

    /* ── Core attach ────────────────────────────────────────────────── */
    function attach(input) {
        if (input.dataset.fpAttached) return;
        input.dataset.fpAttached = '1';

        var wrap = document.createElement('div');
        wrap.className = 'fp-wrap';
        input.parentNode.insertBefore(wrap, input.nextSibling);

        var currentObjUrl = null;

        function cleanup() {
            if (currentObjUrl) { URL.revokeObjectURL(currentObjUrl); currentObjUrl = null; }
            wrap.innerHTML = '';
        }

        function showCard(file, contentEl) {
            cleanup();
            var card = makeCard(file, contentEl);
            wrap.appendChild(card);

            // Capture object URL from embed for later revoke
            var embed = card.querySelector('embed[src]');
            if (embed && embed._objectUrl) currentObjUrl = embed._objectUrl;

            card.querySelector('.fp-btn--change').addEventListener('click', function () { input.click(); });
            card.querySelector('.fp-btn--remove').addEventListener('click', function () {
                cleanup(); input.value = '';
            });
        }

        input.addEventListener('change', function () {
            cleanup();
            var file = input.files && input.files[0];
            if (!file) return;

            var type = detectType(file);

            // Loading spinner
            wrap.innerHTML = '<div class="fp-loading"><i class="fas fa-spinner fa-spin"></i> Rendering preview…</div>';

            var builders = {
                pdf:   buildPdf,
                image: buildImage,
                excel: buildExcel,
                word:  buildWord,
                zip:   buildZip,
            };

            var builder = builders[type] || function () { return Promise.resolve(buildFallback(file, type)); };

            builder(file).then(function (el) {
                showCard(file, el);
            }).catch(function () {
                // Library load or parse failure → graceful fallback
                showCard(file, buildFallback(file, type));
            });
        });
    }

    /* ── Init ───────────────────────────────────────────────────────── */
    function init() {
        document.querySelectorAll('input[type="file"]').forEach(attach);
    }
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

    /* ── Styles ─────────────────────────────────────────────────────── */
    var css = '' +
        '.fp-wrap { margin-top: 10px; }' +

        '.fp-loading {' +
        '  padding: 18px; text-align: center; color: #78909c; font-size: 13px;' +
        '  border: 1.5px dashed #cdd9de; border-radius: 10px; background: #f9fcfd;' +
        '}' +

        /* Card shell */
        '.fp-card {' +
        '  border-radius: 10px; border: 1.5px solid #cde8f2;' +
        '  background: #fff; overflow: hidden; animation: fp-in .22s ease;' +
        '}' +

        /* Card header */
        '.fp-card-hdr {' +
        '  display: flex; align-items: center; gap: 8px; flex-wrap: wrap;' +
        '  padding: 9px 14px; background: #f0fafd; border-bottom: 1px solid #d8eef5;' +
        '}' +
        '.fp-card-name {' +
        '  flex: 1; min-width: 0; font-size: 13px; font-weight: 600;' +
        '  color: #1a2e38; word-break: break-all;' +
        '}' +
        '.fp-card-name .fa-check-circle { color: #00897b; margin-right: 5px; }' +
        '.fp-card-meta { font-size: 11px; color: #90a4ae; white-space: nowrap; }' +
        '.fp-card-acts { display: flex; gap: 6px; }' +

        /* Buttons */
        '.fp-btn {' +
        '  display: inline-flex; align-items: center; gap: 4px; padding: 4px 10px;' +
        '  border: none; border-radius: 6px; font-size: 11px; font-weight: 600;' +
        '  cursor: pointer; transition: background .14s, color .14s;' +
        '}' +
        '.fp-btn--change { background: #e3f2fd; color: #1565c0; }' +
        '.fp-btn--change:hover { background: #1565c0; color: #fff; }' +
        '.fp-btn--remove  { background: #fce4ec; color: #c62828; }' +
        '.fp-btn--remove:hover  { background: #c62828; color: #fff; }' +

        /* Card body */
        '.fp-card-body { overflow: auto; max-height: 460px; }' +

        /* PDF */
        '.fp-pdf-embed { width: 100%; height: 460px; border: none; display: block; }' +

        /* Image */
        '.fp-img-preview { display: block; max-width: 100%; max-height: 440px; margin: 0 auto; padding: 10px; }' +

        /* Excel */
        '.fp-excel-wrap {}' +
        '.fp-sheet-badge {' +
        '  padding: 6px 12px; font-size: 11px; color: #1b5e20;' +
        '  background: #e8f5e9; border-bottom: 1px solid #c8e6c9;' +
        '}' +
        '.fp-table-scroll { overflow: auto; }' +
        '.fp-table-scroll table { border-collapse: collapse; font-size: 12px; width: max-content; }' +
        '.fp-table-scroll td, .fp-table-scroll th {' +
        '  border: 1px solid #e0e0e0; padding: 4px 10px; white-space: nowrap; min-width: 60px;' +
        '}' +
        '.fp-table-scroll tr:first-child td, .fp-table-scroll tr:first-child th {' +
        '  background: #f5f5f5; font-weight: 700; position: sticky; top: 0;' +
        '}' +
        '.fp-more-rows {' +
        '  padding: 5px 12px; font-size: 11px; color: #90a4ae;' +
        '  border-top: 1px solid #eee; background: #fafafa;' +
        '}' +

        /* Word */
        '.fp-word-preview {' +
        '  padding: 20px 24px; font-size: 13px; line-height: 1.7; color: #263238;' +
        '}' +
        '.fp-word-preview h1,.fp-word-preview h2,.fp-word-preview h3 { margin: 14px 0 6px; }' +
        '.fp-word-preview p { margin: 0 0 8px; }' +
        '.fp-word-preview table { border-collapse: collapse; width: 100%; margin: 10px 0; }' +
        '.fp-word-preview td,.fp-word-preview th { border: 1px solid #ddd; padding: 4px 8px; }' +

        /* ZIP */
        '.fp-zip-preview {}' +
        '.fp-zip-header {' +
        '  padding: 10px 14px; font-size: 13px; color: #37474f;' +
        '  border-bottom: 1px solid #eee; background: #f8f8f8;' +
        '}' +
        '.fp-zip-list {' +
        '  list-style: none; margin: 0; padding: 8px 16px;' +
        '  font-size: 12px; font-family: monospace;' +
        '}' +
        '.fp-zip-list li { padding: 2px 0; }' +
        '.fp-zip-dir  i { color: #f9a825; margin-right: 6px; }' +
        '.fp-zip-file i { color: #90a4ae; margin-right: 6px; }' +
        '.fp-zip-more { color: #90a4ae; font-style: italic; }' +

        /* Fallback */
        '.fp-fallback {' +
        '  display: flex; flex-direction: column; align-items: center;' +
        '  gap: 10px; padding: 28px; color: #607d8b;' +
        '}' +
        '.fp-fallback i { font-size: 48px; }' +
        '.fp-fallback-name { font-weight: 600; font-size: 13px; color: #263238; word-break: break-all; text-align: center; }' +
        '.fp-fallback-meta { font-size: 12px; }' +

        /* Animation */
        '@keyframes fp-in {' +
        '  from { opacity:0; transform:translateY(-5px); }' +
        '  to   { opacity:1; transform:translateY(0);    }' +
        '}';

    var st = document.createElement('style');
    st.textContent = css;
    document.head.appendChild(st);

})();
