<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
<input type="file" id="xxx">
<button id="btn">点击</button>

<script src="/static/ui/js/jquery.min.js"></script>

<script>
var x = document.getElementById('xxx');
document.getElementById('btn').onclick = function () {
    var re = new FileReader();
    re.readAsDataURL(x.files[0]);
    re.onload = function () {
        var xxx = this.result;
        $.post('test1.php',{data:xxx},function (e) {

           alert(e);
        })
    }

}
</script>

</body>
</html>