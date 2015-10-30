<?php
if (array_key_exists('source', $_POST) && array_key_exists('change', $_POST)) {
    include(dirname(__FILE__).'/TextDiff.php');
    $diff = new TextDiff($_POST['source'], $_POST['change']);
    $html = $diff->getHtml();
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
            <h1>Text Diff demo</h1>
            <p></p>
        </div>

        <div class="row">
            <div class="col-sm-6">
                <div class="form-group">
                    <label for="source">Original:</label>
                    <textarea id="source" class="form-control" rows="20"></textarea>
                </div>
            </div>

            <div class="col-sm-6">
                <div class="form-group">
                    <label for="source">Change:</label>
                    <textarea id="change" class="form-control" rows="20"></textarea>
                </div>
            </div>
        </div>

        <div class="row text-center">
            <button type="button" id="button" class="btn btn-lg btn-primary">Diff!</button>
        </div>

        <div class="row output">
            <div id="out_source" class="col-sm-6"></div>
            <div id="out_change" class="col-sm-6"></div>
        </div>
    </div>
<script src="https://code.jquery.com/jquery-1.11.3.min.js"></script>
<script type="text/javascript">
var ajax;
var source = $('#source');
var change = $('#change');
var out_source = $('#out_source');
var out_change = $('#out_change');
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
        }
    });
});
</script>
</body>
</html>