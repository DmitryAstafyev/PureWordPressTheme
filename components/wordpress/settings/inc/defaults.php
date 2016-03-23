<?php
namespace Pure\Components\WordPress\Settings{
    class Defaults{
        public function get(){
            return (object)array(
                'basic'             =>(object)array('id'        =>'PureThemeBasicSettings',
                                                    'properties'=>(object)array('header_menu_template'  =>(object)array('value'     =>'B'),
                                                                                'error_page_template'   =>(object)array('value'     =>'A'),
                                                                                'console_access'        =>(object)array('value'     =>'no'),//'yes' || 'no'
                                                    )),
                'buddypress'        =>(object)array('id'        =>'PureThemeBuddyPressSettings',
                                                    'properties'=>(object)array('header_template'       =>(object)array('value'     =>'A'   ),
                                                                                'activity_template'     =>(object)array('value'     =>'A'   ),
                                                                                'records_on_page'       =>(object)array('value'     =>20    ),
                                                                                'background'            =>(object)array('value'     =>'A'   ),
                                                                                'Backgrounds'           =>false,)),
                /*
                 * If server do not update his checkpoint each [heartbeat_interations] or less,
                 * in this case such server considered turned off. In this case new server will be
                 * launched.
                 */
                'webSocketServer'   =>(object)array('id'        =>'PureThemeWebSocketServerSettings',
                                                    'properties'=>(object)array('address'                           =>(object)array('value' =>'127.0.0.1'   ),
                                                                                'port'                              =>(object)array('value' =>'5001'        ),
                                                                                'backlog'                           =>(object)array('value' =>20            ),
                                                                                'logs'                              =>(object)array('value' =>'off'         ),//'on' || 'off'
                                                                                'logs_as_comment'                   =>(object)array('value' =>'off'         ),//'on' || 'off'
                                                                                'heartbeat_timeout'                 =>(object)array('value' =>60            ),
                                                                                'heartbeat_interations'             =>(object)array('value' =>1000          ),
                                                                                'php_debug'                         =>(object)array('value' =>'off'         ),//'on' || 'off'
                                                                                'start_mode'                        =>(object)array('value' =>'off'         ),//'auto' || 'manually' || 'launcher' || 'off'
                                                                                'show_memoryusage_with_heartbeat'   =>(object)array('value' =>'off'         ))),//'on' || 'off'
                'messenger'         =>(object)array('id'        =>'PureThemeMessengerSettings',
                                                    'properties'=>(object)array('template'                  =>(object)array('value' =>'A'               ),
                                                                                'mails_max_count'           =>(object)array('value' =>5                 ),
                                                                                'mail_max_size'             =>(object)array('value' =>5000              ),
                                                                                'mail_subject_max_size'     =>(object)array('value' =>255               ),
                                                                                'allow_attachment_in_mail'  =>(object)array('value' =>'on'              ),//'on' || 'off'
                                                                                'attachment_max_size'       =>(object)array('value' =>8388608           ),//bytes
                                                                                'attachment_max_count'      =>(object)array('value' =>5                 ),
                                                                                'chats_max_count_messages'  =>(object)array('value' =>10                ),
                                                                                'chat_message_max_size'     =>(object)array('value' =>1000              ),
                                                                                'chat_allow_memes'          =>(object)array('value' =>'yes'             ),//'yes' || 'no'
                                                                                'chat_attachment_max_size'  =>(object)array('value' =>8388608           ),//bytes
                                                                                'chat_memes_folder'         =>(object)array('value' =>'messenger-memes' ),//'in uploads'
                                                                                'notifications_max_count'   =>(object)array('value' =>10                ),
                                                    )),
                'mana'              =>(object)array('id'        =>'PureThemeManaSettings',
                                                    'properties'=>(object)array('mana_using'                                =>(object)array('value' =>'on'              ),//'on' || 'off'
                                                                                'mana_name'                                 =>(object)array('value' =>'mana'            ),//displayed name 'mana', 'rate', 'rating' and etc.
                                                                                'mana_maximum_gift'                         =>(object)array('value' =>100               ),
                                                                                'mana_threshold_create_post'                =>(object)array('value' =>0                 ),//-1 => off === not control
                                                                                'mana_threshold_create_event'               =>(object)array('value' =>100               ),//-1 => off === not control
                                                                                'mana_threshold_create_report'              =>(object)array('value' =>100               ),//-1 => off === not control
                                                                                'mana_threshold_create_question'            =>(object)array('value' =>100               ),//-1 => off === not control
                                                                                'mana_threshold_create_comment'             =>(object)array('value' =>100               ),//-1 => off === not control
                                                                                'mana_threshold_create_activity'            =>(object)array('value' =>50                ),//-1 => off === not control
                                                                                'mana_threshold_do_activity_remove'         =>(object)array('value' =>50                ),//-1 => off === not control
                                                                                'mana_threshold_do_comment_remove'          =>(object)array('value' =>50                ),//-1 => off === not control
                                                                                'mana_threshold_vote_comment'               =>(object)array('value' =>50                ),//-1 => off === not control
                                                                                'mana_threshold_vote_post'                  =>(object)array('value' =>50                ),//-1 => off === not control
                                                                                'mana_threshold_manage_categories'          =>(object)array('value' =>100               ),//-1 => off === not control
                                                                                'mana_threshold_manage_categories_sandbox'  =>(object)array('value' =>-1                ),//ID of sandbox category
                                                                                'mana_threshold_manage_comments'            =>(object)array('value' =>100               ),//-1 => off === not control
                                                                                'mana_threshold_manage_vote'                =>(object)array('value' =>10000             ),//-1 => off === not control
                                                                                'mana_icon_template'                        =>(object)array('value' =>'A'               ),
                                                                                //Here (with stars) I should think how to do it more flexible
                                                                                //For the future
                                                                                /*
                                                                                'stars_using'                       =>(object)array('value' =>'on'              ),//'on' || 'off'
                                                                                'star_name'                         =>(object)array('value' =>'star'            ),//displayed name 'star', 'reward', 'premium' and etc.
                                                                                'stars_for_only_plus_post'          =>(object)array('value' =>1                 ),//-1 => off === not control
                                                                                'stars_for_only_plus_post_param'    =>(object)array('value' =>1                 ),//in days
                                                                                'stars_for_each_count_of_plus'      =>(object)array('value' =>1                 ),//-1 => off === not control
                                                                                'stars_for_each_count_of_plus_param'=>(object)array('value' =>500               ),//count of pluses
                                                                                */
                                                    )),
                'comments'          =>(object)array('id'        =>'PureThemeCommentsSettings',
                                                    'properties'=>(object)array('allow_attachment'  =>(object)array('value' =>'on'              ),//'on' || 'off'
                                                                                'allow_memes'       =>(object)array('value' =>'on'              ),//'on' || 'off'
                                                                                'memes_folder'      =>(object)array('value' =>'comments-memes'  ),//'in uploads'
                                                                                'max_length'        =>(object)array('value' =>5000              ),//symbols
                                                                                'show_on_page'      =>(object)array('value' =>10                ),//count of root comments
                                                                                'hot_update'        =>(object)array('value' =>'on'              ),//'on' || 'off' //turn ON or OFF webSocket support for online updating
                                                    )),
                'activities'        =>(object)array('id'        =>'PureThemeActivitiesSettings',
                                                    'properties'=>(object)array('allow_attachment'          =>(object)array('value' =>'on'                  ),//'on' || 'off'
                                                                                'allow_memes'               =>(object)array('value' =>'on'                  ),//'on' || 'off'
                                                                                'memes_folder'              =>(object)array('value' =>'activities-memes'    ),//'in uploads'
                                                                                'max_length'                =>(object)array('value' =>5000                  ),//symbols
                                                                                'show_on_page'              =>(object)array('value' =>10                    ),//count of root comments
                                                                                'allow_remove_comments'     =>(object)array('value' =>'yes'                 ),//'yes' || 'no' //owner of activities can or not remove any comments UNDER activity
                                                                                'allow_remove_activities'   =>(object)array('value' =>'yes'                 ),//'yes' || 'no' //owner of activities can or not remove any his activity
                                                                                'hot_update'                =>(object)array('value' =>'on'                  ),//'on' || 'off' //turn ON or OFF webSocket support for online updating
                                                    )),
                'googlemaps'        =>(object)array('id'        =>'PureThemeGoogleMapsSettings',
                                                    'properties'=>(object)array('script_url'        =>(object)array('value' =>'https://maps.googleapis.com/maps/api/js?key='    ),
                                                                                'access_key'        =>(object)array('value' =>''                                                ),
                                                                                'client_id'         =>(object)array('value' =>''                                                ),
                                                    )),
                'mailer'            =>(object)array('id'        =>'PureThemeMailerSettings',
                                                    'properties'=>(object)array('host'          =>(object)array('value' =>''    ),
                                                                                'port'          =>(object)array('value' =>587   ),
                                                                                'username'      =>(object)array('value' =>''    ),
                                                                                'password'      =>(object)array('value' =>''    ),
                                                                                'SMTPAuth'      =>(object)array('value' =>'on'  ),
                                                                                'SMTPSecure'    =>(object)array('value' =>'tls' ),
                                                    )),
                'front_page'        =>(object)array('id'        =>'PureThemeFrontPageSettings',
                                                    'properties'=>(object)array('template'      =>(object)array('value' =>'City'                                            ),
                                                                                'label'         =>(object)array('value' =>'Copyright &#169;, 2015. All right are reserved'  ),
                                                    )),
                'counts'            =>(object)array('id'        =>'PureThemeCountsSettings',
                                                    'properties'=>(object)array('posts'                     =>(object)array('value' =>10    ),
                                                                                'items_on_sidebars'         =>(object)array('value' =>5     ),
                                                                                'groups_on_groups_page'     =>(object)array('value' =>20    ),
                                                                                'members_on_members_page'   =>(object)array('value' =>20    ),
                                                                                'groups_on_member_page'     =>(object)array('value' =>10    ),
                                                                                'members_on_member_page'    =>(object)array('value' =>10    ),
                                                    )),
                'images'            =>(object)array('id'        =>'PureThemeImagesSettings',
                                                    'properties'=>(object)array('background'                =>(object)array('value' =>''    ),
                                                                                'logo_dark'                 =>(object)array('value' =>''    ),
                                                                                'logo_light'                =>(object)array('value' =>''    ),
                                                    )),
                'footer'            =>(object)array('id'        =>'PureThemeFooterSettings',
                                                    'properties'=>(object)array('after'                     =>(object)array('value' =>'Copyright &#169; 2015 Pure Theme. All Rights Reserved. Powered by WordPress'    ),
                                                    )),
                'information'       =>(object)array('id'        =>'PureThemeInformationSettings',
                                                    'properties'=>(object)array('facebook'                  =>(object)array('value' =>'#'                   ),
                                                                                'google'                    =>(object)array('value' =>'#'                   ),
                                                                                'linkin'                    =>(object)array('value' =>'#'                   ),
                                                                                'twitter'                   =>(object)array('value' =>'#'                   ),
                                                                                'phone'                     =>(object)array('value' =>'+1 400 310 00'       ),
                                                                                'mail'                      =>(object)array('value' =>'support@site.it'     ),
                                                    )),
                'attachments'       =>(object)array('id'        =>'PureThemeAttachmentsSettings',
                                                    'properties'=>(object)array('max_size_attachment'       =>(object)array('value' =>102400                ),//in bytes
                                                                                'max_count_per_object'      =>(object)array('value' =>10                    ),
                                                                                'max_count_per_month'       =>(object)array('value' =>100                   ),
                                                    )),
                'reports'           =>(object)array('id'        =>'PureThemeReportsSettings',
                                                    'properties'=>(object)array('collections'               =>(object)array('value' =>'YToyOntpOjA7Tzo4OiJzdGRDbGFzcyI6Mjp7czo0OiJuYW1lIjtzOjI0OiJSZXN0YXVyYW50cywgY2FmZXMsIGZvb2QiO3M6NzoiaW5kZXhlcyI7YTo0OntpOjA7Tzo4OiJzdGRDbGFzcyI6Mjp7czo0OiJuYW1lIjtzOjQ6IkZvb2QiO3M6MzoibWF4IjtpOjEwO31pOjE7Tzo4OiJzdGRDbGFzcyI6Mjp7czo0OiJuYW1lIjtzOjc6IlNlcnZpY2UiO3M6MzoibWF4IjtpOjEwO31pOjI7Tzo4OiJzdGRDbGFzcyI6Mjp7czo0OiJuYW1lIjtzOjEwOiJBdG1vc3BoZXJlIjtzOjM6Im1heCI7aToxMDt9aTozO086ODoic3RkQ2xhc3MiOjI6e3M6NDoibmFtZSI7czo4OiJMb2NhdGlvbiI7czozOiJtYXgiO2k6MTA7fX19aToxO086ODoic3RkQ2xhc3MiOjI6e3M6NDoibmFtZSI7czo1OiJNb3ZpZSI7czo3OiJpbmRleGVzIjthOjQ6e2k6MDtPOjg6InN0ZENsYXNzIjoyOntzOjQ6Im5hbWUiO3M6MTA6IlNjcmVlbnBsYXkiO3M6MzoibWF4IjtpOjEwO31pOjE7Tzo4OiJzdGRDbGFzcyI6Mjp7czo0OiJuYW1lIjtzOjk6IkRpcmVjdGlvbiI7czozOiJtYXgiO2k6MTA7fWk6MjtPOjg6InN0ZENsYXNzIjoyOntzOjQ6Im5hbWUiO3M6MTM6IkVudGVydGFpbm1lbnQiO3M6MzoibWF4IjtpOjEwO31pOjM7Tzo4OiJzdGRDbGFzcyI6Mjp7czo0OiJuYW1lIjtzOjY6IkFjdG9ycyI7czozOiJtYXgiO2k6MTA7fX19fQ==' ),
                                                    )),
            );
        }
    }
}
?>