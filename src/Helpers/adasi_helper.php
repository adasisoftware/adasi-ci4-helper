<?php
/**
 * Adasi Express Helper
 * Utilities package used in Adasi Software's CodeIgniter 4 projects
 * 
 * Helpers
 * 
 * @author Adasi Software <ricardo@adasi.com.br>
 * @link https://www.adasi.com.br
 */

if (!function_exists('hashEncode')) {
    /**
     * Codifica uma string em base64 de forma legível ao browser.
     *
     * @param string $data
     * @return string
     */
    function hashEncode($data)
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }
}

if (!function_exists('hashDecode')) {
    /**
     * Decodifica uma string codificada com base64url_encode.
     *
     * @param string $data
     * @return string
     */
    function hashDecode($data)
    {
        return base64_decode(str_pad(strtr($data, '-_', '+/'), strlen($data) % 4, '=', STR_PAD_RIGHT));
    }
}

if (! function_exists('maskCpfCnpj')) {

    /**
     * Coloca uma máscara em um cpf ou cpnj
     * 
     * @param string $val
     * @return string
     */
    function maskCpfCnpj($val) {
        return strlen($val) == 11 ? 
            preg_replace("/(\d{3})(\d{3})(\d{3})(\d{2})/", '${1}.${2}.${3}-${4}', $val) : 
            preg_replace("/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/", '${1}.${2}.${3}/${4}-${5}', $val);
    }
}


if (!function_exists('maskPhone')) {
    /**
     * Formata uma string com máscara de telefone
     *
     * @param string $val
     * @return string
     */
    function maskPhone(string $val)
    {
        return $val ? preg_replace('/(\d{2})(\d*)(\d{4})/', '($1) $2-$3', $val) : '';
    }
}

if (! function_exists('toLowerCase')) {
    
    /**
     * Transforma uma string em minúsculo repeitando o utf8
     *
     * @param string $input
     * @return string
     */
    function toLowerCase($input)
    {
        return mb_strtolower(preg_replace('/( ){2,}/', '$1', trim($input)), 'UTF8');
    }
}

if (! function_exists('toUpperCase')) {

    /**
     * Transforma uma string em maiúsculo repeitando o utf8
     *
     * @param string $input
     * @return string
     */
    function toUpperCase($input)
    {
        return mb_strtoupper(preg_replace('/( ){2,}/', '$1', trim($input)), 'UTF8');
    }
}

if (! function_exists('unmaskString')) {

    /**
     * Retira caracteres especiais de uma string
     *
     * @param $val
     * @return string|string[]|null
     */
    function unmaskString($val)
    {
        return preg_replace("/[^a-zA-Z 0-9]+/", "", $val);
    }
}

if (! function_exists('unmaskFloat'))
{
    /**
     * Transforma uma string formato monetário em float
     *
     * @param string $val
     * @return float
     */
    function unmaskFloat(string $value)
    {
        if ($value)
            return (float)preg_replace("/[^0-9\.,]+/", "", str_replace(',', '.', str_replace('.', '', $value)));
    }
}

if (! function_exists('emptyToNull')) {
    /**
     * Converte elementos de um array vazio em null
     *
     * @param array $val
     * @return array|null
     */
    function emptyToNull($val)
    {
        foreach ($val as $name => $value) {
            if (is_string($value) && trim($value) === "") {
                $val[$name] = null;
            }

            if (is_array($value)) {
                $val[$name] = emptyToNull($value);
            }
        }

        return $val;
    }
}

if (! function_exists('removeWhiteSpace')) {

    /**
     * Remove espaços desnecessários de uma string
     * 
     * @param string $val
     * @return string
     */
    function removeWhiteSpace($val)
    {
        return preg_replace('/\s+/', ' ',$val);
    }
}

if (! function_exists('capitalizeName')) {

    /**
     * Faz a capitalização correta para nomes
     *
     * @param string $val
     * @return string
     */
    function capitalizeName($string)
    {
        $word_splitters = array(' ', '-', "O'", "L'", "D'", 'Pe.', 'Mc');
        $lowercase_exceptions = array('da', 'das', 'de', 'do', 'dos', "l'", 'e', 'em', 'com');
        $uppercase_exceptions = array('III', 'IV', 'VI', 'VII', 'VIII', 'IX');

        $string = toLowerCase($string);
        foreach ($word_splitters as $delimiter) {
            $words = explode($delimiter, $string);
            $newwords = array();

            foreach ($words as $word) {
                if (in_array(toUpperCase($word), $uppercase_exceptions)) {
                    $word = toUpperCase($word);
                } elseif (!in_array($word, $lowercase_exceptions)) {
                    $word = ucfirst($word);
                }

                $newwords[] = $word;
            }

            if (in_array(toLowerCase($delimiter), $lowercase_exceptions)) {
                $delimiter = toLowerCase($delimiter);
            }

            $string = join($delimiter, $newwords);
        }
        return removeWhiteSpace( $string );
    }
}

if (! function_exists('parseDateSQL')) {
    /**
     * Formata uma data em formato padrão SQL (YYYY-MM-DD)
     *
     * @param string $val
     * @return string
     */
    function parseDateSQL(string $val)
    {
        if (!empty($val)) {
            $p_dt = explode('/', $val);
            $data_sql = $p_dt[2].'-'.$p_dt[1].'-'.$p_dt[0];

            return $data_sql;
        }
    }
}

if (! function_exists('toPostgresArray'))
{
    /**
     * Prepara um array em PHP para Postgres
     *
     * @param array $set
     * @return string
     */
    function toPostgresArray($set) {
        settype($set, 'array'); // can be called with a scalar or array
        $result = array();
        foreach ($set as $t) {
            if (is_array($t)) {
                $result[] = toPostgresArray($t);
            } else {
                $t = str_replace('"', '\\"', $t); // escape double quote
                if (! is_numeric($t)) // quote only non-numeric values
                    $t = '"' . $t . '"';
                $result[] = $t;
            }
        }
        return '{' . implode(",", $result) . '}'; // format
    }
}

if (! function_exists('postgresArrayParse'))
{
    function postgresArrayParse($literal)
    {
        if ($literal == '') return;
        preg_match_all('/(?<=^\{|,)(([^,"{]*)|\s*"((?:[^"\\\\]|\\\\(?:.|[0-9]+|x[0-9a-f]+))*)"\s*)(,|(?<!^\{)(?=\}$))/i', $literal, $matches, PREG_SET_ORDER);
        $values = [];
        foreach ($matches as $match) {
            $values[] = $match[3] != '' ? stripcslashes($match[3]) : (strtolower($match[2]) == 'null' ? null : $match[2]);
        }
        return $values;
    }
}