<?php

namespace Hyiplab\BackOffice;

use Hyiplab\BackOffice\Validator\Validator;

class Request
{
    private $all = [];
    private $sanitized_data = [];
    
    public function __construct()
    {
        $this->sanitizeInputs();
    }

    /**
     * Sanitize all input data for security
     */
    private function sanitizeInputs()
    {
        // Sanitize POST data
        foreach ($_POST as $key => $value) {
            $sanitized_value = $this->sanitizeValue($value);
            $this->$key = $sanitized_value;
            $this->all[$key] = $sanitized_value;
            $this->sanitized_data[$key] = $sanitized_value;
        }
        
        // Sanitize FILES data
        foreach ($_FILES as $key => $value) {
            $this->$key = $value;
            $this->all[$key] = $value;
            $this->sanitized_data[$key] = $value;
        }
        
        // Sanitize GET data
        foreach ($_GET as $key => $value) {
            $sanitized_value = $this->sanitizeValue($value);
            $this->$key = $sanitized_value;
            $this->all[$key] = $sanitized_value;
            $this->sanitized_data[$key] = $sanitized_value;
        }
    }

    /**
     * Sanitize individual values
     * @param mixed $value Input value
     * @return mixed Sanitized value
     */
    private function sanitizeValue($value)
    {
        if (is_array($value)) {
            return array_map([$this, 'sanitizeValue'], $value);
        }
        
        if (is_string($value)) {
            // Remove null bytes
            $value = str_replace(chr(0), '', $value);
            
            // Trim whitespace
            $value = trim($value);
            
            // Basic XSS protection
            $value = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
            
            // Remove potentially dangerous characters
            $value = preg_replace('/[<>"\']/', '', $value);
        }
        
        return $value;
    }

    /**
     * Check if file upload exists and is valid
     * @param string $keyName File input name
     * @return bool
     */
    public function hasFile($keyName)
    {
        if (!isset($_FILES[$keyName]) || empty($_FILES[$keyName]['name'])) {
            return false;
        }
        
        if (is_array($_FILES[$keyName]['name'])) {
            return !empty($_FILES[$keyName]['name'][0]);
        }
        
        return true;
    }

    /**
     * Get file information with validation
     * @param array $file File array
     * @return object|null File object or null if invalid
     */
    public function file($file)
    {
        if (!$this->validateFile($file)) {
            return null;
        }
        
        $filePath = $file['full_path'];
        return hyiplab_to_object(pathinfo($filePath, PATHINFO_ALL));
    }

    /**
     * Get multiple files with validation
     * @param string $key File input key
     * @return array Valid files array
     */
    public function files($key)
    {
        if (!isset($_FILES[$key])) {
            return [];
        }
        
        $files = $_FILES[$key];
        $fileGroup = [];
        
        if (is_array($files['name'])) {
            foreach ($files['name'] as $index => $file) {
                $fileData = [
                    'name'        => $files['name'][$index],
                    'full_path'   => $files['full_path'][$index],
                    'type'        => $files['type'][$index],
                    'tmp_name'    => $files['tmp_name'][$index],
                    'error'       => $files['error'][$index],
                    'size'        => $files['size'][$index],
                    'direct_file' => true
                ];
                
                if ($this->validateFile($fileData)) {
                    $fileGroup[] = $fileData;
                }
            }
        }
        
        return $fileGroup;
    }

    /**
     * Validate file upload
     * @param array $file File array
     * @return bool
     */
    private function validateFile($file)
    {
        // Check for upload errors
        if (!isset($file['error']) || $file['error'] !== UPLOAD_ERR_OK) {
            return false;
        }
        
        // Check file size (max 10MB)
        if (!isset($file['size']) || $file['size'] > 10 * 1024 * 1024) {
            return false;
        }
        
        // Check file type
        $allowed_types = ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'doc', 'docx', 'txt'];
        $file_extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        
        if (!in_array($file_extension, $allowed_types)) {
            return false;
        }
        
        return true;
    }

    /**
     * Get all sanitized data
     * @return array
     */
    public function all()
    {
        return $this->sanitized_data;
    }

    /**
     * Get raw data (unsanitized)
     * @return array
     */
    public function raw()
    {
        return $this->all;
    }

    /**
     * Validate request data
     * @param array $rules Validation rules
     * @param array $customMessages Custom error messages
     */
    public function validate($rules, $customMessages = [])
    {
        $validations = Validator::make($rules, $customMessages);
        
        if (!empty($validations['errors'])) {
            // Store old input for form repopulation
            foreach ($this->sanitized_data as $key => $value) {
                hyiplab_session()->flash('old_input_value_' . $key, $value);
            }
            
            hyiplab_session()->flash('errors', $validations['errors']);
            hyiplab_back();
        }
    }

    /**
     * Get specific input value
     * @param string $name Input name
     * @param mixed $default Default value
     * @return mixed
     */
    public function input($name, $default = null)
    {
        return $this->sanitized_data[$name] ?? $default;
    }

    /**
     * Check if input exists
     * @param string $name Input name
     * @return bool
     */
    public function has($name)
    {
        return isset($this->sanitized_data[$name]);
    }

    /**
     * Get only specific inputs
     * @param array $keys Input keys to retrieve
     * @return array
     */
    public function only($keys)
    {
        $result = [];
        foreach ($keys as $key) {
            if (isset($this->sanitized_data[$key])) {
                $result[$key] = $this->sanitized_data[$key];
            }
        }
        return $result;
    }

    /**
     * Get all inputs except specified ones
     * @param array $keys Input keys to exclude
     * @return array
     */
    public function except($keys)
    {
        $result = $this->sanitized_data;
        foreach ($keys as $key) {
            unset($result[$key]);
        }
        return $result;
    }

    /**
     * Magic getter for sanitized data
     * @param string $name Property name
     * @return mixed
     */
    public function __get($name)
    {
        return $this->sanitized_data[$name] ?? null;
    }

    /**
     * Check if property exists
     * @param string $name Property name
     * @return bool
     */
    public function __isset($name)
    {
        return isset($this->sanitized_data[$name]);
    }
}
