<?php
namespace Kijtra\TextDiff;

/**
 * Copyright (c) 2020 kijtra
 * MIT license
 * https://github.com/kijtra/textdiff
 */
class TextDiff
{
    private static $word_reg = null;

    private $old = null;
    private $new = null;

    private $is_differ = false;

    private $data = null;
    private $table_html = null;
    private $list_html = null;

    function __construct($old, $new)
    {
        $this->setWordReg();
        $this->old = $old;
        $this->new = $new;

        $this->data = $this->getDiffData();
    }

    public function getDiffArray($old, $new) {
        $matrix = array();
        $max = $old_max = $new_max = 0;
        foreach($old as $old_key => $old_value) {
            foreach(array_keys($new, $old_value) as $new_key) {
                if (!empty($matrix[$old_key - 1][$new_key - 1])) {
                    $matrix[$old_key][$new_key] = $matrix[$old_key - 1][$new_key - 1] + 1;
                } else {
                    $matrix[$old_key][$new_key] = 1;
                }

                if ($matrix[$old_key][$new_key] > $max) {
                    $max = $matrix[$old_key][$new_key];
                    $old_max = $old_key + 1 - $max;
                    $new_max = $new_key + 1 - $max;
                }
            }
        }

        if($max == 0) {
            if ($old == $new) {
                return $old;
            } else {
                return array(
                    array(
                        'source' => $old,
                        'change' => $new
                    )
                );
            }
        } else {
            $arr1 = $this->getDiffArray(array_slice($old, 0, $old_max), array_slice($new, 0, $new_max));
            $arr2 = array_slice($new, $new_max, $max);
            $arr3 = $this->getDiffArray(array_slice($old, $old_max + $max), array_slice($new, $new_max + $max));

            return array_merge($arr1, $arr2, $arr3);
        }
    }

    private function getDiffData()
    {
        if (empty($this->old) && empty($this->new)) {
            return null;
        }

        $line_diff = $this->getLineDiff($this->old, $this->new);

        // ダミー行を除去
        $line_diff = array_filter($line_diff, function($val){return (false !== $val);});
        $line_diff = array_values($line_diff);

        $lines = array();
        $line_count = 1;
        foreach($line_diff as $line) {
            if (is_array($line) && array_key_exists('change', $line)) {
                $this->is_differ = true;
                $words = $this->getWordDiff($line['source'], $line['change']);
                $lines[$line_count] = array(
                    'line' => $line_count,
                    'differ' => true,
                    'source' => implode('', $line['source']),
                    'change' => implode('', $line['change']),
                    'words' => $words,
                );

                ++$line_count;
            } else {
                $lines[$line_count] = array(
                    'line' => $line_count,
                    'differ' => false,
                    'source' => $line,
                    'change' => $line,
                    'words' => null
                );

                ++$line_count;
            }
        }

        return $lines;
    }

    private function getLineDiff($old, $new)
    {
        $old = str_replace("\r\n", "\n", $old);
        $new = str_replace("\r\n", "\n", $new);

        // 差異の行が続くと別の行にならない場合があるため、
        // 各行の後にダミー行（false）を入れる
        $old_lines = array();
        foreach(explode("\n", $old) as $val) {
            $old_lines[] = $val;
            $old_lines[] = false;
        }

        $new_lines = array();
        foreach(explode("\n", $new) as $val) {
            $new_lines[] = $val;
            $new_lines[] = false;
        }

        return $this->getDiffArray($old_lines, $new_lines);
    }

    private function getWordDiff($old_array, $new_array)
    {
        $words = array();
        $count = 0;
        foreach($old_array as $key => $source) {
            $change = (!empty($new_array[$key]) ? $new_array[$key] : '');
            $s_source = $this->getSeparatedWord($source);
            $s_change = $this->getSeparatedWord($change);
            $diff = $this->getDiffArray($s_source, $s_change);

            foreach($diff as $k => $val) {
                if (is_array($val) && array_key_exists('change', $val)) {
                    $words[] = array(
                        'source' => implode('', $val['source']),
                        'change' => implode('', $val['change']),
                    );
                    ++$count;
                } else {
                    $before = $count -1;
                    if(array_key_exists($before, $words) && is_array($words[$before]) && !array_key_exists('source', $words[$before])) {
                        $words[$before] .= $val;
                    } else {
                        $words[] = $val;
                        ++$count;
                    }
                }
            }
        }

        return $words;
    }

    private function setWordReg()
    {
        if (null === self::$word_reg) {
            $regs = array(
                '々〇〻\x{3220}-\x{3244}\x{3280}-\x{32B0}\x{3400}-\x{9FFF}\x{F900}-\x{FAFF}\x{20000}-\x{2FFFF}', // 漢字
                'ぁ-ん～ー', // ひらがな
                'ァ-ヴ～ー', // カタカナ
                'ｦ-ﾟ', // 半角カナ
                '\!-\~', // 英数記号
            );

            $reg = '['.implode(']+|[', $regs).']+';
            $reg .= '|[^'.implode('', $regs).']+';

            self::$word_reg = $reg;
        }
    }

    private function getSeparatedWord($str){
        preg_match_all('/'.self::$word_reg.'/iu', $str, $matches);
        return $matches[0];
    }


    public function isDiffer()
    {
        return $this->is_differ;
    }

    public function getData()
    {
        return $this->data;
    }

    public function getHtml()
    {
        return $this->getTableHtml();
    }

    public function getTableHtml()
    {
        if (empty($this->data)) {
            return null;
        } elseif(!empty($this->table_html)) {
            return $this->table_html;
        }

        $source = '<table class="differ -differ-table -differ-source">';
        $change = '<table class="differ -differ-table -differ-change">';
        foreach($this->data as $line) {
            if (empty($line['line'])) {
                continue;
            }

            if (empty($line['differ'])) {
                $html = '<tr class="-line -no-differ">';
                $html .= '<td class="-number">'.$line['line'].'</td>';
                $html .= '<td class="-text">'.(empty($line['source']) ? '&nbsp;' : $line['source']).'</td>';
                $source .= $html;
                $change .= $html;
            } else {
                $html = '<tr class="-line -is-differ">';
                $html .= '<td class="-number">'.$line['line'].'</td>';
                $html .= '<td class="-text">';
                $source .= $html;
                $change .= $html;

                foreach($line['words'] as $val) {
                    if (is_array($val) && array_key_exists('source', $val)) {
                        $source .= '<span class="-word -source">'.$val['source'].'</span>';
                    } else {
                        $source .= $val;
                    }
                }

                foreach($line['words'] as $val) {
                    if (is_array($val) && array_key_exists('change', $val)) {
                        $change .= '<span class="-word -change">'.$val['change'].'</span>';
                    } else {
                        $change .= $val;
                    }
                }

                $html = '</td>';
                $source .= $html;
                $change .= $html;
            }

            $html = '</tr>';
            $source .= $html;
            $change .= $html;
        }

        $html = '</table>';
        $source .= $html;
        $change .= $html;

        return $this->table_html = array(
            'source' => $source,
            'change' => $change,
        );
    }

    public function getListHtml()
    {
        if (empty($this->data)) {
            return null;
        } elseif(!empty($this->list_html)) {
            return $this->list_html;
        }

        $source = '<ol class="differ -differ-list -differ-source">';
        $change = '<ol class="differ -differ-list -differ-change">';
        foreach($this->data as $line) {
            if (!$line['differ']) {
                $html = '<li class="-line -no-differ">';
                $html .= '<div class="-text">'.(empty($line['source']) ? '&nbsp;' : $line['source']).'</div>';
                $source .= $html;
                $change .= $html;
            } else {
                $html = '<li class="-line -is-differ">';
                $html .= '<div class="-text">';
                $source .= $html;
                $change .= $html;

                foreach($line['words'] as $val) {
                    if (empty($val['change'])) {
                        $source .= $val;
                    } else {
                        $source .= '<span class="-word -source">'.$val['source'].'</span>';
                    }
                }

                foreach($line['words'] as $val) {
                    if (empty($val['change'])) {
                        $change .= $val;
                    } else {
                        $change .= '<span class="-word -change">'.$val['change'].'</span>';
                    }
                }

                $html = '</div>';
                $html .= '</div>';
                $source .= $html;
                $change .= $html;
            }

            $html = '</li>';
            $source .= $html;
            $change .= $html;
        }

        $html = '</ol>';
        $source .= $html;
        $change .= $html;

        return $this->list_html = array(
            'source' => $source,
            'change' => $change,
        );
    }
}