<?php
if (array_key_exists('source', $_POST) && array_key_exists('change', $_POST)) {
    include(dirname(__FILE__).'/TextDiff.php');
    $diff = new TextDiff($_POST['source'], $_POST['change']);
    $html = $diff->getHtml();
    $html['data'] = var_export($diff->getData(), true);
    echo json_encode($html);
    exit;
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="UTF-8">
<title>TextDiff demo</title>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css" integrity="sha512-dTfge/zgoMYpP7QbHy4gWMEGsbsdZeCXz7irItjcC3sPUFtf0kuFbDz/ixG7ArTxmDjLXDmezHubeNikyKGVyQ==" crossorigin="anonymous">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/octicons/3.1.0/octicons.min.css">
<style media="screen">
.differ {
    width:100%;
    margin:0;
    background:#f9f9f9;
    border:1px solid #ddd;
    font-size:inherit;
    border-collapse:separate;
    border-spacing:0;
    border:1px solid #ddd;
}

.differ td {
    padding:1px 4px;
    font-size:inherit;
    border-top:1px dotted #eee;
    border-left:1px solid #ddd;
}

.differ .-line:first-child td {
    border-top:0;
}

.differ .-line td:first-child {
    border-left:0;
}

.differ .-number {
    width:5%;
    padding-top:.4em;
    white-space:nowrap;
    text-align:right;
    vertical-align:top;
    font-size:80%;
    font-family:Arial;
    border-top:1px solid #e6e6e6;
    color:#999;
}

.differ .-text {
    padding-left:8px;
    border-left:3px double #ddd;
    background:#fff;
}

.differ .-is-differ .-text {
    background:#FFFBE6;
}

.differ .-no-differ .-text {
    color:#777;
}

.differ .-word {
    display:inline-block;
    vertical-align:middle;
    /*font-weight:bold;*/
}

.differ .-word.-source {
    color:green;
    background: #dfd;
}

.differ .-word.-change {
    color:red;
    background: #fdd;
}

.output {
    margin-top:40px;
}
</style>
</head>
<body>
    <div class="container">
        <div class="page-header">
            <div class="row">
                <div class="col-sm-7">
                    <h1><span class="mega-octicon octicon-diff"></span> Text Diff demo</h1>
                </div>
                <div class="col-sm-5 text-right" style="margin-top:30px;">
                    <iframe src="https://ghbtns.com/github-btn.html?user=kijtra&repo=textdiff&type=star&count=true" frameborder="0" scrolling="0" width="100px" height="20px"></iframe>
                <iframe src="https://ghbtns.com/github-btn.html?user=kijtra&repo=textdiff&type=watch&count=true&v=2" frameborder="0" scrolling="0" width="100px" height="20px"></iframe>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-6">
                <div class="form-group">
                    <label for="source">Original:</label>
                    <textarea id="source" class="form-control" rows="10"></textarea>
                </div>
            </div>

            <div class="col-sm-6">
                <div class="form-group">
                    <label for="source">Change:</label>
                    <textarea id="change" class="form-control" rows="10"></textarea>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-4 col-md-3">
                <div class="form-group">
                    <select id="sample" class="form-control">
                        <option value="">Input Sample Text</option>
                        <option value="ja">日本語</option>
                        <option value="en">English</option>
                    </select>
                </div>
            </div>
            <div class="col-sm-4 col-md-6 text-center">
                <button type="button" id="button" class="btn btn-lg btn-primary"><span class="octicon octicon-diff"></span> Diff!</button>
            </div>
        </div>

        <div class="row output">
            <div id="out_source" class="col-sm-6"></div>
            <div id="out_change" class="col-sm-6"></div>
        </div>

        <div class="row data" id="out_data" style="display:none">
            <hr>
            <strong>PHP Response Data:</strong>
            <pre></pre>
        </div>

        <hr>

        <div class="text-center text-muted">
            &copy; kijtra
        </div>
    </div>
<script src="https://code.jquery.com/jquery-1.11.3.min.js"></script>
<script type="text/javascript">
var ajax,
source = $('#source'),
change = $('#change'),
out_source = $('#out_source'),
out_change = $('#out_change'),
out_data = $('#out_data'),
data = out_data.find('pre:first');

$('#button').on('click', function() {
    if (ajax) {
        ajax.abort();
    }

    ajax = $.ajax({
        url: location.href,
        type: 'post',
        dataType: 'json',
        data: {
            source: source.val(),
            change: change.val()
        },
        success: function(res) {
            if (!res) {
                var alert = '<div class="alert alert-warning">No Result</div>';
                out_source.html(alert);
                out_change.html(alert);
            } else {
                if ('source' in res) {
                    out_source.html(res.source);
                } else {
                    out_source.empty();
                }

                if (res.change) {
                    out_change.html(res.change);
                } else {
                    out_change.empty();
                }

                if (res.data) {
                    out_data.show();
                    data.html(res.data);
                } else {
                    out_data.hide();
                }
            }
        },
        error: function(res) {
            var alert = '<div class="alert alert-danger">' + res.responseText + '</div>';
            out_source.html(alert);
            out_change.html(alert);
        }
    });
});

var samples = {
    ja: {
        source: [
            "diffプログラムは1970年代初頭に、ニュージャージー州マーレイ・ヒルにあったAT&Tのベル研究所で開発されたUNIX上で開発されたもので、1974年のUNIX第5版に同梱され最初に公開された最終バージョンはダグラス・マキロイが開発したものであった。",
            "このバージョンのDiff実装については1978年にdiffの初期のプロトタイプ実装を行ったジェイムズ・W・ハントとの共著による論文の中で研究成果として発表されました。",
            "",
            "マキロイ氏の研究はスティーブ・ジョンソンによるGCOS上の比較プログラムやマイク・レスクによるproofプログラムの先行する研究の影響を受けたものだった。",
            "",
            "Proofは元々UNIX上で開発されdiffのような行単位の変更箇所を出力するもので、行の挿入・削除を示すのにブラケットによる出力も用いられていた。",
            "これらの初期のアプリケーションではヒューリスティクスが使われていたが、その出力の安定性は低いものだった。",
            "ファイル比較ツールの潜在的な有用性に触発されたマキロイはより頑健で様々な対象に使用でき、かつPDP-11の制約上でもうまく動作するようなツールの研究、設計に取り組んだ。",
            "",
            "このアプローチによる研究にはベル研の同僚であったアルフレッド・エイホ、エリオット・ピンソン、ジェファソン・ウルマン、ハロルド・S・ストーンの研究者らとの協力を受けて取り組んだ。"
        ],
        change: [
            "diffプログラムは1970年代初頭に、ニュージャージー州マーレイ・ヒルにあったAT&Tのベル研究所で開発されたUNIX上で開発されたもので、1974年のUNIX第5版に同梱され最初に公開された最終バージョンはダグラス・マキロイが開発したものであった。",
            "このバージョンのdiff実装については1976年にdiffの初期のプロトタイプ実装を行ったジェームズ・W・ハントとの共著による論文の中で研究成果として発表された。",
            "",
            "マキロイの研究はスティーブ・ジョンソンによるGCOS上の比較プログラムやマイク・レスクによるproofプログラムの先行する研究の影響を受けたものだった。",
            "",
            "proofは元々UNIX上で開発されdiffのような行単位の変更箇所を出力するもので、行の挿入・削除を示すのにブラケット（<、>）による出力も用いられていた。",
            "これらの初期のアプリケーションではヒューリスティクスが使われていたが、その出力の安定性は低いものだった。",
            "ファイル比較ツールの潜在的な有用性に触発されたマキロイはより頑健で様々な対象に使用でき、かつPDP-11のハードウェア的な制約上でもうまく動作するようなツールの研究、設計に取り組んだ。",
            "",
            "このアプローチによる研究にはベル研の同僚であったアルフレッド・エイホ、エリオット・ピンソン、ジェファーソン・ウルマン、ハロルド・S・ストーンの研究者らとの協力を受けて取り組んだ。"
        ]
    },
    en: {
        source: [
            "Lorem ipsum dolor sit amet, ullum inani nec ne, pri meis omnesque ex.",
            "Nobis nonumy maiestatis ut pro.",
            "Ea nostro mentitum detraxit pro, has ut malis graece petentium.",
            "Cibo mazim invidunt ad vix.",
            "Erat omittam delicata pro ut, an sale dicta persecuti sea.",
            "Est eu nonumy graece. Est veritus gubergren an.",
            "Laudem persius dignissim et per.",
            "Vix ei fierent insolens. Pri labore sententiae ei.",
            "Possim liberavisse definitiones his ad, ea qui meliore antiopam intellegebat, no laboramus similique usu.",
            "Eu vel euripidis rationibus reprimique, est no vidisse definiebas, regione oportere usu et. Dico soluta elaboraret quo an, ad paulo audiam pro."
        ],
        change: [
            "Lorem ipsum dolor sit amet, ullum inani nec ne, pri meis omnesque ea.",
            "Nobis nonumy maiestatis ut pro.",
            "Ea nostro mentitum interesset pro, has ut malis graece petentium.",
            "Cibo mazim invidunt ad his.",
            "Erat omittam delicata pro ut, an sale dicta persecuti sea.",
            "Est eu nonumy graece. Est veritus gubergren an.",
            "Laudem aeque eloquentiam, nihil noster essent ex per.",
            "Vix ei fierent insolens. Pri labore sententiae ei.",
            "Possim liberavisse definitiones his ad, ea qui meliore antiopam intellegebat, no laboramus similique usu.",
            "Eu vel euripidis rationibus disputando, est no vidisse definiebas, pertinacia primis usu et. Dico soluta elaboraret quo an, ad paulo audiam pro."
        ]
    }
};
$('#sample').on('change', function() {
    var source_text = '', change_text = '';
    if (samples[this.value]) {
        source_text = samples[this.value].source.join("\n");
        change_text = samples[this.value].change.join("\n");
    }
    source.val(source_text);
    change.val(change_text);
});
</script>
</body>
</html>
