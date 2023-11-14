<div class="modal fade modal-ajax-user-form" id="form-ajax-login-popup" tabindex="-1" role="dialog">
   <div class="modal-dialog" role="document">
      <div class="modal-content">
            <div class="modal-header-form">
               <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
               </button>
            </div>
         <div class="modal-body">
            <div class="ajax-user-form">
               <h2 class="title"><?php echo esc_html__('Iniciar Sesión', 'tolips-themer'); ?></h2>
               <div class="form-ajax-login-popup-content">
                  <?php 
                     if(class_exists('Tolips_Addons_Login_Ajax')){
                        Tolips_Addons_Login_Ajax::instance()->html_form();
                     } 
                  ?>
               </div>
               <div class="user-registration">
                  <?php echo esc_html__("¿No tienes una cuenta?", "tolips-themer"); ?>
                  <?php if(class_exists('uListing\Classes\StmUser')){ ?>
                     <a class="registration-popup" href="<?php echo uListing\Classes\StmUser::getProfileUrl() ?>?tab=register">
                        <?php echo esc_html__('Registrate', 'tolips-themer') ?>
                     </a>
                  <?php }else{ ?>
                     <a class="registration-popup" data-toggle="modal" data-target="#form-ajax-registration-popup">
                        <?php echo esc_html__('Registrate', 'tolips-themer') ?>
                     </a>
                  <?php } ?>   
               </div>   
            </div>   
         </div>
      </div>
   </div>
</div>

<div class="modal fade modal-ajax-user-form" id="form-ajax-lost-password-popup" tabindex="-1" role="dialog">
   <div class="modal-dialog" role="document">
      <div class="modal-content">
         <div class="modal-header-form">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
               <span aria-hidden="true">&times;</span>
            </button>
         </div>
         <div class="modal-body">
            <div class="ajax-user-form">
               <h2 class="title"><?php echo esc_html__('Restablecer Contraseña', 'tolips-themer'); ?></h2>
               <div class="form-ajax-login-popup-content">
                  <?php
                     if(class_exists('Tolips_Addons_Forget_Pwd_Ajax')){
                         Tolips_Addons_Forget_Pwd_Ajax::instance()->html_form();
                     } 
                  ?>
               </div>
             
               <div class="user-registration">
                  <?php echo esc_html__("¿No tienes una cuenta?", "tolips-themer"); ?>
                  <?php if(class_exists('uListing\Classes\StmUser')){ ?>
                     <a class="registration-popup" href="<?php echo uListing\Classes\StmUser::getProfileUrl() ?>?tab=register">
                        <?php echo esc_html__('Registrate', 'tolips-themer') ?>
                     </a>
                  <?php }else{ ?>
                     <a class="registration-popup" data-toggle="modal" data-target="#form-ajax-registration-popup">
                        <?php echo esc_html__('Registrate', 'tolips-themer') ?>
                     </a>
                  <?php } ?>   
               </div>   

            </div>   
         </div>
      </div>
   </div>
</div>

<div class="modal fade modal-ajax-user-form" id="form-ajax-registration-popup" tabindex="-1" role="dialog">
   <div class="modal-dialog" role="document">
      <div class="modal-content">
         <div class="modal-header-form">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
               <span aria-hidden="true">&times;</span>
            </button>
         </div>
         <div class="modal-body">
            <div class="ajax-user-form">
               <h2 class="title"><?php echo esc_html__('Register', 'tolips-themer'); ?></h2>
               <div class="form-ajax-registration-popup-content">
                  <?php 
                     if(class_exists('Tolips_Addons_Registration_Ajax')){
                        Tolips_Addons_Registration_Ajax::instance()->html_form();
                     } 
                  ?>
               </div>
               <div class="user-registration">
                  <?php echo esc_html__("Already have an account?", "tolips-themer"); ?>
                  <a class="login-popup" data-toggle="modal" data-target="#form-ajax-login-popup"><?php echo esc_html__('Login', 'tolips-themer') ?></a>
               </div>
            </div>   
         </div>
      </div>
   </div>
</div>