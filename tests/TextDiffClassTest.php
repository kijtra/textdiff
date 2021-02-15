<?php

namespace Kijtra\TextDiff\Tests;

use PHPUnit\Framework\TestCase;
use Kijtra\TextDiff\TextDiff;

class TextDiffClassTest extends TestCase
{
    protected $text1_en = "aaa bbb ccc\nddd eee fKf";
    protected $text2_en = "aaa bbb ccc\nddD eee fff";

    protected $text1_ja = "このバージョンのDiff実装については1978年にdiffの初期のプロトタイプ実装を行ったジェイムズ・W・ハントとの共著による論文の中で研究成果として発表されました。";
    protected $text2_ja = "このバージョンのdiff実装については1976年にdiffの初期のプロトタイプ実装を行ったジェームズ・W・ハントとの共著による論文の中で研究成果として発表された。";

    /**
     * @test
     */
    function output_count()
    {
        $diff = new TextDiff($this->text1_en, $this->text2_en);
        $data = $diff->getData();
        $this->assertCount(2, $data);
    }

    /**
     * @test
     */
    function output_data()
    {
        $diff = new TextDiff($this->text1_en, $this->text2_en);
        $data = $diff->getData();
        $this->assertSame($data[2]['words'][4], [
            'source' => 'fKf',
            'change' => 'fff'
        ]);
    }

    /**
     * @test
     */
    function japanese_word_count()
    {
        $diff = new TextDiff($this->text1_ja, $this->text2_ja);
        $data = $diff->getData();
        $this->assertCount(35, $data[1]['words']);
    }

    /**
     * @test
     */
    function japanese_word_diff()
    {
        $diff = new TextDiff($this->text1_ja, $this->text2_ja);
        $data = $diff->getData();
        $this->assertSame($data[1]['words'][18], [
            'source' => 'ジェイムズ',
            'change' => 'ジェームズ'
        ]);
    }

    /**
     * @test
     */
    function japanese_html()
    {
        $diff = new TextDiff($this->text1_en, $this->text2_en);
        $data = $diff->getHtml();
        $this->assertSame($data['change'], '<table class="differ -differ-table -differ-change"><tr class="-line -no-differ"><td class="-number">1</td><td class="-text">aaa bbb ccc</td></tr><tr class="-line -is-differ"><td class="-number">2</td><td class="-text"><span class="-word -change">ddD</span> eee <span class="-word -change">fff</span></td></tr></table>');
    }
}
