<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
  
class MY_Form_validation extends CI_Form_validation
{
      
    public function set_rules($field, $label = '', $rules = '')
    {
        // Check if "validate_model" is a rule within our set of rules.  If it is, load up the model's rules
        // within the rule-set and continue on to CI's class.
          
        if (strlen($rules) != 0) {
            $rule_list = explode('|', $rules);
            if ($rule_list != FALSE) {
                foreach ($rule_list as $rule) {
                    if (substr($rule, 0, 15) == 'validate_model[') {
                        // Found a validate_model ruling.  Grab the class name and field type.
                        $rule = substr($rule, 15);
                        $rule = substr($rule, 0, -1);
                        $functionName = explode ('.', $rule);
                        if (count($functionName == 2)) {
                            $modelName = $functionName[0];
                            $variableName = 'model_validate_' . $functionName[1];
                             
                            $this->CI->load->model($modelName, 'validate_model'); // Load the model
                             
                            if (isset($this->CI->validate_model->$variableName)) {
                                $rules = $this->CI->validate_model->$variableName . '|' . $rules;
                            }
                        }
                    }
                }
            }
        }
        // Always continue with the default CI set_rules even if we can't work anything additional out.
        parent::set_rules($field, $label, $rules);
    }
      
    public function validate_model($input = FALSE, $model_field = FALSE)
    {
        $functionName = explode('.', $model_field);
        if (count($functionName) != 2) {
            // Unable to work out 'model.function' to call.
            return;
        }
          
        $modelName = $functionName[0];
        $methodName = 'model_validate_' . $functionName[1];
          
        $this->CI->load->model($modelName, 'validate_model'); // Load the model if it's not already loaded.
          
        if (method_exists($this->CI->validate_model, $methodName)) {
            //$result = call_user_func(array($modelName, $methodName), $input);
            $result = $this->CI->validate_model->$methodName($input);
            if ($result) {
                return TRUE;
            } else {
                $this->set_message("validate_model", $this->CI->validate_model->model_validate_error);
                $this->CI->validate_model->model_validate_error = NULL;
                return FALSE;
            }
        } else {
            log_message('debug', "Unable to find validation rule: $modelName -> $methodName");
            return;
        }
    }
}
 
// Validation Functions
if (!function_exists('validate_string_length_between')) {
    function validate_string_length_between($string, $min, $max)
    {
        if (strlen($string) >= $min AND strlen($string) <= $max) {
            return TRUE;
        } else {
            return FALSE;
        }
    }
}