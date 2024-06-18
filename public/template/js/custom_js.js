$(document).ready(function() {
    $('.select2').select2({
        closeOnSelect: false,
        placeholder: 'Select an option',
    });
    $('.select-all-btn').on('click', function() {
        $('.select2').find('option').prop('selected', true);
        $('.select2').trigger('change');
    });

    $('.deselect-all-btn').on('click', function() {
        $('.select2').find('option').prop('selected', false);
        $('.select2').trigger('change');
    });

    $('#website-transporter').change(function() {
        var selectedValue = $(this).val();
        changeWebSite(selectedValue)
        
    });

    function changeWebSite(website) {
        let url = Routing.generate('app_profile_change_website')
        $.post(url, {website}, (data, status) => window.location.href= Routing.generate('app_dashboard') );
    }
});