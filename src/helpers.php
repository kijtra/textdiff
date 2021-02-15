<?php

use Kijtra\TextDiff\TextDiff;

if (!function_exists('text_diff')) {
    /**
     * Diff結果の取得
     *
     * @param string  $text1
     * @param string  $text2
     * @return array
     */
    function text_diff($text1, $text2)
    {
        $diff = new TextDiff($text1, $text2);
        $output = $diff->getData();
        return $output;
    }
}

if (!function_exists('text_diff_html')) {
    /**
     * Diff結果をHTMLとして取得
     *
     * @param string  $text1
     * @param string  $text2
     * @return array
     */
    function text_diff_html($text1, $text2)
    {
        $diff = new TextDiff($text1, $text2);
        $output = $diff->getHtml();
        return $output;
    }
}
