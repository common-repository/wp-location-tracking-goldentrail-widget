<?php
/*
  Plugin Name: WP-location-tracking
  Plugin URI:
  Description: A notification location tracking system GoldenTrail plugin. Allow you to quickly share location of yourself or people who you are tracking.
  Version: 1.0
  Author: Towards IT Technology Pte Ltd
  Author URI: http://www.towardstech.com/
 */
/*
  Copyright 2011  Towards IT Technology  (email : sales@towardstech.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License as published by
  the Free Software Foundation; either version 2 of the License, or
  (at your option) any later version.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program; if not, write to the Free Software
  Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 */

class wp_location_tracking extends WP_Widget {

    public $prefix_name = 'wp_location_tracking';

    function wp_location_tracking() {
        parent::WP_Widget(false, $name = __('GoldenTrail Location Tracking Widget', $this->prefix_name));
    }

    function update($new_instance, $old_instance) {
        $instance = $old_instance;
        $instance['title'] = strip_tags($new_instance['title']);
        $instance['width'] = strip_tags($new_instance['width']);
        $instance['height'] = strip_tags($new_instance['height']);

        $instance['email'] = strip_tags($new_instance['email']);
        $instance['password'] = strip_tags($new_instance['password']);
        $instance['selectmid'] = strip_tags($new_instance["selectmid"]);

        if ($instance['selectmid'] === "" || !isset($instance['selectmid']) || $instance['selectmid'] == null || $instance['selectmid'] == "") {
            if ($instance['email'] !== "" && $instance['password'] !== "") {
                $json = $this->gm($instance['email'], $instance['password']);
                $instance['members'] = $json["members"];
                $instance['uid'] = $json["uid"];
                $instance['isLogin'] = true;
                $instance['width'] = "220px";
                $instance['height'] = "220px";
            }
        } else if ($instance['isLogin'] === true) {
            $instance['memberSelected'] = true;
            $instance['isLogin'] = true;
        }

        return $instance;
    }

    function gm($email, $password) {
        $content = file_get_contents('http://goldentrail.towardstech.com/jsoncontroller/wp_getDetails?email=' . $email . '&password=' . $password);
        return json_decode($content, true);
    }

    function widget($args, $instance) {
        extract($args);
        $height = $instance['height'];
        $width = $instance['width'];
        $isLogin = $instance['isLogin'];
        $memberSelected = $instance['memberSelected'];
        echo $before_widget;
        if ($isLogin && $memberSelected) {

            $uid = $instance['uid'];
            $mid = $instance['selectmid'];

            echo '<div class="widget-text wp_widget_plugin_box">';
            echo '<iframe id="GoldenTrail_' . $mid . '_' . $uid . '" style="width:' . $width . ';height:' . $height . ';" src="http://goldentrail.towardstech.com//xtrack/' . $uid . '/' . $mid . '" frameborder="0"> </iframe> ';
            echo '</div>';
        } else {
            echo "Please login and activate GoldenTrail Location Tracking.";
        }
        echo $after_widget;
    }

    function form($instance) {

        if ($instance) {
            $isLogin = $instance['isLogin'];
            $email = esc_attr($instance['email']);
            $password = esc_attr($instance['[password']);


            $height = esc_attr($instance['height']);
            $width = esc_attr($instance['width']);
            $selectmid = esc_attr($instance['selectmid']);
            $members = $instance['members'];
        } else {
            $isLogin = null;
            $email = '';
            $password = '';

            $height = '200px';
            $width = '200px';
            $members = array();
            $selectmid = "";
        }
        ?>
        <?php if ($isLogin) { ?>
            <h2>
                Select user and save!
            </h2>
            <p>
                <label for="<?php echo $this->get_field_id('height'); ?>"><?php _e('GoldenTrail Height', $this->prefix_name); ?></label>
                <input class="widefat" id="<?php echo $this->get_field_id('height'); ?>" name="<?php echo $this->get_field_name('height'); ?>" type="text" value="<?php echo $height; ?>" />
            </p>
            <p>
                <label for="<?php echo $this->get_field_id('width'); ?>"><?php _e('GoldenTrail Width', $this->prefix_name); ?></label>
                <input class="widefat" id="<?php echo $this->get_field_id('width'); ?>" name="<?php echo $this->get_field_name('width'); ?>" type="text" value="<?php echo $width; ?>" />
            </p>
            <p>
                <label for="<?php echo $this->get_field_id('width'); ?>"><?php _e('GoldenTrail Your Users', $this->prefix_name); ?></label>
                <select class="widefat" id="<?php echo $this->get_field_id('selectmid'); ?>" name="<?php echo $this->get_field_name('selectmid'); ?>">
                    <?php
                    for ($i = 0, $len = count($members); $i < $len; ++$i) {
                        $member = $members[$i];
                        ?>
                        <option value="<?php echo $member["id"]; ?>" <?php echo ($selectmid == $member["id"]) ? "selected=\"selected\"" : ""; ?>><?php echo $member["Name"]; ?></option>
                    <?php } ?>
                </select>
            </p>
            <?php
        } else {
            ?>
            <p>
                <label for="">Don't have a GoldenTrail Account?</label>
                <a href="http://goldentrail.towardstech.com/signup">Register here!</a>
            </p>
            <p>
                <label for="<?php echo $this->get_field_id('email'); ?>"><?php _e('GoldenTrail Email', $this->prefix_name); ?></label>
                <input class="widefat" id="<?php echo $this->get_field_id('email'); ?>" name="<?php echo $this->get_field_name('email'); ?>" type="text" value="<?php echo $email; ?>" />
            </p>
            <p>
                <label for="<?php echo $this->get_field_id('password'); ?>"><?php _e('GoldenTrail Password', $this->prefix_name); ?></label>
                <input class="widefat" id="<?php echo $this->get_field_id('password'); ?>" name="<?php echo $this->get_field_name('password'); ?>" type="password" value="<?php echo $password; ?>" />
            </p>


            <?php
        }
    }

}

// register widget
add_action('widgets_init', create_function('', 'return register_widget("wp_location_tracking");'));
?>