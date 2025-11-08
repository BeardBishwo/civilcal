    public static function bulkSet($settings)
    {
        // Set each setting individually (no transaction needed for simple settings)
        foreach ($settings as $key => $value) {
            // Determine type based on value
            $type = 'string';
            if (is_bool($value)) {
                $type = 'boolean';
            } elseif (is_int($value)) {
                $type = 'integer';
            } elseif (is_array($value)) {
                $type = 'json';
            }
            
            self::set($key, $value, $type);
        }
        
        return true;
    }
