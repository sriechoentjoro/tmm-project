/**
 * Address Cascading Logic
 * Handles dependency between Province, Kabupaten, Kecamatan, and Kelurahan dropdowns.
 */
$(document).ready(function () {
    const selectors = {
        province: '[id$="PropinsiId"]',
        kabupaten: '[id$="KabupatenId"]',
        kecamatan: '[id$="KecamatanId"]',
        kelurahan: '[id$="KelurahanId"]'
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
                $target.empty().append('<option value="">-- Error Loading --</option>');
                console.error('Failed to load region data');
                console.error('URL:', url);
                console.error('Status:', status);
                console.error('Error:', error);
                console.error('Response:', xhr.responseText);
                console.error('HTTP Status:', xhr.status);
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
