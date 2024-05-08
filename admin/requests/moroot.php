<?php

namespace BuddyBot\Admin\Requests;

class MoRoot extends \BuddyBot\Admin\MoRoot
{
    public function requestsJs()
    {
        echo '
        <script>
        $(document).ready(function(){' . PHP_EOL;

        $this->showAlertJs();
        $this->hideAlertJs();
        $this->loaderBtnJs();
        $this->disableFieldsJs();
        $this->requestJs();
        
        echo 
        PHP_EOL . '});
        </script>';
    }

    protected function showAlertJs()
    {
        echo '
        function showAlert(message = "") {
            $("#mo-alert-container").html(message);
            $("#mo-alert-container").show();
        }
        ';
    }

    protected function hideAlertJs()
    {
        echo '
        function hideAlert(message = "") {
            $("#mo-alert-container").html("");
            $("#mo-alert-container").hide();
        }
        ';
    }

    protected function loaderBtnJs()
    {
        echo '
        function showBtnLoader(btnId) {
            $(btnId).prop("disabled", true);
            $(btnId).children(".mo-loaderbtn-label").addClass("visually-hidden");
            $(btnId).children(".mo-loaderbtn-spinner").removeClass("visually-hidden");
        }

        function hideBtnLoader(btnId) {
            $(btnId).prop("disabled", false);
            $(btnId).children(".mo-loaderbtn-label").removeClass("visually-hidden");
            $(btnId).children(".mo-loaderbtn-spinner").addClass("visually-hidden");
        }
        ';
    }

    protected function disableFieldsJs()
    {
        echo '
        function disableFields(isDisabled) {
            $(".mo-item-field").each(function(){
                $(this).prop("disabled", isDisabled);
            });
        }
        ';
    }

    protected function requestJs()
    {

    }
}