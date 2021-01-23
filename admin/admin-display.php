<?php

  /**
   * Settings Page
   */
  
  global $pagenow;
  $settings = IBX_WP::get_option( "settings" );
  $open_source = true;
  
  // IBX_WP::login_using_hash( $settings['hash'] );
  if( $settings['hash'] != '' ) {
    $open_source = false;
  }

  $tab = 'themes'; 
  // $themes_base_uri='/themes';

  // $plugins_base_uri='/plugins';

  
  if ( isset ( $_GET['tab'] ) && $_GET['tab'] == 'plugins' ) {
    $tab = $_GET['tab']; 
    $existing_items = get_plugins();
    $base_uri='/photos';
  } else {
    $existing_items = wp_get_themes();
    $base_uri='/photos';
  }

  function ibx_wp_admin_tabs( $current = 'themes' ) { 
    $tabs = array( 'themes' => 'Themes', 'plugins' => 'Plugins' ); 
    $links = array();
    echo '<div id="icon-themes" class="icon32"><br></div>';
    echo '<h2 class="nav-tab-wrapper">';
    foreach( $tabs as $tab => $name ){
        $class = ( $tab == $current ) ? ' nav-tab-active' : '';
        echo "<a class='nav-tab$class' href='?page=iboxindia-wordpress&tab=$tab'>$name</a>";
        
      }
      echo '</h2>';
    }
 
    function render_items_array ( $items, $existing_items = [] ) {?>
      <div class="ibx-items wp-clearfix">
        <?php foreach( $items as $item ) {
          // render_item ( $item, array_key_exists( $item['slug'], $existing_items ) );
          render_item ( $item );
        } ?>
      </div>
      <?php
    }

    function render_item ( $item, $existing_item = null ) {
        // var_dump ($item);
      ?>
      <div class="ibx-item" tabindex="0" >
        <div class="ibx-item-screenshot">
          <img src="<?php echo $item['thumbnailUrl']; ?>" alt="<?php echo $item['title']; ?>">
          </div>
    			<div class="update-message notice inline notice-warning notice-alt">
          <?php if ( $existing_item != null ) { ?>
            <p>
					    Installed. 
              <?php if ( $existing_item['ver'] < $item['ver'] ) { ?>
                <button class="button-link" type="button">Update now</button>				
              <?php } ?>
			      </p>
          <?php } ?>
        </div>
	      <div class="ibx-item-container">
          <h2 class="ibx-item-name" id="<?php //echo $item['slug']; ?>">
            <?php echo $item['title']; ?>
          </h2>
          <?php if ( $existing_item == null ) { ?>
  		      <div class="ibx-item-actions">
					    <a class="button button-primary" href="">Install</a>
            </div>
          <?php } ?>
        </div>
      </div>
      <?
    }
  ?>
	
	<div class="wrap">
		<h2>Iboxindia - <?php echo $open_source ? 'Open Source' : 'Premium'; ?></h2>
		
		<?php ibx_wp_admin_tabs( $tab ); ?>
    <div class="ibx-items-browser">
      <?php
        $items = ibx_wp_postman_get( $base_uri );
        render_items_array ( $items, $existing_items );
        // switch ( $tab ){
        //   case 'plugins' : 
        //     render_items_array ( $plugins, $all_plugins );
        //     break;
        //   case 'themes' : 
        //     render_items_array ( $themes, $all_themes );
        //   break;
        // }
      ?>
		</div>
	</div>