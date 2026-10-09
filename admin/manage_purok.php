<?php
require_once("../DBConnection.php");
if(isset($_GET['id'])){
    $qry = $conn->query("SELECT * FROM `purok_list` where purok_id = '{$_GET['id']}'");
    foreach($qry->fetchArray() as $k => $v){
        $$k = $v;
    }
}
?>
<div class="form-shell">
    <form action="" id="purok-form">
        <input type="hidden" name="id" value="<?php echo isset($purok_id) ? $purok_id : '' ?>">
        <div class="form-group">
            <label for="purok" class="control-label">Purok</label>
            <input type="text" autofocus name="purok" id="purok" required class="form-control" value="<?php echo isset($purok) ? $purok : '' ?>">
        </div>
    </form>
</div>

<script>
    $(function(){
        $('#purok-form').submit(function(e){
            e.preventDefault()
            $('.pop_msg').remove()
            var _this = $(this)
            var _el = $('<div>').addClass('pop_msg alert')
            $('#uni_modal button').attr('disabled',true)
            $('#uni_modal button[type="submit"]').text('Saving...')
            $.ajax({
                url:'./../Actions.php?a=save_purok',
                method:'POST',
                data:$(this).serialize(),
                dataType:'JSON',
                error:err=>{
                    console.log(err)
                    _el.addClass('alert-danger').text("An error occurred.")
                    _this.prepend(_el)
                    $('#uni_modal button').attr('disabled',false)
                    $('#uni_modal button[type="submit"]').text('Save')
                },
                success:function(resp){
                    if(resp.status == 'success'){
                        _el.addClass('alert-success').text(resp.msg)
                        _this.prepend(_el)
                        $('#uni_modal').on('hide.bs.modal',function(){
                            location.reload()
                        })
                        if("<?php echo isset($purok_id) ?>" != 1){
                            _this.get(0).reset()
                        }
                    }else{
                        _el.addClass('alert-danger').text(resp.msg)
                        _this.prepend(_el)
                    }
                    $('#uni_modal button').attr('disabled',false)
                    $('#uni_modal button[type="submit"]').text('Save')
                }
            })
        })
    })
</script>
