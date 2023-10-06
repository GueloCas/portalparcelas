<?php
   if (!defined('ABSPATH')) {
      exit; // Exit if accessed directly.
   }
   $button_style = $settings['button_style'] ? $settings['button_style'] : 'btn-line-white';
   $button_size = $settings['button_size'] ? $settings['button_size'] : '';
   $btn_classes = "btn-cta {$button_style} {$button_size}";
   $this->add_render_attribute('wrapper', 'class', ['gva-list-number' , $settings['style'] ]);
   $i = 0;
?>

<div <?php echo $this->get_render_attribute_string('wrapper'); ?>>
   <ul class="list-number">
      <?php foreach ($settings['list_items'] as $item): $i++; ?>
         <li class="list-number-item">
            <div class="content-inner">
               <div class="content-top">
                  <div class="number">
                     <span>
                     <?php printf("%02d", $i); ?>
                      <svg enable-background="new 0 0 64 64" height="512" viewBox="0 0 64 64" width="512" xmlns="http://www.w3.org/2000/svg"><path d="m37.379 12.552c-.799-.761-2.066-.731-2.827.069-.762.8-.73 2.066.069 2.828l15.342 14.551h-39.963c-1.104 0-2 .896-2 2s.896 2 2 2h39.899l-15.278 14.552c-.8.762-.831 2.028-.069 2.828.393.412.92.62 1.448.62.496 0 .992-.183 1.379-.552l17.449-16.62c.756-.755 1.172-1.759 1.172-2.828s-.416-2.073-1.207-2.862z"/></svg>
                     </span>
                  </div>
                  <h3 class="title"><?php echo $item['title'] ?></h3>
               </div>
               <?php if($item['content']){ ?>
                  <div class="descrption"><?php echo $item['content'] ?></div>
               <?php } ?>
            </div>   
         </li>
      <?php endforeach; ?>
   </ul>  
   <?php if($settings['button_url']['url']){ ?>
      <div class="list-number-action">
         <?php $this->gva_render_button($btn_classes); ?>
      </div>
   <?php } ?> 
</div>
  