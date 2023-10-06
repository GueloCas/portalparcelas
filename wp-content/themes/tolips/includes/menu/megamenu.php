<?php 
class Tolips_Mega_Menu {

	function init_mega_menu(){
		if (is_admin()){
			add_action( 'admin_menu' , array( $this , 'menu_init_enqueue'));
			add_filter( 'wp_edit_nav_menu_walker', array($this, 'menu_wp_edit_nav_menu_walker'), 10, 2 );
			add_action( 'wp_update_nav_menu_item', array( $this , 'menu_nav_menu_item_custom_update'),10, 3);
		}
		add_filter( 'wp_setup_nav_menu_item',array( $this , 'menu_menu_item_custom'));
	}
	
	function menu_init_enqueue(){
		global $pagenow;
		$dir = get_template_directory_uri();
		
		if($pagenow == "nav-menus.php"){
			wp_enqueue_media();
		}
	}
	 
	function menu_wp_edit_nav_menu_walker($class, $menu_id){
		return 'tolips_Walker_Nav_Menu_Edit';
	}
	
	function menu_menu_item_custom($menu_item){
		$post_fields = array(
							array('key'=> 'megamenu', 'name' => 'menu-item-megamenu'),
							array('key'=>'megadirection','name'=>'menu-item-megadirection'),
							array('key'=>'megacolumns','name'=>'menu-item-megacolumns'),
							array('key'=>'megawidth','name'=>'menu-item-megawidth'),
							array('key'=>'megaalign','name'=>'menu-item-megaalign'),
							array('key'=>'submegamenu','name'=>'menu-item-submegamenu'),
							array('key'=>'megaicon', 'name'=>'menu-item-megaicon')	
						);
		foreach($post_fields as $field){
			$key_field = $field['key'];
			if($key_field){
				$menu_item->$key_field = get_post_meta( $menu_item->ID, '_'.$field['name'], true );
			}
		}
    	return $menu_item;
	}
	
	/* Save custom field value */
	function menu_nav_menu_item_custom_update($menu_id, $menu_item_db_id, $args){
		
		$post_fields = array('menu-item-megamenu','menu-item-megadirection', 'menu-item-megacolumns', 'menu-item-megawidth', 'menu-item-megaalign', 'menu-item-megaicon', 'menu-item-submegamenu');
		
		if ( ! empty( $_POST['menu-item-db-id'] ) ) {
			
			foreach($post_fields as $field){
				if ( isset( $_POST[$field]) && is_array($_POST[$field])  && isset( $_POST[$field][$menu_item_db_id] )) {
					update_post_meta( $menu_item_db_id, '_'.$field , $_POST[$field][$menu_item_db_id] );
				}else{
					if($field == 'menu-item-megamenu'){
						update_post_meta( $menu_item_db_id, '_'.$field , '' );
					}
				}
			}
		}
	}
}

/* Create Custom HTML list of nav menu items. */
class tolips_Walker extends Walker {
	/**
	 * What the class handles.
	 * @see Walker::$tree_type
	*/
	var $tree_type = array( 'post_type', 'taxonomy', 'custom' );
	/**
	 * Database fields to use.
	 *
	 * @see Walker::$db_fields
	 */
	var $db_fields = array( 'parent' => 'menu_item_parent', 'id' => 'db_id' );
	
	/**
	 * Current menu had been enabled mega style
	 *
	 */
	var $megamenu_enabled = false;
	
	/**
	 * Current menu show direction
	 *
	 */
	var $megamenu_direction = 'horizontal';
	
	/**
	 * Current icon menu
	 *
	 */
	var $mega_columns = 4;

	var $mega_width = 600;

	var $mega_align = 'center';

	/**
	 * Starts the list before the elements are added.
	 *
	 * @param string $output Passed by reference. Used to append additional content.
	 * @param int    $depth  Depth of menu item. Used for padding.
	 * @param array  $args   An array of arguments. @see wp_nav_menu()
	 */
	function start_lvl( &$output, $depth = 0, $args = array() ) {
		$indent = str_repeat("\t", $depth);
		
		if($depth == 0 && $this->megamenu_enabled){
			$output .= "\n$indent<ul class=\"megamenu-sub megamenu-columns-{$this->mega_columns}\">\n";
		}else{
			$output .= "\n$indent<ul class=\"submenu-inner \">\n";
		}
	}

	/**
	 * Ends the list of after the elements are added.
	 *
	 * @param string $output Passed by reference. Used to append additional content.
	 * @param int    $depth  Depth of menu item. Used for padding.
	 * @param array  $args   An array of arguments. @see wp_nav_menu()
	 */
	function end_lvl( &$output, $depth = 0, $args = array() ) {
		$indent = str_repeat("\t", $depth);
		$output .= "$indent</ul>\n";
	}

	public function display_element( $element, &$children_elements, $max_depth, $depth, $args, &$output ) {
        if ( ! $element )
            return;

        $id_field = $this->db_fields['id'];

        // Display this element.
        if ( is_object( $args[0] ) )
           $args[0]->has_children = ! empty( $children_elements[ $element->$id_field ] );

        parent::display_element( $element, $children_elements, $max_depth, $depth, $args, $output );
    }

	/**
	 * Start the element output.
	 *
	 * @param string $output Passed by reference. Used to append additional content.
	 * @param object $item   Menu item data object.
	 * @param int    $depth  Depth of menu item. Used for padding.
	 * @param array  $args   An array of arguments. @see wp_nav_menu()
	 * @param int    $id     Current item ID.
	 */
	function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {
	
		$indent = ( $depth ) ? str_repeat( "\t", $depth ) : '';

		$class_names = $value = '';

		$classes = empty( $item->classes ) ? array() : (array) $item->classes;

		$classes[] = 'menu-item-' . $item->ID;
		$megamenu_content = '';
		$this->megamenu_enabled = false;
		if($depth == 0){
			if($item->megamenu == "enabled"){
				$this->megamenu_enabled = true;
				$classes[] = 'megamenu-main';
			}else{
				$this->megamenu_enabled = false;
			}
			$this->mega_columns = !empty($item->megacolumns) ? $item->megacolumns : '';
			$this->mega_width = !empty($item->megawidth) ? $item->megawidth : '';
			$this->mega_align = !empty($item->megaalign) ? $item->megaalign : '';
			
			if($this->megamenu_enabled && isset($item->megadirection) && $item->megadirection && !empty($item->megadirection) && $item->megadirection!="default"){
				$megamenu = get_post( $item->megadirection );
				if($megamenu && is_object($megamenu)){
					$megamenu_content .= '<div class="megamenu-profile">' . do_shortcode( $megamenu->post_content ) . '</div>';
				}
			}
		}

		
		/**
		 * Filter the CSS class(es) applied to a menu item's <li>.
		 *
		 * @param array  $classes The CSS classes that are applied to the menu item's <li>.
		 * @param object $item    The current menu item.
		 * @param array  $args    An array of arguments. @see wp_nav_menu()
		 */
		$class_names = join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item, $args ) );
		$class_names = $class_names ? ' class="' . esc_attr( $class_names ) . '"' : '';

		/**
		 * Filter the ID applied to a menu item's <li>.
		 *
		 * @param string The ID that is applied to the menu item's <li>.
		 * @param object $item The current menu item.
		 * @param array $args An array of arguments. @see wp_nav_menu()
		 */
		$id = apply_filters( 'nav_menu_item_id', 'menu-item-'. $item->ID, $item, $args );
		$id = $id ? ' id="' . esc_attr( $id ) . '"' : '';

		$output .= $indent . '<li' . $id . $value . $class_names .'>';

		$atts = array();
		$atts['title']  = ! empty( $item->attr_title ) ? $item->attr_title : '';
		$atts['target'] = ! empty( $item->target )     ? $item->target     : '';
		$atts['rel']    = ! empty( $item->xfn )        ? $item->xfn        : '';
		$atts['href']   = ! empty( $item->url )        ? $item->url        : '';
		
		
		/**
		 * Filter the HTML attributes applied to a menu item's <a>.
		 *
		 * @param array $atts {
		 *     The HTML attributes applied to the menu item's <a>, empty strings are ignored.
		 *
		 *     @type string $title  The title attribute.
		 *     @type string $target The target attribute.
		 *     @type string $rel    The rel attribute.
		 *     @type string $href   The href attribute.
		 * }
		 * @param object $item The current menu item.
		 * @param array  $args An array of arguments. @see wp_nav_menu()
		 */
		$atts = apply_filters( 'nav_menu_link_attributes', $atts, $item, $args );

		$attributes = '';
		
		foreach ( $atts as $attr => $value ) {
			if ( ! empty( $value ) ) {
				if( 'href' === $attr ) {
					$value = esc_url( $value );
				}else{
					$value = esc_attr( $value );
				}
				$attributes .= ' ' . $attr . '="' . $value . '"';
			}
		}
		
		$args = (object)$args;

		$item_output = $args->before;
		
		$item_output .= '<a'. $attributes .'>';
	
		if($item->megaicon){
			$item_output .= '<i class="fa ' . $item->megaicon . '"></i>';
		}

		$item_output .= $args->link_before . apply_filters( 'the_title', $item->title, $item->ID ) . $args->link_after;
		
		if($args->has_children || ($this->megamenu_enabled && $depth == 0)){
			$item_output .= '<span class="caret"></span>';
		}

		$item_output .= '</a>';
		
		$item_output .= $args->after;

		if($this->megamenu_enabled){
			$style = '';
			if($this->mega_width){
				$style = " style=\"width: {$this->mega_width}px;\"";
			}
			$item_output .= '<div class="megamenu-wrap-inner submenu-inner megamenu-align-' . $this->mega_align . '"' . $style . '>';
		}
		if($this->megamenu_enabled && $megamenu_content){
			$item_output .= $megamenu_content;
		}	
		$output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
		
	}

	function end_el( &$output, $item, $depth = 0, $args = array() ) {
		$this->megamenu_enabled = false;
		if($depth == 0){
			if($item->megamenu == "enabled"){
				$this->megamenu_enabled = true;
			}else{
				$this->megamenu_enabled = false;
			}
		}
		if($this->megamenu_enabled){
			$output .= '</div>';
		}
		$output .= "</li>\n";

	}
} // Gavias_Walker


/**
 * Edit HTML list of nav menu input items.
 */
class tolips_Walker_Nav_Menu_Edit extends Walker_Nav_Menu {
	/**
	 * Starts the list before the elements are added.
	 *
	 * @param string $output Passed by reference.
	 * @param int    $depth  Depth of menu item. Used for padding.
	 * @param array  $args   Not used.
	 */
	function start_lvl( &$output, $depth = 0, $args = array() ) {}

	/**
	 * Ends the list of after the elements are added.
	 *
	 * @param string $output Passed by reference.
	 * @param int    $depth  Depth of menu item. Used for padding.
	 * @param array  $args   Not used.
	 */
	function end_lvl( &$output, $depth = 0, $args = array() ) {


	}

	/**
	 * Start the element output.
	 *
	 * @param string $output Passed by reference. Used to append additional content.
	 * @param object $item   Menu item data object.
	 * @param int    $depth  Depth of menu item. Used for padding.
	 * @param array  $args   Not used.
	 * @param int    $id     Not used.
	 */
	function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {

		$megamenus = array();
		if(function_exists('gaviasframework_get_megamenu')){
			$megamenus = gaviasframework_get_megamenu();
		}

		global $_wp_nav_menu_max_depth;
		$_wp_nav_menu_max_depth = $depth > $_wp_nav_menu_max_depth ? $depth : $_wp_nav_menu_max_depth;

		ob_start();
		$item_id = esc_attr( $item->ID );
		$removed_args = array(
			'action',
			'customlink-tab',
			'edit-menu-item',
			'menu-item',
			'page-tab',
			'_wpnonce',
		);

		$original_title = '';
		if ( 'taxonomy' == $item->type ) {
			$original_title = get_term_field( 'name', $item->object_id, $item->object, 'raw' );
			if ( is_wp_error( $original_title ) )
				$original_title = false;
		} elseif ( 'post_type' == $item->type ) {
			if( isset($item->object_id) ){
				$original_object = get_post( $item->object_id );
				if( isset($original_object->ID) ){
					$original_title = get_the_title( $original_object->ID );
				}
			}
		}

		$classes = array(
			'menu-item menu-item-depth-' . $depth,
			'menu-item-' . esc_attr( $item->object ),
			'menu-item-edit-' . ( ( isset( $_GET['edit-menu-item'] ) && $item_id == $_GET['edit-menu-item'] ) ? 'active' : 'inactive'),
		);

		$title = $item->title;

		if ( ! empty( $item->_invalid ) ) {
			$classes[] = 'menu-item-invalid';
			/* translators: %s: title of menu item which is invalid */
			$title = sprintf( esc_html__( '%s (Invalid)' , 'tolips'), $item->title );
		} elseif ( isset( $item->post_status ) && 'draft' == $item->post_status ) {
			$classes[] = 'pending';
			/* translators: %s: title of menu item in draft status */
			$title = sprintf( esc_html__('%s (Pending)', 'tolips'), $item->title );
		}

		$title = ( ! isset( $item->label ) || '' == $item->label ) ? $title : $item->label;

		$submenu_text = '';
		if ( 0 == $depth ){
			$submenu_text = 'style="display: none;"';
			
			if(isset($item->megamenu) && $item->megamenu == "enabled"){
				$classes[] = 'megamenu-enabled';
			}
		}
		
		?>
		<li id="menu-item-<?php echo esc_attr($item_id); ?>" class="<?php echo implode(' ', $classes ); ?>">
			<dl class="menu-item-bar">
				<dt class="menu-item-handle">
                	<span class="menu-megamenu-handle"><?php echo esc_html__('Mega Menu', 'tolips'); ?></span>
					<span class="item-title"><span class="menu-item-title"><?php echo esc_html( $title ); ?></span> <span class="is-submenu" <?php echo esc_html($submenu_text); ?>></span></span>
					<span class="item-controls">
						<span class="item-type"><?php echo esc_html( $item->type_label ); ?></span>
						<span class="item-order hide-if-js">
							<a href="<?php
								echo wp_nonce_url(
									add_query_arg(
										array(
											'action' => 'move-up-menu-item',
											'menu-item' => $item_id,
										),
										remove_query_arg($removed_args, admin_url( 'nav-menus.php' ) )
									),
									'move-menu_item'
								);
							?>" class="item-move-up"><abbr title="<?php echo esc_attr__('Move up', 'tolips'); ?>">&#8593;</abbr></a>
							|
							<a href="<?php
								echo wp_nonce_url(
									add_query_arg(
										array(
											'action' => 'move-down-menu-item',
											'menu-item' => $item_id,
										),
										remove_query_arg($removed_args, admin_url( 'nav-menus.php' ) )
									),
									'move-menu_item'
								);
							?>" class="item-move-down"><abbr title="<?php echo esc_attr__('Move down', 'tolips'); ?>">&#8595;</abbr></a>
						</span>
						<a class="item-edit" id="edit-<?php echo esc_attr($item_id); ?>" title="<?php echo esc_attr__('Edit Menu Item', 'tolips'); ?>" href="<?php
							echo ( isset( $_GET['edit-menu-item'] ) && $item_id == $_GET['edit-menu-item'] ) ? admin_url( 'nav-menus.php' ) : add_query_arg( 'edit-menu-item', $item_id, remove_query_arg( $removed_args, admin_url( 'nav-menus.php#menu-item-settings-' . $item_id ) ) );
						?>">&nbsp;</a>
					</span>
				</dt>
			</dl>

			<div class="menu-item-settings" id="menu-item-settings-<?php echo esc_attr($item_id); ?>">
            	<?php // custom field for mega menu  start?>
            	
            	<div class="menu-megamenu" style="border: 2px solid #000;">
                	<h4 class="menu-megamenu-title"><?php echo esc_html__( 'Mega Menu Setting', 'tolips' ); ?></h4>
                	<p class="menu-megamenu-enable">
                    	<label for="edit-menu-item-megamenu-<?php echo esc_attr($item_id); ?>">
                            <input type="checkbox" class="menu-megamenu-enable-checkbox" id="edit-menu-item-megamenu-<?php echo esc_attr($item_id); ?>" value="enabled" name="menu-item-megamenu[<?php echo esc_attr($item_id); ?>]" <?php checked( $item->megamenu, 'enabled' ); ?>/>
                            <?php echo esc_html__( 'Mega Menu Enabled', 'tolips' ); ?>
                        </label>
                    </p>

                    <p class="menu-megamenu-direction" style="display: none;">
                    
                        <label> <?php echo esc_html__( 'Choose MegaMenu', 'tolips' ); ?> <br /></label>
                        <select id="edit-menu-item-megadirection-<?php echo esc_attr($item_id); ?>-2" value="vertical" name="menu-item-megadirection[<?php echo esc_attr($item_id); ?>]">
                        	<?php foreach ($megamenus as $key => $title) { ?>
                        		<option <?php if($key == $item->megadirection){ echo 'selected'; } ?> value="<?php echo esc_attr($key) ?>"><?php echo esc_html($title) ?></option>
                        	<?php } ?>
                        </select> 
                           
                    </p>
                    
                    <p class="menu-megamenu-megacolumns">
                        <label>
                           <?php 
                            if(empty($item->megacolumns) || $item->megacolumns==0){
                            		$item->megacolumns = 3;
                            }
                            echo esc_html__( 'Number columns', 'tolips' ); ?><br />
                            <input type="number" id="edit-menu-item-megacolumns-<?php echo esc_attr($item_id); ?>" class="width-75 code edit-menu-item-megacolumns" name="menu-item-megacolumns[<?php echo esc_attr($item_id); ?>]" value="<?php echo esc_attr( $item->megacolumns ); ?>" />
                        	<p><?php  echo esc_html__( 'Number columns of submenu(support 1,2,3,4,6 columns)', 'tolips' ); ?></p>
                        </label>
                    </p>

                    <p class="menu-megamenu-megawidth">
                        <label>
                           <?php 
                            if(empty($item->megawidth) || $item->megawidth==0){
                            		$item->megawidth = 680;
                            }
                            echo esc_html__( 'Mega menu width', 'tolips' ); ?><br />
                            <input type="number" id="edit-menu-item-megawidth-<?php echo esc_attr($item_id); ?>" class="width-75 code edit-menu-item-megawidth" name="menu-item-megawidth[<?php echo esc_attr($item_id); ?>]" value="<?php echo esc_attr( $item->megawidth ); ?>" />
                        </label>
                    </p>

                     <p class="menu-megamenu-megaalign">
                        <label>
                           <?php 
                           if(empty($item->megaalign)){
                            	$item->megaalign = 'center';
                           }
                           echo esc_html__( 'Mega menu Align', 'tolips' ); ?><br />

                           <select id="edit-menu-item-megaalign-<?php echo esc_attr($item_id); ?>" class="width-75 code edit-menu-item-megaalign" name="menu-item-megaalign[<?php echo esc_attr($item_id); ?>]">
                           	<option value="center" <?php echo esc_attr($item->megaalign == 'center' ? 'selected' : '') ?>><?php echo esc_html__( "Center", 'tolips' ) ?></option>
                           	<option value="left" <?php echo esc_attr($item->megaalign == 'left' ? 'selected' : '') ?>><?php echo esc_html__( "Left", 'tolips' ) ?></option>
                           	<option value="right" <?php echo esc_attr($item->megaalign == 'right' ? 'selected' : '') ?>><?php echo esc_html__( "Right", 'tolips' ) ?></option>
                           </select>

                        </label>
                    </p>

                    <input type="hidden" name="menu-item-submegamenu[<?php echo esc_attr($item_id); ?>]" class="edit-menu-item-submegamenu" value="<?php echo esc_attr( $item->submegamenu ); ?>" />
                </div>

                <?php // custom field for mega menu end ?>

                	<div>
            			<p class="menu-megamenu-megaicon">
                        <label>
                            <?php echo esc_html__( 'Icon', 'tolips' ); ?> <a href="<?php echo is_ssl() ? 'https' : 'http'; ?>://fortawesome.github.io/Font-Awesome/icons/" target="_bank"><?php esc_html_e('(icons in Font Awesome)', 'tolips'); ?></a> <br />
                            <input type="text" id="edit-menu-item-megaicon-<?php echo esc_attr($item_id); ?>" class="widefat code edit-menu-item-megaicon" name="menu-item-megaicon[<?php echo esc_attr($item_id); ?>]" value="<?php echo esc_attr( $item->megaicon ); ?>" />
                        </label>
                    	</p>
               	</div>

				<?php if( 'custom' == $item->type ) : ?>
					<p class="field-url description description-wide">
						<label for="edit-menu-item-url-<?php echo esc_attr($item_id); ?>">
							<?php echo esc_html__( 'URL' , 'tolips'); ?><br />
							<input type="text" id="edit-menu-item-url-<?php echo esc_attr($item_id); ?>" class="widefat code edit-menu-item-url" name="menu-item-url[<?php echo esc_attr($item_id); ?>]" value="<?php echo esc_attr( $item->url ); ?>" />
						</label>
					</p>
				<?php endif; ?>
				<p class="description description-thin">
					<label for="edit-menu-item-title-<?php echo esc_attr($item_id); ?>">
						<?php echo esc_html__( 'Navigation Label' , 'tolips'); ?><br />
						<input type="text" id="edit-menu-item-title-<?php echo esc_attr($item_id); ?>" class="widefat edit-menu-item-title" name="menu-item-title[<?php echo esc_attr($item_id); ?>]" value="<?php echo esc_attr( $item->title ); ?>" />
					</label>
				</p>
				<p class="description description-thin">
					<label for="edit-menu-item-attr-title-<?php echo esc_attr($item_id); ?>">
						<?php echo esc_html__( 'Title Attribute' , 'tolips'); ?><br />
						<input type="text" id="edit-menu-item-attr-title-<?php echo esc_attr($item_id); ?>" class="widefat edit-menu-item-attr-title" name="menu-item-attr-title[<?php echo esc_attr($item_id); ?>]" value="<?php echo esc_attr( $item->post_excerpt ); ?>" />
					</label>
				</p>
				<p class="field-link-target description">
					<label for="edit-menu-item-target-<?php echo esc_attr($item_id); ?>">
						<input type="checkbox" id="edit-menu-item-target-<?php echo esc_attr($item_id); ?>" value="_blank" name="menu-item-target[<?php echo esc_attr($item_id); ?>]"<?php checked( $item->target, '_blank' ); ?> />
						<?php echo esc_html__( 'Open link in a new window/tab' , 'tolips'); ?>
					</label>
				</p>
				<p class="field-css-classes description description-thin">
					<label for="edit-menu-item-classes-<?php echo esc_attr($item_id); ?>">
						<?php echo esc_html__( 'CSS Classes (optional)' , 'tolips'); ?><br />
						<input type="text" id="edit-menu-item-classes-<?php echo esc_attr($item_id); ?>" class="widefat code edit-menu-item-classes" name="menu-item-classes[<?php echo esc_attr($item_id); ?>]" value="<?php echo esc_attr( implode(' ', $item->classes ) ); ?>" />
					</label>
				</p>
				<p class="field-xfn description description-thin">
					<label for="edit-menu-item-xfn-<?php echo esc_attr($item_id); ?>">
						<?php echo esc_html__( 'Link Relationship (XFN)' , 'tolips'); ?><br />
						<input type="text" id="edit-menu-item-xfn-<?php echo esc_attr($item_id); ?>" class="widefat code edit-menu-item-xfn" name="menu-item-xfn[<?php echo esc_attr($item_id); ?>]" value="<?php echo esc_attr( $item->xfn ); ?>" />
					</label>
				</p>
				<p class="field-description description description-wide">
					<label for="edit-menu-item-description-<?php echo esc_attr($item_id); ?>">
						<?php echo esc_html__( 'Description' , 'tolips'); ?><br />
						<textarea id="edit-menu-item-description-<?php echo esc_attr($item_id); ?>" class="widefat edit-menu-item-description" rows="3" cols="20" name="menu-item-description[<?php echo esc_attr($item_id); ?>]"><?php echo esc_html( $item->description ); // textarea_escaped ?></textarea>
						<span class="description"><?php echo esc_html__('The description will be displayed in the menu if the current theme supports it.', 'tolips'); ?></span>
					</label>
				</p>

				<p class="field-move hide-if-no-js description description-wide">
					<label>
						<span><?php echo esc_html__( 'Move' , 'tolips'); ?></span>
						<a href="#" class="menus-move-up"><?php echo esc_html__( 'Up one' , 'tolips'); ?></a>
						<a href="#" class="menus-move-down"><?php echo esc_html__( 'Down one' , 'tolips'); ?></a>
						<a href="#" class="menus-move-left"></a>
						<a href="#" class="menus-move-right"></a>
						<a href="#" class="menus-move-top"><?php echo esc_html__( 'To the top' , 'tolips'); ?></a>
					</label>
				</p>

				<div class="menu-item-actions description-wide submitbox">
					<?php if( 'custom' != $item->type && $original_title !== false ) : ?>
						<p class="link-to-original">
							<?php printf( esc_html__('Original: %s', 'tolips'), '<a href="' . esc_attr( $item->url ) . '">' . esc_html( $original_title ) . '</a>' ); ?>
						</p>
					<?php endif; ?>
					<a class="item-delete submitdelete deletion" id="delete-<?php echo esc_attr($item_id); ?>" href="<?php
					echo wp_nonce_url(
						add_query_arg(
							array(
								'action' => 'delete-menu-item',
								'menu-item' => $item_id,
							),
							admin_url( 'nav-menus.php' )
						),
						'delete-menu_item_' . $item_id
					); ?>"><?php echo esc_html__( 'Remove' , 'tolips'); ?></a> <span class="meta-sep hide-if-no-js"> | </span> <a class="item-cancel submitcancel hide-if-no-js" id="cancel-<?php echo esc_attr($item_id); ?>" href="<?php echo esc_url( add_query_arg( array( 'edit-menu-item' => $item_id, 'cancel' => time() ), admin_url( 'nav-menus.php' ) ) );
						?>#menu-item-settings-<?php echo esc_attr($item_id); ?>"><?php esc_html_e('Cancel', 'tolips'); ?></a>
				</div>

				<input class="menu-item-data-db-id" type="hidden" name="menu-item-db-id[<?php echo esc_attr($item_id); ?>]" value="<?php echo esc_attr($item_id); ?>" />
				<input class="menu-item-data-object-id" type="hidden" name="menu-item-object-id[<?php echo esc_attr($item_id); ?>]" value="<?php echo esc_attr( $item->object_id ); ?>" />
				<input class="menu-item-data-object" type="hidden" name="menu-item-object[<?php echo esc_attr($item_id); ?>]" value="<?php echo esc_attr( $item->object ); ?>" />
				<input class="menu-item-data-parent-id" type="hidden" name="menu-item-parent-id[<?php echo esc_attr($item_id); ?>]" value="<?php echo esc_attr( $item->menu_item_parent ); ?>" />
				<input class="menu-item-data-position" type="hidden" name="menu-item-position[<?php echo esc_attr($item_id); ?>]" value="<?php echo esc_attr( $item->menu_order ); ?>" />
				<input class="menu-item-data-type" type="hidden" name="menu-item-type[<?php echo esc_attr($item_id); ?>]" value="<?php echo esc_attr( $item->type ); ?>" />
			</div>
			<ul class="menu-item-transport"></ul>
		<?php
		$output .= ob_get_clean();
	}

}

$tolips_megamenu = new Tolips_Mega_Menu();
$tolips_megamenu->init_mega_menu();

