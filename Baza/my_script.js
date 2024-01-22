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