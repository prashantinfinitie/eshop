<?php
class Test extends CI_Controller
{
    public $data;
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        get_cod_limits();


    }
    public function tax(){
        recalculateTaxedPrice([]);
    }

    public function test_link()
    {
?>
        <html>

        <head>
            <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
            <title>Insert title here</title>
            <script type="text/javascript">
                function test2() {
                    var di = document.getElementById("di");
                    di.innerHTML = "app have not installed";
                }

                function newOpen() { //184 064 323 438
                    var di = document.getElementById("di");
                    di.innerHTML = "app have installed";
                    var ifc = document.getElementById("ifc");
                    ifc.innerHTML = "<iframe src='mm://xxxxx?a=b&c=d' onload='test2()'></iframe>";
                    return false;
                }
            </script>
        </head>

        <body>
            <a href="#" onclick="return newOpen()">local3</a><br />
            <div id="di"></div>
            <div style="display:none;" id="ifc"></div>
        </body>

        </html>
<?php

    }
}
