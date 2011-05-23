<?php
/*
  Plugin Name: Golf Tracker
  Plugin URI: http://github.com/buddylindsey/golf-tracker
  Description: Track your golf game and other statistics.
  Version: 0.5
  Author: Buddy Lindsey
  Author URI: http://buddylindsey.com/
  License: BSD
*/

register_activation_hook(__FILE__, 'install_golf_tracker');

add_action('admin_menu', 'golf_menu');
add_action('wp_print_styles', 'add_my_stylesheet');
add_action('plugin_loaded', 'golf_tracker_update_db_check');


global $golf_db_version;
$golf_db_version = "0.71";

function install_golf_tracker(){
  global $golf_db_version;
  global $wpdb;

  $table_courses = $wpdb->prefix . "courses";
  $table_scores = $wpdb->prefix . "scores";

  $courses_sql = "CREATE TABLE `" . $table_courses . "` (
                  id mediumint(9) NOT NULL AUTO_INCREMENT,
                  name varchar(100) NOT NULL,
                  city varchar(30),
                  state varchar(2),
                  country varchar(30),
                  front9_par smallint,
                  back9_par smallint,
                  total_par smallint,
                  front9_yard smallint,
                  back9_yard smallint,
                  total_yard smallint,
                  rating smallint,
                  slope smallint,
                  url varchar(100) DEFAULT '' NOT NULL,
                  hole_1 tinyint,
                  hole_2 tinyint,
                  hole_3 tinyint,
                  hole_4 tinyint,
                  hole_5 tinyint,
                  hole_6 tinyint,
                  hole_7 tinyint,
                  hole_8 tinyint,
                  hole_9 tinyint,
                  hole_10 tinyint,
                  hole_11 tinyint,
                  hole_12 tinyint,
                  hole_13 tinyint,
                  hole_14 tinyint,
                  hole_15 tinyint,
                  hole_16 tinyint,
                  hole_17 tinyint,
                  hole_18 tinyint,
                  UNIQUE KEY id(id)
                );";

 $scores_sql = "CREATE TABLE `" . $table_scores . "` (
                  id int NOT NULL AUTO_INCREMENT,
                  course_id mediumint(9) NOT NULL,
                  front9 tinyint(1) DEFAULT '0' NOT NULL,
                  back9 tinyint(1) DEFAULT '0' NOT NULL,
                  date_played date,
                  front9_score smallint,
                  back9_score smallint,
                  total_score smallint NOT NULL,
                  hole_1 tinyint,
                  hole_2 tinyint,
                  hole_3 tinyint,
                  hole_4 tinyint,
                  hole_5 tinyint,
                  hole_6 tinyint,
                  hole_7 tinyint,
                  hole_8 tinyint,
                  hole_9 tinyint,
                  hole_10 tinyint,
                  hole_11 tinyint,
                  hole_12 tinyint,
                  hole_13 tinyint,
                  hole_14 tinyint,
                  hole_15 tinyint,
                  hole_16 tinyint,
                  hole_17 tinyint,
                  hole_18 tinyint,
                  UNIQUE KEY id(id)
                );";

  require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
  dbDelta($courses_sql);
  dbDelta($scores_sql);

  add_option('golf_tracker_db_version', $golf_db_version);
 
}

function golf_tracker_update_db_check(){
  global $golf_db_version;

  if(get_site_option('golf_tracker_db_version') != $golf_db_version){
    install_golf_tracker();
  }
}

function golf_menu(){
  add_menu_page('Golf Tracker', 'Golf Tracker', 'manage_options', 'golf-tracker', 'main_admin_page_html',null,3);
  add_submenu_page('golf-tracker', 'Course Management', 'Course Management', 'manage_options', 'manage-course', 'manage_golf_course_page');
  add_submenu_page('golf-tracker', 'Score Management', 'Score Management', 'manage_options', 'manage-score', 'manage_golf_score_page');
  add_submenu_page('golf-tracker', 'Add Course', 'Add Golf Course', 'manage_options', 'add-course', 'add_golf_course_page');
  add_submenu_page('golf-tracker', 'Add Score', 'Add Score', 'manage_options', 'add-score', 'add_golf_score_page');
  //add_submenu_page('golf-tracker', 'Settings', 'Settings', 'manage_options', 'golf-tracker-settings', 'golf_tracker_settings_page');
}

function main_admin_page_html(){
  echo 'Cool information about this plugin';
}

function manage_golf_course_page(){
  echo '<table class="wp-list-table widefat fixed" cellspacing="0">';
  echo '<thead><tr>'.th("Actions").th("Name").th("City").th("Par").th("Yardage").th("url").'</tr></thead>';
  echo all_golf_courses_for_management_table();
  echo '<tfoot><tr>'.th("Actions").th("Name").th("City").th("Par").th("Yardage").th("url").'</tr></tfoot>';
  echo '</table>';
}



function manage_golf_score_page(){
  echo '<table class="wp-list-table widefat fixed" cellspacing="0">';
  echo '<thead><tr>'.th("Actions").th("Course").th("Score").th("Holes Played").th("Date Played").'</tr></thead>';
  echo all_golf_scores_for_management_table();
  echo '<tfoot><tr>'.th("Actions").th("Course").th("Score").th("Holes Played").th("Date Played").'</tr></tfoot>';
  echo '</table>';
}

function add_golf_course_page(){
  echo '<div class="wrap">';
  echo '<div id="icon-tools" class="icon32"><br /></div>';
  echo '<h2>Add Course</h2>';
  echo '<form method="post" action="' . $_SERVER["REQUEST_URI"] . '">';
  echo '<table class="form-table">';
  echo golf_input_field('Course', 'name');
  echo golf_input_field('City', 'city');
  echo golf_input_field('State', 'state');
  echo golf_input_field('Country', 'country');
  echo golf_input_field('Front 9 Par', 'front9_par');
  echo golf_input_field('Back 9 par', 'back9_par');
  echo golf_input_field('Front 9 Yardage', 'front9_yard');
  echo golf_input_field('Back 9 yardage', 'back9_yard');
  echo golf_input_field('Rating', 'rating');
  echo golf_input_field('Slope', 'slope');
  echo golf_input_field('URL', 'url');
  echo golf_input_holes();
  echo '</table>';
  echo '<input type="submit" name="submit_add_course" value="Add Course" />';
  echo '</form>';
  echo '</div>';
}

function add_golf_score_page(){
  echo '<div class="wrap">';
  echo '<div id="icon-tools" class="icon32"><br /></div>';
  echo '<h2>Add Score</h2>';
  echo '<form method="post" action="'. $_SERVER["REQUEST_URI"] . '">';
  echo '<table class="form-table">';
  echo dropdown_for_all_golf_courses();
  echo golf_check_box_field('Played Front 9', 'front9');
  echo golf_check_box_field('Played Back 9', 'back9'); 
  echo golf_input_field('Front 9 Score', 'front9_score');
  echo golf_input_field('Back 9 Score', 'back9_field');
  echo golf_input_field('Date', 'date_played');
  echo golf_input_holes();
  echo '</table>';
  echo '<input type="submit" name="submit_add_score" value="Add Score" />';
  echo '</form>';
  echo '</div>';
}

function my_handicap(){
  global $wpdb;

  $count = $wpdb->get_var('SELECT COUNT(*) FROM `'.$wpdb->prefix.'scores`');

  if($count < 10){
    $scores = $wpdb->get_results('SELECT total_score, course_id FROM `'.$wpdb->prefix.'scores` ORDER BY \'date_played\' DESC LIMIT 5');

    $s = array();

    $amount = (count($scores) < 5) ? count($s) : 5 ;

    for($i = 0; $i < $amount; $i++){
      $s[$i] = handicap_algorithm_diff($scores[$i], get_golf_course_by_id($scores[$i]->course_id));
    }

    $lowest = array();

    if(count($s) > 0){
      $lowest = min($s);
    }
    
    if(count($lowest) > 0)
      return $lowest * .96 * 100;
  }
  else{
    $scores = $wpdb->get_results('SELECT total_score, course_id FROM `'.$wpdb->prefix.'scores` ORDER BY \'date_played\' DESC LIMIT 20');

    $s = array();

    $amount = (count($scores) < 20) ? count($s) : 5 ;

    for($i = 0; $i < $amount; $i++){
      $s[$i] = handicap_algorithm_diff($scores[$i], get_golf_course_by_id($scores[$i]->course_id));
    }

    $ten_lowest = array();

    if(count($s) == 10){
      $ten_lowest = $s;
    } else {
      $ten_lowest = the_ten_lowest_diffs($s); 
    }

    return average_of_sum($ten_lowest) * .96 * 100;

  }
}

function average_of_array($num){
  return array_sum($num) / 10;
}

function the_ten_lowest_diffs($diffs){
  $n = array();

  for($i = 0; $i < 10; $i++){
    $n[$i] = min($diffs);

    for($j = 0; $j < count(diffs); $j++){
      if($n[$i] == $diffs[$j]){
        unset($diffs[$j]);
      }
    }
  }

  return $n; 
}

function handicap_algorithm_diff($score, $course){
  return ($score->total_score - $course->rating) * (113/$course->slope);
}

function add_the_golf_score(){
  if(isset($_POST['submit_add_score'])){
    global $wpdb;

    $wpdb->insert($wpdb->prefix.'scores', 
                array('course_id' => $_POST['course_id'],                                    
                      'front9' => (isset($_POST['front9'])) ? 1 : 0,                          
                      'back9' => (isset($_POST['back9'])) ? 1 : 0,                           
                      'front9_score' => (!isset($_POST['front9_score'])) ? 0 : $_POST['front9_score'],
                      'back9_score' => (!isset($_POST['back9_score'])) ? 0 : $_POST['back9_score'],
                      'total_score' => get_the_total_score(),                        
                      'date_played' => $_POST['date_played'],
                      'hole_1' => $_POST['hole_1'],                            
                      'hole_2' => $_POST['hole_2'],                            
                      'hole_3' => $_POST['hole_3'],                            
                      'hole_4' => $_POST['hole_4'],                            
                      'hole_5' => $_POST['hole_5'],                            
                      'hole_6' => $_POST['hole_6'],                            
                      'hole_7' => $_POST['hole_7'],                            
                      'hole_8' => $_POST['hole_8'],                            
                      'hole_9' => $_POST['hole_9'],                            
                      'hole_10' => $_POST['hole_10'],                             
                      'hole_11' => $_POST['hole_11'],                             
                      'hole_12' => $_POST['hole_12'],                             
                      'hole_13' => $_POST['hole_13'],                             
                      'hole_14' => $_POST['hole_14'],                             
                      'hole_15' => $_POST['hole_15'],                             
                      'hole_16' => $_POST['hole_16'],                             
                      'hole_17' => $_POST['hole_17'],                             
                      'hole_18' => $_POST['hole_18']));                          
  }   
}

function add_the_golf_course(){
  if(isset($_POST['submit_add_course'])){
    global $wpdb;
                                                      
    $wpdb->insert($wpdb->prefix.'courses', 
                array('name' => $_POST['name'],                                    
                      'city' => $_POST['city'],                              
                      'state' => $_POST['state'],                             
                      'country' => $_POST['country'],
                      'url' => $_POST['url'],                    
                      'front9_par' => $_POST['front9_par'],                          
                      'back9_par' => $_POST['back9_par'],                           
                      'total_par' => $_POST['front9_par'] + $_POST['back9_par'],                           
                      'front9_yard' => $_POST['front9_yard'],                          
                      'back9_yard' => $_POST['back9_yard'],                           
                      'total_yard' => $_POST['front9_yard'] + $_POST['back9_yard'],                        
                      'rating' => $_POST['rating'],
                      'slope' => $_POST['slope'],
                      'hole_1' => $_POST['hole_1'],                            
                      'hole_2' => $_POST['hole_2'],                            
                      'hole_3' => $_POST['hole_3'],                            
                      'hole_4' => $_POST['hole_4'],                            
                      'hole_5' => $_POST['hole_5'],                            
                      'hole_6' => $_POST['hole_6'],                            
                      'hole_7' => $_POST['hole_7'],                            
                      'hole_8' => $_POST['hole_8'],                            
                      'hole_9' => $_POST['hole_9'],                            
                      'hole_10' => $_POST['hole_10'],                             
                      'hole_11' => $_POST['hole_11'],                             
                      'hole_12' => $_POST['hole_12'],                             
                      'hole_13' => $_POST['hole_13'],                             
                      'hole_14' => $_POST['hole_14'],                             
                      'hole_15' => $_POST['hole_15'],                             
                      'hole_16' => $_POST['hole_16'],                             
                      'hole_17' => $_POST['hole_17'],                             
                      'hole_18' => $_POST['hole_18']));
  }  
}                      

function edit_a_golf_course(){
    global $wpdb;
    
    $wpdb->update($wpdb->prefix.'courses',
                 array('name' => $_POST['name'],                                    
                      'city' => $_POST['city'],                              
                      'state' => $_POST['state'],                             
                      'country' => $_POST['country'],
                      'url' => $_POST['url'],                    
                      'front9_par' => $_POST['front9_par'],                          
                      'back9_par' => $_POST['back9_par'],                           
                      'total_par' => $_POST['front9_par'] + $_POST['back9_par'],                           
                      'front9_yard' => $_POST['front9_yard'],                          
                      'back9_yard' => $_POST['back9_yard'],                           
                      'total_yard' => $_POST['front9_yard'] + $_POST['back9_yard'],                        
                      'rating' => $_POST['rating'],
                      'slope' => $_POST['slope'],
                      'hole_1' => $_POST['hole_1'],                            
                      'hole_2' => $_POST['hole_2'],                            
                      'hole_3' => $_POST['hole_3'],                            
                      'hole_4' => $_POST['hole_4'],                            
                      'hole_5' => $_POST['hole_5'],                            
                      'hole_6' => $_POST['hole_6'],                            
                      'hole_7' => $_POST['hole_7'],                            
                      'hole_8' => $_POST['hole_8'],                            
                      'hole_9' => $_POST['hole_9'],                            
                      'hole_10' => $_POST['hole_10'],                             
                      'hole_11' => $_POST['hole_11'],                             
                      'hole_12' => $_POST['hole_12'],                             
                      'hole_13' => $_POST['hole_13'],                             
                      'hole_14' => $_POST['hole_14'],                             
                      'hole_15' => $_POST['hole_15'],                             
                      'hole_16' => $_POST['hole_16'],                             
                      'hole_17' => $_POST['hole_17'],                             
                      'hole_18' => $_POST['hole_18']),
               array('id' => $_GET["id"])); 
}



function golf_input_field($label, $field, $value = ''){
  $final = '<tr>';
  $final .= '<th><label for="'. $label .'">'. $label .'</label></th>';
  $final .= '<td><input type="text" id="' . $label . '" name="' . $field . '" value="'.$value.'" class="regular-text" /></td>';
  $final .= '</tr>';
  return $final;
}

function golf_input_holes(){
  $final = '';
  for($i =  1; $i <= 18; $i++){
    $final .= golf_input_field('Hole '. $i,'hole_'.$i);
  }
  return $final;
}

function golf_check_box_field($label, $field){
  $final = '<tr>';
  $final .= '<th><label for="'.$label.'">'.$label.'</label></th>';
  $final .= '<td><input type="checkbox" id="'.$label.'" name="'.$field.'" value="true" />';
  $final .= '</tr>';
  return $final;
}

function dropdown_for_all_golf_courses(){
  global $wpdb;
  
  $final = '<tr>';
  $final .= '<th><label for="Course">Course</label></th>';
  $final .= '<td>';
  $final .= '<select name="course_id">';

  $courses = $wpdb->get_results("SELECT id, name FROM ".$wpdb->prefix ."courses");
  foreach($courses as $course){
    $final .= '<option value="'.$course->id.'">'.$course->name.'</option>';
  }

  $final .= "</select>";
  $final .= "</td>";
  $final .= "</tr>";
  return $final;
}

function th($name){
  return '<th>'.$name.'</th>';
}

function td($name){
  return '<td>'.$name.'</td>';
}

function all_golf_courses_for_management_table(){
  global $wpdb;
  $final = '';

  $courses = $wpdb->get_results("SELECT id, name, city, total_par, total_yard, url FROM ".$wpdb->prefix ."courses");
  foreach($courses as $course){
    $final .= "<tr>";
    $final .= td('<a href="'.$_SERVER['REQUEST_URI'].'&action=edit&type=course&id='.$course->id.'">Edit</a>');
    $final .= td($course->name);
    $final .= td($course->city);
    $final .= td($course->total_par);
    $final .= td($course->total_yard);
    $final .= td('<a href="'.$course->url.'">'.$course->url.'</a>');
    $final .= "</tr>";
  }
  return $final;
}

function all_golf_scores_for_management_table(){
  global $wpdb;
  $final = '';

  $scores = $wpdb->get_results("SELECT id, course_id, total_score, date_played, front9, back9 FROM ".$wpdb->prefix."scores");

  foreach($scores as $score){
    $final .= "<tr>";
    $final .= td('<a href="'.$_SERVER["REQUEST_URI"].'&action=delete&type=score&id='.$score->id.'">Delete</a>');
    $final .= td(get_course_name_by_id($score->course_id));
    $final .= td($score->total_score);
    $final .= td(total_holes($score->front9,$score->back9));
    $final .= td($score->date_played);
    $final .= "</tr>";
  }
  return $final; 
}

function get_course_name_by_id($id){
  global $wpdb;

  $course = $wpdb->get_row("SELECT name FROM `".$wpdb->prefix."courses` WHERE id = $id");
  return $course->name;
}

function get_golf_course_by_id($id){
  global $wpdb;

  $course = $wpdb->get_row("SELECT * FROM `".$wpdb->prefix."courses` WHERE id = $id");
  return $course;
}

function total_holes($front, $back){
  if($front == $back)
    return 18;
  else
    return 9;
}

function get_the_total_score(){
  $front9 = (!isset($_POST['front9_score'])) ? 0 : $_POST['front9_score']; 
  $back9 = (!isset($_POST['back9_score'])) ? 0 : $_POST['back9_score'];
  return $front9 + $back9;
}

if(isset($_POST['submit_add_course']))
  add_the_golf_course();
if(isset($_POST['submit_add_score']))
  add_the_golf_score();
if(isset($_POST['submit_edit_course']))
  edit_a_golf_course();
if(isset($_GET['action'])){
  if($_GET['action'] == 'delete' && $_GET['type'] == 'score')
    delete_a_golf_score();
  if($_GET['action'] == 'edit' && $_GET['type'] == 'course')
    edit_a_golf_course_page();
}

function edit_a_golf_course_page(){
  $course = get_golf_course_by_id($_GET['id']);
  echo '<div class="wrap">';
  echo '<div id="icon-tools" class="icon32"><br /></div>';
  echo '<h2>Edit Course</h2>';
  echo '<form method="post" action="' . $_SERVER["REQUEST_URI"] . '">';
  echo '<table class="form-table">';
  echo golf_input_field('Course', 'name', $course->name);
  echo golf_input_field('City', 'city', $course->city);
  echo golf_input_field('State', 'state', $course->state);
  echo golf_input_field('Country', 'country', $course->country);
  echo golf_input_field('Front 9 Par', 'front9_par', $course->front9_par);
  echo golf_input_field('Back 9 par', 'back9_par', $course->back9_par);
  echo golf_input_field('Front 9 Yardage', 'front9_yard', $course->front9_yard);
  echo golf_input_field('Back 9 yardage', 'back9_yard', $course->back9_yard);
  echo golf_input_field('Rating', 'rating', $course->rating);
  echo golf_input_field('Slope', 'slope', $course->slope);
  echo golf_input_field('URL', 'url', $course->url);
  echo golf_edit_holes($course);
  echo '</table>';
  echo '<input type="submit" name="submit_edit_course" value="Edit Course" />';
  echo '</form>';
  echo '</div>';

}

function golf_edit_holes($course){
  $final = '';
  $final .= golf_input_field('Hole 1','hole_1', $course->hole_1);
  $final .= golf_input_field('Hole 2','hole_2', $course->hole_2);
  $final .= golf_input_field('Hole 3','hole_3', $course->hole_3);
  $final .= golf_input_field('Hole 4','hole_4', $course->hole_4);
  $final .= golf_input_field('Hole 5','hole_5', $course->hole_5);
  $final .= golf_input_field('Hole 6','hole_6', $course->hole_6);
  $final .= golf_input_field('Hole 7','hole_7', $course->hole_7);
  $final .= golf_input_field('Hole 8','hole_8', $course->hole_8);
  $final .= golf_input_field('Hole 9','hole_9', $course->hole_9);
  $final .= golf_input_field('Hole 10','hole_10', $course->hole_10);
  $final .= golf_input_field('Hole 11','hole_11', $course->hole_11);
  $final .= golf_input_field('Hole 12','hole_12', $course->hole_12);
  $final .= golf_input_field('Hole 13','hole_13', $course->hole_13);
  $final .= golf_input_field('Hole 14','hole_14', $course->hole_14);
  $final .= golf_input_field('Hole 15','hole_15', $course->hole_15);
  $final .= golf_input_field('Hole 16','hole_16', $course->hole_16);
  $final .= golf_input_field('Hole 17','hole_17', $course->hole_17);
  $final .= golf_input_field('Hole 18','hole_18', $course->hole_18);

  return $final;
}

function delete_a_golf_score(){
  global $wpdb;

  if(isset($_GET['id'])){
    $wpdb->query("DELETE FROM `".$wpdb->prefix."scores` WHERE id = '".$_GET['id']."'");
  }
}

function get_all_golf_scores_by_date($date){
  global $wpdb;
  $final = '<ul>';

  $scores = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."scores WHERE date_played = '".$date."'");
  foreach($scores as $score){
    $final .= individual_golf_score_html($score, get_golf_course_by_id($score->course_id));
  }
  
  $final .= '</ul>';
  return $final;
}

function individual_golf_score_html($score, $course){
  $final = '<div class="golfscore">';
  $final .= '<table>';
  $final .= '<tr><td class="golflocationheader" colspan="22">'.$course->name.' ('.$course->city.', '.$course->state.')</td></tr>';
  $final .= '<tr class="holerow">'.table_data_hole_1_through_18_for_score_table().'</tr>';
  $final .= '<tr class="courserow">'.course_data_hole_1_through_18_for_score_table($course).'</tr>';
  $final .= '<tr class="scorerow">'.score_data_hole_1_through_18_for_score_table($score).'</tr>';
  $final .= '</table>';
  $final .= '</div>';
  return $final;
}

function score_data_hole_1_through_18_for_score_table($score){
  $final = '<td class="scoreheader">Score</td>';  

  $final .= td($score->hole_1);
  $final .= td($score->hole_2);
  $final .= td($score->hole_3);
  $final .= td($score->hole_4);
  $final .= td($score->hole_5);
  $final .= td($score->hole_6);
  $final .= td($score->hole_7);
  $final .= td($score->hole_8);
  $final .= td($score->hole_9);
  $final .= td($score->front9_score);
  $final .= td($score->hole_10);
  $final .= td($score->hole_11);
  $final .= td($score->hole_12);
  $final .= td($score->hole_13);
  $final .= td($score->hole_14);
  $final .= td($score->hole_15);
  $final .= td($score->hole_16);
  $final .= td($score->hole_17);
  $final .= td($score->hole_18);
  $final .= td($score->back9_score);  
  $final .= td($score->total_score);

  return $final;
}

function course_data_hole_1_through_18_for_score_table($course){
  $final = '<td class="scoreheader">Par</td>';  

  $final .= td($course->hole_1);
  $final .= td($course->hole_2);
  $final .= td($course->hole_3);
  $final .= td($course->hole_4);
  $final .= td($course->hole_5);
  $final .= td($course->hole_6);
  $final .= td($course->hole_7);
  $final .= td($course->hole_8);
  $final .= td($course->hole_9);
  $final .= td($course->front9_par);
  $final .= td($course->hole_10);
  $final .= td($course->hole_11);
  $final .= td($course->hole_12);
  $final .= td($course->hole_13);
  $final .= td($course->hole_14);
  $final .= td($course->hole_15);
  $final .= td($course->hole_16);
  $final .= td($course->hole_17);
  $final .= td($course->hole_18);
  $final .= td($course->back9_par);  
  $final .= td($course->total_par);

  return $final;
}

function table_data_hole_1_through_18_for_score_table(){
  $final = '<td class="scoreheader">Hole</td>';
  for($i = 1; $i <= 18; $i++){
    $final .= td($i);
    if($i == 9)
      $final .= td("Out");
    else if($i == 18)
      $final .= td("In");
  }
  $final .= td("TOT");
  return $final;
}

function shortcode_round_by_date($atts, $content = null){
  extract(shortcode_atts(array(
    "date" => '1111-11-11'
  ), $atts));
  return get_all_golf_scores_by_date($date);
}

function all_scores_for_a_single_page($atts){
  global $wpdb;
  $final = '<ul>';

  $scores = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."scores");
  foreach($scores as $score){
    $final .= individual_golf_score_html($score, get_golf_course_by_id($score->course_id));
  }
  
  $final .= '</ul>';
  return $final;
}

add_shortcode('allscores', 'all_scores_for_a_single_page');
add_shortcode('score','shortcode_round_by_date'); 
add_shortcode('allcourses', 'all_golf_courses_for_a_single_page');

function all_golf_courses_for_a_single_page(){
  global $wpdb;
  $final = '';

  $courses = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."courses");
  $final .= "<table>";
  $final .= "<tr>".th('Course Name').th('Location').th('Par')."</tr>";
  foreach($courses as $course){
    $final .= individual_golf_course_html($course);
  }
  $final .= "</table>";
  return $final;
}

function individual_golf_course_html($course){
  $final = "<tr>";
  $final .= td($course->name);
  $final .= td("(".$course->city.", ".$course->state.")");
  $final .= td($course->total_par);
  $final .= "</tr>";

  return $final;
}

function add_my_stylesheet(){
  $myStyleUrl = WP_PLUGIN_URL . '/golf-tracker/style.css';
  $myStyleFile = WP_PLUGIN_DIR . '/golf-tracker/style.css';

  if( file_exists($myStyleFile) ){
    wp_register_style('golfTrackerStyleSheets', $myStyleUrl);
    wp_enqueue_style('golfTrackerStyleSheets');
  }
}

?>
