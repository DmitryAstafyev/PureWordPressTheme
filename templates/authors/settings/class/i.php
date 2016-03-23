<?php
namespace Pure\Templates\Authors\Settings{
    class I extends \Pure\Templates\Settings implements \Pure\Templates\iSettings{
        private function validate(&$settings){
            $this->basic_validate($settings);
            $content = array('posts', 'groups', 'friends', 'all');
            $settings['addition_information'] = (isset($settings['addition_information']) === true ? $settings['addition_information'] : $content[0] );
            $settings['addition_information'] = (in_array($settings['addition_information'], $content) === true ? $settings['addition_information'] : $content[0] );
        }
        public function show($settings = NULL, $wrap_group = true, $widget = false){
            $this->validate($settings);
            if ($wrap_group === true){
                $groups = \Pure\Templates\Admin\Groups\Initialization::instance()->get('B');
            }
?>
            <?php
            if ($wrap_group === true){
                $groups->open(array(    "title"             =>"Settings of template I",
                                        "group"             =>"Settings_of_template_I",
                                        "echo"              =>true,
                                        "content_style"     =>'padding:1em 0em 1em 0;'));
            }
            ?>
            <p style="padding: 0.5em 1em 0.5em 1em;">This template supports two types of content. As default user see summary data, if user clicks on avatar, template will show addition information. So, you can choose what will be shown as addition information. </p>
            <select class="widefat" id="<?php echo $this->field('addition_information', $widget); ?>" name="<?php echo $this->field('addition_information', $widget); ?>" style="width: 90%;left:5%;position: relative;">
                <option value="all"     <?php selected( $settings['addition_information'], 'all'        ); ?>>Show all</option>
                <option value="posts"   <?php selected( $settings['addition_information'], 'posts'      ); ?>>Last posts of user</option>
                <option value="groups"  <?php selected( $settings['addition_information'], 'groups'     ); ?>>Groups where user in</option>
                <option value="friends" <?php selected( $settings['addition_information'], 'friends'    ); ?>>Friends of user</option>
            </select>
            <?php
            if ($wrap_group === true){
                $groups->close(array("echo"=>true));
            }
            ?>
<?php
        }
        public function initialize(){
        }
    }
}
?>