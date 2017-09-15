<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>API Tool</title>

    <!-- Bootstrap -->
    <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet">

    <!--[if lt IE 9]>
    <script src="https://cdn.bootcss.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://cdn.bootcss.com/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->

    <style>
        pre{
            margin-bottom: 0px;
        }
        .string { color: green; }
        .number { color: darkorange; }
        .boolean { color: blue; }
        .null { color: magenta; }
        .key { color: red; }
        .border-xs {
            border: 1px solid #ccc;
        }
        body {
            padding: 40px;
        }
        .error {
            color: red;
        }
        .block {
            display: block;
        }
        .pointer {
            cursor: pointer;
        }
    </style>
</head>
<body>
<div class="row">
    <nav class="navbar navbar-default">
        <div class="container-fluid">
            <!-- Brand and toggle get grouped for better mobile display -->
            <div class="navbar-header">
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="#">REDINFO</a>
            </div>

            <!-- Collect the nav links, forms, and other content for toggling -->
            <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                @if($username)
                    <div class="nav navbar-nav navbar-form navbar-right">当前用户：{{ $username }}</div>
                @else
                    <form class="navbar-form navbar-right" role="search" action="{{ route('api_login') }}" method="post">
                        {{ csrf_field() }}
                        <div class="form-group">
                            <input type="text" name="username" class="form-control" style="width: 260px;" placeholder="请输入姓名，以便管理你的历史API!">
                        </div>
                        <button type="submit" class="btn btn-default">Submit</button>
                    </form>
                @endif
            </div><!-- /.navbar-collapse -->
        </div><!-- /.container-fluid -->
    </nav>
</div>
<h1>自定义测试API</h1>
<p>自定义测试API适用于开发阶段，您可以自定义接口地址和返回内容，以便快速开发！</p>
<hr>
<div class="row">
    <form action="{{ route('api_save') }}" method="post" id="apiform"  class="col-xs-8">
        {{ csrf_field() }}
        <input type="hidden" name="username" value="{{ $username }}" required>
        <input type="hidden" name="id" value="">
        <label id="username-error" class="error bg-warning block" style="padding: 10px; display: none;" for="username">您需要在右上角填入用户名</label>
        <div class="form-group">
            <label for="name">API名称</label>
            <input type="text" class="form-control" id="name" name="name" placeholder="API名称" required>
        </div>
        <div class="form-group">
            <label for="url">API地址</label>
            <input type="text" class="form-control" id="path" name="path" placeholder="API地址" required>
        </div>
        <div class="form-group">
            <label for="response">接口返回内容</label>
            <div class="row">
                <div class="col-xs-12">
                    <p class="bg-info" style="padding: 15px 10px;">
                        <span class="glyphicon glyphicon-info-sign" aria-hidden="true"></span>
                        json格式要求所有的key和字符型value都要用<code>双引号</code>包裹，使用<code>单引号</code>是最常见的问题
                    </p>
                </div>
                <div class="col-xs-5" style="min-height: 200px;">
                    <textarea name="response" class="form-control" style="padding: 0px;" rows="10" id="jsonstring" placeholder="返回值内容" required></textarea>
                </div>
                <div class="col-xs-2 text-center">
                    <div class="btn btn-primary checkjson">校验格式</div>
                </div>
                <div class="col-xs-5" style="min-height: 200px;">
                    <pre id='json-container' style="min-height: 200px;"></pre>
                </div>
                <div class="col-xs-12">
                    <p class="bg-danger error-container" style="padding: 15px 10px; display: none;">
                    </p>
                </div>
            </div>
        </div>
        <div class="form-group">
            <div class="col-xs-offset-2 col-xs-8">
                <button type="submit" class="btn btn-success btn-lg btn-block">提交</button>
            </div>
        </div>
    </form>
    <div class="col-xs-4 text-center" style="border: 1px solid #ccc; min-height: 500px;">
        <div class="row">
            <p class=" col-xs-12 bg-primary" style="padding: 10px;">接口列表</p>
        </div>
        <ul class="list-group api-list">
            @foreach($apis as $api)
                <li class="list-group-item pointer" data-info="{{ json_encode($api) }}">{{ $api->name }}</li>
            @endforeach
        </ul>
    </div>
</form>
</div>

<script src="{{ asset('js/jquery-3.2.1.min.js') }}"></script>
<script src="{{ asset('js/bootstrap.min.js') }}"></script>
<script src="{{ asset('js/jquery.validate.min.js') }}"></script>
<script src="{{ asset('js/jquery.form.min.js') }}"></script>

<script>
    function syntaxHighlight(json) {
        if (typeof json != 'string') {
            json = JSON.stringify(json, undefined, 4);
        }
        json = json.replace(/&/g, '&').replace(/</g, '<').replace(/>/g, '>');
        return json.replace(/("(\\u[a-zA-Z0-9]{4}|\\[^u]|[^\\"])*"(\s*:)?|\b(true|false|null)\b|-?\d+(?:\.\d*)?(?:[eE][+\-]?\d+)?)/g, function(match) {
            var cls = 'number';
            if (/^"/.test(match)) {
                if (/:$/.test(match)) {
                    cls = 'key';
                } else {
                    cls = 'string';
                }
            } else if (/true|false/.test(match)) {
                cls = 'boolean';
            } else if (/null/.test(match)) {
                cls = 'null';
            }
            return '<span class="' + cls + '">' + match + '</span>';
        });
    }
    $(function() {
        $(".checkjson").on('click', function() {
            var jsonstring = $("#jsonstring").val();
            jsonstring = jsonstring.replace(/\ +/g, "");
            jsonstring = jsonstring.replace(/[ ]/g, "");
            jsonstring = jsonstring.replace(/[\r\n]/g, "");
            try {
                $("#json-container").html(syntaxHighlight(JSON.parse(jsonstring)));
                $("#jsonstring").val(jsonstring);
                $(".error-container").hide();
            } catch (e) {
                $(".error-container").html(e).show();
            }
        });
        
        $(".api-list li").on('click', function() {
            var info = $(this).data('info');
            $("#apiform [name='id']").val(info.id);
            $("#apiform [name='name']").val(info.name);
            $("#apiform [name='path']").val(info.path);
            $("#apiform [name='response']").val(info.response);
        });
        
        $('#apiform').validate({
            ignore: '',
            messages: {
                username: "您需要在右上角填入用户名",
            },
            submitHandler:function(form){
                $(form).ajaxSubmit({
                    success: function(result) {
                        if (result && result.status == 'success') {
                            alert('保存成功');
                            location.reload();
                        } else {
                            alert(result.msg);
                        }
                    }
                });
                return false;
            }
        })
    })
</script>
</body>
</html>