<?php
/*
Plugin Name: Database Crud
Plugin URI: https://Database-Crud.com/
Description: My plugin to explain the crud function.
Version: 1.0.0
Author: Muslim Khan
Author URI: https://example.com
License: GPL2
*/

class dc_database_crud{
     public function __construct() {

          // Register the activation hook inside the constructor
          register_activation_hook(__FILE__, [$this, 'table_creatore']);

          // admin menu creation
          add_action('admin_menu', array($this, 'admin_menu_create'));

          //Css configuration
          add_action('admin_enqueue_scripts', array($this, 'wp_enqueue_script')); 
     }

    
     

     //Function to create the table
     public function table_creatore() {
          global $wpdb;
          $charset_collate = $wpdb->get_charset_collate();
          $table_name = $wpdb->prefix. 'db_crud';

          // SQL query to create table
          $sql = "DROP TABLE IF EXISTS $table_name;
               CREATE TABLE $table_name (
                    db_crud_id mediumint(11) NOT NULL AUTO_INCREMENT,
                    db_crud_name varchar(50) NOT NULL,
                    db_crud_email varchar(50) NOT NULL,
                    PRIMARY KEY (db_crud_id)
               ) $charset_collate;";
          
          require_once (ABSPATH . 'wp-admin/includes/upgrade.php');
          dbDelta($sql);
     }



     
     // Function to create the admin menu and submenus
     public function admin_menu_create() {
          // Main menu page
          add_menu_page(
          'Database CRUD',
          'Database CRUD', 
          'manage_options', 
          'dc_database_crud', 
          array($this, 'admin_page'), 
          'dashicons-database', 
          6 
          );

          // 1st submenu (list)
          add_submenu_page(
          'dc_database_crud', 
          'Database List', 
          'Database List', 
          'manage_options', 
          'dc_database_list', 
          array($this, 'list_admin_subpage') 
          );

          // 2nd submenu (add)
          add_submenu_page(
          'dc_database_crud', 
          'Add Database', 
          'Add Database', 
          'manage_options', 
          'add_database', 
          array($this, 'add_database') 
          );

          // Update submenu
          add_submenu_page(
               'null', 
               'Update Database', 
               'Update Database', 
               'manage_options', 
               'update_crud', 
               array($this, 'update_callback') 
          );


          // Deleted submenu
          add_submenu_page(
               'dc_database_crud', 
               'Deleted Database', 
               'Deleted Database', 
               'manage_options', 
               'deleted-crud', 
               array($this, 'deleted_database') 
          );

     }

     // Main admin page
     public function admin_page() {

     echo 'Vaiya ami sob kisu database list ar vitor korci ';
     }

     // Submenu 1 page
     public function list_admin_subpage() {

          
     }

     // add Submenu 2 page
     public function add_database() {

          global $wpdb;
          $table_name = $wpdb->prefix . 'db_crud';
          $msg = '';

          if ( isset($_POST['submit'])) {
               $name = sanitize_text_field($_POST['db_crud_name']);
               $email = sanitize_email($_POST['db_crud_email']);

          
               $result = $wpdb->insert($table_name, [
                    'db_crud_name' => $name,
                    'db_crud_email' => $email,
               ]);

               if ($result) {
                    $msg = "Save successfully";
               } else {
                    $msg = "Save failed";
               }

          }
     
         

          ?>
            <h4> <?php echo $msg; ?></h4>


               <div class="wrapper">
                    <form method="post">
                         <h1> Add_Database </h1>
                         <p>
                              <label>ID:</label>
                              <input type="text" name="db_crud_id" placeholder="Enter your ID">
                         </p>

                         <p>
                              <label>Name:</label>
                              <input type="text" name="db_crud_name" placeholder="Enter your Name">
                         </p>

                         <p>
                              <label>Email:</label>
                              <input type="email" name="db_crud_email" placeholder="Enter your Email">
                         </p>

                         <button type="submit" name="submit">Submit</button>
                    </form>
               </div>



          <?php
     }





     
     //update_callback
     function update_callback(){


          global $wpdb;
          if( isset($_GET["action"]) == "delete") {
               
               global $wpdb;
               $table_name = $wpdb->prefix . 'db_crud';
               $id = isset($_GET['id']) ? $_GET['id'] : "";

                    $delete= $wpdb->delete($table_name, array('db_crud_id' => $id));

               if( $delete) {


                 
                    
               
                    ?>
                    <script>
                         location.href = "<?php echo site_url('/wp-admin/admin.php?page=dc_database_list'); ?>";
                    </script>
                    <?php
               } else {
                    echo 'Not deleted';
               }

          } else {
               $table_name = $wpdb->prefix . 'db_crud';
               $msg = '';
               $id = isset($_REQUEST['id']) ? intval($_REQUEST['id']) : "";
               if (isset($_REQUEST['update'])) {
               if (!empty($id)) {
                    $wpdb->update("$table_name", 
                    [ 
                    'db_crud_name' => $_REQUEST['db_crud_name'], 
                    'db_crud_email' => $_REQUEST['db_crud_email'], 
                    
                    ], 
                    ["db_crud_id" => $id]);
                    $msg = 'Data updated';
               }
               }
              $employee_details = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name where db_crud_id = %d", $id), ARRAY_A);
   
               ?>


                    <h4><?php echo $msg; ?></h4>

                         <div class="main_form">
                              <form method="post">
                                   <p>
                                        <label>EMP ID</label>
                                        <input type="text" name="db_crud_id" placeholder="Enter ID" 
                                             value="<?php echo $employee_details['db_crud_id']; ?>" required>
                                   </p>

                                   <p>
                                        <label>Name</label>
                                        <input type="text" name="db_crud_name" placeholder="Enter Name" 
                                             value="<?php echo $employee_details['db_crud_name']; ?>" required>
                                   </p>

                                   <p>
                                        <label>Email</label>
                                        <input type="email" name="db_crud_email" placeholder="Enter Email" 
                                             value="<?php echo $employee_details['db_crud_email']; ?>" required>
                                   </p>

                                   <p>
                                        <button type="submit" name="update">Update</button>
                                   </p>
                              </form>
                         </div>


               <?php
          }  
          
     }
      
      



     // Deleted callback function
     public function deleted_database() { 
          

     }   
     
     
     public function wp_enqueue_script() {
          $folder_path = plugin_dir_url(__FILE__);   // Plugin file path
          $css_path = $folder_path . "css/";         // CSS folder path
          
      
          wp_enqueue_style("rp_style", $css_path . "frontend.css", [], '1.0.0', 'all');    // CSS file path
     }
      
}
// Initialize the class
new dc_database_crud();

