<?php

namespace MetagaussOpenAI\Admin\Requests;

final class EditAssistant extends \MetagaussOpenAI\Admin\Requests\MoRoot
{
    protected $assistant_id = '';

    protected function setAssistantId()
    {
        if (!empty($_GET['assistant_id'])) {
            $this->assistant_id = sanitize_text_field($_GET['assistant_id']);
        }
    }

    public function requestJs()
    {
        $this->setVarsJs();
        $this->getModelsJs();
        $this->getFilesJs();
        $this->filesCountJs();
        $this->updateFilesCountJs();
        $this->toggleCheckboxesJs();
        $this->assistantDataJs();
        $this->createAssistantJs();
        $this->loadAssistantValuesJs();
    }

    private function setVarsJs()
    {
        $context = 'create';

        if ($this->assistant_id !== '') {
            $context = 'update';
        }

        echo '
        const context = "' . $context . '";
        ';
    }

    private function getModelsJs()
    {
        $nonce = wp_create_nonce('get_models');
        echo '
        getModels();
        function getModels(){
            const select = $("#mo-editassistant-assistantmodel");
            const data = {
                "action": "getModels",
                "nonce": "' . $nonce . '"
            };
      
            $.post(ajaxurl, data, function(response) {
                response = JSON.parse(response);
                if (response.success) {
                    select.html(response.html);
                    select.siblings(".mo-dataload-spinner").hide();
                    if (context === "update") {
                        getAssistantData();
                    }
                } else {
                    showAlert(response.message);
                }
            });
        };
        ';
    }

    private function getFilesJs()
    {
        $nonce = wp_create_nonce('get_files');
        echo '
        getFiles();
        function getFiles(){
            const data = {
                "action": "getFiles",
                "nonce": "' . $nonce . '"
            };
      
            $.post(ajaxurl, data, function(response) {
                response = JSON.parse(response);
                if (response.success) {
                    $("#mo-editassistant-assistantfiles").html(response.html);
                    filesCount();
                } else {
                    showAlert(response.message);
                }
            });
        };
        ';
    }

    private function filesCountJs()
    {
        echo '
        function filesCount() {
            let count = 0;

            $("#mo-editassistant-assistantfiles").find("input[type=checkbox]").each(function(){
                if ($(this).is(":checked")) {
                    count++;
                }
            });

            $("#mo-editassistant-assistantfiles-filescount").text(count);
            return count;
        }
        ';
    }

    private function updateFilesCountJs()
    {
        echo '
        $("#mo-editassistant-assistantfiles").click(function(){
            let count = filesCount();
            if (count >= 20) {
                disableCheckBoxes();
            } else {
                enableCheckBoxes();
            }
        });
        ';
    }

    private function toggleCheckboxesJs()
    {
        echo '
        function disableCheckBoxes() {
            $("#mo-editassistant-assistantfiles").find("input[type=checkbox]").each(function(){
                if (!$(this).is(":checked")) {
                    $(this).prop("disabled", true);
                }
            });
        }

        function enableCheckBoxes() {
            $("#mo-editassistant-assistantfiles").find("input[type=checkbox]").each(function(){
                $(this).prop("disabled", false);
            });
        }
        ';
    }

    private function assistantDataJs()
    {
        echo '
        function assistantData() {
            let assistantData = {};
            assistantData["name"] = $("#mo-editassistant-assistantname").val();
            assistantData["description"] = $("#mo-editassistant-assistantdescription").val();
            assistantData["model"] = $("#mo-editassistant-assistantmodel").val();
            assistantData["instructions"] = $("#mo-editassistant-assistantinstructions").val();
            assistantData["tools"] = assistantTools();
            assistantData["file_ids"] = assistantFiles();

            return assistantData;
        }

        function assistantTools() {
            let assistantTools = [];

            $("#mo-editassistant-assistanttools").find("input[type=checkbox]").each(function(){
                if ($(this).is(":checked")) {
                    let value = $(this).val();
                    assistantTools.push(value);
                }
            });

            return assistantTools;
        }

        function assistantFiles() {
            let assistantFiles = [];

            $("#mo-editassistant-assistantfiles").find("input[type=checkbox]").each(function(){
                if ($(this).is(":checked")) {
                    let value = $(this).val();
                    assistantFiles.push(value);
                }
            });

            return assistantFiles;
        }
        ';
    }

    private function createAssistantJs()
    {
        $nonce = wp_create_nonce('create_assistant');
        echo '
        $("#mo-editassistant-editassistant-submit").click(createAssistant);

        function createAssistant(){
            hideAlert();
            showBtnLoader("#mo-editassistant-editassistant-submit");
            let aData = assistantData();

            const data = {
                "action": "createAssistant",
                "assistant_id": "' . $this->assistant_id . '",
                "assistant_data": JSON.stringify(aData),
                "nonce": "' . $nonce . '"
            };
      
            $.post(ajaxurl, data, function(response) {
                hideBtnLoader("#mo-editassistant-editassistant-submit");
                response = JSON.parse(response);
                if (response.success) {
                    location.replace("' . get_admin_url() . 'admin.php?page=metagaussopenai-assistant&assistant_id=' . '" + response.result.id);
                } else {
                    showAlert(response.message);
                }
            });
        };
        ';
    }

    private function loadAssistantValuesJs()
    {
        if ($this->assistant_id === null or $this->assistant_id === '') {
            return;
        }

        $nonce = wp_create_nonce('get_assistant_data');
        echo '

        getAssistantData();
        function getAssistantData(){

            const data = {
                "action": "getAssistantData",
                "assistant_id": "' . $this->assistant_id . '",
                "nonce": "' . $nonce . '"
            };
      
            $.post(ajaxurl, data, function(response) {
                response = JSON.parse(response);
                if (response.success) {
                    fillAssistantValues(response.result);
                } else {
                    showAlert(response.message);
                }
            });
        };

        function fillAssistantValues(assistant) {
            assistant = JSON.parse(assistant);
            $("#mo-editassistant-assistantname").val(assistant.name);
            $("#mo-editassistant-assistantdescription").val(assistant.description);
            $("#mo-editassistant-assistantmodel").val(assistant.model);
            $("#mo-editassistant-assistantinstructions").val(assistant.instructions);
            checkEnabledTools(assistant.tools);
            selectAttachedFiles(assistant.file_ids);
        }

        function checkEnabledTools(tools) {
            
            let cbValues = [];
            
            $.each(tools, function(index, tool) {
                cbValues.push(tool.type);
            });

            if (cbValues.length === 0) {
                return;
            }

            $("#mo-editassistant-assistanttools").find("input[type=checkbox]").each(function(){
                
                let cbValue = $(this).val();

                if ($.inArray(cbValue, cbValues) > -1) {
                    $(this).prop("checked", true);
                }
            
            });
        }

        function selectAttachedFiles(fileIds) {

            if (fileIds.length === 0 || !$.isArray(fileIds)) {
                return;
            }

            $("#mo-editassistant-assistantfiles").find("input[type=checkbox]").each(function(){
                
                let fileId = $(this).val();
                
                if ($.inArray(fileId, fileIds) > -1) {
                    $(this).prop("checked", true);
                }

                filesCount();

            });
        }
        ';
    }
}