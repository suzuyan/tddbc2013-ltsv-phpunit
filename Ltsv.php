<?php
class Ltsv
{
    private $_values;

    private static $_escape_patterns = [
        ":" =>  "\:",
        "\t" => "\\t",
        "\n" => "\\n"
    ];
    
    function set($key, $value)
    {
        if ($key === null)   throw new InvalidArgumentException('キーにNULLが設定されています');
        if ($key === "")     throw new InvalidArgumentException('キーに空文字列が設定されています');
        if ($value === null) throw new InvalidArgumentException('値にNULLが設定されています');

        $old_value = null;
        if (isset($this->_values[$key])) {
            $old_value = $this->_values[$key];
            unset($this->_values[$key]);
        }
        $this->_values[$key] = $value;
        return $old_value;
    }

    function get($key)
    {
        return $this->_values[$key];
    }

    function dump()
    {
        $labels = [];
        foreach ($this->_values as $key => $value) {
            $key      = strtr($key,   self::$_escape_patterns);
            $value    = strtr($value, self::$_escape_patterns);
            $labels[] = "{$key}:{$value}";
        }
        return join("\t", $labels) . "\n";
    }

    public static function parse($dump_str)
    {
        // TODO: リファクタリング
        $dump_str = rtrim($dump_str);

        $ltsv = new Ltsv();
        $labels = explode("\t", $dump_str);
        foreach ($labels as $label) {
            list($key, $value) = explode(":", $label);
            $ltsv->set($key, $value);
        }
        return $ltsv;
    }
}
