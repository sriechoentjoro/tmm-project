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
                // A refusal does not arrive as an error status. The permission
                // check redirects to another page, so the response is a whole
                // HTML document with a 200 on it and only the JSON parse
                // fails. Saying "error loading" to that sends whoever reads it
                // looking for a network fault that is not there.
                var refused = xhr.status === 403 || xhr.status === 401
                    || (xhr.responseText || '').slice(0, 200).indexOf('<') === 0;
                $target.empty().append($('<option></option>').attr('value', '').text(
                    refused ? '-- Not allowed to look this up --' : '-- Could not load --'
                ));
                console.error(refused
                    ? 'Region lookup refused - the action is not granted to this role'
                    : 'Failed to load region data');
                console.error('URL:', url);
                console.error('Status:', status);
                console.error('Error:', error);
                console.error('HTTP Status:', xhr.status);
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
