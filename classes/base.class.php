<?php
/**
 * Code Ninjas base class to do some random checks and setup for our plugins as well and other useful stuff.
 * http://www.96down.com/codecanyon-woocommerce-seo/
 * Current version - 1.1
 * 
 * 1.1
 *  A little more streamlined with checks and errors
 *  Fixed bug that would cause error is required plugin wasnt installed.
 * 
 * 1.0
 *  Initial implemntation
 */
if(!class_exists('Code_Ninjas_Base_Class')){
    
    include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
    
    class Code_Ninjas_Base_Class{

        private static $admin_notices;
        private $required_plugins = array();
        private $child_plugin;
        private $verified = true; 
        private $errors = array(
            'not_installed' => '<strong>%2$s</strong> has been deactivated because <strong>%1$s</strong> is not installed.  Please install and activate <strong>%1$s</strong> before activating <strong>%2$s</strong>.',
            'not_active' => '<strong>%2$s</strong> has been deactivated because <strong>%1$s</strong> is currently inactive.  Please activate <strong>%1$s</strong> before activating <strong>%2$s</strong>.',
            'wrong_version' => '<strong>%1$s</strong> has been deactivated because it requires <strong>%2$s %3$s</strong> or greater.  Please update <strong>%2$s</strong> before activating <strong>%1$s</strong>.'
        );        

        function __construct( $this_plugin_slug ) {  
            
            global $pagenow;
            
            add_action( 'extra_plugin_headers', array( $this, 'extra_plugin_headers' ) );
            
            add_action( 'admin_notices', array( $this, 'output_admin_notices' ) );
            
            add_filter( 'plugin_row_meta', array( $this, 'add_extra_meta_links' ), 10, 2 );
    
            $this->child_plugin = $this_plugin_slug; 
            
            $this_plugin_data = get_plugin_data(WP_PLUGIN_DIR.'/'.$this_plugin_slug);

            if( $pagenow == 'update.php' ){
                //check if were on update page 
                $this->verified = false; //dont run child plugin setup.
            } else {
                //run checks as normal
                $installed_plugins = get_plugins();            

                //check required plugins
                if(!empty($this->required_plugins)){

                    foreach($this->required_plugins as $plugin){ 

                        //check if installed
                        if( array_key_exists( $plugin['slug'], $installed_plugins ) ){
                            //check if active
                            if( is_plugin_active( $plugin['slug'] ) ){
                                //check min version number
                                if( isset( $plugin['min_version'] ) ){

                                    $current_version = $installed_plugins[$plugin['slug']]['Version']; 

                                    if( version_compare( $current_version, $plugin['min_version'], '<' ) ){   
                                        deactivate_plugins( $this_plugin_slug );
                                        $this->verified = false;
                                        $this->add_admin_notice('error', sprintf( $this->errors['wrong_version'], $this_plugin_data['Name'], $plugin['name'], $plugin['min_version'] ) );
                                    } else {
                                        //all checks are OK! Continue with setup

                                        
                                    }

                                }

                            } else { //not active
                                deactivate_plugins( $this_plugin_slug );
                                $this->verified = false;
                                $this->add_admin_notice('error', sprintf( $this->errors['not_active'], $plugin['name'], $this_plugin_data['Name'] ) );
                            }


                        } else { //not installed
                            deactivate_plugins( $this_plugin_slug );
                            $this->verified = false;
                            $this->add_admin_notice('error', sprintf( $this->errors['not_installed'], $plugin['name'], $this_plugin_data['Name'] ));
                        }

                    }

                }  
            
            }

        }
        
        function add_extra_meta_links( $links, $file ){
            
            if( $file == $this->child_plugin ){
                
                $plugin_data = get_plugin_data(WP_PLUGIN_DIR.'/'.$this->child_plugin);
                
                if(array_key_exists( 'Documentation URI', $plugin_data )){
                    $links[] = '<a href="'.$plugin_data['Documentation URI'].'" title="'.$plugin_data['Name'].' Documentation">Documentation</a>';
                }
            }
            
            return $links;
            
        }
        
        /**
         * Add Documentation URL plugin header
         * @param array $headers
         * @return array 
         */
        function extra_plugin_headers($headers){
            $headers['Documentation URI'] = 'Documentation URI';
            
            return $headers;
        }
        
        protected function add_required_plugin($plugin_info){ 
            $this->required_plugins[$plugin_info['slug']] = $plugin_info;
        }
        
        protected function everything_ok(){
            return $this->verified;
        }


        public static function add_admin_notice($type, $message){
            self::$admin_notices[] = array($type => $message);
        }

        public function output_admin_notices(){      
            if( !empty( self::$admin_notices ) ){
                foreach(self::$admin_notices as $notice){
                    echo '<div class="'.key($notice).'"><p>'.$notice[key($notice)].'</p></div>';
                }
            }
        }

 
    }   

}

?>
