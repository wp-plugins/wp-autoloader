<?php
namespace WPExtend;

use WPAutoloader\AutoLoad;

/**
 * Autoloder for WordPress Action Hooks.
 * Supports almost 500 WordPress action hooks at the moment.
 * Instead of calling for each method an add_action method, all class methods can be hooked automatically.
 * For example, to hook method to action activated_plugin, define plublis static method for your class named OnActionActivatedPlugin.
 * Now call inside of you class constructor \WPExtend\BasicWPActions::HookAllClassActionMethods( __CLASS__ );
 * and all appropriate methods are automatically hooked.
 * 
 * @author Dave A. Holyfield
 * @since 2.0.7
 * @version 2.0.8
 */
final class BasicWPActions {

    /**
     * Disable constructor
     */
    private function __construct () {
        ;
    }

    public static function GetActionsList () {
        return self::$_actions;
    }

    /**
     *
     * @param string $class            
     * @return multitype:
     * @since 2.0.7
     */
    public static function GetStaticMethods ( $class = null ) {
        $class = (is_null( $class )) ? get_called_class() : $class;
        if ( ! class_exists( $class ) ) {
            return array ();
        }
        $reflection = new \ReflectionClass( $class );
        $methods = $reflection->getMethods( \ReflectionMethod::IS_STATIC );
        foreach ( $methods as $key => $item ) {
            if ( $item->class == $class ) {
                $methods[$key] = $item->name;
            } else {
                unset( $methods[$key] );
            }
        }
        return $methods;
    }

    /**
     *
     * @param array $actions            
     * @return array
     * @since 2.0.7
     */
    public static function GetActionDefinitions ( $actions = null ) {
        static $default;
        if ( is_null( $actions ) ) {
            if ( ! is_array( $default ) ) {
                $default = array ();
                foreach ( self::$_actions as $key => $act ) {
                    $default[$key] = array ( 
                        'method' => $act,
                        'priority' => 10,
                        'args' => (array_key_exists( $key, self::$_actions_args )) ? self::$_actions_args[$key] : 1
                    );
                }
            }
            return $default;
        } elseif ( is_array( $actions ) && count( $actions ) > 0 ) {
            foreach ( $actions as $key => $act ) {
                $actions[$key] = array ( 
                    'method' => $act,
                    'priority' => 10,
                    'args' => (array_key_exists( $key, self::$_actions_args )) ? self::$_actions_args[$key] : 1
                );
            }
            return $actions;
        }
        return array ();
    }

    /**
     *
     * @param string $class            
     * @since 2.0.7
     */
    public static function GetClassActionMethods ( $class ) {
        $actions = self::GetActionsList();
        $statics = self::GetStaticMethods( $class );
        $methods = array_intersect( $actions, $statics );
        return self::GetActionDefinitions( $methods );
    }

    public static function HookAllClassActionMethods ( $class, $priorites = null ) {
        // If class does not exists, try to load this class
        if ( ! class_exists( $class ) ) {
            AutoLoad::LoadClass( $class );
        }
        // Hook class methods
        if ( class_exists( $class ) ) {
            $methods = self::GetClassActionMethods( $class );
            if ( is_array( $priorites ) && count( $priorites ) > 0 ) {
                foreach ( $priorites as $id => $priority ) {
                    if ( array_key_exists( $id, $methods ) ) {
                        $methods[$id]['priority'] = $priority;
                    }
                }
            }
            foreach ( $methods as $key => $actioninfo ) {
                add_action( $key, $class . '::' . $actioninfo['method'], $actioninfo['priority'], $actioninfo['args'] );
            }
        }
    }

    public static function GetHtmlTable () {
        $html[] = '<table width="100%" border="0" cellspacing="0" cellpadding="5">';
        $html[] = '<tr><th scope="col">Action</th><th scope="col">Method Name</th></tr>';
        foreach ( self::$_actions as $key => $value ) {
            $html[] = '<tr><td><a href="https://developer.wordpress.org/reference/hooks/' . $key . '" target="_blank" style="text-decoration: none;">' . $key . '</a></td><td>' . $value . '</td></tr>';
        }
        $html[] = '</table>';
        return implode( PHP_EOL, $html );
    }

    public static function GetCount () {
        return count( self::$_actions );
    }

    private static $_actions_args = array ( 
        'activate_plugin' => 4
    );

    /**
     * All supported action hooks.
     * 
     * @version 4.1
     * @var array
     */
    private static $_actions = array ( 
        'activate_blog' => 'OnActionActivateBlog',
        'activate_header' => 'OnActionActivateHeader',
        'activate_plugin' => 'OnActionActivatePlugin',
        'activate_wp_head' => 'OnActionActivateWPHead',
        'activated_plugin' => 'OnActionActivatedPlugin',
        'activity_box_end' => 'OnActionActivityBoxEnd',
        'add_admin_bar_menus' => 'OnActionAddAdminBarMenus',
        'add_attachment' => 'OnActionAddAttachment',
        'add_category_form_pre' => 'OnActionAddCategoryFormPre',
        'add_link' => 'OnActionAddLink',
        'add_link_category_form_pre' => 'OnActionAddLinkCategoryFormPre',
        'add_meta_boxes' => 'OnActionAddMetaBoxes',
        'add_meta_boxes_comment' => 'OnActionAddMetaBoxesComment',
        'add_meta_boxes_link' => 'OnActionAddMetaBoxesLink',
        'add_option' => 'OnActionAddOption',
        'add_site_option' => 'OnActionAddSiteOption',
        'add_tag_form' => 'OnActionAddTagForm',
        'add_tag_form_fields' => 'OnActionAddTagFormFields',
        'add_tag_form_pre' => 'OnActionAddTagFormPre',
        'add_term_relationship' => 'OnActionAddTermRelationship',
        'add_user_to_blog' => 'OnActionAddUserToBlog',
        'added_existing_user' => 'OnActionAddedExistingUser',
        'added_option' => 'OnActionAddedOption',
        'added_term_relationship' => 'OnActionAddedTermRelationship',
        'added_usermeta' => 'OnActionAddedUsermeta',
        'admin_bar_init' => 'OnActionAdminBarInit',
        'admin_bar_menu' => 'OnActionAdminBarMenu',
        'admin_color_scheme_picker' => 'OnActionAdminColorSchemePicker',
        'admin_enqueue_scripts' => 'OnActionAdminEnqueueScripts',
        'admin_footer' => 'OnActionAdminFooter',
        'admin_footer-widgets-php' => 'OnActionAdminFooterWidgetsPhp',
        'admin_head' => 'OnActionAdminHead',
        'admin_head-media-upload-popup' => 'OnActionAdminHeadMediaUploadPopup',
        'admin_head-press-this-php' => 'OnActionAdminHeadPressThisPhp',
        'admin_init' => 'OnActionAdminInit',
        'admin_menu' => 'OnActionAdminMenu',
        'admin_notices' => 'OnActionAdminNotices',
        'admin_page_access_denied' => 'OnActionAdminPageAccessDenied',
        'admin_print_footer_scripts' => 'OnActionAdminPrintFooterScripts',
        'admin_print_scripts' => 'OnActionAdminPrintScripts',
        'admin_print_scripts-media-upload-popup' => 'OnActionAdminPrintScriptsMediaUploadPopup',
        'admin_print_scripts-press-this-php' => 'OnActionAdminPrintScriptsPressThisPhp',
        'admin_print_scripts-widgets-php' => 'OnActionAdminPrintScriptsWidgetsPhp',
        'admin_print_styles' => 'OnActionAdminPrintStyles',
        'admin_print_styles-media-upload-popup' => 'OnActionAdminPrintStylesMediaUploadPopup',
        'admin_print_styles-press-this-php' => 'OnActionAdminPrintStylesPressThisPhp',
        'admin_print_styles-widgets-php' => 'OnActionAdminPrintStylesWidgetsPhp',
        'admin_xml_ns' => 'OnActionAdminXmlNs',
        'adminmenu' => 'OnActionAdminmenu',
        'after_db_upgrade' => 'OnActionAfterDbUpgrade',
        'after_delete_post' => 'OnActionAfterDeletePost',
        'after_menu_locations_table' => 'OnActionAfterMenuLocationsTable',
        'after_mu_upgrade' => 'OnActionAfterMuUpgrade',
        'after_plugin_row' => 'OnActionAfterPluginRow',
        'after_setup_theme' => 'OnActionAfterSetupTheme',
        'after_signup_form' => 'OnActionAfterSignupForm',
        'after_switch_theme' => 'OnActionAfterSwitchTheme',
        'after_theme_row' => 'OnActionAfterThemeRow',
        'after_wp_tiny_mce' => 'OnActionAfterWPTinyMce',
        'akismet_comment_check_response' => 'OnActionAkismetCommentCheckResponse',
        'akismet_spam_caught' => 'OnActionAkismetSpamCaught',
        'akismet_submit_nonspam_comment' => 'OnActionAkismetSubmitNonspamComment',
        'akismet_submit_spam_comment' => 'OnActionAkismetSubmitSpamComment',
        'all_admin_notices' => 'OnActionAllAdminNotices',
        'archive_blog' => 'OnActionArchiveBlog',
        'atom_author' => 'OnActionAtomAuthor',
        'atom_comments_ns' => 'OnActionAtomCommentsNs',
        'atom_entry' => 'OnActionAtomEntry',
        'atom_head' => 'OnActionAtomHead',
        'atom_ns' => 'OnActionAtomNs',
        'attachment_submitbox_misc_actions' => 'OnActionAttachmentSubmitboxMiscActions',
        'auth_cookie_bad_hash' => 'OnActionAuthCookieBadHash',
        'auth_cookie_bad_username' => 'OnActionAuthCookieBadUsername',
        'auth_cookie_expired' => 'OnActionAuthCookieExpired',
        'auth_cookie_malformed' => 'OnActionAuthCookieMalformed',
        'auth_cookie_valid' => 'OnActionAuthCookieValid',
        'auth_redirect' => 'OnActionAuthRedirect',
        'automatic_updates_complete' => 'OnActionAutomaticUpdatesComplete',
        'before_delete_post' => 'OnActionBeforeDeletePost',
        'before_signup_form' => 'OnActionBeforeSignupForm',
        'before_wp_tiny_mce' => 'OnActionBeforeWPTinyMce',
        'begin_fetch_post_thumbnail_html' => 'OnActionBeginFetchPostThumbnailHtml',
        'blog_privacy_selector' => 'OnActionBlogPrivacySelector',
        'bulk_edit_custom_box' => 'OnActionBulkEditCustomBox',
        'check_admin_referer' => 'OnActionCheckAdminReferer',
        'check_ajax_referer' => 'OnActionCheckAjaxReferer',
        'check_comment_flood' => 'OnActionCheckCommentFlood',
        'check_passwords' => 'OnActionCheckPasswords',
        'clean_attachment_cache' => 'OnActionCleanAttachmentCache',
        'clean_object_term_cache' => 'OnActionCleanObjectTermCache',
        'clean_page_cache' => 'OnActionCleanPageCache',
        'clean_post_cache' => 'OnActionCleanPostCache',
        'clean_term_cache' => 'OnActionCleanTermCache',
        'clear_auth_cookie' => 'OnActionClearAuthCookie',
        'comment_add_author_url' => 'OnActionCommentAddAuthorUrl',
        'comment_atom_entry' => 'OnActionCommentAtomEntry',
        'comment_closed' => 'OnActionCommentClosed',
        'comment_duplicate_trigger' => 'OnActionCommentDuplicateTrigger',
        'comment_flood_trigger' => 'OnActionCommentFloodTrigger',
        'comment_form' => 'OnActionCommentForm',
        'comment_form_after' => 'OnActionCommentFormAfter',
        'comment_form_after_fields' => 'OnActionCommentFormAfterFields',
        'comment_form_before' => 'OnActionCommentFormBefore',
        'comment_form_before_fields' => 'OnActionCommentFormBeforeFields',
        'comment_form_comments_closed' => 'OnActionCommentFormCommentsClosed',
        'comment_form_logged_in_after' => 'OnActionCommentFormLoggedInAfter',
        'comment_form_must_log_in_after' => 'OnActionCommentFormMustLogInAfter',
        'comment_form_top' => 'OnActionCommentFormTop',
        'comment_id_not_found' => 'OnActionCommentIdNotFound',
        'comment_loop_start' => 'OnActionCommentLoopStart',
        'comment_on_draft' => 'OnActionCommentOnDraft',
        'comment_on_password_protected' => 'OnActionCommentOnPasswordProtected',
        'comment_on_trash' => 'OnActionCommentOnTrash',
        'comment_post' => 'OnActionCommentPost',
        'comment_remove_author_url' => 'OnActionCommentRemoveAuthorUrl',
        'commentrss2_item' => 'OnActionCommentrss2Item',
        'comments_atom_head' => 'OnActionCommentsAtomHead',
        'commentsrss2_head' => 'OnActionCommentsrss2Head',
        'core_upgrade_preamble' => 'OnActionCoreUpgradePreamble',
        'create_term' => 'OnActionCreateTerm',
        'created_term' => 'OnActionCreatedTerm',
        'custom_header_options' => 'OnActionCustomHeaderOptions',
        'customize_controls_enqueue_scripts' => 'OnActionCustomizeControlsEnqueueScripts',
        'customize_controls_init' => 'OnActionCustomizeControlsInit',
        'customize_controls_print_footer_scripts' => 'OnActionCustomizeControlsPrintFooterScripts',
        'customize_controls_print_scripts' => 'OnActionCustomizeControlsPrintScripts',
        'customize_controls_print_styles' => 'OnActionCustomizeControlsPrintStyles',
        'customize_preview_init' => 'OnActionCustomizePreviewInit',
        'customize_register' => 'OnActionCustomizeRegister',
        'customize_render_control' => 'OnActionCustomizeRenderControl',
        'customize_render_panel' => 'OnActionCustomizeRenderPanel',
        'customize_render_section' => 'OnActionCustomizeRenderSection',
        'customize_save' => 'OnActionCustomizeSave',
        'customize_save_after' => 'OnActionCustomizeSaveAfter',
        'dbx_post_advanced' => 'OnActionDbxPostAdvanced',
        'dbx_post_sidebar' => 'OnActionDbxPostSidebar',
        'deactivate_blog' => 'OnActionDeactivateBlog',
        'deactivate_plugin' => 'OnActionDeactivatePlugin',
        'deactivated_plugin' => 'OnActionDeactivatedPlugin',
        'delete_attachment' => 'OnActionDeleteAttachment',
        'delete_blog' => 'OnActionDeleteBlog',
        'delete_comment' => 'OnActionDeleteComment',
        'delete_link' => 'OnActionDeleteLink',
        'delete_option' => 'OnActionDeleteOption',
        'delete_post' => 'OnActionDeletePost',
        'delete_postmeta' => 'OnActionDeletePostmeta',
        'delete_site_option' => 'OnActionDeleteSiteOption',
        'delete_term' => 'OnActionDeleteTerm',
        'delete_term_relationships' => 'OnActionDeleteTermRelationships',
        'delete_term_taxonomy' => 'OnActionDeleteTermTaxonomy',
        'delete_user' => 'OnActionDeleteUser',
        'delete_user_form' => 'OnActionDeleteUserForm',
        'delete_usermeta' => 'OnActionDeleteUsermeta',
        'deleted_comment' => 'OnActionDeletedComment',
        'deleted_link' => 'OnActionDeletedLink',
        'deleted_option' => 'OnActionDeletedOption',
        'deleted_post' => 'OnActionDeletedPost',
        'deleted_postmeta' => 'OnActionDeletedPostmeta',
        'deleted_site_transient' => 'OnActionDeletedSiteTransient',
        'deleted_term_relationships' => 'OnActionDeletedTermRelationships',
        'deleted_term_taxonomy' => 'OnActionDeletedTermTaxonomy',
        'deleted_transient' => 'OnActionDeletedTransient',
        'deleted_user' => 'OnActionDeletedUser',
        'deleted_usermeta' => 'OnActionDeletedUsermeta',
        'deprecated_argument_run' => 'OnActionDeprecatedArgumentRun',
        'deprecated_file_included' => 'OnActionDeprecatedFileIncluded',
        'deprecated_function_run' => 'OnActionDeprecatedFunctionRun',
        'do_meta_boxes' => 'OnActionDoMetaBoxes',
        'do_robots' => 'OnActionDoRobots',
        'do_robotstxt' => 'OnActionDoRobotstxt',
        'doing_it_wrong_run' => 'OnActionDoingItWrongRun',
        'dynamic_sidebar' => 'OnActionDynamicSidebar',
        'dynamic_sidebar_after' => 'OnActionDynamicSidebarAfter',
        'dynamic_sidebar_before' => 'OnActionDynamicSidebarBefore',
        'edit_attachment' => 'OnActionEditAttachment',
        'edit_category_form' => 'OnActionEditCategoryForm',
        'edit_category_form_fields' => 'OnActionEditCategoryFormFields',
        'edit_category_form_pre' => 'OnActionEditCategoryFormPre',
        'edit_comment' => 'OnActionEditComment',
        'edit_form_advanced' => 'OnActionEditFormAdvanced',
        'edit_form_after_editor' => 'OnActionEditFormAfterEditor',
        'edit_form_after_title' => 'OnActionEditFormAfterTitle',
        'edit_form_before_permalink' => 'OnActionEditFormBeforePermalink',
        'edit_form_top' => 'OnActionEditFormTop',
        'edit_link' => 'OnActionEditLink',
        'edit_link_category_form' => 'OnActionEditLinkCategoryForm',
        'edit_link_category_form_fields' => 'OnActionEditLinkCategoryFormFields',
        'edit_link_category_form_pre' => 'OnActionEditLinkCategoryFormPre',
        'edit_page_form' => 'OnActionEditPageForm',
        'edit_post' => 'OnActionEditPost',
        'edit_tag_form' => 'OnActionEditTagForm',
        'edit_tag_form_fields' => 'OnActionEditTagFormFields',
        'edit_tag_form_pre' => 'OnActionEditTagFormPre',
        'edit_term' => 'OnActionEditTerm',
        'edit_term_taxonomies' => 'OnActionEditTermTaxonomies',
        'edit_term_taxonomy' => 'OnActionEditTermTaxonomy',
        'edit_terms' => 'OnActionEditTerms',
        'edit_user_profile' => 'OnActionEditUserProfile',
        'edit_user_profile_update' => 'OnActionEditUserProfileUpdate',
        'edited_term' => 'OnActionEditedTerm',
        'edited_term_taxonomies' => 'OnActionEditedTermTaxonomies',
        'edited_term_taxonomy' => 'OnActionEditedTermTaxonomy',
        'edited_terms' => 'OnActionEditedTerms',
        'end_fetch_post_thumbnail_html' => 'OnActionEndFetchPostThumbnailHtml',
        'export_filters' => 'OnActionExportFilters',
        'export_wp' => 'OnActionExportWP',
        'generate_rewrite_rules' => 'OnActionGenerateRewriteRules',
        'get_footer' => 'OnActionGetFooter',
        'get_header' => 'OnActionGetHeader',
        'get_search_form' => 'OnActionGetSearchForm',
        'get_sidebar' => 'OnActionGetSidebar',
        'grant_super_admin' => 'OnActionGrantSuperAdmin',
        'granted_super_admin' => 'OnActionGrantedSuperAdmin',
        'heartbeat_nopriv_tick' => 'OnActionHeartbeatNoprivTick',
        'heartbeat_tick' => 'OnActionHeartbeatTick',
        'http_api_curl' => 'OnActionHttpApiCurl',
        'http_api_debug' => 'OnActionHttpApiDebug',
        'in_admin_footer' => 'OnActionInAdminFooter',
        'in_admin_header' => 'OnActionInAdminHeader',
        'in_widget_form' => 'OnActionInWidgetForm',
        'init' => 'OnActionInit',
        'install_plugins_table_header' => 'OnActionInstallPluginsTableHeader',
        'install_themes_table_header' => 'OnActionInstallThemesTableHeader',
        'load-categories-php' => 'OnActionLoadCategoriesPhp',
        'load-edit-link-categories-php' => 'OnActionLoadEditLinkCategoriesPhp',
        'load_feed_engine' => 'OnActionLoadFeedEngine',
        'load-page-new-php' => 'OnActionLoadPageNewPhp',
        'load-page-php' => 'OnActionLoadPagePhp',
        'load_textdomain' => 'OnActionLoadTextdomain',
        'load-widgets-php' => 'OnActionLoadWidgetsPhp',
        'login_enqueue_scripts' => 'OnActionLoginEnqueueScripts',
        'login_footer' => 'OnActionLoginFooter',
        'login_form' => 'OnActionLoginForm',
        'login_head' => 'OnActionLoginHead',
        'login_init' => 'OnActionLoginInit',
        'loop_end' => 'OnActionLoopEnd',
        'loop_start' => 'OnActionLoopStart',
        'lost_password' => 'OnActionLostPassword',
        'lostpassword_form' => 'OnActionLostpasswordForm',
        'lostpassword_post' => 'OnActionLostpasswordPost',
        'make_delete_blog' => 'OnActionMakeDeleteBlog',
        'make_ham_blog' => 'OnActionMakeHamBlog',
        'make_ham_user' => 'OnActionMakeHamUser',
        'make_spam_blog' => 'OnActionMakeSpamBlog',
        'make_spam_user' => 'OnActionMakeSpamUser',
        'make_undelete_blog' => 'OnActionMakeUndeleteBlog',
        'manage_comments_custom_column' => 'OnActionManageCommentsCustomColumn',
        'manage_comments_nav' => 'OnActionManageCommentsNav',
        'manage_link_custom_column' => 'OnActionManageLinkCustomColumn',
        'manage_media_custom_column' => 'OnActionManageMediaCustomColumn',
        'manage_pages_custom_column' => 'OnActionManagePagesCustomColumn',
        'manage_plugins_custom_column' => 'OnActionManagePluginsCustomColumn',
        'manage_posts_custom_column' => 'OnActionManagePostsCustomColumn',
        'manage_sites_custom_column' => 'OnActionManageSitesCustomColumn',
        'manage_themes_custom_column' => 'OnActionManageThemesCustomColumn',
        'mature_blog' => 'OnActionMatureBlog',
        'media_buttons' => 'OnActionMediaButtons',
        'ms_site_not_found' => 'OnActionMsSiteNotFound',
        'mu_activity_box_end' => 'OnActionMuActivityBoxEnd',
        'mu_rightnow_end' => 'OnActionMuRightnowEnd',
        'muplugins_loaded' => 'OnActionMupluginsLoaded',
        'myblogs_allblogs_options' => 'OnActionMyblogsAllblogsOptions',
        'network_admin_menu' => 'OnActionNetworkAdminMenu',
        'network_admin_notices' => 'OnActionNetworkAdminNotices',
        'network_site_users_after_list_table' => 'OnActionNetworkSiteUsersAfterListTable',
        'opml_head' => 'OnActionOpmlHead',
        'parse_query' => 'OnActionParseQuery',
        'parse_request' => 'OnActionParseRequest',
        'parse_tax_query' => 'OnActionParseTaxQuery',
        'password_reset' => 'OnActionPasswordReset',
        'permalink_structure_changed' => 'OnActionPermalinkStructureChanged',
        'personal_options' => 'OnActionPersonalOptions',
        'personal_options_update' => 'OnActionPersonalOptionsUpdate',
        'phpmailer_init' => 'OnActionPhpmailerInit',
        'pingback_post' => 'OnActionPingbackPost',
        'plugins_loaded' => 'OnActionPluginsLoaded',
        'populate_options' => 'OnActionPopulateOptions',
        'post_comment_status_meta_box-options' => 'OnActionPostCommentStatusMetaBoxOptions',
        'post_edit_form_tag' => 'OnActionPostEditFormTag',
        'post-html-upload-ui' => 'OnActionPostHtmlUploadUi',
        'post_lock_lost_dialog' => 'OnActionPostLockLostDialog',
        'post_locked_dialog' => 'OnActionPostLockedDialog',
        'post-plupload-upload-ui' => 'OnActionPostPluploadUploadUi',
        'post_submitbox_misc_actions' => 'OnActionPostSubmitboxMiscActions',
        'post_submitbox_start' => 'OnActionPostSubmitboxStart',
        'post_updated' => 'OnActionPostUpdated',
        'post-upload-ui' => 'OnActionPostUploadUi',
        'posts_selection' => 'OnActionPostsSelection',
        'pre_comment_on_post' => 'OnActionPreCommentOnPost',
        'pre_current_active_plugins' => 'OnActionPreCurrentActivePlugins',
        'pre_delete_term' => 'OnActionPreDeleteTerm',
        'pre_get_comments' => 'OnActionPreGetComments',
        'pre_get_posts' => 'OnActionPreGetPosts',
        'pre_get_search_form' => 'OnActionPreGetSearchForm',
        'pre_get_users' => 'OnActionPreGetUsers',
        'pre-html-upload-ui' => 'OnActionPreHtmlUploadUi',
        'pre_ping' => 'OnActionPrePing',
        'pre-plupload-upload-ui' => 'OnActionPrePluploadUploadUi',
        'pre_post_update' => 'OnActionPrePostUpdate',
        'pre-upload-ui' => 'OnActionPreUploadUi',
        'pre_user_query' => 'OnActionPreUserQuery',
        'pre_user_search' => 'OnActionPreUserSearch',
        'preprocess_signup_form' => 'OnActionPreprocessSignupForm',
        'print_media_templates' => 'OnActionPrintMediaTemplates',
        'private_to_published' => 'OnActionPrivateToPublished',
        'profile_personal_options' => 'OnActionProfilePersonalOptions',
        'profile_update' => 'OnActionProfileUpdate',
        'publish_phone' => 'OnActionPublishPhone',
        'quick_edit_custom_box' => 'OnActionQuickEditCustomBox',
        'rdf_header' => 'OnActionRdfHeader',
        'rdf_item' => 'OnActionRdfItem',
        'rdf_ns' => 'OnActionRdfNs',
        'refresh_blog_details' => 'OnActionRefreshBlogDetails',
        'register_form' => 'OnActionRegisterForm',
        'register_post' => 'OnActionRegisterPost',
        'register_sidebar' => 'OnActionRegisterSidebar',
        'registered_post_type' => 'OnActionRegisteredPostType',
        'registered_taxonomy' => 'OnActionRegisteredTaxonomy',
        'remove_user_from_blog' => 'OnActionRemoveUserFromBlog',
        'resetpass_form' => 'OnActionResetpassForm',
        'restrict_manage_comments' => 'OnActionRestrictManageComments',
        'restrict_manage_posts' => 'OnActionRestrictManagePosts',
        'restrict_manage_users' => 'OnActionRestrictManageUsers',
        'retreive_password' => 'OnActionRetreivePassword',
        'retrieve_password' => 'OnActionRetrievePassword',
        'retrieve_password_key' => 'OnActionRetrievePasswordKey',
        'revoke_super_admin' => 'OnActionRevokeSuperAdmin',
        'revoked_super_admin' => 'OnActionRevokedSuperAdmin',
        'rightnow_end' => 'OnActionRightnowEnd',
        'rss2_comments_ns' => 'OnActionRss2CommentsNs',
        'rss2_head' => 'OnActionRss2Head',
        'rss2_item' => 'OnActionRss2Item',
        'rss2_ns' => 'OnActionRss2Ns',
        'rss_head' => 'OnActionRssHead',
        'rss_item' => 'OnActionRssItem',
        'rss_tag_pre' => 'OnActionRssTagPre',
        'sanitize_comment_cookies' => 'OnActionSanitizeCommentCookies',
        'sanitize_title' => 'OnActionSanitizeTitle',
        'save_post' => 'OnActionSavePost',
        'send_headers' => 'OnActionSendHeaders',
        'set_auth_cookie' => 'OnActionSetAuthCookie',
        'set_comment_cookies' => 'OnActionSetCommentCookies',
        'set_current_user' => 'OnActionSetCurrentUser',
        'set_logged_in_cookie' => 'OnActionSetLoggedInCookie',
        'set_object_terms' => 'OnActionSetObjectTerms',
        'set_user_role' => 'OnActionSetUserRole',
        'setted_site_transient' => 'OnActionSettedSiteTransient',
        'setted_transient' => 'OnActionSettedTransient',
        'setup_theme' => 'OnActionSetupTheme',
        'show_user_profile' => 'OnActionShowUserProfile',
        'shutdown' => 'OnActionShutdown',
        'sidebar_admin_page' => 'OnActionSidebarAdminPage',
        'sidebar_admin_setup' => 'OnActionSidebarAdminSetup',
        'signup_blogform' => 'OnActionSignupBlogform',
        'signup_extra_fields' => 'OnActionSignupExtraFields',
        'signup_finished' => 'OnActionSignupFinished',
        'signup_header' => 'OnActionSignupHeader',
        'signup_hidden_fields' => 'OnActionSignupHiddenFields',
        'spam_comment' => 'OnActionSpamComment',
        'spammed_comment' => 'OnActionSpammedComment',
        'start_previewing_theme' => 'OnActionStartPreviewingTheme',
        'stop_previewing_theme' => 'OnActionStopPreviewingTheme',
        'submitlink_box' => 'OnActionSubmitlinkBox',
        'submitpage_box' => 'OnActionSubmitpageBox',
        'submitpost_box' => 'OnActionSubmitpostBox',
        'switch_blog' => 'OnActionSwitchBlog',
        'switch_theme' => 'OnActionSwitchTheme',
        'template_redirect' => 'OnActionTemplateRedirect',
        'the_post' => 'OnActionThePost',
        'the_widget' => 'OnActionTheWidget',
        'tool_box' => 'OnActionToolBox',
        'trackback_post' => 'OnActionTrackbackPost',
        'transition_comment_status' => 'OnActionTransitionCommentStatus',
        'transition_post_status' => 'OnActionTransitionPostStatus',
        'trash_comment' => 'OnActionTrashComment',
        'trash_post_comments' => 'OnActionTrashPostComments',
        'trashed_comment' => 'OnActionTrashedComment',
        'trashed_post' => 'OnActionTrashedPost',
        'trashed_post_comments' => 'OnActionTrashedPostComments',
        'twentyfourteen_credits' => 'OnActionTwentyfourteenCredits',
        'twentyfourteen_featured_posts_after' => 'OnActionTwentyfourteenFeaturedPostsAfter',
        'twentyfourteen_featured_posts_before' => 'OnActionTwentyfourteenFeaturedPostsBefore',
        'twentythirteen_credits' => 'OnActionTwentythirteenCredits',
        'twentytwelve_credits' => 'OnActionTwentytwelveCredits',
        'unarchive_blog' => 'OnActionUnarchiveBlog',
        'unload_textdomain' => 'OnActionUnloadTextdomain',
        'unmature_blog' => 'OnActionUnmatureBlog',
        'unspam_comment' => 'OnActionUnspamComment',
        'unspammed_comment' => 'OnActionUnspammedComment',
        'untrash_comment' => 'OnActionUntrashComment',
        'untrash_post' => 'OnActionUntrashPost',
        'untrash_post_comments' => 'OnActionUntrashPostComments',
        'untrashed_comment' => 'OnActionUntrashedComment',
        'untrashed_post' => 'OnActionUntrashedPost',
        'untrashed_post_comments' => 'OnActionUntrashedPostComments',
        'update_blog_public' => 'OnActionUpdateBlogPublic',
        'update_option' => 'OnActionUpdateOption',
        'update_postmeta' => 'OnActionUpdatePostmeta',
        'update_site_option' => 'OnActionUpdateSiteOption',
        'update_usermeta' => 'OnActionUpdateUsermeta',
        'update_wpmu_options' => 'OnActionUpdateWpmuOptions',
        'updated_option' => 'OnActionUpdatedOption',
        'updated_postmeta' => 'OnActionUpdatedPostmeta',
        'updated_usermeta' => 'OnActionUpdatedUsermeta',
        'upgrader_process_complete' => 'OnActionUpgraderProcessComplete',
        'upload_ui_over_quota' => 'OnActionUploadUiOverQuota',
        'user_admin_menu' => 'OnActionUserAdminMenu',
        'user_admin_notices' => 'OnActionUserAdminNotices',
        'user_edit_form_tag' => 'OnActionUserEditFormTag',
        'user_new_form' => 'OnActionUserNewForm',
        'user_new_form_tag' => 'OnActionUserNewFormTag',
        'user_profile_update_errors' => 'OnActionUserProfileUpdateErrors',
        'user_register' => 'OnActionUserRegister',
        'validate_password_reset' => 'OnActionValidatePasswordReset',
        'wp' => 'OnActionWP',
        'wp_after_admin_bar_render' => 'OnActionWPAfterAdminBarRender',
        'wp_authenticate' => 'OnActionWPAuthenticate',
        'wp_before_admin_bar_render' => 'OnActionWPBeforeAdminBarRender',
        'wp_blacklist_check' => 'OnActionWPBlacklistCheck',
        'wp_create_nav_menu' => 'OnActionWPCreateNavMenu',
        'wp_creating_autosave' => 'OnActionWPCreatingAutosave',
        'wp_dashboard_setup' => 'OnActionWPDashboardSetup',
        'wp_default_scripts' => 'OnActionWPDefaultScripts',
        'wp_default_styles' => 'OnActionWPDefaultStyles',
        'wp_delete_nav_menu' => 'OnActionWPDeleteNavMenu',
        'wp_delete_post_revision' => 'OnActionWPDeletePostRevision',
        'wp_enqueue_editor' => 'OnActionWPEnqueueEditor',
        'wp_enqueue_media' => 'OnActionWPEnqueueMedia',
        'wp_enqueue_scripts' => 'OnActionWPEnqueueScripts',
        'wp_feed_options' => 'OnActionWPFeedOptions',
        'wp_footer' => 'OnActionWPFooter',
        'wp_head' => 'OnActionWPHead',
        'wp_insert_comment' => 'OnActionWPInsertComment',
        'wp_insert_post' => 'OnActionWPInsertPost',
        'wp_install' => 'OnActionWPInstall',
        'wp_loaded' => 'OnActionWPLoaded',
        'wp_login' => 'OnActionWPLogin',
        'wp_login_failed' => 'OnActionWPLoginFailed',
        'wp_logout' => 'OnActionWPLogout',
        'wp-mail-php' => 'OnActionWPMailPhp',
        'wp_maybe_auto_update' => 'OnActionWPMaybeAutoUpdate',
        'wp_meta' => 'OnActionWPMeta',
        'wp_network_dashboard_setup' => 'OnActionWPNetworkDashboardSetup',
        'wp_playlist_scripts' => 'OnActionWPPlaylistScripts',
        'wp_print_footer_scripts' => 'OnActionWPPrintFooterScripts',
        'wp_print_scripts' => 'OnActionWPPrintScripts',
        'wp_print_styles' => 'OnActionWPPrintStyles',
        'wp_register_sidebar_widget' => 'OnActionWPRegisterSidebarWidget',
        'wp_restore_post_revision' => 'OnActionWPRestorePostRevision',
        'wp_set_comment_status' => 'OnActionWPSetCommentStatus',
        'wp_tiny_mce_init' => 'OnActionWPTinyMceInit',
        'wp_trash_post' => 'OnActionWPTrashPost',
        'wp_unregister_sidebar_widget' => 'OnActionWPUnregisterSidebarWidget',
        'wp_update_comment_count' => 'OnActionWPUpdateCommentCount',
        'wp_update_nav_menu' => 'OnActionWPUpdateNavMenu',
        'wp_update_nav_menu_item' => 'OnActionWPUpdateNavMenuItem',
        'wp_upgrade' => 'OnActionWPUpgrade',
        'wp_user_dashboard_setup' => 'OnActionWPUserDashboardSetup',
        'welcome_panel' => 'OnActionWelcomePanel',
        'widgets_admin_page' => 'OnActionWidgetsAdminPage',
        'widgets_init' => 'OnActionWidgetsInit',
        'widgets-php' => 'OnActionWidgetsPhp',
        'wpmu_activate_blog' => 'OnActionWpmuActivateBlog',
        'wpmu_activate_user' => 'OnActionWpmuActivateUser',
        'wpmu_blog_updated' => 'OnActionWpmuBlogUpdated',
        'wpmu_delete_user' => 'OnActionWpmuDeleteUser',
        'wpmu_new_blog' => 'OnActionWpmuNewBlog',
        'wpmu_new_user' => 'OnActionWpmuNewUser',
        'wpmu_options' => 'OnActionWpmuOptions',
        'wpmu_update_blog_options' => 'OnActionWpmuUpdateBlogOptions',
        'wpmu_upgrade_page' => 'OnActionWpmuUpgradePage',
        'wpmu_upgrade_site' => 'OnActionWpmuUpgradeSite',
        'wpmuadminedit' => 'OnActionWpmuadminedit',
        'wpmuadminresult' => 'OnActionWpmuadminresult',
        'wpmublogsaction' => 'OnActionWpmublogsaction',
        'wpmueditblogaction' => 'OnActionWpmueditblogaction',
        'xmlrpc_call' => 'OnActionXmlrpcCall',
        'xmlrpc_call_success_blogger_deletePost' => 'OnActionXmlrpcCallSuccessBloggerDeletePost',
        'xmlrpc_call_success_blogger_editPost' => 'OnActionXmlrpcCallSuccessBloggerEditPost',
        'xmlrpc_call_success_blogger_newPost' => 'OnActionXmlrpcCallSuccessBloggerNewPost',
        'xmlrpc_call_success_mw_editPost' => 'OnActionXmlrpcCallSuccessMwEditPost',
        'xmlrpc_call_success_mw_newMediaObject' => 'OnActionXmlrpcCallSuccessMwNewMediaObject',
        'xmlrpc_call_success_mw_newPost' => 'OnActionXmlrpcCallSuccessMwNewPost',
        'xmlrpc_call_success_wp_deleteCategory' => 'OnActionXmlrpcCallSuccessWPDeleteCategory',
        'xmlrpc_call_success_wp_deleteComment' => 'OnActionXmlrpcCallSuccessWPDeleteComment',
        'xmlrpc_call_success_wp_deletePage' => 'OnActionXmlrpcCallSuccessWPDeletePage',
        'xmlrpc_call_success_wp_editComment' => 'OnActionXmlrpcCallSuccessWPEditComment',
        'xmlrpc_call_success_wp_newCategory' => 'OnActionXmlrpcCallSuccessWPNewCategory',
        'xmlrpc_call_success_wp_newComment' => 'OnActionXmlrpcCallSuccessWPNewComment',
        'xmlrpc_publish_post' => 'OnActionXmlrpcPublishPost',
        'xmlrpc_rsd_apis' => 'OnActionXmlrpcRsdApis',
        '_admin_menu' => 'OnAction_AdminMenu',
        '_core_updated_successfully' => 'OnAction_CoreUpdatedSuccessfully',
        '_network_admin_menu' => 'OnAction_NetworkAdminMenu',
        '_user_admin_menu' => 'OnAction_UserAdminMenu',
        '_wp_put_post_revision' => 'OnAction_WPPutPostRevision'
    );

}
