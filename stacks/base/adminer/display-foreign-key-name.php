<?php

/**
 * Adminer plugin that display the first CHAR/VARCHAR column of the foreign key
 *
 * @category Plugin
 * @link http://www.adminer.org/plugins/#use
 * @author Bruno VIBERT <http://www.netapsys.fr>
 * @modified by Peter Hostačný <hostacny.peter AT gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU General Public License, version 2 (one or other)
 */
class AdminerDisplayForeignKeyName
{

    protected static $_valueCache = [];

    /**
     * Get a cache entry
     */
    protected static function _getCache($key)
    {
        if (array_key_exists($key, self::$_valueCache)) {
            return self::$_valueCache[$key];
        }

        return false;
    }

    /**
     * Set a cache entry
     */
    protected static function _setCache($key, $value)
    {
        self::$_valueCache[$key] = $value;
    }

    /**
     * Render a foreign key value
     */
    function selectVal($val, $link, $field, $original)
    {
        $return = ($val === null ? "<i>NULL</i>" :
            (preg_match("~char|binary~", $field["type"]) && !preg_match("~var~", $field["type"]) ? "<code>$val</code>" :
                $val));
        if (preg_match('~blob|bytea|raw|file~', $field["type"]) && !Adminer\is_utf8($val)) {
            $return = Adminer\lang('%d byte(s)', strlen($original));
        } else {
            parse_str(substr($link, 1), $params);

            if (
                true == is_array($params) &&
                true == array_key_exists('where', $params) &&
                !preg_match("~var~", $field["type"])
            ) {
                $where = [];
                foreach ($params['where'] as $param) {
                    //$where[] = join(' ', $param);
                   $where[] = Adminer\escape_key($param['col']) . ' ' . $param['op'] . ' ' . Adminer\q($param['val']);
                }

                // Find the first char/varchar/enum field to display
                $fieldName = false;
                foreach (Adminer\fields($params['select']) as $field) {
                    if (true == in_array($field['type'], ['char', 'varchar', 'enum'])) {
                        $fieldName = $field['field'];
                        break;
                    }
                }

                if (false !== $fieldName) {
                    $query = sprintf(
                        'SELECT %s FROM %s WHERE %s LIMIT 1',
                        $fieldName,
                        $params['select'],
                        join(' AND ', $where)
                    );

                    $return = self::_getCache(md5($query));
                    if (false === $return) {
                        $result = Adminer\connection()->query($query);
                        if ($result && $result->num_rows == 1) {
                            $row = $result->fetch_assoc();
                            $value = $row[$fieldName];

                            if (strlen($value) > 50) {
                                $value = substr($value, 0, 50) . '...';
                            }

                            $return = sprintf('<strong>[%s]</strong> %s', $original, $value);
                            self::_setCache(md5($query), $return);
                        } else {
                            $return = sprintf('<strong>[%s]</strong>', $original);
                        }
                    }
                }
            }
        }

        return ($link ?
            "<a href='" . Adminer\h($link) . "'" . (Adminer\is_url($link) ? " rel='noreferrer'" : "") . ">$return</a>" :
            $return);
    }
}
