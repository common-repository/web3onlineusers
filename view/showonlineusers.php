<?php

wp_enqueue_style('web3boostrap', plugins_url( '/css/bootstrap.min.css',__FILE__));
wp_register_script ( 'web3boostrap-script', plugins_url( '/js/bootstrap.min.js',__FILE__) );
wp_enqueue_script ( 'web3boostrap-script' );


        $_SESSION['tiertmo']='dfsjhfdjhfdjhfdjhfd';

?>



        <h4 style="color:red" id="mid">  </h4>

       <br><table border="1" style="width:100%" class="table table-condensed table-bordered"><tr><th>Status</th><th>Date</th><th>Page</th></tr><tbody id="list"></tbody></table>

<script>
    function updatepage()
    {
      jQuery.ajax({
                        type: "POST",
                        url: "/wp-admin/admin-ajax.php?action=web3onlineusers_trackerlive",
                        data: {dane:0}
			                ,  success: function(msg){     
			
					                jQuery("#list").html(msg);
			                }
                    });
 
    }
    
   window.setInterval(function(){
       /// call your function here
      updatepage();
}, 5000);
    
    updatepage();
    
</script>
