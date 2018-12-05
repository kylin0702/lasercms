function getShock(a) {
    var snu=$(a).attr("data-snu");
    $.get('/admin/equstatuss/getShock?s=' + 'I00032171103', function (data) {
           draw(data);
     });

}
function  draw(data) {

    // 获取上下文
    var shock_canvas = document.getElementById('shock_canvas');
    var context = shock_canvas.getContext("2d");


    // 绘制背景
    var gradient = context.createLinearGradient(0,0,0,300);


    gradient.addColorStop(0,"#e0e0e0");
    gradient.addColorStop(1,"#ffffff");


    context.fillStyle = gradient;

    context.fillRect(0,0,shock_canvas.width,shock_canvas.height);


    // 描绘边框
    var grid_cols = 32;
    var grid_rows = 8;
    var cell_height = 40;
    var cell_width = 40;
    context.lineWidth = 2;
    context.strokeStyle = "#a0a0a0";

    // 结束边框描绘
    context.beginPath();
    // 准备画横线
    for (var col = 0; col <= grid_cols; col++) {
        var x = col * cell_width;
        context.moveTo(x,0);
        context.lineTo(x,shock_canvas.height);
    }
    // 准备画竖线
    for(var row = 0; row <= grid_rows; row++){
        var y = row * cell_height;
        context.moveTo(0,y);
        context.lineTo(shock_canvas.width, y);
    }
    context.lineWidth = 1;
    context.strokeStyle = "#c0c0c0";
    context.stroke();


    //绘制坐标图形
    for(var i=0;i<data.length;i++){
        var p =data[i].split('');
        for(var j=0;j<p.length;j++){
            if(p[j]==1){
                context.beginPath();
                context.arc(40*j+20,40*i+20,10,0,2*Math.PI);
                context.fillStyle = "#ee0000";
                context.fill();
                context.fillStyle = "#333";
                context.fillText(parseInt(i*32+j+1),40*j+13,40*i+39)
                context.closePath();
            }
            else{
                context.beginPath();
                context.arc(40*j+20,40*i+20,10,0,2*Math.PI);
                context.fillStyle = "#00ee00";
                context.fill();
                context.closePath();
            }
        }

    }
}
