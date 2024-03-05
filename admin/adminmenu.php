<?php
namespace MetagaussOpenAI\Admin;

final class AdminMenu extends \MetagaussOpenAI\Admin\MoRoot
{
    public function topLevelMenu()
    {
        $this->mainMenuItem();
        $this->orgFilesSubmenuItem();
        $this->addFileSubmenuItem();
    }

    public function mainMenuItem()
    {
        add_menu_page(
            'Metagauss',
            'Metagauss',
            'manage_options',
            'metagaussopenai-chatbot',
            array($this, 'appMenuPage'),
            'dashicons-superhero',
            6
        );
    }

    public function orgFilesSubmenuItem()
    {
        add_submenu_page(
            'metagaussopenai-chatbot',
            __('Files', 'metgauss-openai'),
            __('Files', 'metgauss-openai'),
            'manage_options',
            'metagaussopenai-files',
            array($this, 'filesMenuPage'),
            1
        );
    }

    public function addFileSubmenuItem()
    {
        add_submenu_page(
            'metagaussopenai-chatbot',
            __('Add File', 'metgauss-openai'),
            __('Add File', 'metgauss-openai'),
            'manage_options',
            'metagaussopenai-addfile',
            array($this, 'addFileMenuPage'),
            1
        );
    }

    public function appMenuPage()
    {
        include_once(plugin_dir_path(__FILE__) . 'pages/chatbot.php');
    }

    public function filesMenuPage()
    {
        include_once(plugin_dir_path(__FILE__) . 'pages/orgfiles.php');
    }

    public function addFileMenuPage()
    {
        include_once(plugin_dir_path(__FILE__) . 'pages/addFile.php');
    }

    public function __construct()
    {
        add_action( 'admin_menu', array($this, 'topLevelMenu'));
    }
}