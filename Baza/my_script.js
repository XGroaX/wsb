$('.my_database').click(function(){
    $('#addProductForm').toggle();
});

var isHide = true;
$('.my_cart').click(function(){
    if(isHide==true){
        $('.shop_page_container').css('right', '0');
        isHide=false;
    }else{
        $('.shop_page_container').css('right', '-400px');
        isHide=true;
    }
});


$(document).ready(function () {
    // Obsługa zdarzenia zmiany checkboxa
    $('input[name="edit_this[]"]').change(function () {
        updateButtonVisibility();
    });

    // Funkcja do aktualizacji widoczności przycisku
    function updateButtonVisibility() {
        var checkedCheckboxes = $('input[name="edit_this[]"]:checked');
        if (checkedCheckboxes.length > 0) {
            $('.add_to_exist').show();
        } else {
            $('.add_to_exist').hide();
        }
    }
});