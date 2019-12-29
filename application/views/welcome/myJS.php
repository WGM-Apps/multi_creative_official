<script>
function new_order()
{
    let data = $('#contactForm').serialize();
    $.ajax({
        type: "post",
        url: "welcome/new_order",
        data: data,
        dataType: "json",
        success: function (e) {
            let isValid = e.isValid,
                isPesan = e.isPesan;
            if(isValid==0){
                alert(isPesan);
            }else{
                alert(isPesan);
                window.location.href='./';
            }
        }
    });
}
</script>