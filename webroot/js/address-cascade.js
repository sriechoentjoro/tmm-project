/**
 * Address Cascading Logic
 * Handles dependency between Province, Kabupaten, Kecamatan, and Kelurahan dropdowns.
 *
 * The selectors used to be [id$="PropinsiId"] and friends, which is how
 * CakePHP 2 named a field. This application runs 3.9, which emits
 * id="master-propinsi-id", so none of them ever matched and the cascade had
 * never run on any form. Both spellings are matched now, and the bare
 * propinsi-id that the region tables' own forms use as well.
 */
$(document).ready(function () {
    const selectors = {
        province: '[id$="propinsi-id"], [id$="PropinsiId"]',
        kabupaten: '[id$="kabupaten-id"], [id$="KabupatenId"]',
        kecamatan: '[id$="kecamatan-id"], [id$="KecamatanId"]',
        kelurahan: '[id$="kelurahan-id"], [id$="KelurahanId"]'
    };

    function loadRegion(sourceId, targetSelector, type, parentParam) {
        const parentId = $(sourceId).val();
        const $target = $(targetSelector);

        // Clear target and downstream dropdowns
        $target.empty().append('<option value="">-- Loading... --</option>');

        if (type === 'kabupaten') {
            $(selectors.kecamatan).empty().append('<option value="">-- Select Kecamatan --</option>');
            $(selectors.kelurahan).empty().append('<option value="">-- Select Kelurahan --</option>');
        } else if (type === 'kecamatan') {
            $(selectors.kelurahan).empty().append('<option value="">-- Select Kelurahan --</option>');
        }

        if (!parentId) {
            $target.empty().append('<option value="">-- Select --</option>');
            return;
        }

        let url = '';
        let params = {};

        const base = (typeof APP_BASE_URL !== 'undefined') ? APP_BASE_URL : '/';

        if (type === 'kabupaten') {
            url = base + 'master-kabupatens/get-by-province';
            params = { master_propinsi_id: parentId };
        } else if (type === 'kecamatan') {
            url = base + 'master-kecamatans/get-by-kabupaten';
            params = { master_kabupaten_id: parentId };
        } else if (type === 'kelurahan') {
            url = base + 'master-kelurahans/get-by-kecamatan';
            params = { master_kecamatan_id: parentId };
        }

        $.ajax({
            url: url,
            data: params,
            type: 'GET',
            dataType: 'json',
            success: function (response) {
                $target.empty();
                $target.append('<option value="">-- Select --</option>');
                // Handle both direct array and {data: array} response formats
                const data = response.data || response;
                $.each(data, function (key, value) {
                    $target.append($('<option></option>').attr('value', key).text(value));
                });
            },
            error: function (xhr, status, error) {
                // Say what the server said, not what we guess it meant.
                //
                // This used to decide between "not allowed" and "could not
                // load" by whether the body started with a '<', because a
                // refusal arrived as a 302 to an HTML page with 200 on it.
                // AppController now turns off unauthorizedRedirect for AJAX,
                // so a refusal is a 403 and an exception is a 500, and both
                // carry a message. Guessing put "Not allowed to look this up"
                // on a screen where the role was allowed, and sent everybody
                // looking at permissions for an hour.
                var said = (xhr.responseJSON && xhr.responseJSON.message) || '';
                var html = (xhr.responseText || '').slice(0, 200).indexOf('<') === 0;
                var label;
                if (xhr.status === 403 || xhr.status === 401) {
                    label = '-- Not allowed to look this up --';
                } else if (xhr.status >= 500) {
                    label = '-- The lookup failed on the server --';
                } else if (xhr.status === 0) {
                    label = '-- No answer from the server --';
                } else if (html) {
                    label = '-- The server sent a page, not a list --';
                } else {
                    label = '-- Could not load --';
                }
                $target.empty().append(
                    $('<option></option>').attr('value', '')
                        .attr('title', said || (label + ' (HTTP ' + xhr.status + ')'))
                        .text(label)
                );
                console.error('Region lookup failed:', label);
                console.error('URL:', url);
                console.error('HTTP status:', xhr.status, status, error);
                console.error('Server said:', said || '(no message)');
                console.error('Response:', (xhr.responseText || '').slice(0, 500));
            }
        });
    }

    // Event Listeners
    $(document).on('change', selectors.province, function () {
        loadRegion(this, selectors.kabupaten, 'kabupaten', 'master_propinsi_id');
    });

    $(document).on('change', selectors.kabupaten, function () {
        loadRegion(this, selectors.kecamatan, 'kecamatan', 'master_kabupaten_id');
    });

    $(document).on('change', selectors.kecamatan, function () {
        loadRegion(this, selectors.kelurahan, 'kelurahan', 'master_kecamatan_id');
    });
});
