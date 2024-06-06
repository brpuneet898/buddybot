<?php
namespace BuddyBot\Frontend\Views\Bootstrap;

use BuddyBot\Traits\Singleton;
use BuddyBot\Frontend\Views\Bootstrap\BuddybotChat\SecurityChecks;
use BuddyBot\Frontend\Views\Bootstrap\BuddybotChat\SingleConversation;

class BuddybotChat extends \BuddyBot\Frontend\Views\Bootstrap\MoRoot
{
    use Singleton;
    use SecurityChecks;
    use SingleConversation;

    protected $sql;
    protected $conversations;

    public function shortcodeHtml($atts, $content = null)
    {
        $html = $this->securityChecksHtml();

        if (!$this->errors) {
            $html .= $this->conversationListWrapper();
            $html .= $this->singleConversationHtml();
        }

        return $html;
    }

    private function conversationListWrapper()
    {
        $html = '<div id="buddybot-chat-conversation-list-wrapper">';
        $html .= '</div>';
        return $html;
    }

    public function conversationList()
    {
        $user_id = get_current_user_id();
        $this->conversations = $this->sql->getConversationsByUserId($user_id);
        
        if (!empty($this->conversations)) {
            $this->listHtml();
        }
    }

    protected function listHtml()
    {
        echo '<ol class="list-group list-group-numbered small">';
        foreach ($this->conversations as $conversation) {
            echo '<li class="list-group-item d-flex justify-content-between align-items-start">';
            echo '<div class="ms-2 me-auto">';
            echo '<div class="fw-bold">' . $conversation->thread_name . '</div>';
            echo '<div class="text-muted small text-end">' . $conversation->created . '</div>';
            echo '</div>';
            echo '</li>';
        }
        echo '</ol>';
    }
}