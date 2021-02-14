<?php
  $result = ibx_wp_download();
  $link = admin_url('?page=iboxindia&tab='.$result['type']);
?>
<a href="<?php echo $link; ?>"> <-- Go Back</a>
<script>
// setTimeout(function(){
  // window.location = "";
// }, 10000);
</script>