<?php

class Validate
{
    private $_passed = false,
            $_errors = array(),
            $_db = null;
    
    public function __construct()
    {
        $this->_db = DB::getInstance();
    }
    
    public function check($source, $items = array())
    {
        foreach($items as $item => $rules)
        {
            foreach($rules as $rule => $rule_value)
            {
                $value = trim($source[$item]);
                
                if($rule === 'required' && empty($value))
                {
                    $this->addError("{$item} vaaditaan.");
                }
                else if(!empty($value))
                {
                    switch($rule)
                    {
                        case 'min':
                            if(strlen($value) < $rule_value)
                            {
                                $this->addError("{$item} täytyy olla vähintään {$rule_value} merkkiä pitkä.");
                            }
                            break;
                        
                        case 'max':
                            if(strlen($value) > $rule_value)
                            {
                                $this->addError("{$item} saa olla enintään {$rule_value} merkkiä pitkä.");
                            }
                            break;
                        
                        case 'matches':
                            if($value != $source[$rule_value])
                            {
                                $this->addError("{$rule_value} täytyy olla sama kuin {$item}.");
                            }
                            break;
                        
                        case 'unique':
                            $check = $this->_db->get($rule_value, array($item, '=', $value));
                            if($check->count())
                            {
                                $this->addError("{$item} on jo olemassa.");
                            }
                            break;
                        
                    }
                }
            }
        }
        if(empty($this->_errors))
        {
            $this->_passed = true;
        }

        return $this;
    }
    
    private function addError($error)
    {
        $this->_errors[] = $error;
    }
    
    public function errors()
    {
        return $this->_errors;
    }
    
    public function passed()
    {
        return $this->_passed;
    }
}