<?php
namespace BuddyBot\Frontend\Requests;

class BuddybotChat extends \BuddyBot\Frontend\Requests\Moroot
{
    protected function shortcodeJs()
    {
        $this->toggleAlertJs();
        $this->onLoadJs();
        $this->getUserThreadsJs();
        $this->startNewThreadBtnJs();
        $this->singleThreadBackBtnJs();
        $this->threadListItemJs();
        $this->getThreadInfoJs();
        $this->loadThreadListViewJs();
        $this->loadSingleThreadViewJs();
        $this->getMessagesJs();
        $this->hasMoreMessagesJs();
        $this->getPreviousMessagesJs();
        $this->sendUserMessageJs();
        $this->scrollToMessageJs();
    }

    private function toggleAlertJs()
    {
        echo '
        function showAlert(type = "danger", text = "") {
            let alert = $(".buddybot-chat-conversation-alert[data-bb-alert=" + type + "]");
            alert.text(text);
            alert.removeClass("visually-hidden");
        }

        function hideAlerts() {
            let alert = $(".buddybot-chat-conversation-alert");
            alert.addClass("visually-hidden");
        }
        ';
    }

    private function onLoadJs()
    {
        echo '
            loadThreadListView();
        ';
    }

    private function getUserThreadsJs()
    {
        echo '
        function getUserThreads() {

            const data = {
                "action": "getConversationList"
            };
  
            $.post(ajaxurl, data, function(response) {
                $("#buddybot-chat-conversation-list-loader").addClass("visually-hidden");
                $("#buddybot-chat-conversation-list-wrapper").html(response);
            });
        }
        ';
    }

    private function startNewThreadBtnJs()
    {
        echo '
        $("#buddybot-chat-conversation-start-new").click(function(){
            loadSingleThreadView();
        });
        ';
    }

    private function singleThreadBackBtnJs()
    {
        echo '
        $("#buddybot-single-conversation-back-btn").click(function(){
            loadThreadListView();
        });
        ';
    }

    private function threadListItemJs()
    {
        echo '
        $("#buddybot-chat-conversation-list-wrapper").on("click", "li", function(){
            let threadId = $(this).attr("data-bb-threadid");
            loadSingleThreadView(threadId);
        });';
    }

    private function getThreadInfoJs()
    {
        echo '
        function getThreadInfo(threadId = "") {

            if (threadId === "") {
                return;
            }

            const data = {
                "action": "getThreadInfo",
                "thread_id": threadId,
                "nonce": "' . wp_create_nonce('get_thread_info') . '"
            };

            $.post(ajaxurl, data, function(response) {
                alert(response);
            });
        }
        ';
    }

    private function loadThreadListViewJs()
    {
        echo '
        function loadThreadListView() {
            hideAlerts();
            getUserThreads();
            $("#buddybot-chat-conversation-list-header").removeClass("visually-hidden");
            $("#buddybot-chat-conversation-list-loader").removeClass("visually-hidden");
            $("#buddybot-chat-conversation-list-wrapper").removeClass("visually-hidden");
            $("#buddybot-single-conversation-wrapper").addClass("visually-hidden");
            sessionStorage.removeItem("bbCurrentThreadId");
            sessionStorage.removeItem("bbFirstId");
            sessionStorage.removeItem("bbLastId");
            $("#buddybot-single-conversation-messages-wrapper").html("");
        }';
    }

    private function loadSingleThreadViewJs()
    {
        echo '
        function loadSingleThreadView(threadId = false) {
            hideAlerts();
            $("#buddybot-chat-conversation-list-header").addClass("visually-hidden");
            $("#buddybot-chat-conversation-list-wrapper").addClass("visually-hidden");
            $("#buddybot-chat-conversation-list-wrapper").html("");
            $("#buddybot-single-conversation-wrapper").removeClass("visually-hidden");

            if (threadId === false) {
                sessionStorage.removeItem("bbCurrentThreadId");
            } else {
                sessionStorage.setItem("bbCurrentThreadId", threadId);
                getMessages(limit = 2);
            }
        }';
    }

    private function getMessagesJs()
    {
        echo '
        function getMessages(limit = 10, after = "") {
            const data = {
                "action": "getMessages",
                "thread_id": sessionStorage.getItem("bbCurrentThreadId"),
                "limit": limit,
                "order": "desc",
                "after": after,
                "nonce": "' . wp_create_nonce('get_messages') . '"
            };

            $.post(ajaxurl, data, function(response) {
                response = JSON.parse(response);
                
                if (response.success) {

                    hasMoreMessages(response.result);

                    $("#buddybot-single-conversation-messages-wrapper").prepend(response.html);
                } else {
                    showAlert("danger", response.message);
                }
                
            });
        }';
    }

    private function hasMoreMessagesJs()
    {
        echo '
        function hasMoreMessages(thread) {

            if(thread.has_more) {
                $("#buddybot-single-conversation-load-messages-btn").removeClass("visually-hidden");
            } else {
                $("#buddybot-single-conversation-load-messages-btn").addClass("visually-hidden");
            }

            sessionStorage.setItem("bbFirstId", thread.first_id);
            sessionStorage.setItem("bbLastId", thread.last_id);
        }
        ';
    }

    private function getPreviousMessagesJs()
    {
        echo '
        $("#buddybot-single-conversation-load-messages-btn").click(getPreviousMessages);

        function getPreviousMessages() {
            let lastId = sessionStorage.getItem("bbLastId");

            if (lastId === "") {
                return;
            }

            getMessages(limit = 10, lastId);
            scrollToTop();
        }

        ';
    }

    private function sendUserMessageJs()
    {
        echo '
        $("#buddybot-single-conversation-send-message-btn").click(sendUserMessage);

        function sendUserMessage() {
            let userMessage = $.trim($("#buddybot-single-conversation-user-message").val());
            
            if (userMessage === "" || userMessage == null) {
                return;
            }
            
            const messageData = {
                "action": "sendUserMessage",
                "thread_id": sessionStorage.getItem("bbCurrentThreadId"),
                "user_message": userMessage,
                "nonce": "' . wp_create_nonce('send_user_message') . '"
            };

            $.post(ajaxurl, messageData, function(response) {
                response = JSON.parse(response);
                
                if (response.success) {
                    $("#buddybot-single-conversation-user-message").val("");
                    $("#buddybot-single-conversation-messages-wrapper").append(response.html);
                    sessionStorage.setItem("bbFirstId", response.result.id);
                    scrollToBottom(response.result.id);
                    createRun();
                } else {
                    showAlert("danger", response.message);
                }
            });
        }
        ';
    }

    private function createRunJs()
    {
        echo '
        function createRun() {

            const assistantId = $("#buddybot-playground-assistants-list").val();

            const data = {
                "action": "createRun",
                "thread_id": sessionStorage.getItem("bbCurrentThreadId"),
                "assistant_id": assistantId,
                "nonce": "' . wp_create_nonce('create_run') . '"
            };
  
            $.post(ajaxurl, data, function(response) {
                response = JSON.parse(response);
                if (response.success) {
                    updateStatus(runCreated);
                    $("#mgao-playground-run-id-input").val(response.result.id);
                    checkRun = setInterval(retrieveRun, 2000);
                } else {
                    disableMessage(false);
                    updateStatus(response.message);
                }
            });
        }
        ';
    }

    private function scrollToMessageJs()
    {
        echo '
        function scrollToBottom(id) {
            let height = $("#" + id).outerHeight();
            $("#buddybot-single-conversation-messages-wrapper").animate({
                scrollTop: $("#buddybot-single-conversation-messages-wrapper")[0].scrollHeight - height - 200
            }, 1000);
        }

        function scrollToTop() {
            $("#buddybot-single-conversation-messages-wrapper").animate({
                scrollTop: 0
            }, 1000);
        }
        ';
    }
}