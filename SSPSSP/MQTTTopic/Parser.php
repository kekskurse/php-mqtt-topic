<?php
namespace SSPSSP\MQTTTopic;

class Parser
{
    public function __construct() {

    }

    public function match($topic, $topicPattern)
    {
        $topicArray = explode("/", $topic);
        $topicPattern = explode("/", $topicPattern);
        return $this->matchNext($topicArray, $topicPattern, 0);

    }

    public function matchACL($topic, $rule) {
        $topicArray = explode("/", $topic);
        $ruleArray = explode("/", $rule);
        //Special Rules I dont want to fix with recursion
        /*if($topic == "#" && $rule == "+"){
            return false;
        }*/
        return $this->matchACLNext($topicArray, $ruleArray, 0);
    }

    private function matchACLNext($topicArray, $ruleArray, $i) {
        if(isset($ruleArray[$i]) && $ruleArray[$i] == "#") {
            return true;
        }
        if(!isset($topicArray[$i]) && !isset($ruleArray[$i]))
        {
            return true;
        }
        if(!isset($topicArray[$i]) || !isset($ruleArray[$i]))
        {
            return false;
        }

        //Bei beiden ist ein eintrag vorhanden
        if(isset($topicArray[$i]) && $topicArray[$i] == "#") {
            return false; //Rule Array must also be # to do this, but its checked in the first if
        }
        if($ruleArray[$i] == "+") {
            return $this->matchACLNext($topicArray, $ruleArray, $i + 1);
        }
        if($topicArray[$i] == "+" && $ruleArray[$i] != "+") {
            return false;
        }
        if($topicArray[$i] == $ruleArray[$i]) {
            return $this->matchACLNext($topicArray, $ruleArray, $i + 1);
        }
        return false;

    }

    private function matchNext($topicArray, $topicPattern, $i) {
        if(!isset($topicPattern[$i]) && !isset($topicArray[$i]))
        {
            return true;
        }
        if(isset($topicPattern[$i]) && $topicPattern[$i] == "#") {
            return true;
        }
        if(!isset($topicArray[$i]) || !isset($topicPattern[$i])) {
            return false;
        }


        if($topicPattern[$i] == "+") {
            return $this->matchNext($topicArray, $topicPattern, $i+1);
        }
        if($topicPattern[$i] == $topicArray[$i]) {
            return $this->matchNext($topicArray, $topicPattern, $i+1);
        }
        return false;
    }
}
